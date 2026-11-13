<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Kwitansi Penyaluran Beasiswa - {{ $alokasi->nomor_kwitansi }}</title>
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
        <div class="p-6 bg-emerald-50/70 border-b border-emerald-100 text-center">
            <div class="w-14 h-14 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-3 shadow-xs">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <span class="inline-block px-3 py-1 bg-emerald-600 text-white text-xs font-black uppercase tracking-wider rounded-full shadow-xs mb-2">
                Penyaluran Beasiswa Terverifikasi Sah
            </span>
            <p class="text-xs text-emerald-950 font-medium leading-relaxed max-w-sm mx-auto">
                Kwitansi penyaluran beasiswa ini telah diverifikasi secara elektronik dan sah tercatat dalam sistem administrasi OTA SIRAGA.
            </p>
        </div>

        <!-- Rincian Penyaluran -->
        <div class="p-5 sm:p-6 space-y-4">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                Informasi Penyaluran Beasiswa
            </div>

            <table class="w-full text-xs">
                <tbody class="divide-y divide-slate-100">
                    <tr>
                        <td class="py-2 text-slate-500 font-medium w-36">Nomor Kwitansi</td>
                        <td class="py-2 font-mono font-bold text-slate-900">: {{ $alokasi->nomor_kwitansi }}</td>
                    </tr>
                    <tr>
                        <td class="py-2 text-slate-500 font-medium">Tanggal Alokasi</td>
                        <td class="py-2 font-semibold text-slate-800">: {{ $alokasi->tanggal_alokasi->translatedFormat('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td class="py-2 text-slate-500 font-medium">Siswa Penerima</td>
                        <td class="py-2 font-bold text-slate-900">: {{ $alokasi->siswa->nama }} (NISN: {{ $alokasi->siswa->nisn ?? '-' }})</td>
                    </tr>
                    <tr>
                        <td class="py-2 text-slate-500 font-medium">Kelas / Rombel</td>
                        <td class="py-2 text-slate-800">: {{ $alokasi->siswa->rombel?->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="py-2 text-slate-500 font-medium">Periode Semester</td>
                        <td class="py-2 text-slate-800">: {{ $alokasi->semester?->label ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="py-2 text-slate-500 font-medium">Sumber Pembiayaan</td>
                        <td class="py-2 text-slate-800">
                            : @if($alokasi->tipe_alokasi === 'rutin_semester')
                                Kelompok OTA: <strong>{{ $alokasi->kelompok?->nama_kelompok ?? '-' }}</strong>
                            @else
                                <strong>Kas Donasi Insidental</strong>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="py-3 text-slate-500 font-medium">Total Dana Disalurkan</td>
                        <td class="py-3">
                            <span class="text-base font-extrabold text-emerald-600">: Rp {{ number_format($alokasi->total_nominal, 0, ',', '.') }},-</span>
                            <div class="text-[11px] text-slate-500 italic mt-0.5 pl-2"># {{ $terbilang }} #</div>
                        </td>
                    </tr>
                    @if(!empty($alokasi->catatan))
                    <tr>
                        <td class="py-2 text-slate-500 font-medium">Catatan</td>
                        <td class="py-2 text-slate-700 italic">: {{ $alokasi->catatan }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="py-2 text-slate-500 font-medium">Pengelola OTA</td>
                        <td class="py-2 font-semibold text-slate-800">: {{ $bendaharaNama }}</td>
                    </tr>
                    <tr>
                        <td class="py-2 text-slate-500 font-medium">Kepala Sekolah</td>
                        <td class="py-2 font-semibold text-slate-800">: {{ $kepsekNama }}</td>
                    </tr>
                </tbody>
            </table>

            <!-- Rincian Komponen -->
            <div class="pt-2">
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">
                    Rincian Komponen
                </div>
                <div class="border border-slate-200 rounded-lg overflow-hidden">
                    <table class="w-full text-xs">
                        <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200">
                            <tr>
                                <th class="py-2 px-3 text-left">Komponen</th>
                                <th class="py-2 px-3 text-left">Periode</th>
                                <th class="py-2 px-3 text-right">Nominal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($alokasi->details as $item)
                            <tr>
                                <td class="py-2 px-3 font-semibold text-slate-800">{{ $item->nama_komponen }}</td>
                                <td class="py-2 px-3 text-slate-600">
                                    {{ $item->bulan ? 'Bulan ' . $item->bulan . ' ' . ($item->tahun ?? '') : '-' }}
                                </td>
                                <td class="py-2 px-3 text-right font-medium text-slate-900">
                                    Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                </td>
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
