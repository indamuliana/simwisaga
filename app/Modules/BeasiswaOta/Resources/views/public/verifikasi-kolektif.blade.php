<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Tanda Terima Kolektif OTA - {{ $batchKode }}</title>
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
    <div class="max-w-xl w-full bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden my-4">
        <!-- Header Sekolah -->
        <div class="bg-gradient-to-r from-slate-900 to-slate-800 text-white p-5 text-center">
            @if(!empty($school->nama_yayasan))
                <div class="text-[11px] font-semibold text-slate-300 uppercase tracking-widest">{{ $school->nama_yayasan }}</div>
            @endif
            <h1 class="text-base sm:text-lg font-bold tracking-tight mt-0.5">{{ $school->nama ?? 'SMK WIKRAMA 1 GARUT' }}</h1>
            <div class="text-xs text-slate-400 mt-1">Program Beasiswa Orang Tua Asuh (OTA) • Setoran Kolektif</div>
        </div>

        <!-- Banner Status Terverifikasi -->
        <div class="p-6 bg-emerald-50/70 border-b border-emerald-100 text-center">
            <div class="w-14 h-14 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-3 shadow-xs">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <span class="inline-block px-3 py-1 bg-emerald-600 text-white text-xs font-black uppercase tracking-wider rounded-full shadow-xs mb-2">
                Setoran Kolektif Terverifikasi
            </span>
            <p class="text-xs text-emerald-950 font-medium leading-relaxed max-w-sm mx-auto">
                Tanda terima donasi kolektif ini telah diverifikasi secara elektronik dan sah tercatat dalam sistem administrasi OTA SIRAGA.
            </p>
        </div>

        <!-- Rincian Data Setoran Kolektif -->
        <div class="p-5 sm:p-6 space-y-4">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                Ringkasan Setoran Kelompok
            </div>

            <table class="w-full text-xs">
                <tbody class="divide-y divide-slate-100">
                    <tr>
                        <td class="py-2.5 text-slate-500 font-medium w-36">Kode Batch</td>
                        <td class="py-2.5 font-mono font-bold text-slate-900">: {{ $batchKode }}</td>
                    </tr>
                    <tr>
                        <td class="py-2.5 text-slate-500 font-medium">Kelompok Donatur</td>
                        <td class="py-2.5 font-bold text-slate-900">
                            : {{ $first->kelompok?->nama_kelompok ?? 'Kelompok Donatur' }}
                            @if($first->kelompok?->kode_kelompok)
                                <span class="text-slate-500 font-normal">({{ $first->kelompok->kode_kelompok }})</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2.5 text-slate-500 font-medium">Penyetor / Koordinator</td>
                        <td class="py-2.5 font-semibold text-slate-800">
                            : {{ $first->penyetor_nama ?: ($first->kelompok?->koordinator_nama ?: 'Koordinator Kelompok') }}
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2.5 text-slate-500 font-medium">Tanggal Penyetoran</td>
                        <td class="py-2.5 font-semibold text-slate-800">: {{ $first->tanggal_donasi->translatedFormat('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td class="py-3 text-slate-500 font-medium">Total Nominal</td>
                        <td class="py-3">
                            <span class="text-base font-extrabold text-emerald-600">: Rp {{ number_format($totalNominal, 0, ',', '.') }},-</span>
                            <div class="text-[11px] text-slate-500 italic mt-0.5 pl-2"># {{ $terbilang }} #</div>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2.5 text-slate-500 font-medium">Diterima Oleh</td>
                        <td class="py-2.5 font-semibold text-slate-800">: {{ $bendaharaNama }} (Bendahara OTA)</td>
                    </tr>
                </tbody>
            </table>

            <!-- Tabel Daftar Donatur Anggota -->
            <div class="pt-2">
                <div class="text-xs font-bold text-slate-700 mb-2">
                    Daftar Donatur Anggota ({{ $donasis->count() }} Orang):
                </div>
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200">
                            <tr>
                                <th class="py-2 px-3 text-center w-8">No</th>
                                <th class="py-2 px-3">Nama Donatur</th>
                                <th class="py-2 px-3">Periode</th>
                                <th class="py-2 px-3 text-right">Nominal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($donasis as $idx => $d)
                                <tr class="hover:bg-slate-50/50">
                                    <td class="py-2 px-3 text-center text-slate-400">{{ $idx + 1 }}</td>
                                    <td class="py-2 px-3 font-semibold text-slate-900">{{ $d->donatur->nama }}</td>
                                    <td class="py-2 px-3 text-slate-600">{{ $d->keterangan_periode ?: '-' }}</td>
                                    <td class="py-2 px-3 text-right font-bold text-slate-900">Rp {{ number_format($d->nominal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="p-4 bg-slate-50 border-t border-slate-200 text-center text-[11px] text-slate-400">
            Diverifikasi melalui Scan QR Code • SIRAGA {{ date('Y') }}
        </div>
    </div>
</body>
</html>
