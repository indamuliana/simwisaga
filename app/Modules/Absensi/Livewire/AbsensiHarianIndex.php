<?php

namespace App\Modules\Absensi\Livewire;

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
    public $tanggal;
    public $rombel_id;
    public $gender = '';
    public $rayon_id = '';

    public $rombels = [];
    public $rayons = [];
    public $absensiData = [];

    public function mount()
    {
        $this->tanggal = date('Y-m-d');
        $this->rombels = Rombel::orderBy('nama')->get();
        $this->rayons = Rayon::orderBy('nama')->get();
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
        if (!$this->rombel_id || !$this->tanggal) {
            $this->absensiData = [];
            return;
        }

        $allSiswas = $this->getAllRombelSiswas();
        $absensis = AbsensiHarian::where('tanggal', $this->tanggal)
            ->where('rombel_id', $this->rombel_id)
            ->get()
            ->keyBy('siswa_id');

        $this->absensiData = [];
        foreach ($allSiswas as $siswa) {
            $this->absensiData[$siswa->id] = [
                'status' => $absensis[$siswa->id]->status->value ?? null,
                'keterangan' => $absensis[$siswa->id]->keterangan ?? '',
            ];
        }
    }

    public function getAllRombelSiswas()
    {
        if (!$this->rombel_id) {
            return collect();
        }

        return Siswa::with('rayon')
            ->where('rombel_id', $this->rombel_id)
            ->orderBy('nama')
            ->get();
    }

    public function getFilteredSiswas()
    {
        $all = $this->getAllRombelSiswas();

        return $all->filter(function ($s) {
            if ($this->gender !== '' && strtoupper($s->jk) !== strtoupper($this->gender)) {
                return false;
            }
            if ($this->rayon_id !== '' && (int) $s->rayon_id !== (int) $this->rayon_id) {
                return false;
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

        // Gabungkan secara real-time dengan data absensi yang sedang diedit di layar (jika rombel aktif)
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
            $this->absensiData[$siswa->id]['status'] = StatusAbsensi::HADIR->value;
        }
    }

    public function save()
    {
        $this->validate([
            'tanggal' => 'required|date',
            'rombel_id' => 'required|exists:rombels,id',
            'absensiData.*.status' => 'nullable|string',
            'absensiData.*.keterangan' => 'nullable|string|max:255',
        ]);

        $semesterId = AcademicContext::currentSemester()?->id;

        DB::transaction(function () use ($semesterId) {
            foreach ($this->absensiData as $siswaId => $data) {
                if (empty($data['status'])) {
                    continue;
                }

                AbsensiHarian::updateOrCreate(
                    [
                        'tanggal' => $this->tanggal,
                        'siswa_id' => $siswaId,
                    ],
                    [
                        'rombel_id' => $this->rombel_id,
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
