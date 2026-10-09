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
</head>
<body class="min-h-screen bg-bg font-sans text-text-main antialiased">
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
