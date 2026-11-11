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
        const adminTheme = localStorage.getItem('siraga_admin_theme') || localStorage.getItem('theme');
        if (adminTheme === 'dark') {
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
        <nav class="space-y-2 p-4 text-xs">
            @php
                $searchableMenus = [];
                foreach ($sidebarMenu ?? [] as $group) {
                    foreach ($group['modules'] ?? [] as $module) {
                        foreach ($module['items'] ?? [] as $item) {
                            if (\Illuminate\Support\Facades\Route::has($item['route'])) {
                                $searchableMenus[] = [
                                    'group'  => $group['label'],
                                    'module' => $module['label'],
                                    'label'  => $item['label'],
                                    'url'    => route($item['route']),
                                ];
                            }
                        }
                    }
                }

                $getGroupSvg = function($icon, $label) {
                    return match($icon ?? $label) {
                        'chart-bar', 'ANALISIS' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />',
                        'academic-cap', 'KURIKULUM' => '<path d="M12 14l9-5-9-5-9 5 9 5z" /><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0v6" />',
                        'user-group', 'KESISWAAN' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />',
                        'moon', 'KEPESANTRENAN' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />',
                        'badge-check', 'KEPEGAWAIAN & SDM' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />',
                        'office-building', 'SARANA & PRASARANA' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />',
                        'cog', 'PENGATURAN' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />',
                        'user', 'Portal Siswa' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />',
                        default => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />',
                    };
                };
            @endphp

            {{-- Kotak Pencarian Menu (Sebelum Dashboard) --}}
            <div x-data="{
                    searchQuery: '',
                    allMenus: {{ Js::from($searchableMenus) }},
                    get filteredMenus() {
                        if (!this.searchQuery.trim()) return [];
                        const q = this.searchQuery.toLowerCase().trim();
                        return this.allMenus.filter(m => 
                            m.label.toLowerCase().includes(q) || 
                            m.module.toLowerCase().includes(q) || 
                            m.group.toLowerCase().includes(q)
                        ).slice(0, 10);
                    }
                 }"
                 class="mb-2">
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-2.5 text-text-muted">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text"
                           x-model="searchQuery"
                           @keydown.escape="searchQuery = ''"
                           placeholder="Cari menu..."
                           class="w-full rounded-lg border border-border bg-surface py-1.5 pl-8 pr-7 text-xs text-text-main placeholder-text-muted transition-colors focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                    <button type="button"
                            x-show="searchQuery.trim().length > 0"
                            x-cloak
                            @click="searchQuery = ''"
                            class="absolute inset-y-0 right-0 flex items-center pr-2 text-text-muted hover:text-text-main"
                            title="Hapus pencarian">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Dropdown Hasil Pencarian --}}
                <div x-show="searchQuery.trim().length > 0"
                     x-cloak
                     class="mt-1 space-y-1 rounded-lg border border-border bg-surface p-1.5 shadow-md max-h-56 overflow-y-auto">
                    <template x-for="item in filteredMenus" :key="item.url">
                        <a :href="item.url"
                           @click="if (window.innerWidth < 1024) mobileOpen = false"
                           class="block rounded-md px-2.5 py-1.5 text-xs text-text-secondary hover:bg-primary-soft hover:text-primary transition-colors">
                            <div class="font-medium text-text-main truncate" x-text="item.label"></div>
                            <div class="text-[10px] text-text-muted flex items-center gap-1 truncate">
                                <span x-text="item.group"></span>
                                <span>&rsaquo;</span>
                                <span x-text="item.module"></span>
                            </div>
                        </a>
                    </template>
                    <div x-show="filteredMenus.length === 0" class="px-2.5 py-2.5 text-center text-xs text-text-muted">
                        Menu "<span x-text="searchQuery" class="font-semibold"></span>" tidak ditemukan
                    </div>
                </div>
            </div>

            <a href="{{ route('dashboard') }}"
               @click="if (window.innerWidth < 1024) mobileOpen = false"
               class="flex items-center justify-between rounded-lg px-2.5 py-2 text-xs font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-primary-soft text-primary font-semibold' : 'text-text-secondary hover:bg-surface-hover hover:text-text-main' }}">
                <span class="flex items-center gap-2">
                    <svg class="h-4 w-4 {{ request()->routeIs('dashboard') ? 'text-primary' : 'text-text-muted' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Dashboard</span>
                </span>
            </a>
            @foreach (($sidebarMenu ?? []) as $group)
                @php
                    $isGroupActive = collect($group['items'] ?? [])->contains(function ($item) {
                        return \Illuminate\Support\Facades\Route::has($item['route'])
                            && request()->routeIs($item['route'].'*');
                    });
                @endphp
                {{-- TINGKAT 1: KELOMPOK --}}
                <div x-data="{ openGroup: {{ $isGroupActive ? 'true' : 'false' }} }" class="pt-2 border-t border-border/40 first:border-t-0 first:pt-0">
                    <button type="button"
                            @click="openGroup = !openGroup"
                            class="flex w-full items-center justify-between rounded-lg px-2.5 py-2 text-xs font-bold uppercase tracking-wider transition-colors {{ $isGroupActive ? 'text-primary bg-primary-soft/40' : 'text-text-muted hover:bg-surface-hover hover:text-text-main' }}">
                        <span class="flex items-center gap-2 truncate">
                            <svg class="h-4 w-4 shrink-0 transition-colors {{ $isGroupActive ? 'text-primary' : 'text-text-muted' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                {!! $getGroupSvg($group['icon'] ?? null, $group['label']) !!}
                            </svg>
                            <span class="truncate">{{ $group['label'] }}</span>
                        </span>
                        <svg class="h-3.5 w-3.5 transform transition-transform duration-200 shrink-0"
                             :class="{ 'rotate-180 text-primary': openGroup, 'text-text-muted': !openGroup }"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    {{-- TINGKAT 2: MODUL-MODUL DI DALAM KELOMPOK (TANPA ICON) --}}
                    <div x-show="openGroup"
                         x-collapse
                         x-cloak
                         class="mt-1 space-y-1 pl-1.5">
                        @foreach ($group['modules'] ?? [] as $module)
                            @php
                                $isModuleActive = collect($module['items'])->contains(function ($item) {
                                    return \Illuminate\Support\Facades\Route::has($item['route'])
                                        && request()->routeIs($item['route'].'*');
                                });
                            @endphp
                            <div x-data="{ openModule: {{ $isModuleActive ? 'true' : 'false' }} }" class="space-y-0.5">
                                <button type="button"
                                        @click="openModule = !openModule"
                                        class="flex w-full items-center justify-between rounded-md px-2.5 py-1.5 text-xs font-medium transition-colors {{ $isModuleActive ? 'text-primary font-semibold bg-primary-soft/60' : 'text-text-secondary hover:bg-surface-hover hover:text-text-main' }}">
                                    <span class="truncate">{{ $module['label'] }}</span>
                                    <svg class="h-3 w-3 transform transition-transform duration-200 shrink-0"
                                         :class="{ 'rotate-180 text-primary': openModule, 'text-text-muted': !openModule }"
                                         fill="none"
                                         viewBox="0 0 24 24"
                                         stroke="currentColor">
                                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                {{-- TINGKAT 3: SUBMODUL LINK HALAMAN (TANPA ICON, FONT SAMA DENGAN KELOMPOK) --}}
                                <div x-show="openModule"
                                     x-collapse
                                     x-cloak
                                     class="space-y-0.5 pl-3.5 ml-2.5 border-l border-border/70 my-1">
                                    @foreach ($module['items'] as $item)
                                        @php
                                            $hasRoute = \Illuminate\Support\Facades\Route::has($item['route']);
                                            $url = $hasRoute ? route($item['route']) : '#';
                                            $isActive = $hasRoute && request()->routeIs($item['route'].'*');
                                        @endphp
                                        <a href="{{ $url }}"
                                           @click="if (window.innerWidth < 1024) mobileOpen = false"
                                           class="flex items-center justify-between rounded-md px-2.5 py-1.5 text-xs transition-colors {{ $isActive ? 'bg-primary-soft font-semibold text-primary' : 'text-text-secondary hover:bg-surface-hover hover:text-text-main' }}">
                                            <span class="truncate">{{ $item['label'] }}</span>
                                            @if (! $hasRoute)
                                                <span class="text-[10px] text-text-muted">(segera)</span>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            </div>
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
