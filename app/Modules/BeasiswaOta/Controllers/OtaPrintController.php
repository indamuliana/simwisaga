<?php

namespace App\Modules\BeasiswaOta\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\BeasiswaOta\Models\Alokasi;
use App\Modules\BeasiswaOta\Models\DonasiMasuk;
use App\Modules\BeasiswaOta\Services\OtaDocumentService;
use Illuminate\Http\Response;

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
}
