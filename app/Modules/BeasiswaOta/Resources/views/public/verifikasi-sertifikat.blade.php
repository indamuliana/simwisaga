<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Piagam Penghargaan Donatur OTA - {{ $nomorSertifikat }}</title>
    @vite(['resources/css/app.css'])
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
            margin: 0;
            padding: 16px;
        }
    </style>
</head>
<body class="min-h-screen bg-slate-50 flex items-center justify-center p-4">
    <div class="max-w-lg w-full bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden my-4">
        <!-- Header Sekolah -->
        <div class="bg-gradient-to-r from-slate-900 to-slate-800 text-white p-5 text-center">
            @if(!empty($school->nama_yayasan))
                <div class="text-[11px] font-semibold text-slate-300 uppercase tracking-widest">{{ $school->nama_yayasan }}</div>
            @endif
            <h1 class="text-base sm:text-lg font-bold tracking-tight mt-0.5">{{ $school->nama ?? 'SMK WIKRAMA 1 GARUT' }}</h1>
            <div class="text-xs text-slate-400 mt-1">Program Beasiswa Orang Tua Asuh (OTA)</div>
        </div>

        <!-- Banner Status Terverifikasi -->
        <div class="p-6 bg-amber-50/70 border-b border-amber-100 text-center">
            <div class="w-14 h-14 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-3 shadow-xs">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
            </div>
            <span class="inline-block px-3 py-1 bg-amber-600 text-white text-xs font-black uppercase tracking-wider rounded-full shadow-xs mb-2">
                Piagam Penghargaan Resmi Terverifikasi
            </span>
            <p class="text-xs text-amber-950 font-medium leading-relaxed max-w-sm mx-auto">
                Sertifikat piagam penghargaan donatur ini telah diverifikasi secara elektronik dan sah tercatat dalam sistem administrasi OTA SIRAGA.
            </p>
        </div>

        <!-- Rincian Data Piagam -->
        <div class="p-5 sm:p-6 space-y-4">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                Informasi Piagam Penghargaan
            </div>

            <table class="w-full text-xs">
                <tbody class="divide-y divide-slate-100">
                    <tr>
                        <td class="py-2.5 text-slate-500 font-medium w-36">Nomor Piagam</td>
                        <td class="py-2.5 font-mono font-bold text-slate-900">: {{ $nomorSertifikat }}</td>
                    </tr>
                    <tr>
                        <td class="py-2.5 text-slate-500 font-medium">Diberikan Kepada</td>
                        <td class="py-2.5 font-bold text-base text-slate-900">
                            : {{ $donasi->donatur->nama }}
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2.5 text-slate-500 font-medium">Status Keanggotaan</td>
                        <td class="py-2.5 font-semibold text-slate-800">
                            : <span class="inline-block px-2 py-0.5 rounded bg-sky-100 text-sky-800 text-[11px] font-bold">
                                Donatur {{ ucfirst($donasi->tipe_donatur) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2.5 text-slate-500 font-medium">Alokasi / Kelompok</td>
                        <td class="py-2.5 text-slate-800">
                            : @if($donasi->kelompok)
                                <strong>{{ $donasi->kelompok->nama_kelompok }}</strong> ({{ $donasi->kelompok->kode_kelompok }})
                            @else
                                <strong>Program Donasi Insidental</strong>
                            @endif
                        </td>
                    </tr>
                    @if(!empty($donasi->keterangan_periode))
                    <tr>
                        <td class="py-2.5 text-slate-500 font-medium">Periode Donasi</td>
                        <td class="py-2.5 font-semibold text-slate-800">: {{ $donasi->keterangan_periode }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="py-2.5 text-slate-500 font-medium">Tanggal Diterbitkan</td>
                        <td class="py-2.5 font-semibold text-slate-800">: {{ $donasi->tanggal_donasi->translatedFormat('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td class="py-2.5 text-slate-500 font-medium">Koordinator OTA</td>
                        <td class="py-2.5 font-semibold text-slate-800">: {{ $ketuaOtaNama }}</td>
                    </tr>
                    <tr>
                        <td class="py-2.5 text-slate-500 font-medium">Kepala Sekolah</td>
                        <td class="py-2.5 font-semibold text-slate-800">: {{ $kepsekNama }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="p-4 bg-slate-50 border-t border-slate-200 text-center text-[11px] text-slate-400">
            Diverifikasi melalui Scan QR Code • SIRAGA {{ date('Y') }}
        </div>
    </div>
</body>
</html>
