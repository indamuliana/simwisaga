<?php

namespace App\Modules\Sarpras\Livewire;

use App\Modules\Sarpras\Models\Aset;
use App\Modules\Sarpras\Models\Bhp;
use App\Modules\Sarpras\Models\Kategori;
use App\Modules\Sarpras\Models\Pemeliharaan;
use App\Modules\Sarpras\Models\Peminjaman;
use App\Modules\Sarpras\Models\Pengajuan;
use App\Modules\Sarpras\Models\Ruangan;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Dashboard Sarpras')]
class SarprasDashboard extends Component
{
    public function render()
    {
        $totalAset = Aset::count();
        $totalNilaiAset = Aset::sum('harga_perolehan');
        $kondisiBaik = Aset::where('kondisi', 'Baik')->count();
        $kondisiRusak = Aset::whereIn('kondisi', ['Rusak Ringan', 'Rusak Berat'])->count();

        $totalBhp = Bhp::count();
        $bhpMenipis = Bhp::menipis()->count();

        $asetDipinjam = Peminjaman::where('status', 'Dipinjam')->count();
        $pemeliharaanPending = Pemeliharaan::where('status', 'Menunggu Tindakan')->count();
        $pengajuanPending = Pengajuan::whereIn('status', ['Diajukan', 'Review Sarpras'])->count();

        $kategoriStats = Kategori::withCount('asets')->get();
        $ruanganStats = Ruangan::withCount('asets')->get();

        $recentAsets = Aset::with(['kategori', 'ruangan'])->latest()->take(5)->get();
        $recentPeminjamans = Peminjaman::with('aset')->latest()->take(5)->get();
        $recentPemeliharaans = Pemeliharaan::with(['aset', 'pelapor'])->latest()->take(5)->get();

        return view('sarpras::livewire.dashboard', compact(
            'totalAset',
            'totalNilaiAset',
            'kondisiBaik',
            'kondisiRusak',
            'totalBhp',
            'bhpMenipis',
            'asetDipinjam',
            'pemeliharaanPending',
            'pengajuanPending',
            'kategoriStats',
            'ruanganStats',
            'recentAsets',
            'recentPeminjamans',
            'recentPemeliharaans'
        ));
    }
}
