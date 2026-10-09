<?php

namespace App\Modules\Absensi\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Absensi\Models\AbsensiHarian;
use App\Modules\Akademik\Models\Rombel;
use App\Enums\StatusAbsensi;
use App\Core\Services\DocumentEngine;
use App\Modules\Kepegawaian\Models\Pegawai;

class RekapAbsensiController extends Controller
{
    public function cetakPdf(Request $request)
    {
        $request->validate([
            'bulan' => 'required',
            'rombel_id' => 'required'
        ]);

        $rombel = Rombel::findOrFail($request->rombel_id);
        $year = substr($request->bulan, 0, 4);
        $month = substr($request->bulan, 5, 2);
        
        $absensi = AbsensiHarian::where('rombel_id', $rombel->id)
            ->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->with('siswa:id,nama,nisn,nipd')
            ->get();

        $rekap = [];
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
        usort($rekap, fn($a, $b) => strcmp($a['nama'], $b['nama']));

        $namaBulan = \Carbon\Carbon::createFromFormat('Y-m', $request->bulan)->translatedFormat('F Y');

        $html = view('absensi::pdf.rekap-absensi', [
            'rombel' => $rombel,
            'bulan' => $namaBulan,
            'rekap' => $rekap
        ])->render();

        $waliKelas = null; // Ini bisa didapat dari PenugasanWaliKelas jika diimplementasi penuh
        
        $engine = new DocumentEngine();
        $engine->loadHtml($html)
            ->setPaper('A4', 'portrait')
            ->withKopSurat()
            ->withTitimangsa();

        // Cari Kepala Sekolah
        $kepsek = Pegawai::where('status_kepegawaian', 'PNS')->first(); // contoh sederhana
        
        if ($kepsek) {
            $engine->addSignatory('Kepala Sekolah', $kepsek->nama_lengkap, $kepsek->nip);
        }

        return $engine->stream("Rekap_Absensi_{$rombel->nama}_{$request->bulan}.pdf");
    }
}
