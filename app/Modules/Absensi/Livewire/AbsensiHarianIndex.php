<?php

namespace App\Modules\Absensi\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Enums\StatusAbsensi;
use App\Modules\Absensi\Models\AbsensiHarian;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Support\AcademicContext;
use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

class AbsensiHarianIndex extends Component
{
    use GuardsPermission;

    public $tanggal;
    public $rombel_id = '';
    public $gender = '';
    public $rayon_id = '';
    public $search = '';

    public $rombels = [];
    public $rayons = [];
    public $absensiData = [];

    public function mount()
    {
        $this->tanggal = date('Y-m-d');
        $activeTa = AcademicContext::currentTahunAjaran();
        $this->rombels = Rombel::where('tahun_ajaran_id', $activeTa?->id)->orderBy('tingkat')->orderBy('nama')->get();
        if ($this->rombels->isEmpty()) {
            $this->rombels = Rombel::orderBy('tingkat')->orderBy('nama')->get();
        }
        $this->rayons = Rayon::orderBy('nama')->get();
        $this->loadData();
    }

    public function updatedRombelId()
    {
        $this->loadData();
    }

    public function updatedTanggal()
    {
        $this->loadData();
    }

    public function loadData()
    {
        if (!$this->tanggal) {
            $this->absensiData = [];
            return;
        }

        $allSiswas = $this->getAllSiswas();
        $query = AbsensiHarian::where('tanggal', $this->tanggal);
        if ($this->rombel_id) {
            $query->where('rombel_id', $this->rombel_id);
        }
        $absensis = $query->get()->keyBy('siswa_id');

        $this->absensiData = [];
        foreach ($allSiswas as $siswa) {
            $this->absensiData[$siswa->id] = [
                'status' => $absensis[$siswa->id]->status->value ?? null,
                'keterangan' => $absensis[$siswa->id]->keterangan ?? '',
            ];
        }
    }

    public function getAllSiswas()
    {
        $castType = DB::getDriverName() === 'sqlite' ? 'INTEGER' : 'UNSIGNED';

        $query = Siswa::with(['rayon', 'rombel']);

        if (Siswa::where('status', 'aktif')->exists()) {
            $query->where('status', 'aktif');
        }

        if ($this->rombel_id) {
            $query->inRombel($this->rombel_id);
        }

        return $query->orderByRaw("CAST(nipd AS {$castType}) ASC, nipd ASC")->get();
    }

