<?php

namespace App\Modules\Ekstrakurikuler\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Modules\Ekstrakurikuler\Models\Ekstrakurikuler;
use App\Modules\Ekstrakurikuler\Models\EkstrakurikulerAnggota;
use App\Modules\Ekstrakurikuler\Models\EkskulPresensi;
use App\Modules\Ekstrakurikuler\Models\EkskulPresensiDetail;
use App\Modules\Ekstrakurikuler\Services\EkstrakurikulerService;
use App\Modules\Akademik\Models\Semester;
use Carbon\Carbon;

class EkstrakurikulerPresensi extends Component
{
    public $ekskul_id;
    public $ekskul;
    public $semester_id;

    public $selectedSesiId = null;

    // Sesi details
    public $pertemuan_ke = 1;
    public $tanggal;
    public $jam_mulai;
    public $jam_selesai;
    public $materi = '';
    public $catatan_sesi = '';

    // Attendance checklist [siswa_id => ['status' => 'Hadir', 'catatan' => '']]
    public $attendanceData = [];

    // Filter siswa
    public $searchSiswa = '';

    protected $service;

    public function boot(EkstrakurikulerService $service)
    {
        $this->service = $service;
    }

    public function mount($ekskul_id)
    {
        $this->ekskul_id = $ekskul_id;
        $this->ekskul = Ekstrakurikuler::with(['pembina', 'instrukturUser'])->findOrFail($ekskul_id);

        $activeSemester = Semester::where('is_active', true)->first();
        $this->semester_id = $activeSemester?->id ?? Semester::latest('id')->first()?->id;

        // Cek apakah sudah ada sesi presensi di semester ini
        $latestSesi = EkskulPresensi::where('ekstrakurikuler_id', $this->ekskul_id)
            ->where('semester_id', $this->semester_id)
            ->orderByDesc('pertemuan_ke')
            ->first();

        if ($latestSesi) {
            $this->loadSesi($latestSesi->id);
        } else {
            $this->initNewSesi();
        }
    }

    public function initNewSesi()
    {
        $this->selectedSesiId = null;

        $lastPertemuan = EkskulPresensi::where('ekstrakurikuler_id', $this->ekskul_id)
            ->where('semester_id', $this->semester_id)
            ->max('pertemuan_ke');

        $this->pertemuan_ke = ($lastPertemuan ?? 0) + 1;
        $this->tanggal = Carbon::now()->toDateString();
        $this->jam_mulai = $this->ekskul->jam_mulai ? substr($this->ekskul->jam_mulai, 0, 5) : '15:30';
        $this->jam_selesai = $this->ekskul->jam_selesai ? substr($this->ekskul->jam_selesai, 0, 5) : '17:00';
        $this->materi = '';
        $this->catatan_sesi = '';

        $this->loadAnggotaDefaultAttendance();
    }

    public function loadSesi($sesiId)
    {
        $sesi = EkskulPresensi::with('details')->findOrFail($sesiId);
        $this->selectedSesiId = $sesi->id;
        $this->pertemuan_ke = $sesi->pertemuan_ke;
        $this->tanggal = $sesi->tanggal->format('Y-m-d');
        $this->jam_mulai = $sesi->jam_mulai ? substr($sesi->jam_mulai, 0, 5) : '';
        $this->jam_selesai = $sesi->jam_selesai ? substr($sesi->jam_selesai, 0, 5) : '';
        $this->materi = $sesi->materi ?? '';
        $this->catatan_sesi = $sesi->catatan ?? '';

        $detailsMap = $sesi->details->keyBy('siswa_id');

        // Ambil semua anggota aktif ekskul ini
        $anggotas = EkstrakurikulerAnggota::with('siswa.rombel')
            ->where('ekstrakurikuler_id', $this->ekskul_id)
            ->where('semester_id', $this->semester_id)
            ->where('status_keanggotaan', 'aktif')
            ->get();

        $this->attendanceData = [];
        foreach ($anggotas as $a) {
            $detail = $detailsMap->get($a->siswa_id);
            $this->attendanceData[$a->siswa_id] = [
                'nama' => $a->siswa?->nama ?? 'Siswa',
                'rombel' => $a->siswa?->rombel?->nama ?? '-',
                'status' => $detail?->status ?? 'Hadir',
                'catatan' => $detail?->catatan ?? '',
            ];
        }
    }

    public function loadAnggotaDefaultAttendance()
    {
        $anggotas = EkstrakurikulerAnggota::with('siswa.rombel')
            ->where('ekstrakurikuler_id', $this->ekskul_id)
            ->where('semester_id', $this->semester_id)
            ->where('status_keanggotaan', 'aktif')
            ->get();

        $this->attendanceData = [];
        foreach ($anggotas as $a) {
            $this->attendanceData[$a->siswa_id] = [
                'nama' => $a->siswa?->nama ?? 'Siswa',
                'rombel' => $a->siswa?->rombel?->nama ?? '-',
                'status' => 'Hadir', // Default cepat: hadir
                'catatan' => '',
            ];
        }
    }

