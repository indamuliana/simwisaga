<?php

namespace App\Modules\Absensi\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Enums\StatusAbsensi;
use App\Modules\Absensi\Models\AbsensiHarian;
use App\Modules\Absensi\Models\AbsensiMapel;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Support\AcademicContext;
use App\Modules\Kepegawaian\Models\PenugasanMengajar;
use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AbsensiMapelIndex extends Component
{
    use GuardsPermission;

    public $tanggal;
    public $rombel_id = '';
    public $penugasan_id = '';
    public $gender = '';
    public $rayon_id = '';
    public $search = '';

    public $penugasans = [];
    public $rayons = [];
    public $absensiMapelData = [];
    public $absensiHarianStatus = [];

    public function mount()
    {
        $this->tanggal = date('Y-m-d');
        $this->rayons = Rayon::orderBy('nama')->get();
        $this->loadPenugasans();

        // Auto-select rombel jika guru hanya mengampu 1 rombel
        $rombels = $this->availableRombels;
        if ($rombels->count() === 1) {
            $this->rombel_id = (string) $rombels->first()->id;

            // Auto-select penugasan jika rombel tersebut hanya punya 1 mapel
            $penugasans = $this->availablePenugasans;
            if ($penugasans->count() === 1) {
                $this->penugasan_id = (string) $penugasans->first()->id;
                $this->loadData();
            }
        }
    }

    public function loadPenugasans()
    {
        $user = auth()->user();
        $currentSem = AcademicContext::currentSemester();
        $query = PenugasanMengajar::with(['rombel', 'mapel', 'pegawai']);
        if ($currentSem) {
            $query->where('semester_id', $currentSem->id);
        }

        if ($user && !$user->isSuperAdmin()) {
            $pegawai = \App\Modules\Kepegawaian\Models\Pegawai::where('user_id', $user->id)->first();
            if ($pegawai) {
                $query->where('pegawai_id', $pegawai->id);
            } else {
                $query->where('id', 0); // User bukan guru
            }
        }

        $this->penugasans = $query->get();
    }

    public function getAvailableRombelsProperty()
    {
        $user = auth()->user();
        if ($user && !$user->isSuperAdmin()) {
            $pegawai = \App\Modules\Kepegawaian\Models\Pegawai::where('user_id', $user->id)->first();
            if ($pegawai) {
                return collect($this->penugasans)
                    ->pluck('rombel')
                    ->filter()
                    ->unique('id')
                    ->sortBy(fn($r) => sprintf('%s-%s', $r->tingkat ?? '', $r->nama ?? ''))
                    ->values();
            }
        }

        // Bagi Super Admin / Pengguna Umum: Tampilkan seluruh rombel sekolah (X, XI, XII)
        $activeTa = AcademicContext::currentTahunAjaran();
        $query = \App\Modules\Akademik\Models\Rombel::query();
        if ($activeTa) {
            $query->where('tahun_ajaran_id', $activeTa->id);
        }

        $rombels = $query->orderBy('tingkat')->orderBy('nama')->get();
        if ($rombels->isEmpty()) {
            $rombels = \App\Modules\Akademik\Models\Rombel::orderBy('tingkat')->orderBy('nama')->get();
        }

        return $rombels;
    }

    public function getAvailablePenugasansProperty()
    {
        if (!$this->rombel_id) {
            return collect();
        }

        return collect($this->penugasans)
            ->where('rombel_id', (int) $this->rombel_id)
            ->sortBy(fn($p) => $p->mapel?->nama ?? '')
            ->values();
    }

    public function updatedRombelId($value)
    {
        $this->penugasan_id = '';
        $this->absensiMapelData = [];
        $this->absensiHarianStatus = [];

        // Auto-select mapel jika rombel yang dipilih hanya memiliki 1 penugasan
        $available = $this->availablePenugasans;
        if ($available->count() === 1) {
            $this->penugasan_id = (string) $available->first()->id;
            $this->loadData();
        }
    }

    public function updatedPenugasanId()
    {
        // Sinkronisasi rombel_id jika penugasan_id diset secara langsung
        if ($this->penugasan_id) {
            $penugasan = collect($this->penugasans)->firstWhere('id', (int) $this->penugasan_id);
            if ($penugasan && (string) $this->rombel_id !== (string) $penugasan->rombel_id) {
                $this->rombel_id = (string) $penugasan->rombel_id;
            }
        }
        $this->loadData();
    }

    public function updatedTanggal()
    {
        $this->loadData();
    }

    public function loadData()
    {
        if (!$this->penugasan_id || !$this->tanggal) {
            $this->absensiMapelData = [];
            $this->absensiHarianStatus = [];
            return;
        }

        $penugasan = PenugasanMengajar::find($this->penugasan_id);
        if (!$penugasan) return;

        $allSiswas = $this->getAllPenugasanSiswas();

        $harian = AbsensiHarian::where('tanggal', $this->tanggal)
            ->where('rombel_id', $penugasan->rombel_id)
            ->get()
            ->keyBy('siswa_id');

        $mapel = AbsensiMapel::where('tanggal', $this->tanggal)
            ->where('penugasan_mengajar_id', $this->penugasan_id)
            ->get()
            ->keyBy('siswa_id');

        $this->absensiMapelData = [];
        $this->absensiHarianStatus = [];

        foreach ($allSiswas as $siswa) {
            $harianStatus = $harian[$siswa->id]->status->value ?? null;
            $this->absensiHarianStatus[$siswa->id] = $harianStatus;

            $existingMapelStatus = $mapel[$siswa->id]->status->value ?? null;
            $defaultStatus = $existingMapelStatus ?? ($harianStatus ?? StatusAbsensi::HADIR->value);

            $this->absensiMapelData[$siswa->id] = [
                'status' => $defaultStatus,
                'keterangan' => $mapel[$siswa->id]->keterangan ?? '',
            ];
        }
    }

    public function getAllPenugasanSiswas()
    {
        if (!$this->penugasan_id) {
            return collect();
        }

        $penugasan = PenugasanMengajar::find($this->penugasan_id);
        if (!$penugasan) {
            return collect();
        }

        $castType = DB::getDriverName() === 'sqlite' ? 'INTEGER' : 'UNSIGNED';

        return Siswa::with(['rayon', 'rombel'])
            ->inRombel($penugasan->rombel_id)
            ->orderByRaw("CAST(nipd AS {$castType}) ASC, nipd ASC")
            ->get();
    }

    public function getFilteredSiswas()
    {
        $all = $this->getAllPenugasanSiswas();

        return $all->filter(function ($s) {
            if ($this->gender !== '' && strtoupper($s->jk) !== strtoupper($this->gender)) {
                return false;
            }
            if ($this->rayon_id !== '' && (int) $s->rayon_id !== (int) $this->rayon_id) {
                return false;
            }
            if ($this->search !== '') {
                $term = strtolower(trim($this->search));
                $matchNama = str_contains(strtolower($s->nama), $term);
                $matchNipd = str_contains(strtolower((string) ($s->nipd ?? '')), $term);
                $matchNisn = str_contains(strtolower((string) ($s->nisn ?? '')), $term);

                if (!$matchNama && !$matchNipd && !$matchNisn) {
                    return false;
                }
            }
            return true;
        });
    }

    public function getStatistikProperty(): array
    {
        $all = $this->getAllPenugasanSiswas();
        $total = $all->count();

        $counts = [
            'total' => $total,
            'hadir' => 0,
            'sakit' => 0,
            'izin' => 0,
            'alpa' => 0,
            'dispen_cbt_plh' => 0,
            'dispen_cbt_kwh' => 0,
            'dispen_lainnya' => 0,
            'belum_absen' => 0,
        ];

        foreach ($all as $siswa) {
            $status = $this->absensiMapelData[$siswa->id]['status'] ?? null;
            match ($status) {
                StatusAbsensi::HADIR->value => $counts['hadir']++,
                StatusAbsensi::SAKIT->value => $counts['sakit']++,
                StatusAbsensi::IZIN->value => $counts['izin']++,
                StatusAbsensi::ALPA->value => $counts['alpa']++,
                StatusAbsensi::DISPEN_CBT_PLH->value => $counts['dispen_cbt_plh']++,
                StatusAbsensi::DISPEN_CBT_KWH->value => $counts['dispen_cbt_kwh']++,
                StatusAbsensi::DISPEN_LAINNYA->value => $counts['dispen_lainnya']++,
                default => $counts['belum_absen']++,
            };
        }

        return $counts;
    }

    public function tandaiSemuaHadir()
    {
        $filtered = $this->getFilteredSiswas();
        foreach ($filtered as $siswa) {
            // Tandai hadir bagi yang belum terisi status absensinya
            if (empty($this->absensiMapelData[$siswa->id]['status'])) {
                $this->absensiMapelData[$siswa->id]['status'] = StatusAbsensi::HADIR->value;
            }
        }
    }

    public function save()
    {
        $this->guard('absensi.mapel.input');

        $this->validate([
            'tanggal' => 'required|date',
            'rombel_id' => 'required|exists:rombels,id',
            'penugasan_id' => 'required|exists:penugasan_mengajars,id',
            'absensiMapelData.*.status' => 'nullable|string',
            'absensiMapelData.*.keterangan' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () {
            foreach ($this->absensiMapelData as $siswaId => $data) {
                if (empty($data['status'])) {
                    continue;
                }

                AbsensiMapel::updateOrCreate(
                    [
                        'tanggal' => $this->tanggal,
                        'siswa_id' => $siswaId,
                        'penugasan_mengajar_id' => $this->penugasan_id,
                    ],
                    [
                        'status' => $data['status'],
                        'keterangan' => $data['keterangan'] ?? null,
                        'diinput_oleh' => auth()->id(),
                    ]
                );
            }
        });

        session()->flash('message', 'Absensi mapel berhasil disimpan.');
    }

    public function render()
    {
        $statuses = StatusAbsensi::cases();
        $siswas = $this->getFilteredSiswas();
        $penugasan = $this->penugasan_id ? PenugasanMengajar::with(['rombel', 'mapel', 'pegawai'])->find($this->penugasan_id) : null;
        $statistik = $this->statistik;

        return view('absensi::livewire.absensi-mapel-index', compact('statuses', 'siswas', 'penugasan', 'statistik'));
    }
}
