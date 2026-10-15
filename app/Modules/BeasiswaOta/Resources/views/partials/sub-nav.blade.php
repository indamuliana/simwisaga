@props(['active' => 'index'])

<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b border-border pb-4">
    <div>
        <h2 class="text-2xl font-bold text-text-main flex items-center gap-2">
            <svg class="w-7 h-7 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
            Program Beasiswa Orang Tua Asuh (OTA)
        </h2>
        <p class="text-sm text-text-secondary mt-1">
            Pengelolaan donatur rutin & insidental, kelompok asuh, komponen pembiayaan siswa, serta akuntabilitas penyaluran dana.
        </p>
    </div>

    <!-- Tab Sub-Modul Navigasi -->
    <div class="flex flex-wrap items-center gap-1.5 bg-surface-hover p-1.5 rounded-xl border border-border">
        <a href="{{ route('beasiswa-ota.index') }}" 
           class="px-3 py-1.5 rounded-lg text-xs md:text-sm font-medium transition {{ $active === 'index' ? 'bg-white dark:bg-gray-800 text-primary shadow-xs font-semibold' : 'text-text-secondary hover:text-text-main hover:bg-surface' }}">
            Dashboard
        </a>
        <a href="{{ route('beasiswa-ota.donatur-kelompok') }}" 
           class="px-3 py-1.5 rounded-lg text-xs md:text-sm font-medium transition {{ $active === 'donatur-kelompok' ? 'bg-white dark:bg-gray-800 text-primary shadow-xs font-semibold' : 'text-text-secondary hover:text-text-main hover:bg-surface' }}">
            Donatur & Kelompok
        </a>
        <a href="{{ route('beasiswa-ota.penerima') }}" 
           class="px-3 py-1.5 rounded-lg text-xs md:text-sm font-medium transition {{ $active === 'penerima' ? 'bg-white dark:bg-gray-800 text-primary shadow-xs font-semibold' : 'text-text-secondary hover:text-text-main hover:bg-surface' }}">
            Siswa & Komponen
        </a>
        <a href="{{ route('beasiswa-ota.donasi-masuk') }}" 
           class="px-3 py-1.5 rounded-lg text-xs md:text-sm font-medium transition {{ $active === 'donasi-masuk' ? 'bg-white dark:bg-gray-800 text-primary shadow-xs font-semibold' : 'text-text-secondary hover:text-text-main hover:bg-surface' }}">
            Donasi Masuk
        </a>
        <a href="{{ route('beasiswa-ota.penyaluran') }}" 
           class="px-3 py-1.5 rounded-lg text-xs md:text-sm font-medium transition {{ $active === 'penyaluran' ? 'bg-white dark:bg-gray-800 text-primary shadow-xs font-semibold' : 'text-text-secondary hover:text-text-main hover:bg-surface' }}">
            Penyaluran
        </a>
        <a href="{{ route('beasiswa-ota.laporan') }}" 
           class="px-3 py-1.5 rounded-lg text-xs md:text-sm font-medium transition {{ $active === 'laporan' ? 'bg-white dark:bg-gray-800 text-primary shadow-xs font-semibold' : 'text-text-secondary hover:text-text-main hover:bg-surface' }}">
            Laporan
        </a>
        <a href="{{ route('beasiswa-ota.setting') }}" 
           class="px-3 py-1.5 rounded-lg text-xs md:text-sm font-medium transition {{ $active === 'setting' ? 'bg-white dark:bg-gray-800 text-primary shadow-xs font-semibold' : 'text-text-secondary hover:text-text-main hover:bg-surface' }}">
            Pengaturan
        </a>
    </div>
</div>
