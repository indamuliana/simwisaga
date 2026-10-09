<?php

namespace App\Modules\Absensi\Livewire;

use Livewire\Component;
use App\Modules\Absensi\Models\AbsensiHarian;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Akademik\Models\Rombel;
use App\Enums\StatusAbsensi;
use App\Modules\Akademik\Support\AcademicContext;
use Livewire\Attributes\Layout;

class AbsensiHarianIndex extends Component
{
    public $tanggal;
    public $rombel_id;
    public $rombels = [];
    public $siswas = [];
    public $absensiData = [];

    public function mount()
    {
        $this->tanggal = date('Y-m-d');
        $this->rombels = Rombel::orderBy('nama')->get();
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
            $this->siswas = [];
            $this->absensiData = [];
            return;
        }

        $this->siswas = Siswa::where('rombel_id', $this->rombel_id)->orderBy('nama')->get();
        $absensis = AbsensiHarian::where('tanggal', $this->tanggal)
            ->where('rombel_id', $this->rombel_id)
            ->get()
            ->keyBy('siswa_id');

        $this->absensiData = [];
        foreach ($this->siswas as $siswa) {
            $this->absensiData[$siswa->id] = [
                'status' => $absensis[$siswa->id]->status->value ?? null,
                'keterangan' => $absensis[$siswa->id]->keterangan ?? '',
            ];
        }
    }

    public function tandaiSemuaHadir()
    {
        foreach ($this->siswas as $siswa) {
            $this->absensiData[$siswa->id]['status'] = StatusAbsensi::HADIR->value;
        }
    }

    public function save()
    {
        $this->validate([
            'tanggal' => 'required|date',
            'rombel_id' => 'required|exists:rombels,id',
            'absensiData.*.status' => 'nullable|string',
        ]);

        $semesterId = AcademicContext::currentSemester()?->id;

        foreach ($this->absensiData as $siswaId => $data) {
            if (empty($data['status'])) continue;

            AbsensiHarian::updateOrCreate(
                [
                    'tanggal' => $this->tanggal,
                    'siswa_id' => $siswaId,
                    'rombel_id' => $this->rombel_id,
                ],
                [
                    'status' => $data['status'],
                    'keterangan' => $data['keterangan'] ?? null,
                    'semester_id' => $semesterId,
                    'diinput_oleh' => auth()->id(),
                ]
            );
        }

        session()->flash('message', 'Absensi harian berhasil disimpan.');
    }

    public function render()
    {
        $statuses = StatusAbsensi::cases();
        return view('absensi::livewire.absensi-harian-index', compact('statuses'));
    }
}
