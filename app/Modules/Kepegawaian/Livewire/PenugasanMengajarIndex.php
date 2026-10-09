<?php

namespace App\Modules\Kepegawaian\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Core\Services\DocumentEngine;
use App\Modules\Akademik\Models\Mapel;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\StrukturKurikulum;
use App\Modules\Akademik\Support\AcademicContext;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kepegawaian\Models\PenugasanMengajar;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Penugasan Mengajar')]
class PenugasanMengajarIndex extends Component
{
    use GuardsPermission;

    public bool $showModal = false;
    public ?int $penugasanId = null;

    // Filters
    public ?int $filterSemester = null;
    public ?int $filterPegawai = null;
    public ?int $filterRombel = null;

    // Form fields
    public ?int $semester_id = null;
    public ?int $pegawai_id = null;
    public ?int $mapel_id = null;
    public ?int $rombel_id = null;
    public int $jp = 2;
    public ?string $keterangan = null;

    public function mount(): void
    {
        $currentSem = AcademicContext::currentSemester();
        $this->filterSemester = $currentSem?->id;
        $this->semester_id = $currentSem?->id;
    }

    public function updated($propertyName): void
    {
        if (in_array($propertyName, ['rombel_id', 'mapel_id'])) {
            $this->checkCurriculumJp();
        }
    }

    protected function checkCurriculumJp(): void
    {
        if ($this->rombel_id && $this->mapel_id && $this->semester_id) {
            $rombel = Rombel::find($this->rombel_id);
            if ($rombel) {
                $sk = StrukturKurikulum::where('tahun_ajaran_id', $rombel->tahun_ajaran_id)
                    ->where('jurusan_id', $rombel->jurusan_id)
                    ->where('tingkat', $rombel->tingkat)
                    ->where('mapel_id', $this->mapel_id)
                    ->first();
                if ($sk) {
                    $this->jp = $sk->jp_per_minggu;
                }
            }
        }
    }

