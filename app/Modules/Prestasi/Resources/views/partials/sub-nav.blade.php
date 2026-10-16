@props(['active' => 'index'])

@php
    $pendingCount = \App\Modules\Prestasi\Models\Prestasi::pending()->count();
    $isSiswa = auth()->user()?->hasRole('Siswa') || auth()->user()?->siswa !== null;
@endphp

<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b border-border pb-4">
    <div>
        <h2 class="text-2xl font-bold text-text-main flex items-center gap-2">
            <svg class="w-7 h-7 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
            </svg>
            Manajemen Prestasi Terpadu
        </h2>
        <p class="text-sm text-text-secondary mt-1">
            Pusat data portofolio prestasi Siswa, Guru & Tenaga Kependidikan, serta Penghargaan Kelembagaan Sekolah.
        </p>
    </div>

    <!-- Tab Sub-Modul Navigasi -->
    <div class="flex flex-wrap items-center gap-1.5 bg-surface-hover p-1.5 rounded-xl border border-border">
        <a href="{{ route('prestasi.index') }}" 
           class="px-3 py-1.5 rounded-lg text-xs md:text-sm font-medium transition {{ $active === 'index' ? 'bg-white dark:bg-gray-800 text-primary shadow-xs font-semibold' : 'text-text-secondary hover:text-text-main hover:bg-surface' }}">
            Dashboard
        </a>
        <a href="{{ route('prestasi.siswa') }}" 
           class="px-3 py-1.5 rounded-lg text-xs md:text-sm font-medium transition {{ $active === 'siswa' ? 'bg-white dark:bg-gray-800 text-primary shadow-xs font-semibold' : 'text-text-secondary hover:text-text-main hover:bg-surface' }}">
            Prestasi Siswa
        </a>
        <a href="{{ route('prestasi.guru') }}" 
           class="px-3 py-1.5 rounded-lg text-xs md:text-sm font-medium transition {{ $active === 'guru' ? 'bg-white dark:bg-gray-800 text-primary shadow-xs font-semibold' : 'text-text-secondary hover:text-text-main hover:bg-surface' }}">
            Prestasi Guru & GTK
        </a>
        <a href="{{ route('prestasi.sekolah') }}" 
           class="px-3 py-1.5 rounded-lg text-xs md:text-sm font-medium transition {{ $active === 'sekolah' ? 'bg-white dark:bg-gray-800 text-primary shadow-xs font-semibold' : 'text-text-secondary hover:text-text-main hover:bg-surface' }}">
            Prestasi Sekolah
        </a>
        
        @can('prestasi.verifikasi')
        <a href="{{ route('prestasi.verifikasi') }}" 
           class="relative px-3 py-1.5 rounded-lg text-xs md:text-sm font-medium transition {{ $active === 'verifikasi' ? 'bg-white dark:bg-gray-800 text-primary shadow-xs font-semibold' : 'text-text-secondary hover:text-text-main hover:bg-surface' }}">
            Verifikasi
            @if($pendingCount > 0)
                <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-bold bg-amber-500 text-white">
                    {{ $pendingCount }}
                </span>
            @endif
        </a>
        @endcan

        @if($isSiswa)
        <a href="{{ route('prestasi.mandiri') }}" 
           class="px-3 py-1.5 rounded-lg text-xs md:text-sm font-medium transition {{ $active === 'mandiri' ? 'bg-white dark:bg-gray-800 text-primary shadow-xs font-semibold' : 'text-text-secondary hover:text-text-main hover:bg-surface' }}">
            Prestasi Saya
        </a>
        @endif
    </div>
</div>
