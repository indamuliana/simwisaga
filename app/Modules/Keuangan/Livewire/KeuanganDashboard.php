<?php

namespace App\Modules\Keuangan\Livewire;

use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Keuangan\Models\PosBiaya;
use App\Modules\Keuangan\Models\Rekening;
use App\Modules\Keuangan\Models\Tagihan;
use App\Modules\Keuangan\Models\Transaksi;
use App\Modules\Keuangan\Models\PembayaranOnline;
use Livewire\Component;

class KeuanganDashboard extends Component
{
    public ?int $selectedTahunAjaranId = null;

    public function mount(): void
    {
        $ta = TahunAjaran::first();
        $this->selectedTahunAjaranId = $ta?->id;
    }

    public function render()
    {
        $currentMonth = (int) date('n');
        $currentYear = (int) date('Y');

        // 1. Total Penerimaan Bulan Ini
        $penerimaanBulanIni = (float) Transaksi::whereMonth('tanggal_bayar', $currentMonth)
            ->whereYear('tanggal_bayar', $currentYear)
            ->sum('total_bayar');

        // 2. Piutang Bulan Ini (Tagihan jatuh tempo / bulan aktif ini yang belum lunas)
        $piutangBulanIni = (float) Tagihan::whereHas('siswa', fn ($q) => $q->where('status', 'aktif'))
            ->where('bulan', $currentMonth)
            ->where('tahun', $currentYear)
            ->sum('sisa_tagihan');

        // 3. Total Piutang Aktif Kumulatif & Rasio Ketuntasan
        $statsAktif = Tagihan::whereHas('siswa', fn ($q) => $q->where('status', 'aktif'))
            ->selectRaw('COALESCE(SUM(sisa_tagihan), 0) as total_piutang, COALESCE(SUM(nominal_tagihan), 0) as total_tagihan, COALESCE(SUM(nominal_terbayar), 0) as total_terbayar')
            ->first();

        $totalPiutangAktif = (float) ($statsAktif->total_piutang ?? 0);
        $totalTagihanAktif = (float) ($statsAktif->total_tagihan ?? 0);
        $totalTerbayarAktif = (float) ($statsAktif->total_terbayar ?? 0);

        // 4. Total Piutang Alumni (Siswa yang lulus/alumni)
        $totalPiutangAlumni = (float) Tagihan::whereHas('siswa', fn ($q) => $q->where('status', 'lulus'))
            ->sum('sisa_tagihan');

        $rasioKetuntasan = $totalTagihanAktif > 0
            ? round(($totalTerbayarAktif / $totalTagihanAktif) * 100, 1)
            : 100.0;

        // 6. Antrean Verifikasi Online Belum Diproses
        $menungguVerifikasiCount = PembayaranOnline::where('status', 'menunggu')->count();

        // 7. Saldo Kas & Bank
        $rekenings = Rekening::where('is_aktif', true)->get();
        $totalSaldoKas = $rekenings->sum('saldo_saat_ini');

        // 8. Breakdown Tunggakan per Rombel Top 6
        $tunggakanPerRombel = Rombel::addSelect([
            'total_tunggakan' => Tagihan::selectRaw('COALESCE(SUM(keuangan_tagihans.sisa_tagihan), 0)')
                ->join('siswas', 'siswas.id', '=', 'keuangan_tagihans.siswa_id')
                ->whereColumn('siswas.rombel_id', 'rombels.id')
                ->where('siswas.status', 'aktif')
                ->whereIn('keuangan_tagihans.status', ['belum_bayar', 'dicicil'])
        ])->orderByDesc('total_tunggakan')->take(6)->get();

        // 9. Transaksi Terbaru
        $transaksiTerbaru = Transaksi::with(['siswa.rombel', 'rekening'])
            ->latest('tanggal_bayar')
            ->take(8)
            ->get();

        return view('keuangan::livewire.keuangan-dashboard', [
            'penerimaanBulanIni' => $penerimaanBulanIni,
            'piutangBulanIni' => $piutangBulanIni,
            'totalPiutangAktif' => $totalPiutangAktif,
            'totalPiutangAlumni' => $totalPiutangAlumni,
            'rasioKetuntasan' => $rasioKetuntasan,
            'menungguVerifikasiCount' => $menungguVerifikasiCount,
            'rekenings' => $rekenings,
            'totalSaldoKas' => $totalSaldoKas,
            'tunggakanPerRombel' => $tunggakanPerRombel,
            'transaksiTerbaru' => $transaksiTerbaru,
        ])->layout('components.layouts.app', ['title' => 'Dashboard Keuangan & Pembayaran Siswa']);
    }
}