    protected function rules(): array
    {
        return [
            'semester_id' => ['required', 'exists:semesters,id'],
            'pegawai_id' => ['required', 'exists:pegawais,id'],
            'rombel_id' => ['required', 'exists:rombels,id'],
            'mapel_id' => ['required', 'exists:mapels,id'],
            'jp' => ['required', 'integer', 'min:1', 'max:40'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function create(): void
    {
        $this->guard('kepegawaian.penugasan.tambah');
        $this->reset(['penugasanId', 'keterangan']);
        $this->semester_id = $this->filterSemester ?? AcademicContext::currentSemester()?->id;

        $firstGuru = Pegawai::where('jenis_gtk', 'guru')->where('is_active', true)->first();
        $this->pegawai_id = $this->filterPegawai ?? $firstGuru?->id;

        $firstRombel = Rombel::first();
        $this->rombel_id = $this->filterRombel ?? $firstRombel?->id;

        $firstMapel = Mapel::where('is_active', true)->first();
        $this->mapel_id = $firstMapel?->id;

        $this->jp = 2;
        $this->checkCurriculumJp();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $this->guard('kepegawaian.penugasan.edit');
        $p = PenugasanMengajar::findOrFail($id);
        $this->penugasanId = $p->id;
        $this->semester_id = $p->semester_id;
        $this->pegawai_id = $p->pegawai_id;
        $this->rombel_id = $p->rombel_id;
        $this->mapel_id = $p->mapel_id;
        $this->jp = $p->jp;
        $this->keterangan = $p->keterangan;
        $this->showModal = true;
    }

    public function save(): void
    {
        if ($this->penugasanId) {
            $this->guard('kepegawaian.penugasan.edit');
        } else {
            $this->guard('kepegawaian.penugasan.tambah');
        }

        $this->validate();

        $data = [
            'semester_id' => $this->semester_id,
            'pegawai_id' => $this->pegawai_id,
            'rombel_id' => $this->rombel_id,
            'mapel_id' => $this->mapel_id,
            'jp' => $this->jp,
            'keterangan' => $this->keterangan ?: null,
        ];

        if ($this->penugasanId) {
            PenugasanMengajar::findOrFail($this->penugasanId)->update($data);
        } else {
            PenugasanMengajar::updateOrCreate(
                [
                    'semester_id' => $this->semester_id,
                    'pegawai_id' => $this->pegawai_id,
                    'rombel_id' => $this->rombel_id,
                    'mapel_id' => $this->mapel_id,
                ],
                $data
            );
        }

        $this->showModal = false;
        session()->flash('status', 'Penugasan mengajar berhasil disimpan.');
    }

    public function delete(int $id): void
    {
        $this->guard('kepegawaian.penugasan.hapus');
        PenugasanMengajar::findOrFail($id)->delete();
        session()->flash('status', 'Penugasan mengajar berhasil dihapus.');
    }

    public function cetakRekap()
    {
        $this->guard('kepegawaian.penugasan.cetak');

        $sem = Semester::with('tahunAjaran')->find($this->filterSemester);
        $query = PenugasanMengajar::with(['pegawai', 'rombel', 'mapel']);
        if ($this->filterSemester) {
            $query->where('semester_id', $this->filterSemester);
        }
        $assignments = $query->get()->groupBy('pegawai_id');

        $rows = '';
        $no = 1;
        $grandTotalJp = 0;

        foreach ($assignments as $pegawaiId => $items) {
            $pegawai = $items->first()->pegawai;
            $nama = $pegawai?->nama_gelar ?? 'Guru';
            $nip = $pegawai?->nip ?? '-';
            $totalJpGuru = $items->sum('jp');
            $grandTotalJp += $totalJpGuru;

            $mapelList = $items->map(fn ($it) => "{$it->mapel?->nama} ({$it->rombel?->nama} - {$it->jp} JP)")->join('<br>');

            $rows .= "
                <tr>
                    <td style='text-align: center;'>{$no}</td>
                    <td><b>{$nama}</b><br><small style='color: #64748b;'>NIP: {$nip}</small></td>
                    <td>{$mapelList}</td>
                    <td style='text-align: center; font-weight: bold;'>{$totalJpGuru} JP</td>
                </tr>
            ";
            $no++;
        }

        $html = "
            <table class='content-table'>
                <thead>
                    <tr>
                        <th style='width: 30px; text-align: center;'>No</th>
                        <th>Nama Guru & NIP</th>
                        <th>Daftar Mapel & Rombel yang Diampu</th>
                        <th style='width: 90px; text-align: center;'>Beban (JP)</th>
                    </tr>
                </thead>
                <tbody>
                    {$rows}
                    <tr style='background-color: #f8fafc; font-weight: bold;'>
                        <td colspan='3' style='text-align: right;'>TOTAL KESELURUHAN BEBAN JAM MENGAJAR:</td>
                        <td style='text-align: center;'>{$grandTotalJp} JP</td>
                    </tr>
                </tbody>
            </table>
        ";

        $pdf = DocumentEngine::createPdf(
            title: 'REKAPITULASI PEMBAGIAN TUGAS MENGAJAR GURU',
            content: $html,
            options: [
                'subtitle' => 'Tahun Ajaran ' . ($sem?->tahunAjaran?->nama ?? '') . ' Semester ' . ucfirst($sem?->semester ?? ''),
                'template_kode' => 'leger_nilai',
            ]
        );

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'Rekap_Beban_Mengajar_Guru.pdf'
        );
    }

    public function render()
    {
        $query = PenugasanMengajar::with(['semester.tahunAjaran', 'pegawai', 'mapel', 'rombel']);

        if ($this->filterSemester) {
            $query->where('semester_id', $this->filterSemester);
        }
        if ($this->filterPegawai) {
            $query->where('pegawai_id', $this->filterPegawai);
        }
        if ($this->filterRombel) {
            $query->where('rombel_id', $this->filterRombel);
        }

        $penugasans = $query->orderBy('pegawai_id')->get();
        $totalJp = $penugasans->sum('jp');

        $semesters = Semester::with('tahunAjaran')->orderByDesc('tahun_ajaran_id')->get();
        $gurus = Pegawai::where('jenis_gtk', 'guru')->where('is_active', true)->orderBy('nama_lengkap')->get();
        $rombels = Rombel::orderBy('tingkat')->orderBy('nama')->get();
        $mapels = Mapel::where('is_active', true)->orderBy('kelompok')->orderBy('nama')->get();

        return view('kepegawaian::livewire.penugasan-mengajar-index', [
            'penugasans' => $penugasans,
            'totalJp' => $totalJp,
            'semesters' => $semesters,
            'gurus' => $gurus,
            'rombels' => $rombels,
            'mapels' => $mapels,
        ]);
    }
}