    public function setStatus($siswaId, $status)
    {
        if (isset($this->attendanceData[$siswaId])) {
            $this->attendanceData[$siswaId]['status'] = $status;
        }
    }

    public function toggleHadirTidakHadir($siswaId)
    {
        if (isset($this->attendanceData[$siswaId])) {
            $current = $this->attendanceData[$siswaId]['status'];
            $this->attendanceData[$siswaId]['status'] = ($current === 'Hadir') ? 'Tidak Hadir' : 'Hadir';
        }
    }

    public function setSemuaHadir()
    {
        foreach ($this->attendanceData as $siswaId => $item) {
            $this->attendanceData[$siswaId]['status'] = 'Hadir';
        }
        session()->flash('message', 'Seluruh siswa berhasil ditandai Hadir.');
    }

    public function savePresensi()
    {
        $this->validate([
            'pertemuan_ke' => 'required|integer|min:1',
            'tanggal' => 'required|date',
            'jam_mulai' => 'nullable',
            'jam_selesai' => 'nullable',
            'materi' => 'nullable|string|max:255',
        ]);

        // Simpan / update sesi
        $sesi = EkskulPresensi::updateOrCreate(
            [
                'id' => $this->selectedSesiId,
            ],
            [
                'ekstrakurikuler_id' => $this->ekskul_id,
                'semester_id' => $this->semester_id,
                'pertemuan_ke' => $this->pertemuan_ke,
                'tanggal' => $this->tanggal,
                'jam_mulai' => $this->jam_mulai ?: null,
                'jam_selesai' => $this->jam_selesai ?: null,
                'materi' => $this->materi ?: null,
                'catatan' => $this->catatan_sesi ?: null,
                'created_by' => auth()->id(),
            ]
        );

        $this->selectedSesiId = $sesi->id;

        // Simpan rincian kehadiran
        foreach ($this->attendanceData as $siswaId => $data) {
            EkskulPresensiDetail::updateOrCreate(
                [
                    'ekskul_presensi_id' => $sesi->id,
                    'siswa_id' => $siswaId,
                ],
                [
                    'status' => $data['status'] ?? 'Hadir',
                    'catatan' => $data['catatan'] ?? null,
                ]
            );
        }

        // Hitung ulang akumulasi kehadiran untuk seluruh anggota ekskul ini
        $this->service->recalculateAttendance($this->ekskul_id, $this->semester_id);

        session()->flash('message', "Presensi Pertemuan Ke-{$sesi->pertemuan_ke} ({$sesi->tanggal->format('d/m/Y')}) berhasil disimpan. Akumulasi kehadiran rapor diperbarui.");
    }

    public function deleteSesi($sesiId)
    {
        $sesi = EkskulPresensi::findOrFail($sesiId);
        $ke = $sesi->pertemuan_ke;
        $sesi->delete();

        // Recalculate stats
        $this->service->recalculateAttendance($this->ekskul_id, $this->semester_id);

        session()->flash('message', "Sesi Pertemuan Ke-{$ke} berhasil dihapus.");
        $this->initNewSesi();
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        $sesiList = EkskulPresensi::where('ekstrakurikuler_id', $this->ekskul_id)
            ->where('semester_id', $this->semester_id)
            ->orderBy('pertemuan_ke')
            ->get();

        // Hitung hadir vs tidak hadir saat ini
        $countHadir = 0;
        $countTidakHadir = 0;
        $countIzin = 0;
        $countSakit = 0;

        foreach ($this->attendanceData as $item) {
            $st = $item['status'] ?? 'Hadir';
            if ($st === 'Hadir') $countHadir++;
            elseif ($st === 'Tidak Hadir') $countTidakHadir++;
            elseif ($st === 'Izin') $countIzin++;
            elseif ($st === 'Sakit') $countSakit++;
        }

        // Filter list siswa jika ada search
        $filteredAttendance = collect($this->attendanceData);
        if ($this->searchSiswa) {
            $q = strtolower($this->searchSiswa);
            $filteredAttendance = $filteredAttendance->filter(function ($item) use ($q) {
                return str_contains(strtolower($item['nama']), $q) || str_contains(strtolower($item['rombel']), $q);
            });
        }

        return view('ekstrakurikuler::livewire.presensi', [
            'sesiList' => $sesiList,
            'filteredAttendance' => $filteredAttendance,
            'countHadir' => $countHadir,
            'countTidakHadir' => $countTidakHadir,
            'countIzin' => $countIzin,
            'countSakit' => $countSakit,
            'totalSiswa' => count($this->attendanceData),
        ]);
    }
}
