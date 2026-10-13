<?php

namespace App\Modules\SeniBudaya\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Modules\SeniBudaya\Models\SeniBudaya;
use App\Modules\SeniBudaya\Models\SeniBudayaAnggota;
use App\Modules\SeniBudaya\Models\SeniBudayaPresensi as PresensiModel;
use App\Modules\SeniBudaya\Models\SeniBudayaPresensiDetail;
use App\Modules\SeniBudaya\Services\SeniBudayaService;
use App\Modules\Akademik\Models\Semester;

#[Layout('components.layouts.app')]
#[Title('Presensi Seni Budaya')]
class SeniBudayaPresensi extends Component
{
    public int $seni_budaya_id;
    public $cabang;
    public $semester;

    // Sesi Terpilih
    public ?int $selectedPresensiId = null;
    public $presensis = [];

    // Form Tambah Sesi
    public bool $showSessionModal = false;
    public int $pertemuan_ke = 1;
    public string $tanggal = '';
    public ?string $jam_mulai = null;
    public ?string $jam_selesai = null;
    public string $materi = '';
    public string $catatan = '';

    // Matriks Presensi Siswa: [siswa_id => ['status' => 'Hadir', 'catatan' => '']]
    public array $attendanceData = [];

    protected $service;

    public function boot(SeniBudayaService $service)
    {
        $this->service = $service;
    }

    public function mount(int $seni_budaya_id)
    {
        $this->seni_budaya_id = $seni_budaya_id;
        $this->cabang = SeniBudaya::with('guru')->findOrFail($seni_budaya_id);
        
        $this->semester = Semester::where('is_active', true)->first();
        $this->tanggal = date('Y-m-d');

        $this->loadSessions();
    }

    public function loadSessions()
    {
        $semesterId = $this->semester?->id ?? 0;
        $this->presensis = PresensiModel::where('seni_budaya_id', $this->seni_budaya_id)
            ->where('semester_id', $semesterId)
            ->orderBy('pertemuan_ke')
            ->get();

        if ($this->presensis->isNotEmpty()) {
            if (!$this->selectedPresensiId || !$this->presensis->contains('id', $this->selectedPresensiId)) {
                $this->selectedPresensiId = $this->presensis->last()->id;
            }
            $this->loadAttendanceData();
        } else {
            $this->selectedPresensiId = null;
            $this->attendanceData = [];
        }
    }

    public function updatedSelectedPresensiId()
    {
        $this->loadAttendanceData();
    }

    public function loadAttendanceData()
    {
        if (!$this->selectedPresensiId) {
            $this->attendanceData = [];
            return;
        }

        $semesterId = $this->semester?->id ?? 0;

        // Ambil semua anggota aktif
        $anggotas = SeniBudayaAnggota::with('siswa.rombel')
            ->where('seni_budaya_id', $this->seni_budaya_id)
            ->where('semester_id', $semesterId)
            ->where('status_keanggotaan', 'aktif')
            ->get()
            ->sortBy(fn($a) => $a->siswa?->nama);

        // Ambil detail presensi sesi ini
        $details = SeniBudayaPresensiDetail::where('seni_budaya_presensi_id', $this->selectedPresensiId)
            ->get()
            ->keyBy('siswa_id');

        $this->attendanceData = [];
        foreach ($anggotas as $a) {
            $d = $details->get($a->siswa_id);
            $this->attendanceData[$a->siswa_id] = [
                'nama' => $a->siswa?->nama ?? '-',
                'rombel' => $a->siswa?->rombel?->nama ?? '-',
                'nisn' => $a->siswa?->nisn ?? '-',
                'status' => $d ? $d->status : 'Hadir',
                'catatan' => $d ? $d->catatan : '',
            ];
        }
    }

    public function openCreateSessionModal()
    {
        $semesterId = $this->semester?->id ?? 0;
        $lastPertemuan = PresensiModel::where('seni_budaya_id', $this->seni_budaya_id)
            ->where('semester_id', $semesterId)
            ->max('pertemuan_ke') ?? 0;

        $this->pertemuan_ke = $lastPertemuan + 1;
        $this->tanggal = date('Y-m-d');
        $this->jam_mulai = $this->cabang->jam_mulai;
        $this->jam_selesai = $this->cabang->jam_selesai;
        $this->materi = '';
        $this->catatan = '';

        $this->showSessionModal = true;
    }

    public function createSession()
    {
        $this->validate([
            'pertemuan_ke' => 'required|integer|min:1',
            'tanggal' => 'required|date',
            'materi' => 'nullable|string|max:255',
        ]);

        $semesterId = $this->semester?->id ?? 0;

        $session = PresensiModel::create([
            'seni_budaya_id' => $this->seni_budaya_id,
            'semester_id' => $semesterId,
            'pertemuan_ke' => $this->pertemuan_ke,
            'tanggal' => $this->tanggal,
            'jam_mulai' => $this->jam_mulai,
            'jam_selesai' => $this->jam_selesai,
            'materi' => $this->materi,
            'catatan' => $this->catatan,
            'created_by' => auth()->id(),
        ]);

        // Inisialisasi default seluruh siswa hadir
        $anggotas = SeniBudayaAnggota::where('seni_budaya_id', $this->seni_budaya_id)
            ->where('semester_id', $semesterId)
            ->where('status_keanggotaan', 'aktif')
            ->get();

        foreach ($anggotas as $a) {
            SeniBudayaPresensiDetail::create([
                'seni_budaya_presensi_id' => $session->id,
                'siswa_id' => $a->siswa_id,
                'status' => 'Hadir',
            ]);
        }

        $this->service->recalculateAttendance($this->seni_budaya_id, $semesterId);

        $this->showSessionModal = false;
        $this->selectedPresensiId = $session->id;
        $this->loadSessions();
        session()->flash('message', "Sesi Pertemuan Ke-{$session->pertemuan_ke} berhasil dibuat.");
    }

    public function markAllPresent()
    {
        foreach ($this->attendanceData as $siswaId => $row) {
            $this->attendanceData[$siswaId]['status'] = 'Hadir';
        }
        $this->saveAttendance();
    }

    public function saveAttendance()
    {
        if (!$this->selectedPresensiId) {
            return;
        }

        foreach ($this->attendanceData as $siswaId => $item) {
            SeniBudayaPresensiDetail::updateOrCreate(
                [
                    'seni_budaya_presensi_id' => $this->selectedPresensiId,
                    'siswa_id' => $siswaId,
                ],
                [
                    'status' => $item['status'],
                    'catatan' => $item['catatan'] ?? null,
                ]
            );
        }

        $semesterId = $this->semester?->id ?? 0;
        $this->service->recalculateAttendance($this->seni_budaya_id, $semesterId);

        session()->flash('message', 'Presensi berhasil disimpan dan akumulasi kehadiran siswa diperbarui.');
    }

    public function deleteSession(int $sessionId)
    {
        $session = PresensiModel::findOrFail($sessionId);
        $pertemuan = $session->pertemuan_ke;
        $session->delete();

        $semesterId = $this->semester?->id ?? 0;
        $this->service->recalculateAttendance($this->seni_budaya_id, $semesterId);

        $this->selectedPresensiId = null;
        $this->loadSessions();
        session()->flash('message', "Sesi Pertemuan Ke-{$pertemuan} berhasil dihapus.");
    }

    public function render()
    {
        $activeSession = $this->selectedPresensiId ? PresensiModel::find($this->selectedPresensiId) : null;

        return view('senibudaya::livewire.presensi', [
            'activeSession' => $activeSession,
        ]);
    }
}
