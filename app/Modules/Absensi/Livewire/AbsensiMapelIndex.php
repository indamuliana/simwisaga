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
    public $penugasan_id;
    public $gender = '';
    public $rayon_id = '';

    public $penugasans = [];
    public $rayons = [];
    public $absensiMapelData = [];
    public $absensiHarianStatus = [];

    public function mount()
    {
        $this->tanggal = date('Y-m-d');
        $this->rayons = Rayon::orderBy('nama')->get();

        $user = auth()->user();
        $currentSem = AcademicContext::currentSemester();
        $query = PenugasanMengajar::with(['rombel', 'mapel']);
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

        return Siswa::with('rayon')
            ->inRombel($penugasan->rombel_id)
            ->orderBy('nama')
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
            $this->absensiMapelData[$siswa->id]['status'] = StatusAbsensi::HADIR->value;
        }
    }

    public function save()
    {
        $this->guard('absensi.mapel.input');

        $this->validate([
            'tanggal' => 'required|date',
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
        $penugasan = $this->penugasan_id ? PenugasanMengajar::with(['rombel', 'mapel'])->find($this->penugasan_id) : null;
        $statistik = $this->statistik;

        return view('absensi::livewire.absensi-mapel-index', compact('statuses', 'siswas', 'penugasan', 'statistik'));
    }
}
