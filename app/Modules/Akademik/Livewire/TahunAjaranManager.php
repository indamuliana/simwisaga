<?php

namespace App\Modules\Akademik\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Akademik\Support\AcademicContext;
use App\Modules\Kepegawaian\Models\PenugasanMengajar;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Tahun Ajaran & Semester')]
class TahunAjaranManager extends Component
{
    use GuardsPermission;

    public bool $showModal = false;
    public string $nama = '';
    public ?string $tgl_mulai = null;
    public ?string $tgl_selesai = null;
    public bool $is_active = false;

    // Smart Transition Dialog
    public bool $showTransitionModal = false;
    public ?int $targetSemesterId = null;
    public ?int $penugasanCountToCopy = 0;
    public bool $lockNilaiSemesterLama = true;
    public bool $lockAbsensiSemesterLama = true;
    public bool $salinPenugasanMengajar = true;

    protected function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:20', 'unique:tahun_ajarans,nama'],
            'tgl_mulai' => ['nullable', 'date'],
            'tgl_selesai' => ['nullable', 'date', 'after_or_equal:tgl_mulai'],
            'is_active' => ['boolean'],
        ];
    }

    public function create(): void
    {
        $this->guard('akademik.tahunajaran.tambah');
        $this->reset(['nama', 'tgl_mulai', 'tgl_selesai', 'is_active']);
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->guard('akademik.tahunajaran.tambah');
        $this->validate();

        if ($this->is_active) {
            TahunAjaran::query()->update(['is_active' => false]);
            Semester::query()->update(['is_active' => false]);
        }

        $ta = TahunAjaran::create([
            'nama' => $this->nama,
            'tgl_mulai' => $this->tgl_mulai ?: null,
            'tgl_selesai' => $this->tgl_selesai ?: null,
            'is_active' => $this->is_active,
        ]);

        // Otomatis buat semester Ganjil dan Genap
        Semester::create([
            'tahun_ajaran_id' => $ta->id,
            'semester' => 'ganjil',
            'is_active' => $this->is_active,
        ]);
        Semester::create([
            'tahun_ajaran_id' => $ta->id,
            'semester' => 'genap',
            'is_active' => false,
        ]);

        $this->showModal = false;
        session()->flash('status', "Tahun ajaran {$ta->nama} berhasil ditambahkan.");
    }

    public function openTransitionModal(int $semesterId): void
    {
        $this->guard('akademik.tahunajaran.edit');
        $this->targetSemesterId = $semesterId;
        $currentSem = Semester::where('is_active', true)->first();

        if ($currentSem) {
            $this->penugasanCountToCopy = PenugasanMengajar::where('semester_id', $currentSem->id)->count();
        } else {
            $this->penugasanCountToCopy = 0;
        }

        $this->lockNilaiSemesterLama = true;
        $this->lockAbsensiSemesterLama = true;
        $this->salinPenugasanMengajar = ($this->penugasanCountToCopy > 0);
        $this->showTransitionModal = true;
    }

    public function closeTransitionModal(): void
    {
        $this->showTransitionModal = false;
        $this->targetSemesterId = null;
    }

    public function executeTransition(): void
    {
        $this->guard('akademik.tahunajaran.edit');
        if (! $this->targetSemesterId) {
            return;
        }

        $target = Semester::with('tahunAjaran')->findOrFail($this->targetSemesterId);
        $prev = Semester::with('tahunAjaran')->where('is_active', true)->first();
        $copiedCount = 0;

        DB::transaction(function () use ($target, $prev, &$copiedCount) {
            // 1. Kunci semester lama jika diminta
            if ($prev && $prev->id !== $target->id) {
                if ($this->lockNilaiSemesterLama) {
                    $prev->update(['nilai_locked_at' => $prev->nilai_locked_at ?? now()]);
                }
                if ($this->lockAbsensiSemesterLama) {
                    $prev->update(['absensi_locked_at' => $prev->absensi_locked_at ?? now()]);
                }
            }

            // 2. Salin penugasan mengajar jika diminta
            if ($prev && $this->salinPenugasanMengajar && $this->penugasanCountToCopy > 0) {
                $prevPenugasans = PenugasanMengajar::where('semester_id', $prev->id)->get();
                $isSameTa = ($prev->tahun_ajaran_id === $target->tahun_ajaran_id);

                foreach ($prevPenugasans as $p) {
                    $targetRombelId = $p->rombel_id;

                    if (! $isSameTa) {
                        $prevRombel = Rombel::find($p->rombel_id);
                        if ($prevRombel) {
                            $targetRombel = Rombel::where('tahun_ajaran_id', $target->tahun_ajaran_id)
                                ->where('nama', $prevRombel->nama)
                                ->first();
                            $targetRombelId = $targetRombel?->id;
                        } else {
                            $targetRombelId = null;
                        }
                    }

                    if ($targetRombelId) {
                        $exists = PenugasanMengajar::where('semester_id', $target->id)
                            ->where('pegawai_id', $p->pegawai_id)
                            ->where('mapel_id', $p->mapel_id)
                            ->where('rombel_id', $targetRombelId)
                            ->exists();

                        if (! $exists) {
                            PenugasanMengajar::create([
                                'semester_id' => $target->id,
                                'pegawai_id' => $p->pegawai_id,
                                'mapel_id' => $p->mapel_id,
                                'rombel_id' => $targetRombelId,
                                'jp' => $p->jp,
                                'keterangan' => $p->keterangan,
                            ]);
                            $copiedCount++;
                        }
                    }
                }
            }

            // 3. Aktifkan target semester & tahun ajaran
            TahunAjaran::query()->update(['is_active' => false]);
            Semester::query()->update(['is_active' => false]);

            $target->update(['is_active' => true]);
            $target->tahunAjaran->update(['is_active' => true]);

            AcademicContext::resetToActive();
        });

        $this->showTransitionModal = false;

        $msg = "Semester aktif berhasil diubah menjadi: {$target->label}.";
        if ($copiedCount > 0) {
            $msg .= " Sebanyak {$copiedCount} penugasan mengajar guru berhasil disalin.";
        }
        if ($prev && ($this->lockNilaiSemesterLama || $this->lockAbsensiSemesterLama)) {
            $msg .= " Data semester sebelumnya ({$prev->label}) telah dikunci aman.";
        }

        session()->flash('status', $msg);
    }

    public function setActiveSemester(int $semesterId, bool $direct = false): void
    {
        if ($direct) {
            $this->targetSemesterId = $semesterId;
            $this->executeTransition();
        } else {
            $this->openTransitionModal($semesterId);
        }
    }

    public function toggleLockNilai(int $semesterId): void
    {
        $this->guard('akademik.tahunajaran.edit');

        $sem = Semester::findOrFail($semesterId);
        $sem->nilai_locked_at = $sem->nilai_locked_at ? null : now();
        $sem->save();

        session()->flash('status', 'Status penguncian nilai diperbarui.');
    }

    public function toggleLockAbsensi(int $semesterId): void
    {
        $this->guard('akademik.tahunajaran.edit');

        $sem = Semester::findOrFail($semesterId);
        $sem->absensi_locked_at = $sem->absensi_locked_at ? null : now();
        $sem->save();

        session()->flash('status', 'Status penguncian absensi diperbarui.');
    }

    public function render()
    {
        $tahunAjarans = TahunAjaran::with('semesters')->orderBy('id', 'desc')->get();
        $targetSem = $this->targetSemesterId ? Semester::with('tahunAjaran')->find($this->targetSemesterId) : null;
        $activeSem = Semester::with('tahunAjaran')->where('is_active', true)->first();

        return view('akademik::livewire.tahun-ajaran-manager', [
            'tahunAjarans' => $tahunAjarans,
            'targetSem' => $targetSem,
            'activeSem' => $activeSem,
        ]);
    }
}
