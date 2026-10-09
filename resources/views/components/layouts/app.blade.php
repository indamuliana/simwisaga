<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'SIRAGA' }} - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @stack('styles')
    @livewireStyles
</head>
<body class="min-h-screen bg-bg font-sans text-text-main antialiased"
      x-data="{ sidebar: false, pinned: localStorage.getItem('sidebar_pinned') !== 'false' }"
      x-init="$watch('pinned', val => localStorage.setItem('sidebar_pinned', val))">

    {{-- Sidebar --}}
    @php
        $sekolah = \App\Modules\Sistem\Models\School::current();
    @endphp
    <!-- Overlay -->
    <div x-show="sidebar" x-cloak @click="sidebar = false" class="fixed inset-0 z-30 bg-black/40"
         :class="pinned ? 'lg:hidden' : ''"></div>
         
    <!-- Sidebar -->
    <aside class="fixed inset-y-0 left-0 z-40 w-64 -translate-x-full overflow-y-auto border-r border-border bg-sidebar transition-transform duration-300"
           :class="(sidebar || pinned) ? 'translate-x-0' : '-translate-x-full'">
        <div class="flex h-16 items-center justify-between border-b border-border px-4 gap-2">
            <div class="flex items-center gap-2 min-w-0 flex-1">
                @if($sekolah->logoAplikasiUrl())
                    <img src="{{ $sekolah->logoAplikasiUrl() }}" alt="Logo" class="h-8 w-auto flex-shrink-0">
                @endif
                <a href="{{ route('dashboard') }}" class="text-lg font-bold text-primary truncate block" title="{{ $sekolah->nama_aplikasi }}">{{ $sekolah->nama_aplikasi }}</a>
            </div>
            <button @click="pinned = !pinned; if(!pinned) sidebar = false" class="hidden lg:flex p-1.5 rounded-md text-text-muted hover:text-text-main hover:bg-surface-hover transition-colors flex-shrink-0" title="Toggle Sidebar">
                <svg class="w-5 h-5 transition-transform duration-300" :class="!pinned ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                </svg>
            </button>
        </div>
        <nav class="space-y-2 p-4 text-sm">
            <a href="{{ route('dashboard') }}"
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
        <header class="sticky top-0 z-20 flex h-16 items-center justify-between border-b border-border bg-surface/90 px-4 backdrop-blur">
            <div class="flex items-center gap-3">
                <button class="rounded-lg p-2 text-text-secondary hover:bg-surface-hover"
                        :class="pinned ? 'lg:hidden' : 'block'"
                        @click="sidebar = true" aria-label="Buka menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                </button>
                @foreach(app(\App\Core\Modules\ModuleRegistry::class)->topbarWidgets() as $widget)
                    @livewire($widget)
                @endforeach
                <div class="hidden text-xs text-text-muted lg:block ml-2">
                    {{ now()->translatedFormat('l, d F Y') }}
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="hidden text-right text-sm leading-tight sm:block">
                    <p class="font-medium text-text-main">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-text-muted">{{ auth()->user()->getRoleNames()->join(', ') ?: 'Tanpa role' }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-secondary">Keluar</button>
                </form>
            </div>
        </header>

        <main class="p-4 lg:p-6">
            @if (session('status'))
                <div class="mb-4 rounded-lg border border-success/30 bg-success-soft px-4 py-3 text-sm text-success">
                    {{ session('status') }}
                </div>
            @endif
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
