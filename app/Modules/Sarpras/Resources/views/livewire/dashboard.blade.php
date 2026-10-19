<div class="space-y-6">
    @include('sarpras::partials.sub-nav', ['active' => 'index'])

    {{-- Kartu Metrik Utama --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Aset Tetap -->
        <div class="bg-surface border border-border rounded-2xl p-5 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-text-muted uppercase tracking-wider">Total Aset Tetap</p>
                <h3 class="text-2xl font-bold text-text-main mt-1">{{ number_format($totalAset) }} <span class="text-sm font-normal text-text-secondary">unit</span></h3>
                <p class="text-xs text-text-secondary mt-1">Rp {{ number_format($totalNilaiAset, 0, ',', '.') }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
        </div>

        <!-- Kondisi Fisik Aset -->
        <div class="bg-surface border border-border rounded-2xl p-5 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-text-muted uppercase tracking-wider">Kondisi Aset</p>
                <div class="flex items-center gap-3 mt-1">
                    <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">Baik: {{ $kondisiBaik }}</span>
                    <span class="text-sm font-semibold text-rose-600 dark:text-rose-400">Rusak: {{ $kondisiRusak }}</span>
                </div>
                <p class="text-xs text-text-secondary mt-1">{{ $totalAset > 0 ? round(($kondisiBaik / $totalAset) * 100) : 0 }}% dalam kondisi optimal</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- Bahan Habis Pakai -->
        <div class="bg-surface border border-border rounded-2xl p-5 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-text-muted uppercase tracking-wider">Bahan Habis Pakai</p>
                <h3 class="text-2xl font-bold text-text-main mt-1">{{ number_format($totalBhp) }} <span class="text-sm font-normal text-text-secondary">item</span></h3>
                <p class="text-xs {{ $bhpMenipis > 0 ? 'text-amber-500 font-semibold' : 'text-text-secondary' }} mt-1">
                    {{ $bhpMenipis }} item stok menipis
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>
        </div>

        <!-- Sirkulasi & Tiket -->
        <div class="bg-surface border border-border rounded-2xl p-5 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-text-muted uppercase tracking-wider">Sirkulasi & Pemeliharaan</p>
                <div class="flex items-center gap-3 mt-1">
                    <span class="text-sm font-semibold text-blue-600 dark:text-blue-400">Pinjam: {{ $asetDipinjam }}</span>
                    <span class="text-sm font-semibold text-rose-600 dark:text-rose-400">Servis: {{ $pemeliharaanPending }}</span>
                </div>
                <p class="text-xs text-purple-600 dark:text-purple-400 font-medium mt-1">{{ $pengajuanPending }} pengajuan pending</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        </div>
    </div>

    {{-- Sebaran Aset per Kategori & Ruangan --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Kategori -->
        <div class="bg-surface border border-border rounded-2xl p-5 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-base font-bold text-text-main">Aset per Kategori</h4>
                <a href="{{ route('sarpras.kategori') }}" class="text-xs text-primary hover:underline">Kelola Kategori &rarr;</a>
            </div>
            <div class="space-y-3">
                @forelse($kategoriStats as $k)
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="font-medium text-text-main">{{ $k->nama }} ({{ $k->kode }})</span>
                            <span class="text-text-muted">{{ $k->asets_count }} aset</span>
                        </div>
                        <div class="w-full bg-border rounded-full h-2 overflow-hidden">
                            <div class="bg-primary h-2 rounded-full" style="width: {{ $totalAset > 0 ? ($k->asets_count / $totalAset) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-text-muted text-center py-4">Belum ada data kategori.</p>
                @endforelse
            </div>
        </div>

        <!-- Ruangan -->
        <div class="bg-surface border border-border rounded-2xl p-5 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-base font-bold text-text-main">Aset per Ruangan</h4>
                <a href="{{ route('sarpras.ruangan') }}" class="text-xs text-primary hover:underline">Setting Ruangan &rarr;</a>
            </div>
            <div class="space-y-3">
                @forelse($ruanganStats as $r)
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="font-medium text-text-main">{{ $r->nama_ruangan }}</span>
                            <span class="text-text-muted">{{ $r->asets_count }} aset</span>
                        </div>
                        <div class="w-full bg-border rounded-full h-2 overflow-hidden">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $totalAset > 0 ? ($r->asets_count / $totalAset) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-text-muted text-center py-4">Belum ada data ruangan.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Tabel Ringkasan Terkini --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Aset Baru Ditambahkan -->
        <div class="bg-surface border border-border rounded-2xl p-5 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-base font-bold text-text-main">Aset Baru Ditambahkan</h4>
                <a href="{{ route('sarpras.aset') }}" class="text-xs text-primary hover:underline">Lihat Semua &rarr;</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-border text-text-muted">
                            <th class="py-2">Kode</th>
                            <th class="py-2">Nama Barang</th>
                            <th class="py-2">Ruangan</th>
                            <th class="py-2">Kondisi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse($recentAsets as $a)
                            <tr>
                                <td class="py-2.5 font-mono text-primary font-medium">{{ $a->kode_aset }}</td>
                                <td class="py-2.5 font-medium text-text-main">{{ $a->nama_barang }}</td>
                                <td class="py-2.5 text-text-secondary">{{ $a->ruangan?->nama_ruangan ?? '-' }}</td>
                                <td class="py-2.5">
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $a->kondisi === 'Baik' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300' }}">
                                        {{ $a->kondisi }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-text-muted">Belum ada aset terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tiket Kerusakan Terkini -->
        <div class="bg-surface border border-border rounded-2xl p-5 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-base font-bold text-text-main">Laporan Kerusakan & Servis</h4>
                <a href="{{ route('sarpras.pemeliharaan') }}" class="text-xs text-primary hover:underline">Kelola Servis &rarr;</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-border text-text-muted">
                            <th class="py-2">No. Tiket</th>
                            <th class="py-2">Aset</th>
                            <th class="py-2">Pelapor</th>
                            <th class="py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse($recentPemeliharaans as $p)
                            <tr>
                                <td class="py-2.5 font-mono font-medium">{{ $p->nomor_tiket }}</td>
                                <td class="py-2.5 font-medium text-text-main">{{ $p->aset?->nama_barang }}</td>
                                <td class="py-2.5 text-text-secondary">{{ $p->pelapor?->name }}</td>
                                <td class="py-2.5">
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $p->status === 'Selesai' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ $p->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-text-muted">Tidak ada laporan kerusakan aktif.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
