<?php

namespace App\Modules\Keuangan\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Keuangan\Models\Transaksi;
use App\Modules\Sistem\Models\School;
use Illuminate\View\View;

class KeuanganPublicVerificationController extends Controller
{
    public function verifikasi(string $kodeVerifikasi): View
    {
        $transaksi = Transaksi::with([
            'siswa.rombel',
            'siswa.rayon',
            'items.posBiaya',
            'rekening',
            'user',
        ])->where('kode_verifikasi', $kodeVerifikasi)->first();

        $school = School::current();

        return view('keuangan::public.verifikasi-kwitansi', [
            'transaksi' => $transaksi,
            'school' => $school,
            'kodeVerifikasi' => $kodeVerifikasi,
        ]);
    }
}

