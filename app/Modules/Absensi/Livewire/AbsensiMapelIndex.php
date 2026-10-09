<?php

namespace App\Modules\Absensi\Livewire;

use Livewire\Component;
use App\Modules\Absensi\Models\AbsensiMapel;
use App\Modules\Absensi\Models\AbsensiHarian;
use App\Modules\Kepegawaian\Models\PenugasanMengajar;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Enums\StatusAbsensi;

class AbsensiMapelIndex extends Component
{
    public $tanggal;
    public $penugasan_id;
    public $penugasans = [];
    public $siswas = [];
    public $absensiMapelData = [];
    public $absensiHarianStatus = [];

    public function mount()
    {
        $this->tanggal = date('Y-m-d');
        
        $user = auth()->user();
        $query = PenugasanMengajar::with(['rombel', 'mapel']);

        if (!$user->isSuperAdmin()) {
            $pegawai = \App\Modules\Kepegawaian\Models\Pegawai::where('user_id', $user->id)->first();
            if ($pegawai) {
                $query->where('pegawai_id', $pegawai->id);
            } else {
                $query->where('id', 0); // User is not a teacher
            }
        }
        
        $this->penugasans = $query->get();
    }

    public function updatedPenugasanId()
    {
        $this->loadData();
    }

    public function updatedTanggal()
    {
        $this->loadData();
    }

    public function loadData()
    {
        if (!$this->penugasan_id || !$this->tanggal) {
            $this->siswas = [];
            $this->absensiMapelData = [];
            $this->absensiHarianStatus = [];
            return;
        }

        $penugasan = PenugasanMengajar::find($this->penugasan_id);
        if (!$penugasan) return;

        $this->siswas = Siswa::where('rombel_id', $penugasan->rombel_id)->orderBy('nama')->get();
        
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

        foreach ($this->siswas as $siswa) {
            $harianStatus = $harian[$siswa->id]->status->value ?? null;
            $this->absensiHarianStatus[$siswa->id] = $harianStatus;

            $isHadirHarian = ($harianStatus === StatusAbsensi::HADIR->value || $harianStatus === null);

            $this->absensiMapelData[$siswa->id] = [
                'status' => $mapel[$siswa->id]->status->value ?? ($isHadirHarian ? StatusAbsensi::HADIR->value : $harianStatus),
            ];
        }
    }

    public function save()
    {
        $this->validate([
            'tanggal' => 'required|date',
            'penugasan_id' => 'required|exists:penugasan_mengajars,id',
        ]);

        foreach ($this->absensiMapelData as $siswaId => $data) {
            $harianStatus = $this->absensiHarianStatus[$siswaId] ?? null;
            $isHadirHarian = ($harianStatus === StatusAbsensi::HADIR->value || $harianStatus === null);
            
            $statusToSave = $isHadirHarian ? ($data['status'] ?? StatusAbsensi::HADIR->value) : $harianStatus;

            AbsensiMapel::updateOrCreate(
                [
                    'tanggal' => $this->tanggal,
                    'siswa_id' => $siswaId,
                    'penugasan_mengajar_id' => $this->penugasan_id,
                ],
                [
                    'status' => $statusToSave,
                    'diinput_oleh' => auth()->id(),
                ]
            );
        }

        session()->flash('message', 'Absensi mapel berhasil disimpan.');
    }

    public function render()
    {
        return view('absensi::livewire.absensi-mapel-index');
    }
}
