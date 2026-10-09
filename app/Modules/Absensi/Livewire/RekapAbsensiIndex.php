<?php

namespace App\Modules\Absensi\Livewire;

use Livewire\Component;
use App\Modules\Absensi\Models\AbsensiHarian;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Support\AcademicContext;
use Illuminate\Support\Facades\DB;
use App\Enums\StatusAbsensi;

class RekapAbsensiIndex extends Component
{
    public $bulan;
    public $rombelId;

    public function mount()
    {
        $this->bulan = date('Y-m');
    }

    public function cetakPdf()
    {
        $this->validate([
            'bulan' => 'required',
            'rombelId' => 'required'
        ]);

        return redirect()->route('absensi.rekap.pdf', [
            'bulan' => $this->bulan,
            'rombel_id' => $this->rombelId
        ]);
    }

    public function render()
    {
        $rombels = Rombel::all();
        $semesterId = AcademicContext::currentSemester()?->id;
        
        $rekap = [];
        if ($this->rombelId && $this->bulan) {
            $year = substr($this->bulan, 0, 4);
            $month = substr($this->bulan, 5, 2);

            $absensi = AbsensiHarian::where('rombel_id', $this->rombelId)
                ->whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month)
                ->with('siswa:id,nama,nisn,nipd')
                ->get();

            $grouped = $absensi->groupBy('siswa_id');

            foreach ($grouped as $siswaId => $records) {
                $siswa = $records->first()->siswa;
                $rekap[] = [
                    'nama' => $siswa->nama,
                    'nis' => $siswa->nipd ?: $siswa->nisn,
                    'hadir' => $records->filter(fn($r) => $r->status->isHadirStatistik())->count(),
                    'sakit' => $records->where('status', StatusAbsensi::SAKIT)->count(),
                    'izin' => $records->where('status', StatusAbsensi::IZIN)->count(),
                    'alpa' => $records->where('status', StatusAbsensi::ALPA)->count(),
                ];
            }
            
            // Sort by nama
            usort($rekap, fn($a, $b) => strcmp($a['nama'], $b['nama']));
        }

        return view('absensi::livewire.rekap-absensi-index', compact('rombels', 'rekap'));
    }
}
