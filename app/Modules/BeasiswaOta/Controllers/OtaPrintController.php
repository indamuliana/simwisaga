<?php

namespace App\Modules\BeasiswaOta\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\BeasiswaOta\Models\Alokasi;
use App\Modules\BeasiswaOta\Models\DonasiMasuk;
use App\Modules\BeasiswaOta\Models\OtaSetting;
use App\Modules\BeasiswaOta\Services\OtaDocumentService;
use App\Modules\Sistem\Models\School;
use Illuminate\Http\Response;
use Illuminate\View\View;

class OtaPrintController extends Controller
{
    /**
     * Cetak Tanda Terima Donasi Masuk (A5 Landscape).
     */
    public function tandaTerima(int $id): Response
    {
        $donasi = DonasiMasuk::with(['donatur', 'kelompok'])->findOrFail($id);
        $pdf = OtaDocumentService::createTandaTerimaPdf($donasi);

        $safeNum = str_replace(['/', '\\'], '-', $donasi->nomor_transaksi);
        return $pdf->stream("Tanda_Terima_{$safeNum}.pdf");
    }

    /**
     * Cetak Tanda Terima Kolektif Setoran Kelompok (A4 Portrait).
     */
    public function tandaTerimaKolektif(string $batchKode): Response
    {
        $pdf = OtaDocumentService::createTandaTerimaKolektifPdf($batchKode);
        $safeNum = str_replace(['/', '\\'], '-', $batchKode);
        return $pdf->stream("Tanda_Terima_Kolektif_{$safeNum}.pdf");
    }

    /**
     * Cetak Sertifikat Piagam Donatur (A4 Landscape).
     */
    public function sertifikat(int $id): Response
    {
        $donasi = DonasiMasuk::with(['donatur', 'kelompok'])->findOrFail($id);
        $pdf = OtaDocumentService::createSertifikatPdf($donasi);

        $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $donasi->donatur->nama);
        return $pdf->stream("Sertifikat_Donatur_{$safeName}.pdf");
    }

    /**
     * Cetak Kwitansi Penyaluran Alokasi Beasiswa (A4 Portrait).
     */
    public function kwitansiPenyaluran(int $id): Response
    {
        $alokasi = Alokasi::with(['details', 'siswa.rombel', 'kelompok', 'semester.tahunAjaran'])->findOrFail($id);
        $pdf = OtaDocumentService::createKwitansiPenyaluranPdf($alokasi);

        $safeNum = str_replace(['/', '\\'], '-', $alokasi->nomor_kwitansi);
        return $pdf->stream("Kwitansi_Penyaluran_{$safeNum}.pdf");
    }

    /**
     * Halaman Publik Verifikasi Tanda Terima Donasi (Akses Scan QR Code).
     */
    public function verifikasiDonasi(string $nomorTransaksi): View
    {
        $donasi = DonasiMasuk::with(['donatur', 'kelompok', 'tahunAjaran'])
            ->where('nomor_transaksi', $nomorTransaksi)
            ->when(is_numeric($nomorTransaksi), fn($q) => $q->orWhere('id', (int) $nomorTransaksi))
            ->firstOrFail();

        $school = School::current();
        $bendaharaNama = OtaSetting::get('bendahara_nama', 'Bendahara Program OTA');
        $terbilang = OtaDocumentService::terbilang((float) $donasi->nominal) . ' Rupiah';

        return view('beasiswaota::public.verifikasi-tanda-terima', compact(
            'donasi',
            'school',
            'bendaharaNama',
            'terbilang'
        ));
    }

    /**
     * Halaman Publik Verifikasi Tanda Terima Donasi Kolektif (Akses Scan QR Code).
     */
    public function verifikasiKolektif(string $batchKode): View
    {
        $donasis = DonasiMasuk::with(['donatur', 'kelompok', 'tahunAjaran'])
            ->where('batch_kode', $batchKode)
            ->get();

        if ($donasis->isEmpty()) {
            abort(404, 'Data setoran kolektif tidak ditemukan.');
        }

        $first = $donasis->first();
        $totalNominal = (float) $donasis->sum('nominal');
        $school = School::current();
        $bendaharaNama = OtaSetting::get('bendahara_nama', 'Bendahara Program OTA');
        $terbilang = OtaDocumentService::terbilang($totalNominal) . ' Rupiah';

        return view('beasiswaota::public.verifikasi-kolektif', compact(
            'batchKode',
            'first',
            'donasis',
            'totalNominal',
            'school',
            'bendaharaNama',
            'terbilang'
        ));
    }

    /**
     * Halaman Publik Verifikasi Kwitansi Penyaluran Beasiswa (Akses Scan QR Code).
     */
    public function verifikasiPenyaluran(string $nomorKwitansi): View
    {
        $alokasi = Alokasi::with(['details', 'siswa.rombel', 'kelompok', 'semester.tahunAjaran'])
            ->where('nomor_kwitansi', $nomorKwitansi)
            ->when(is_numeric($nomorKwitansi), fn($q) => $q->orWhere('id', (int) $nomorKwitansi))
            ->firstOrFail();

        $school = School::current();
        $bendaharaNama = trim((string) OtaSetting::get('bendahara_nama', ''));
        if ($bendaharaNama === '') {
            $bendaharaNama = 'Bendahara Program OTA';
        }
        $kepsekNama = $school?->kepala_sekolah ?? OtaSetting::get('kepsek_nama', 'Kepala Sekolah');
        $terbilang = OtaDocumentService::terbilang((float) $alokasi->total_nominal) . ' Rupiah';

        return view('beasiswaota::public.verifikasi-penyaluran', compact(
            'alokasi',
            'school',
            'bendaharaNama',
            'kepsekNama',
            'terbilang'
        ));
    }

    /**
     * Halaman Publik Verifikasi Sertifikat Piagam Donatur (Akses Scan QR Code).
     */
    public function verifikasiSertifikat(int $donasiId): View
    {
        $donasi = DonasiMasuk::with(['donatur', 'kelompok'])->findOrFail($donasiId);
        $school = School::current();

        $nomorSertifikat = sprintf(
            'OTA/SRT/%s/%04d',
            $donasi->tanggal_donasi->format('Y/m'),
            $donasi->id
        );

        $kepsekNama = $school?->kepala_sekolah ?? OtaSetting::get('kepsek_nama', 'Kepala Sekolah');
        $ketuaOtaNama = OtaSetting::get('ketua_ota_nama', 'Koordinator OTA');

        return view('beasiswaota::public.verifikasi-sertifikat', compact(
            'donasi',
            'school',
            'nomorSertifikat',
            'kepsekNama',
            'ketuaOtaNama'
        ));
    }
}