    public function getFilteredSiswas()
    {
        $all = $this->getAllSiswas();

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
                $matchRombel = str_contains(strtolower($s->rombel?->nama ?? ''), $term);

                if (!$matchNama && !$matchNipd && !$matchNisn && !$matchRombel) {
                    return false;
                }
            }
            return true;
        });
    }

    public function getStatistikProperty(): array
    {
        $totalSiswa = Siswa::where('status', 'aktif')->count();
        if ($totalSiswa === 0) {
            $totalSiswa = Siswa::count();
        }

        $counts = [
            'total' => $totalSiswa,
            'hadir' => 0,
            'sakit' => 0,
            'izin' => 0,
            'alpa' => 0,
            'dispen_cbt_plh' => 0,
            'dispen_cbt_kwh' => 0,
            'dispen_lainnya' => 0,
            'belum_absen' => 0,
        ];

        // Ambil seluruh data absensi pada tanggal ini dari database (sekolah-wide / seluruh siswa)
        $dbAbsensi = AbsensiHarian::where('tanggal', $this->tanggal)
            ->get()
            ->mapWithKeys(function ($item) {
                $val = $item->status instanceof StatusAbsensi ? $item->status->value : $item->status;
                return [$item->siswa_id => $val];
            })
            ->toArray();

        // Gabungkan secara real-time dengan data absensi yang sedang diedit di layar
        if (!empty($this->absensiData)) {
            foreach ($this->absensiData as $siswaId => $data) {
                if (!empty($data['status'])) {
                    $dbAbsensi[$siswaId] = $data['status'];
                } else {
                    unset($dbAbsensi[$siswaId]);
                }
            }
        }

        foreach ($dbAbsensi as $statusVal) {
            match ($statusVal) {
                StatusAbsensi::HADIR->value => $counts['hadir']++,
                StatusAbsensi::SAKIT->value => $counts['sakit']++,
                StatusAbsensi::IZIN->value => $counts['izin']++,
                StatusAbsensi::ALPA->value => $counts['alpa']++,
                StatusAbsensi::DISPEN_CBT_PLH->value => $counts['dispen_cbt_plh']++,
                StatusAbsensi::DISPEN_CBT_KWH->value => $counts['dispen_cbt_kwh']++,
                StatusAbsensi::DISPEN_LAINNYA->value => $counts['dispen_lainnya']++,
                default => null,
            };
        }

        $totalSudahAbsen = count($dbAbsensi);
        $counts['belum_absen'] = max(0, $totalSiswa - $totalSudahAbsen);

        return $counts;
    }

    public function tandaiSemuaHadir()
    {
        $filtered = $this->getFilteredSiswas();
        foreach ($filtered as $siswa) {
            // Hanya menandai jika status masih kosong/belum diisi (tidak menimpa yang sudah diisi)
            if (empty($this->absensiData[$siswa->id]['status'])) {
                $this->absensiData[$siswa->id]['status'] = StatusAbsensi::HADIR->value;
            }
        }
    }

    public function save()
    {
        $this->guard('absensi.harian.input');

        $this->validate([
            'tanggal' => 'required|date',
            'rombel_id' => 'nullable|exists:rombels,id',
            'absensiData.*.status' => 'nullable|string',
            'absensiData.*.keterangan' => 'nullable|string|max:255',
        ]);

        $allSiswas = Siswa::with('rombel')->whereIn('id', array_keys($this->absensiData))->get()->keyBy('id');

        $defaultSemesterId = AcademicContext::currentSemester()?->id;
        if (!$defaultSemesterId) {
            $defaultSemesterId = \App\Modules\Akademik\Models\Semester::where('is_active', true)->first()?->id;
        }
        if (!$defaultSemesterId) {
            $defaultSemesterId = \App\Modules\Akademik\Models\Semester::orderByDesc('id')->first()?->id;
        }

        DB::transaction(function () use ($allSiswas, $defaultSemesterId) {
            foreach ($this->absensiData as $siswaId => $data) {
                if (empty($data['status'])) {
                    continue;
                }

                $siswa = $allSiswas->get($siswaId);
                $targetRombelId = $this->rombel_id ?: $siswa?->rombel_id;

                $semesterId = $defaultSemesterId;
                if ($targetRombelId && $siswa?->rombel?->tahun_ajaran_id) {
                    $sem = \App\Modules\Akademik\Models\Semester::where('tahun_ajaran_id', $siswa->rombel->tahun_ajaran_id)
                        ->where('is_active', true)
                        ->first();
                    if ($sem) {
                        $semesterId = $sem->id;
                    }
                }

                AbsensiHarian::updateOrCreate(
                    [
                        'tanggal' => $this->tanggal,
                        'siswa_id' => $siswaId,
                    ],
                    [
                        'rombel_id' => $targetRombelId,
                        'status' => $data['status'],
                        'keterangan' => $data['keterangan'] ?? null,
                        'semester_id' => $semesterId,
                        'diinput_oleh' => auth()->id(),
                    ]
                );
            }
        });

        session()->flash('message', 'Absensi harian berhasil disimpan.');
    }

    public function render()
    {
        $statuses = StatusAbsensi::cases();
        $siswas = $this->getFilteredSiswas();
        $statistik = $this->statistik;

        return view('absensi::livewire.absensi-harian-index', compact('statuses', 'siswas', 'statistik'));
    }
}
