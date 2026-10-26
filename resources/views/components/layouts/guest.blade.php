<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $sekolah = \App\Modules\Sistem\Models\School::current();
        $faviconUrl = $sekolah->logoAplikasiUrl() ?: $sekolah->logoSekolahUrl() ?: asset('favicon.ico');
    @endphp
    <title>{{ $title ?? $sekolah->nama_aplikasi ?? 'SIRAGA' }} - {{ config('app.name') }}</title>
    <link rel="icon" href="{{ $faviconUrl }}">
    <link rel="shortcut icon" href="{{ $faviconUrl }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <script>
        const adminTheme = localStorage.getItem('siraga_admin_theme') || localStorage.getItem('theme');
        if (adminTheme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="min-h-screen bg-bg font-sans text-text-main antialiased relative"
      x-data="{ 
          darkMode: (localStorage.getItem('siraga_admin_theme') || localStorage.getItem('theme')) === 'dark',
          toggleTheme() {
              this.darkMode = !this.darkMode;
              localStorage.setItem('siraga_admin_theme', this.darkMode ? 'dark' : 'light');
              localStorage.removeItem('theme');
              if (this.darkMode) {
                  document.documentElement.classList.add('dark');
              } else {
                  document.documentElement.classList.remove('dark');
              }
          }
      }">
    <!-- Theme Toggle in Guest Layout (Top Right) -->
    <div class="absolute top-4 right-4">
        <button type="button" 
                @click="toggleTheme()" 
                class="rounded-lg p-2.5 text-text-secondary bg-surface border border-border hover:bg-surface-hover hover:text-text-main transition-colors shadow-xs" 
                :title="darkMode ? 'Beralih ke Mode Terang' : 'Beralih ke Mode Gelap'">
            <span x-show="darkMode" x-cloak>
                <svg class="w-5 h-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </span>
            <span x-show="!darkMode">
                <svg class="w-5 h-5 text-slate-600 hover:text-primary transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
            </span>
        </button>
    </div>
    <main class="flex min-h-screen items-center justify-center p-4">
 <div class="w-full max-w-md">
 <div class="mb-6 text-center">
 @if($sekolah->logoAplikasiUrl())
 <img src="{{ $sekolah->logoAplikasiUrl() }}" alt="Logo" class="h-20 w-auto mx-auto mb-4">
 @endif
 <h1 class="text-3xl font-bold text-primary">{{ $sekolah->nama_aplikasi }}</h1>
 <p class="text-sm text-text-secondary">{{ $sekolah->kepanjangan_aplikasi }} &middot; {{ $sekolah->nama }}</p>
 </div>
 {{ $slot }}
 </div>
 </main>
 @livewireScripts
</body>
</html>
