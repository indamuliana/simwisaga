<div class="space-y-6">
    @include('sarpras::partials.sub-nav', ['active' => 'laporan'])

    {{-- Filter Panel --}}
    <div class="bg-surface border border-border rounded-2xl p-5 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b border-border pb-3">
            <div>
                <h3 class="text-base font-bold text-text-main">Kriteria Rekapitulasi Laporan Aset</h3>
                <p class="text-xs text-text-secondary">Pilih parameter filter untuk menampilkan rekapitulasi data aset sekolah.</p>
            </div>

            <!-- Export Buttons -->
            <div class="flex items-center gap-2">
                <button type="button" wire:click="exportExcel" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs flex items-center gap-1.5 transition shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Export Excel (.xlsx)
                </button>

                <a href="{{ route('sarpras.cetak.laporan-pdf', [
                    'ruangan' => $filterRuangan, 
                    'kategori' => $filterKategori, 
                    'kondisi' => $filterKondisi, 
                    'sumber' => $filterSumberDana
                ]) }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-primary hover:bg-primary-hover text-white font-semibold text-xs flex items-center gap-1.5 transition shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Cetak Laporan (PDF)
                </a>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
            <div>
                <label class="block font-medium text-text-main mb-1">Filter Ruangan</label>
                <select wire:model.live="filterRuangan" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3">
                    <option value="">Semua Ruangan</option>
                    @foreach($ruangans as $r)
                        <option value="{{ $r->id }}">{{ $r->nama_ruangan }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-medium text-text-main mb-1">Filter Kategori</label>
                <select wire:model.live="filterKategori" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoris as $k)
                        <option value="{{ $k->id }}">{{ $k->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-medium text-text-main mb-1">Filter Kondisi</label>
                <select wire:model.live="filterKondisi" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3">
                    <option value="">Semua Kondisi</option>
                    <option value="Baik">Baik</option>
                    <option value="Rusak Ringan">Rusak Ringan</option>
                    <option value="Rusak Berat">Rusak Berat</option>
                    <option value="Hilang">Hilang</option>
                    <option value="Dimusnahkan">Dimusnahkan</option>
                </select>
            </div>

            <div>
                <label class="block font-medium text-text-main mb-1">Sumber Dana</label>
                <select wire:model.live="filterSumberDana" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3">
                    <option value="">Semua Sumber Dana</option>
                    @foreach($sumberDanas as $s)
                        <option value="{{ $s->nama }}">{{ $s->nama }} ({{ $s->kode }})</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Kartu Ringkasan Hasil Filter --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-surface border border-border rounded-xl p-4 shadow-xs">
            <span class="text-text-muted text-[10px] uppercase font-semibold">Total Aset Terpilih</span>
            <div class="text-xl font-bold text-text-main mt-1">{{ number_format($totalItems) }} unit</div>
        </div>
        <div class="bg-surface border border-border rounded-xl p-4 shadow-xs">
            <span class="text-text-muted text-[10px] uppercase font-semibold">Total Nilai Perolehan</span>
            <div class="text-xl font-bold text-text-main mt-1">Rp {{ number_format($totalNilai, 0, ',', '.') }}</div>
        </div>
        <div class="bg-surface border border-border rounded-xl p-4 shadow-xs">
            <span class="text-text-muted text-[10px] uppercase font-semibold">Kondisi Baik</span>
            <div class="text-xl font-bold text-emerald-600 mt-1">{{ number_format($kondisiBaik) }} unit</div>
        </div>
        <div class="bg-surface border border-border rounded-xl p-4 shadow-xs">
            <span class="text-text-muted text-[10px] uppercase font-semibold">Kondisi Rusak</span>
            <div class="text-xl font-bold text-rose-600 mt-1">{{ number_format($kondisiRusak) }} unit</div>
        </div>
    </div>

    {{-- Tabel Preview Laporan --}}
    <div class="bg-surface border border-border rounded-2xl shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-surface-hover/60 border-b border-border text-text-muted font-medium uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-3">No</th>
                        <th class="py-3 px-3">Kode Aset</th>
                        <th class="py-3 px-3">Nama Barang</th>
                        <th class="py-3 px-3">Kategori</th>
                        <th class="py-3 px-3">Ruangan</th>
                        <th class="py-3 px-3">Tahun</th>
                        <th class="py-3 px-3">Kondisi</th>
                        <th class="py-3 px-3 text-right">Nilai Perolehan (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($asets as $idx => $a)
                        <tr class="hover:bg-surface-hover/50 transition">
                            <td class="py-2.5 px-3 text-text-muted">{{ $idx + 1 }}</td>
                            <td class="py-2.5 px-3 font-mono font-semibold text-primary">{{ $a->kode_aset }}</td>
                            <td class="py-2.5 px-3 font-bold text-text-main">{{ $a->nama_barang }}</td>
                            <td class="py-2.5 px-3 text-text-secondary">{{ $a->kategori?->nama }}</td>
                            <td class="py-2.5 px-3 text-text-secondary">{{ $a->ruangan?->nama_ruangan ?? '-' }}</td>
                            <td class="py-2.5 px-3">{{ $a->tahun_perolehan }} ({{ $a->sumber_dana }})</td>
                            <td class="py-2.5 px-3">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $a->kondisi === 'Baik' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                    {{ $a->kondisi }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3 text-right font-medium text-text-main">
                                {{ number_format($a->harga_perolehan, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-text-muted">Tidak ada data aset pada filter yang dipilih.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
