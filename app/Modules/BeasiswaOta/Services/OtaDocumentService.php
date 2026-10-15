<?php

namespace App\Modules\BeasiswaOta\Services;

use App\Modules\BeasiswaOta\Models\Alokasi;
use App\Modules\BeasiswaOta\Models\DonasiMasuk;
use App\Modules\BeasiswaOta\Models\OtaSetting;
use App\Modules\Sistem\Models\School;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdfWrapper;

class OtaDocumentService
{
    /**
     * Konversi angka nominal ke kalimat terbilang Rupiah.
     */
    public static function terbilang(float $nilai): string
    {
        $nilai = abs($nilai);
        $huruf = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
        $hasil = '';

        if ($nilai < 12) {
            $hasil = ' ' . $huruf[(int) $nilai];
        } elseif ($nilai < 20) {
            $hasil = static::terbilang($nilai - 10) . ' Belas';
        } elseif ($nilai < 100) {
            $hasil = static::terbilang($nilai / 10) . ' Puluh' . static::terbilang($nilai % 10);
        } elseif ($nilai < 200) {
            $hasil = ' Seratus' . static::terbilang($nilai - 100);
        } elseif ($nilai < 1000) {
            $hasil = static::terbilang($nilai / 100) . ' Ratus' . static::terbilang($nilai % 100);
        } elseif ($nilai < 2000) {
            $hasil = ' Seribu' . static::terbilang($nilai - 1000);
        } elseif ($nilai < 1000000) {
            $hasil = static::terbilang($nilai / 1000) . ' Ribu' . static::terbilang($nilai % 1000);
        } elseif ($nilai < 1000000000) {
            $hasil = static::terbilang($nilai / 1000000) . ' Juta' . static::terbilang($nilai % 1000000);
        } elseif ($nilai < 1000000000000) {
            $hasil = static::terbilang($nilai / 1000000000) . ' Milyar' . static::terbilang(fmod($nilai, 1000000000));
        } else {
            $hasil = static::terbilang($nilai / 1000000000000) . ' Triliun' . static::terbilang(fmod($nilai, 1000000000000));
        }

        return trim($hasil);
    }

    /**
     * Cetak Tanda Terima Uang Masuk Donasi.
     */
    public static function createTandaTerimaPdf(DonasiMasuk $donasi): DomPdfWrapper
    {
        $school = School::current();
        $terbilang = static::terbilang((float) $donasi->nominal) . ' Rupiah';

        $data = [
            'school' => $school,
            'donasi' => $donasi,
            'terbilang' => $terbilang,
            'bendaharaNama' => OtaSetting::get('bendahara_nama', 'Bendahara Program OTA'),
            'titimangsa' => OtaSetting::get('titimangsa_kota', 'Garut') . ', ' . $donasi->tanggal_donasi->translatedFormat('d F Y'),
        ];

        return Pdf::loadView('beasiswaota::pdf.tanda-terima', $data)
            ->setPaper('A5', 'landscape');
    }

    /**
     * Cetak Tanda Terima Kolektif per Batch Setoran Kelompok.
     */
    public static function createTandaTerimaKolektifPdf(string $batchKode): DomPdfWrapper
    {
        $school = School::current();
        $donasis = DonasiMasuk::with(['donatur', 'kelompok'])
            ->where('batch_kode', $batchKode)
            ->get();

        if ($donasis->isEmpty()) {
            abort(404, 'Data batch donasi tidak ditemukan.');
        }

        $first = $donasis->first();
        $totalNominal = (float) $donasis->sum('nominal');
        $terbilang = static::terbilang($totalNominal) . ' Rupiah';

        $data = [
            'school' => $school,
            'batchKode' => $batchKode,
            'first' => $first,
            'donasis' => $donasis,
            'totalNominal' => $totalNominal,
            'terbilang' => $terbilang,
            'bendaharaNama' => OtaSetting::get('bendahara_nama', 'Bendahara Program OTA'),
            'titimangsa' => OtaSetting::get('titimangsa_kota', 'Garut') . ', ' . $first->tanggal_donasi->translatedFormat('d F Y'),
        ];

        return Pdf::loadView('beasiswaota::pdf.tanda-terima-kolektif', $data)
            ->setPaper('A4', 'portrait');
    }

    /**
     * Cetak Sertifikat Piagam Donatur.
     */
    public static function createSertifikatPdf(DonasiMasuk $donasi): DomPdfWrapper
    {
        $school = School::current();
        $bgPath = OtaSetting::get('certificate_bg_path');
        $bgBase64 = null;

        if ($bgPath && file_exists(storage_path('app/public/' . $bgPath))) {
            $fileData = file_get_contents(storage_path('app/public/' . $bgPath));
            $type = pathinfo($bgPath, PATHINFO_EXTENSION);
            $bgBase64 = 'data:image/' . $type . ';base64,' . base64_encode($fileData);
        }

        $terbilang = static::terbilang((float) $donasi->nominal) . ' Rupiah';

        $nomorSertifikat = sprintf(
            'OTA/SRT/%s/%04d',
            $donasi->tanggal_donasi->format('Y/m'),
            $donasi->id
        );

        $data = [
            'school' => $school,
            'donasi' => $donasi,
            'nomorSertifikat' => $nomorSertifikat,
            'terbilang' => $terbilang,
            'bgBase64' => $bgBase64,
            'kepsekNama' => $school?->kepala_sekolah ?? OtaSetting::get('kepsek_nama', 'H. Dedik, M.Pd.'),
            'kepsekNip' => $school?->nip_kepsek ?? OtaSetting::get('kepsek_nip', '-'),
            'ketuaOtaNama' => OtaSetting::get('ketua_ota_nama', 'Koordinator OTA'),
            'titimangsa' => OtaSetting::get('titimangsa_kota', 'Garut') . ', ' . $donasi->tanggal_donasi->translatedFormat('d F Y'),
        ];

        return Pdf::loadView('beasiswaota::pdf.sertifikat-donatur', $data)
            ->setPaper('A4', 'landscape');
    }

    /**
     * Cetak Kwitansi Penyaluran Alokasi Beasiswa.
     */
    public static function createKwitansiPenyaluranPdf(Alokasi $alokasi): DomPdfWrapper
    {
        $school = School::current();
        $terbilang = static::terbilang((float) $alokasi->total_nominal) . ' Rupiah';

        $data = [
            'school' => $school,
            'alokasi' => $alokasi,
            'terbilang' => $terbilang,
            'bendaharaNama' => OtaSetting::get('bendahara_nama', 'Bendahara OTA'),
            'kepsekNama' => $school?->kepala_sekolah ?? OtaSetting::get('kepsek_nama', 'Kepala Sekolah'),
            'kepsekNip' => $school?->nip_kepsek ?? OtaSetting::get('kepsek_nip', '-'),
            'titimangsa' => OtaSetting::get('titimangsa_kota', 'Garut') . ', ' . $alokasi->tanggal_alokasi->translatedFormat('d F Y'),
        ];

        return Pdf::loadView('beasiswaota::pdf.kwitansi-penyaluran', $data)
            ->setPaper('A4', 'portrait');
    }
}
