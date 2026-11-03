<?php

namespace App\Modules\Absensi\Livewire;

use App\Enums\StatusAbsensi;
use App\Modules\Absensi\Models\AbsensiHarian;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Support\AcademicContext;
use App\Modules\Kepegawaian\Models\PenugasanWaliKelas;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class WidgetPeringatanAlpa extends Component
{
    public $siswaPeringatan = [];

    public function mount()
    {
        $user = auth()->user();
        if (!$user || (!$user->can('absensi.harian.lihat') && !$user->hasRole(['Wali Kelas', 'Kesiswaan', 'Super Admin']))) {
            return;
        }

        $semester = AcademicContext::currentSemester();
        $semesterId = $semester?->id;
        $tahunAjaranId = $semester?->tahun_ajaran_id ?? AcademicContext::currentTahunAjaran()?->id;

        if (!$semesterId) {
            return;
        }

        $rombelIds = [];
        
        // Jika Wali Kelas, ambil rombel yang dibinanya berdasarkan tahun ajaran aktif
        if ($user->pegawai) {
            $penugasanQuery = PenugasanWaliKelas::where('pegawai_id', $user->pegawai->id);
            if ($tahunAjaranId) {
                $penugasanQuery->where('tahun_ajaran_id', $tahunAjaranId);
            }
            $rombelIds = $penugasanQuery->pluck('rombel_id')->toArray();
        }

        // Juga cek jika wali kelas ditugaskan langsung pada model Rombel (wali_kelas_id)
        $rombelDirectQuery = Rombel::where('wali_kelas_id', $user->id);
        if ($user->pegawai) {
            $rombelDirectQuery->orWhere('wali_kelas_id', $user->pegawai->id);
        }
        if ($tahunAjaranId) {
            $rombelDirectQuery->where('tahun_ajaran_id', $tahunAjaranId);
        }
        $rombelDirectIds = $rombelDirectQuery->pluck('id')->toArray();

        $rombelIds = array_values(array_unique(array_merge($rombelIds, $rombelDirectIds)));

        // Jika kesiswaan / admin, tampilkan semua (jangan difilter rombel)
        $isKesiswaan = $user->hasRole(['Kesiswaan', 'Super Admin', 'Operator']);

        $query = AbsensiHarian::select('siswa_id', 'rombel_id', DB::raw('count(*) as total_alpa'))
            ->where('semester_id', $semesterId)
            ->where('status', StatusAbsensi::ALPA->value)
            ->groupBy('siswa_id', 'rombel_id')
            ->having('total_alpa', '>=', 3)
            ->with(['siswa:id,nama,nisn,nipd', 'rombel:id,nama']);

        if (!$isKesiswaan) {
            if (empty($rombelIds)) {
                return; // bukan kesiswaan dan bukan wali kelas aktif
            }
            $query->whereIn('rombel_id', $rombelIds);
        }

        $results = $query->orderBy('total_alpa', 'desc')->limit(20)->get();

        $this->siswaPeringatan = $results->map(function($row) {
            $spLevel = 'SP 1';
            $bgColor = 'bg-yellow-100 text-yellow-800';
            if ($row->total_alpa >= 9) {
                $spLevel = 'SP 3';
                $bgColor = 'bg-red-100 text-red-800';
            } elseif ($row->total_alpa >= 6) {
                $spLevel = 'SP 2';
                $bgColor = 'bg-orange-100 text-orange-800';
            }

            return [
                'nama' => $row->siswa?->nama ?? '-',
                'nis' => $row->siswa ? ($row->siswa->nipd ?: $row->siswa->nisn) : '-',
                'rombel' => $row->rombel?->nama ?? '-',
                'total_alpa' => $row->total_alpa,
                'sp_level' => $spLevel,
                'bg_color' => $bgColor,
            ];
        });
    }

    public function render()
    {
        return view('absensi::livewire.widget-peringatan-alpa');
    }
}
