<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Tanda Terima Donasi OTA - {{ $donasi->nomor_transaksi }}</title>
    @vite(['resources/css/app.css'])
    <style>
        /* Fallback styling jika app.css belum dicompile */
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
        <div class="p-6 bg-emerald-50/70 border-b border-emerald-100 text-center">
            <div class="w-14 h-14 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-3 shadow-xs">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <span class="inline-block px-3 py-1 bg-emerald-600 text-white text-xs font-black uppercase tracking-wider rounded-full shadow-xs mb-2">
                Dokumen Resmi Terverifikasi
            </span>
            <p class="text-xs text-emerald-950 font-medium leading-relaxed max-w-sm mx-auto">
                Tanda terima donasi ini telah diverifikasi secara elektronik dan sah tercatat dalam sistem administrasi OTA SIRAGA.
            </p>
        </div>

        <!-- Rincian Data Transaksi (Tabel Ringkas Minimalis) -->
        <div class="p-5 sm:p-6 space-y-4">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                Rincian Transaksi Donasi
            </div>

            <table class="w-full text-xs">
                <tbody class="divide-y divide-slate-100">
                    <tr>
                        <td class="py-2.5 text-slate-500 font-medium w-36">Nomor Transaksi</td>
                        <td class="py-2.5 font-mono font-bold text-slate-900">: {{ $donasi->nomor_transaksi }}</td>
                    </tr>
                    <tr>
                        <td class="py-2.5 text-slate-500 font-medium">Tanggal Donasi</td>
                        <td class="py-2.5 font-semibold text-slate-800">: {{ $donasi->tanggal_donasi->translatedFormat('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td class="py-2.5 text-slate-500 font-medium">Nama Donatur</td>
                        <td class="py-2.5 font-bold text-slate-900">
                            : {{ $donasi->donatur->nama }}
                            <span class="ml-1 text-[10px] font-semibold px-2 py-0.5 rounded bg-sky-100 text-sky-800">
                                {{ ucfirst($donasi->tipe_donatur) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2.5 text-slate-500 font-medium">Alokasi Program</td>
                        <td class="py-2.5 text-slate-800">
                            : @if($donasi->kelompok)
                                <strong>{{ $donasi->kelompok->nama_kelompok }}</strong> ({{ $donasi->kelompok->kode_kelompok }})
                            @else
                                <strong>Kas Bebas / Cadangan Insidental</strong>
                            @endif
                        </td>
                    </tr>
                    @if(!empty($donasi->keterangan_periode))
                    <tr>
                        <td class="py-2.5 text-slate-500 font-medium">Periode Donasi</td>
                        <td class="py-2.5 font-semibold text-slate-800">
                            : {{ $donasi->keterangan_periode }}
                            @if($donasi->tahunAjaran)
                                <span class="text-slate-500 font-normal">({{ $donasi->tahunAjaran->nama }})</span>
                            @endif
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <td class="py-3 text-slate-500 font-medium">Jumlah Donasi</td>
                        <td class="py-3">
                            <span class="text-base font-extrabold text-emerald-600">: Rp {{ number_format($donasi->nominal, 0, ',', '.') }},-</span>
                            <div class="text-[11px] text-slate-500 italic mt-0.5 pl-2">(Terbilang: {{ $terbilang }})</div>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2.5 text-slate-500 font-medium">Disahkan Oleh</td>
                        <td class="py-2.5 font-semibold text-slate-800">: {{ $bendaharaNama }} (Bendahara OTA)</td>
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
