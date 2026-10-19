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
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @stack('styles')
    @livewireStyles
    <script>
        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="min-h-screen bg-bg font-sans text-text-main antialiased"
      x-data="{ 
          mobileOpen: false, 
          pinned: localStorage.getItem('sidebar_pinned') !== 'false',
          darkMode: localStorage.getItem('theme') === 'dark',
          toggleTheme() {
              this.darkMode = !this.darkMode;
              localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
              if (this.darkMode) {
                  document.documentElement.classList.add('dark');
              } else {
                  document.documentElement.classList.remove('dark');
              }
          }
      }"
      x-init="$watch('pinned', val => localStorage.setItem('sidebar_pinned', val))"
      @keydown.escape.window="mobileOpen = false">

    {{-- Sidebar --}}
    <!-- Backdrop Overlay Mobile -->
    <div x-show="mobileOpen" 
         x-cloak 
         @click="mobileOpen = false" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-30 bg-black/60 backdrop-blur-xs lg:hidden"></div>
         
    <!-- Sidebar -->
    <aside class="fixed inset-y-0 left-0 z-40 w-64 overflow-y-auto border-r border-border bg-sidebar transition-transform duration-300 ease-in-out"
           :class="{
               'translate-x-0': mobileOpen,
               '-translate-x-full': !mobileOpen,
               'lg:translate-x-0': pinned,
               'lg:-translate-x-full': !pinned
           }">
        <div class="flex h-16 items-center justify-between border-b border-border px-4 gap-2">
            <div class="flex items-center gap-2 min-w-0 flex-1">
                @if($sekolah->logoAplikasiUrl())
                    <img src="{{ $sekolah->logoAplikasiUrl() }}" alt="Logo" class="h-8 w-auto flex-shrink-0">
                @endif
                <a href="{{ route('dashboard') }}" class="text-lg font-bold text-primary truncate block" title="{{ $sekolah->nama_aplikasi }}">{{ $sekolah->nama_aplikasi }}</a>
            </div>
            <!-- Desktop Pin/Unpin Toggle Button -->
            <button type="button" 
                    @click="pinned = !pinned" 
                    class="hidden lg:flex p-1.5 rounded-md text-text-muted hover:text-text-main hover:bg-surface-hover transition-colors flex-shrink-0" 
                    title="Toggle Pin Sidebar">
                <svg class="w-5 h-5 transition-transform duration-300" :class="!pinned ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                </svg>
            </button>
            <!-- Mobile Close Button -->
            <button type="button" 
                    @click="mobileOpen = false" 
                    class="flex lg:hidden p-1.5 rounded-md text-text-muted hover:text-text-main hover:bg-surface-hover transition-colors flex-shrink-0" 
                    title="Tutup Menu">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <nav class="space-y-2 p-4 text-sm">
            <a href="{{ route('dashboard') }}"
               @click="if (window.innerWidth < 1024) mobileOpen = false"
               class="flex items-center justify-between rounded-lg px-3 py-2 font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-primary-soft text-primary font-semibold' : 'text-text-secondary hover:bg-surface-hover hover:text-text-main' }}">
                <span class="flex items-center gap-2">
                    <svg class="h-4 w-4 {{ request()->routeIs('dashboard') ? 'text-primary' : 'text-text-muted' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Dashboard</span>
                </span>
            </a>
            @foreach (($sidebarMenu ?? []) as $group)
                @php
                    $isGroupActive = collect($group['items'])->contains(function ($item) {
                        return \Illuminate\Support\Facades\Route::has($item['route'])
                            && request()->routeIs($item['route'].'*');
                    });
                @endphp
                <div x-data="{ open: {{ $isGroupActive ? 'true' : 'false' }} }" class="pt-1">
                    <button type="button"
                            @click="open = !open"
                            class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-xs font-semibold uppercase tracking-wider transition-colors {{ $isGroupActive ? 'text-primary font-bold' : 'text-text-muted hover:bg-surface-hover hover:text-text-main' }}">
                        <span class="truncate">{{ $group['label'] }}</span>
                        <svg class="h-3.5 w-3.5 transform transition-transform duration-200"
                             :class="{ 'rotate-180 text-primary': open, 'text-text-muted': !open }"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open"
                         x-collapse
                         x-cloak
                         class="mt-1 space-y-1 pl-2">
                        @foreach ($group['items'] as $item)
                            @php
                                $hasRoute = \Illuminate\Support\Facades\Route::has($item['route']);
                                $url = $hasRoute ? route($item['route']) : '#';
                                $isActive = $hasRoute && request()->routeIs($item['route'].'*');
                            @endphp
                            <a href="{{ $url }}"
                               @click="if (window.innerWidth < 1024) mobileOpen = false"
                               class="flex items-center justify-between rounded-lg px-3 py-1.5 text-xs transition-colors {{ $isActive ? 'bg-primary-soft font-semibold text-primary' : 'text-text-secondary hover:bg-surface-hover hover:text-text-main' }}">
                                <span class="truncate">{{ $item['label'] }}</span>
                                @if (! $hasRoute)
                                    <span class="text-[10px] text-text-muted">(segera)</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </nav>
    </aside>

    <div class="transition-all duration-300" :class="pinned ? 'lg:pl-64' : 'lg:pl-0'">
        {{-- Topbar --}}
        <header class="sticky top-0 z-20 flex h-16 items-center justify-between border-b border-border bg-surface/90 px-3 sm:px-4 backdrop-blur">
            <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                <button type="button"
                        class="rounded-lg p-2 text-text-secondary hover:bg-surface-hover hover:text-text-main transition-colors flex-shrink-0"
                        :class="pinned ? 'lg:hidden' : 'flex'"
                        @click="if (window.innerWidth >= 1024) { pinned = !pinned } else { mobileOpen = !mobileOpen }" 
                        aria-label="Toggle menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                </button>
                @foreach(app(\App\Core\Modules\ModuleRegistry::class)->topbarWidgets() as $widget)
                    @livewire($widget)
                @endforeach
                <div class="hidden text-xs text-text-muted lg:block ml-2 whitespace-nowrap">
                    {{ now()->translatedFormat('l, d F Y') }}
                </div>
            </div>
            <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
                <!-- Theme Toggle Button -->
                <button type="button" 
                        @click="toggleTheme()" 
                        class="rounded-lg p-2 text-text-secondary hover:bg-surface-hover hover:text-text-main transition-colors flex-shrink-0" 
                        :title="darkMode ? 'Beralih ke Mode Terang' : 'Beralih ke Mode Gelap'"
                        :aria-label="darkMode ? 'Beralih ke Mode Terang' : 'Beralih ke Mode Gelap'">
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

                <!-- User Profile & Action Dropdown -->
                <div x-data="{ profileOpen: false }" class="relative">
                    <button type="button" 
                            @click="profileOpen = !profileOpen"
                            @click.outside="profileOpen = false"
                            class="flex items-center gap-2.5 p-1 rounded-xl hover:bg-surface-hover transition-colors text-left focus:outline-hidden"
                            aria-expanded="false">
                        <div class="w-8 h-8 rounded-xl bg-primary-soft text-primary font-bold flex items-center justify-center text-xs shrink-0 border border-primary/20">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                        <div class="hidden text-left text-xs leading-tight sm:block max-w-[140px] truncate">
                            <p class="font-bold text-text-main truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[10px] text-text-muted truncate">{{ auth()->user()->getRoleNames()->join(', ') ?: 'Tanpa role' }}</p>
                        </div>
                        <svg class="w-3.5 h-3.5 text-text-muted transition-transform duration-200" :class="{ 'rotate-180': profileOpen }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="profileOpen" 
                         x-cloak
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-52 rounded-2xl bg-surface border border-border shadow-xl py-1.5 z-30 divide-y divide-border">
                        <div class="px-3.5 py-2.5">
                            <p class="text-xs font-bold text-text-main truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[10px] text-text-muted truncate mt-0.5">{{ auth()->user()->username }} &bull; {{ auth()->user()->getRoleNames()->first() ?: 'User' }}</p>
                        </div>
                        <div class="py-1">
                            <button type="button" 
                                    @click="profileOpen = false; $dispatch('open-change-password-modal')"
                                    class="w-full px-3.5 py-2 text-xs font-semibold text-text-main hover:bg-surface-hover transition-colors flex items-center gap-2">
                                <span>🔑</span>
                                <span>Ganti Password</span>
                            </button>
                        </div>
                        <div class="py-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full px-3.5 py-2 text-xs font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors flex items-center gap-2">
                                    <span>🚪</span>
                                    <span>Keluar dari Akun</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="p-3 sm:p-4 lg:p-6">
            @if (session('status'))
                <div class="mb-4 rounded-lg border border-success/30 bg-success-soft px-4 py-3 text-sm text-success">
                    {{ session('status') }}
                </div>
            @endif
            {{ $slot }}
        </main>
    </div>

    @livewire('core.auth.modal-change-password')
    @livewireScripts
    @stack('scripts')
</body>
</html>
