<?php

namespace App\Modules\Keuangan\Controllers;

use App\Core\Services\QrCodeService;
use App\Http\Controllers\Controller;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Keuangan\Models\Rekening;
use App\Modules\Keuangan\Models\Tagihan;
use App\Modules\Keuangan\Models\Transaksi;
use App\Modules\Sistem\Models\School;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class KeuanganPrintController extends Controller
{
    public static function terbilang(float $nilai): string
    {
        $nilai = abs($nilai);
        if ($nilai <= 0) {
            return 'Nol';
        }

        $huruf = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
        $hasil = '';

        if ($nilai < 12) {
            $hasil = ' ' . $huruf[(int) $nilai];
        } elseif ($nilai < 20) {
            $hasil = static::terbilang($nilai - 10) . ' Belas ';
        } elseif ($nilai < 100) {
            $hasil = static::terbilang($nilai / 10) . ' Puluh ' . static::terbilang($nilai % 10);
        } elseif ($nilai < 200) {
            $hasil = ' Seratus ' . static::terbilang($nilai - 100);
        } elseif ($nilai < 1000) {
            $hasil = static::terbilang($nilai / 100) . ' Ratus ' . static::terbilang($nilai % 100);
        } elseif ($nilai < 2000) {
            $hasil = ' Seribu ' . static::terbilang($nilai - 1000);
        } elseif ($nilai < 1000000) {
            $hasil = static::terbilang($nilai / 1000) . ' Ribu ' . static::terbilang($nilai % 1000);
        } elseif ($nilai < 1000000000) {
            $hasil = static::terbilang($nilai / 1000000) . ' Juta ' . static::terbilang($nilai % 1000000);
        } elseif ($nilai < 1000000000000) {
            $hasil = static::terbilang($nilai / 1000000000) . ' Milyar ' . static::terbilang(fmod($nilai, 1000000000));
        } else {
            $hasil = static::terbilang($nilai / 1000000000000) . ' Triliun ' . static::terbilang(fmod($nilai, 1000000000000));
        }

        return trim(preg_replace('/\s+/', ' ', $hasil));
    }

    public function kwitansi(int $id): Response
    {
        $transaksi = Transaksi::with([
            'siswa.rombel.jurusan',
            'siswa.rayon',
            'items.posBiaya',
            'items.tagihan',
            'rekening',
            'user',
        ])->findOrFail($id);

        $user = auth()->user();
        if ($user && $user->hasRole('Orang Tua')) {
            $isOwnChild = Siswa::where('id', $transaksi->siswa_id)
                ->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                    if (str_starts_with($user->username, 'ortu_')) {
                        $q->orWhere('nipd', substr($user->username, 5));
                    }
                })->exists();

            if (! $isOwnChild && ! $user->can('keuangan.cetak')) {
                abort(403, 'Anda tidak memiliki hak akses untuk mengunduh kwitansi siswa lain.');
            }
        } elseif (! $user || ! $user->can('keuangan.cetak')) {
            abort(403, 'Akses ditolak.');
        }

        $school = School::current();
        $verifyUrl = route('keuangan.verifikasi-kwitansi', $transaksi->kode_verifikasi);
        $qrCodePng = QrCodeService::pngDataUri($verifyUrl, 150);
        $terbilang = static::terbilang((float) $transaksi->total_bayar) . ' Rupiah';

        // Ambil sisa total tunggakan siswa saat ini
        $sisaTunggakanSiswa = Tagihan::where('siswa_id', $transaksi->siswa_id)->sum('sisa_tagihan');

        $pdf = Pdf::loadView('keuangan::pdf.kwitansi', [
            'transaksi' => $transaksi,
            'school' => $school,
            'qrCodePng' => $qrCodePng,
            'terbilang' => $terbilang,
            'sisaTunggakan' => $sisaTunggakanSiswa,
        ])->setPaper('a5', 'landscape');

        $safeNum = str_replace(['/', '\\'], '-', $transaksi->nomor_transaksi);
        return $pdf->stream("Kwitansi_{$safeNum}.pdf");
    }

    public function suratTagihan(int $siswaId): Response
    {
        $siswa = Siswa::with(['rombel.jurusan', 'rayon.pembimbing', 'orangTuas'])->findOrFail($siswaId);
        $tagihans = Tagihan::with('posBiaya')
            ->where('siswa_id', $siswaId)
            ->belumLunas()
            ->urutTahunAjaran()
            ->get();

        $school = School::current();
        $rekenings = Rekening::where('is_aktif', true)->where('is_kas_tunai', false)->get();
        $totalTunggakan = $tagihans->sum('sisa_tagihan');
        $terbilang = static::terbilang((float) $totalTunggakan) . ' Rupiah';

        $pdf = Pdf::loadView('keuangan::pdf.surat-tagihan', [
            'siswa' => $siswa,
            'tagihans' => $tagihans,
            'school' => $school,
            'rekenings' => $rekenings,
            'totalTunggakan' => $totalTunggakan,
            'terbilang' => $terbilang,
        ])->setPaper('a4', 'portrait');

        $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $siswa->nama);
        return $pdf->stream("Surat_Tagihan_{$safeName}.pdf");
    }

    public function rekapKas(Request $request): Response
    {
        $startDate = $request->query('start_date', date('Y-m-01'));
        $endDate = $request->query('end_date', date('Y-m-d'));
        $rekeningId = $request->query('rekening_id');

        $query = Transaksi::with(['siswa.rombel', 'rekening', 'items.posBiaya'])
            ->whereDate('tanggal_bayar', '>=', $startDate)
            ->whereDate('tanggal_bayar', '<=', $endDate);

        if ($rekeningId) {
            $query->where('rekening_id', $rekeningId);
        }

        $transaksis = $query->orderBy('tanggal_bayar')->get();
        $totalPenerimaan = $transaksis->sum('total_bayar');
        $school = School::current();

        $pdf = Pdf::loadView('keuangan::pdf.rekap-kas', [
            'transaksis' => $transaksis,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalPenerimaan' => $totalPenerimaan,
            'school' => $school,
            'rekening' => $rekeningId ? Rekening::find($rekeningId) : null,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream("Rekap_Kas_{$startDate}_sd_{$endDate}.pdf");
    }
}

