@props(['active' => 'index'])

@php
    $menipisCount = \App\Modules\Sarpras\Models\Bhp::menipis()->count();
    $pinjamCount = \App\Modules\Sarpras\Models\Peminjaman::where('status', 'Dipinjam')->count();
    $mtcPendingCount = \App\Modules\Sarpras\Models\Pemeliharaan::where('status', 'Menunggu Tindakan')->count();
    $pengajuanPendingCount = \App\Modules\Sarpras\Models\Pengajuan::whereIn('status', ['Diajukan', 'Review Sarpras'])->count();
@endphp

<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b border-border pb-4 mb-6">
    <div>
        <h2 class="text-2xl font-bold text-text-main flex items-center gap-2">
            <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            Modul Sarana & Prasarana
        </h2>
        <p class="text-sm text-text-secondary mt-1">
            Inventarisasi aset fisik, pengelolaan bahan habis pakai, sirkulasi peminjaman, dan pemeliharaan fasilitas.
        </p>
    </div>

    <!-- Tab Sub-Modul Navigasi -->
    <div class="flex flex-wrap items-center gap-1.5 bg-surface-hover p-1.5 rounded-xl border border-border">
        <a href="{{ route('sarpras.index') }}" 
           class="px-3 py-1.5 rounded-lg text-xs md:text-sm font-medium transition {{ $active === 'index' ? 'bg-white dark:bg-gray-800 text-primary shadow-xs font-semibold' : 'text-text-secondary hover:text-text-main hover:bg-surface' }}">
            Dashboard
        </a>
        <a href="{{ route('sarpras.aset') }}" 
           class="px-3 py-1.5 rounded-lg text-xs md:text-sm font-medium transition {{ $active === 'aset' ? 'bg-white dark:bg-gray-800 text-primary shadow-xs font-semibold' : 'text-text-secondary hover:text-text-main hover:bg-surface' }}">
            Aset Tetap
        </a>
        <a href="{{ route('sarpras.bhp') }}" 
           class="relative px-3 py-1.5 rounded-lg text-xs md:text-sm font-medium transition {{ $active === 'bhp' ? 'bg-white dark:bg-gray-800 text-primary shadow-xs font-semibold' : 'text-text-secondary hover:text-text-main hover:bg-surface' }}">
            BHP
            @if($menipisCount > 0)
                <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500 text-white" title="{{ $menipisCount }} stok menipis">
                    {{ $menipisCount }}
                </span>
            @endif
        </a>
        <a href="{{ route('sarpras.peminjaman') }}" 
           class="relative px-3 py-1.5 rounded-lg text-xs md:text-sm font-medium transition {{ $active === 'peminjaman' ? 'bg-white dark:bg-gray-800 text-primary shadow-xs font-semibold' : 'text-text-secondary hover:text-text-main hover:bg-surface' }}">
            Peminjaman
            @if($pinjamCount > 0)
                <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-600 text-white" title="{{ $pinjamCount }} sedang dipinjam">
                    {{ $pinjamCount }}
                </span>
            @endif
        </a>
        <a href="{{ route('sarpras.pemeliharaan') }}" 
           class="relative px-3 py-1.5 rounded-lg text-xs md:text-sm font-medium transition {{ $active === 'pemeliharaan' ? 'bg-white dark:bg-gray-800 text-primary shadow-xs font-semibold' : 'text-text-secondary hover:text-text-main hover:bg-surface' }}">
            Pemeliharaan
            @if($mtcPendingCount > 0)
                <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-500 text-white" title="{{ $mtcPendingCount }} butuh tindakan">
                    {{ $mtcPendingCount }}
                </span>
            @endif
        </a>
        <a href="{{ route('sarpras.pengajuan') }}" 
           class="relative px-3 py-1.5 rounded-lg text-xs md:text-sm font-medium transition {{ $active === 'pengajuan' ? 'bg-white dark:bg-gray-800 text-primary shadow-xs font-semibold' : 'text-text-secondary hover:text-text-main hover:bg-surface' }}">
            Pengajuan
            @if($pengajuanPendingCount > 0)
                <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-600 text-white" title="{{ $pengajuanPendingCount }} butuh persetujuan">
                    {{ $pengajuanPendingCount }}
                </span>
            @endif
        </a>
        <a href="{{ route('sarpras.ruangan') }}" 
           class="px-3 py-1.5 rounded-lg text-xs md:text-sm font-medium transition {{ $active === 'ruangan' ? 'bg-white dark:bg-gray-800 text-primary shadow-xs font-semibold' : 'text-text-secondary hover:text-text-main hover:bg-surface' }}">
            Ruangan
        </a>
        <a href="{{ route('sarpras.kategori') }}" 
           class="px-3 py-1.5 rounded-lg text-xs md:text-sm font-medium transition {{ $active === 'kategori' ? 'bg-white dark:bg-gray-800 text-primary shadow-xs font-semibold' : 'text-text-secondary hover:text-text-main hover:bg-surface' }}">
            Kategori
        </a>
        <a href="{{ route('sarpras.sumber-dana') }}" 
           class="px-3 py-1.5 rounded-lg text-xs md:text-sm font-medium transition {{ $active === 'sumber' ? 'bg-white dark:bg-gray-800 text-primary shadow-xs font-semibold' : 'text-text-secondary hover:text-text-main hover:bg-surface' }}">
            Sumber Dana
        </a>
        <a href="{{ route('sarpras.laporan') }}" 
           class="px-3 py-1.5 rounded-lg text-xs md:text-sm font-medium transition {{ $active === 'laporan' ? 'bg-white dark:bg-gray-800 text-primary shadow-xs font-semibold' : 'text-text-secondary hover:text-text-main hover:bg-surface' }}">
            Laporan
        </a>
    </div>
</div>
