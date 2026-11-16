<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Kwitansi Pembayaran - {{ $transaksi?->nomor_transaksi ?? 'Tidak Ditemukan' }}</title>
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
            <h1 class="text-base sm:text-lg font-bold tracking-tight mt-0.5">{{ $school->nama_sekolah ?? 'SMK WIKRAMA 1 GARUT' }}</h1>
            <div class="text-xs text-sky-400 font-semibold mt-1">Sistem Administrasi Keuangan Siswa (SIWARAGA)</div>
        </div>

        @if($transaksi)
            <!-- Status Terverifikasi Sah -->
            <div class="p-6 bg-emerald-50/70 border-b border-emerald-100 text-center">
                <div class="w-14 h-14 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-3 shadow-xs">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <span class="inline-block px-3 py-1 bg-emerald-600 text-white text-xs font-black uppercase tracking-wider rounded-full shadow-xs mb-2">
                    Kwitansi Resmi Terverifikasi Sah
                </span>
                <p class="text-xs text-emerald-950 font-medium leading-relaxed max-w-sm mx-auto">
                    Kwitansi ini tercatat secara elektronik dan valid pada pangkalan data administrasi keuangan sekolah.
                </p>
            </div>

            <!-- Detail Transaksi -->
            <div class="p-5 sm:p-6 space-y-4">
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                    Informasi Pembayaran
                </div>

                <table class="w-full text-xs">
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            <td class="py-2.5 text-slate-500 font-medium w-36">Nomor Kwitansi</td>
                            <td class="py-2.5 font-mono font-bold text-sky-600">: {{ $transaksi->nomor_transaksi }}</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 text-slate-500 font-medium">Tanggal Bayar</td>
                            <td class="py-2.5 font-semibold text-slate-800">: {{ $transaksi->tanggal_bayar->translatedFormat('d F Y, H:i') }} WIB</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 text-slate-500 font-medium">Nama Siswa</td>
                            <td class="py-2.5 font-bold text-slate-900">: {{ $transaksi->siswa?->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 text-slate-500 font-medium">Kelas / Rombel</td>
                            <td class="py-2.5 font-semibold text-slate-700">: {{ $transaksi->siswa?->rombel?->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 text-slate-500 font-medium">Penyetor</td>
                            <td class="py-2.5 font-medium text-slate-800">: {{ $transaksi->nama_penyetor ?: $transaksi->siswa?->nama }}</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 text-slate-500 font-medium">Metode Kas</td>
                            <td class="py-2.5 font-medium text-slate-800">: {{ $transaksi->rekening?->nama_bank ?? 'Kasir Loket' }}</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 text-slate-500 font-medium">Total Dibayar</td>
                            <td class="py-2.5 font-black text-sm text-emerald-600">: Rp {{ number_format($transaksi->total_bayar, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="mt-4 pt-3 border-t border-slate-100">
                    <div class="text-[11px] font-bold text-slate-500 mb-2">Rincian Pos Terbayar:</div>
                    <ul class="text-xs space-y-1.5 text-slate-700">
                        @foreach($transaksi->items as $item)
                            <li class="flex justify-between items-center bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-200">
                                <span>{{ $item->tagihan?->label_tagihan ?? ($item->posBiaya?->nama ?? 'Biaya') }}</span>
                                <span class="font-bold text-slate-900">Rp {{ number_format($item->nominal_dibayar, 0, ',', '.') }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @else
            <!-- Tidak Ditemukan -->
            <div class="p-8 text-center bg-rose-50/70">
                <div class="w-14 h-14 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <h2 class="text-sm font-bold text-rose-900 uppercase">Kwitansi Tidak Valid</h2>
                <p class="text-xs text-rose-700 mt-1">Data kwitansi dengan kode verifikasi ini tidak ditemukan dalam sistem sekolah kami.</p>
            </div>
        @endif

        <div class="p-4 bg-slate-50 border-t border-slate-100 text-center text-[10px] text-slate-400">
            Diverifikasi melalui Scan QR Code &bull; SIWARAGA &copy; {{ date('Y') }} {{ $school->nama_sekolah ?? 'SMK Wikrama 1 Garut' }}
        </div>
    </div>
</body>
</html>

