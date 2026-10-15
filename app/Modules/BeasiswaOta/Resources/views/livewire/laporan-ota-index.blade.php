<div class="space-y-6">
    @include('beasiswaota::partials.sub-nav', ['active' => 'laporan'])

    <!-- KPI Ringkasan Kas -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="card p-4 border border-border bg-surface shadow-xs">
            <span class="text-xs font-semibold text-text-secondary uppercase">Saldo Kas Kelompok</span>
            <div class="text-xl font-bold text-text-main mt-1">Rp {{ number_format($ringkasanKas['saldo_kelompok'], 0, ',', '.') }}</div>
        </div>
        <div class="card p-4 border border-border bg-surface shadow-xs">
            <span class="text-xs font-semibold text-text-secondary uppercase">Saldo Kas Insidental</span>
            <div class="text-xl font-bold text-amber-600 mt-1">Rp {{ number_format($ringkasanKas['saldo_insidental'], 0, ',', '.') }}</div>
        </div>
        <div class="card p-4 border border-border bg-surface shadow-xs">
            <span class="text-xs font-semibold text-text-secondary uppercase">Total Saldo Kas Terkumpul</span>
            <div class="text-xl font-black text-emerald-600 mt-1">Rp {{ number_format($ringkasanKas['total_kas'], 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Tab Navigasi Laporan -->
    <div class="flex flex-wrap gap-2 border-b border-border pb-2">
        <button wire:click="$set('activeTab', 'buku-kas')" 
                class="px-4 py-2 font-semibold text-sm rounded-xl transition {{ $activeTab === 'buku-kas' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
            Buku Kas Mutasi Keuangan OTA
        </button>
        <button wire:click="$set('activeTab', 'rekap-donatur')" 
                class="px-4 py-2 font-semibold text-sm rounded-xl transition {{ $activeTab === 'rekap-donatur' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
            Rekapitulasi Donatur & Pemasukan
        </button>
        <button wire:click="$set('activeTab', 'rekap-penyaluran')" 
                class="px-4 py-2 font-semibold text-sm rounded-xl transition {{ $activeTab === 'rekap-penyaluran' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
            Rekapitulasi Penyaluran Siswa
        </button>
    </div>

    <!-- TAB 1: BUKU KAS MUTASI -->
    @if($activeTab === 'buku-kas')
        <div class="card border border-border rounded-2xl p-6 bg-surface shadow-xs space-y-4">
            <div class="flex flex-wrap items-center gap-3">
                <select wire:model.live="filterPosKas" class="select select-bordered select-sm text-xs">
                    <option value="">Semua Pos Kas</option>
                    <option value="kelompok">Kas Kelompok</option>
                    <option value="insidental">Kas Insidental</option>
                </select>

                <div class="flex items-center gap-1.5 text-xs">
                    <span>Dari:</span>
                    <input type="date" wire:model.live="startDate" class="input input-bordered input-sm text-xs">
                    <span>Sampai:</span>
                    <input type="date" wire:model.live="endDate" class="input input-bordered input-sm text-xs">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-border bg-surface-hover/50 text-xs font-semibold text-text-secondary uppercase">
                            <th class="py-3 px-4">Tanggal</th>
                            <th class="py-3 px-4">Pos Kas</th>
                            <th class="py-3 px-4">Deskripsi / Transaksi</th>
                            <th class="py-3 px-4 text-right">Masuk (Debet)</th>
                            <th class="py-3 px-4 text-right">Keluar (Kredit)</th>
                            <th class="py-3 px-4 text-right">Saldo Pos</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border text-xs">
                        @forelse($mutasis as $m)
                            <tr class="hover:bg-surface-hover/30 transition">
                                <td class="py-3 px-4 font-mono">{{ $m->tanggal->translatedFormat('d/m/Y') }}</td>
                                <td class="py-3 px-4">
                                    @if($m->pos_kas === 'kelompok')
                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 text-blue-700">
                                            {{ $m->kelompok?->nama_kelompok ?? 'Kelompok' }}
                                        </span>
                                    @else
                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-700">
                                            Kas Insidental
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-text-main font-medium">{{ $m->deskripsi }}</td>
                                <td class="py-3 px-4 text-right font-bold text-emerald-600">
                                    {{ $m->tipe === 'masuk' ? 'Rp ' . number_format($m->nominal, 0, ',', '.') : '-' }}
                                </td>
                                <td class="py-3 px-4 text-right font-bold text-rose-500">
                                    {{ $m->tipe === 'keluar' ? 'Rp ' . number_format($m->nominal, 0, ',', '.') : '-' }}
                                </td>
                                <td class="py-3 px-4 text-right font-black text-text-main">
                                    Rp {{ number_format($m->saldo_pos, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-text-secondary">Belum ada mutasi keuangan pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $mutasis->links() }}</div>
        </div>
    @endif

    <!-- TAB 2: REKAP DONATUR -->
    @if($activeTab === 'rekap-donatur')
        <div class="card border border-border rounded-2xl p-6 bg-surface shadow-xs space-y-4">
            <h3 class="text-base font-bold text-text-main">Rekapitulasi Total Pemasukan Donatur</h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-border bg-surface-hover/50 text-xs font-semibold text-text-secondary uppercase">
                            <th class="py-3 px-4">Nama Donatur</th>
                            <th class="py-3 px-4">Tipe</th>
                            <th class="py-3 px-4">Kelompok Terafiliasi</th>
                            <th class="py-3 px-4 text-center">Frekuensi Donasi</th>
                            <th class="py-3 px-4 text-right">Total Donasi Disetor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border text-xs">
                        @php $grandTotalDonasi = 0; @endphp
                        @forelse($rekapDonatur as $don)
                            @php 
                                $total = (float) $don->total_donasi; 
                                $grandTotalDonasi += $total;
                            @endphp
                            <tr class="hover:bg-surface-hover/30 transition">
                                <td class="py-3 px-4 font-bold text-text-main">{{ $don->nama }}</td>
                                <td class="py-3 px-4">
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold {{ $don->tipe === 'rutin' ? 'bg-blue-50 text-blue-700' : 'bg-amber-50 text-amber-700' }}">
                                        {{ ucfirst($don->tipe) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    {{ $don->kelompoks->pluck('nama_kelompok')->join(', ') ?: '-' }}
                                </td>
                                <td class="py-3 px-4 text-center">{{ $don->donasiMasuks->count() }} kali</td>
                                <td class="py-3 px-4 text-right font-black text-emerald-600">
                                    Rp {{ number_format($total, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-text-secondary">Belum ada donatur terdaftar.</td>
                            </tr>
                        @endforelse
                        <tr class="bg-surface-hover/50 font-bold text-sm">
                            <td colspan="4" class="py-3 px-4 text-right">Total Seluruh Donasi Masuk:</td>
                            <td class="py-3 px-4 text-right text-emerald-600 font-black">
                                Rp {{ number_format($grandTotalDonasi, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- TAB 3: REKAP PENYALURAN -->
    @if($activeTab === 'rekap-penyaluran')
        <div class="card border border-border rounded-2xl p-6 bg-surface shadow-xs space-y-4">
            <h3 class="text-base font-bold text-text-main">Rekapitulasi Penyaluran Beasiswa ke Siswa</h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-border bg-surface-hover/50 text-xs font-semibold text-text-secondary uppercase">
                            <th class="py-3 px-4">Tanggal & No. Kwitansi</th>
                            <th class="py-3 px-4">Siswa Penerima</th>
                            <th class="py-3 px-4">Sumber Pembiayaan</th>
                            <th class="py-3 px-4">Rincian Komponen Tercover</th>
                            <th class="py-3 px-4 text-right">Nominal Disalurkan</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border text-xs">
                        @php $grandTotalSalur = 0; @endphp
                        @forelse($rekapAlokasi as $al)
                            @php $grandTotalSalur += (float) $al->total_nominal; @endphp
                            <tr class="hover:bg-surface-hover/30 transition">
                                <td class="py-3 px-4">
                                    <div class="font-bold text-text-main">{{ $al->nomor_kwitansi }}</div>
                                    <div class="text-[10px] text-text-secondary">{{ $al->tanggal_alokasi->translatedFormat('d/m/Y') }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-bold text-text-main">{{ $al->siswa->nama }}</div>
                                    <div class="text-[10px] text-text-secondary">{{ $al->siswa->rombel?->nama ?? '-' }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    {{ $al->tipe_alokasi === 'rutin_semester' ? ($al->kelompok?->nama_kelompok ?? 'Kelompok') : 'Kas Insidental' }}
                                </td>
                                <td class="py-3 px-4">
                                    <ul class="list-disc list-inside space-y-0.5">
                                        @foreach($al->details as $d)
                                            <li>{{ $d->nama_komponen }} {{ $d->bulan ? "({$d->bulan})" : '' }}: Rp {{ number_format($d->nominal, 0, ',', '.') }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td class="py-3 px-4 text-right font-black text-text-main">
                                    Rp {{ number_format($al->total_nominal, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <a href="{{ route('beasiswa-ota.cetak.kwitansi', $al->id) }}" target="_blank" 
                                       class="text-primary hover:underline font-semibold">
                                        Kwitansi
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-text-secondary">Belum ada penyaluran beasiswa.</td>
                            </tr>
                        @endforelse
                        <tr class="bg-surface-hover/50 font-bold text-sm">
                            <td colspan="4" class="py-3 px-4 text-right">Total Seluruh Dana Disalurkan:</td>
                            <td class="py-3 px-4 text-right text-text-main font-black">
                                Rp {{ number_format($grandTotalSalur, 0, ',', '.') }}
                            </td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
