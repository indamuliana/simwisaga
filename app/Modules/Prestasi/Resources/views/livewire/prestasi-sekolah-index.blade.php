<div class="space-y-6">
    @include('prestasi::partials.sub-nav', ['active' => 'sekolah'])

    @if (session()->has('message'))
        <div class="p-4 rounded-xl bg-purple-500/10 border border-purple-500/20 text-purple-700 dark:text-purple-300 text-sm flex items-center justify-between">
            <span class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('message') }}
            </span>
            <button type="button" @click="$el.parentElement.remove()" class="text-purple-700 hover:opacity-75">&times;</button>
        </div>
    @endif

    <!-- Toolbar & Filter -->
    <div class="bg-surface rounded-2xl border border-border p-4 space-y-4">
        <div class="flex flex-col md:flex-row justify-between items-stretch md:items-center gap-3">
            <div class="relative flex-1 max-w-md">
                <input type="text" wire:model.live.debounce.300ms="search" 
                       placeholder="Cari akreditasi, penghargaan, nomor SK..." 
                       class="w-full text-xs rounded-xl border border-border bg-surface pl-9 pr-3 py-2 focus:ring-1 focus:ring-primary focus:border-primary">
                <svg class="w-4 h-4 text-text-muted absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <div class="flex items-center gap-2 self-end md:self-auto">
                <button wire:click="exportExcel" class="btn btn-sm btn-outline border-border flex items-center gap-1.5 text-xs text-text-secondary hover:text-text-main">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export Excel
                </button>

                @can('prestasi.sekolah.tambah')
                <button wire:click="openCreateModal" class="btn btn-sm btn-primary flex items-center gap-1.5 text-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    + Catat Prestasi Sekolah
                </button>
                @endcan
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-border/60">
            <div>
                <label class="text-[10px] uppercase font-bold text-text-muted block mb-1">Kategori Portofolio</label>
                <select wire:model.live="filterKategori" class="w-full text-xs rounded-xl border border-border bg-surface px-2.5 py-1.5">
                    <option value="">Semua Kategori</option>
                    <option value="akreditasi">Akreditasi Sekolah</option>
                    <option value="kejuaraan_institusi">Kejuaraan / Lomba Sekolah</option>
                    <option value="sertifikasi_iso_lsp">Sertifikasi Mutu (ISO/LSP)</option>
                    <option value="penghargaan_khusus">Penghargaan / Apresiasi Khusus</option>
                </select>
            </div>
            <div>
                <label class="text-[10px] uppercase font-bold text-text-muted block mb-1">Tingkat</label>
                <select wire:model.live="filterTingkat" class="w-full text-xs rounded-xl border border-border bg-surface px-2.5 py-1.5">
                    <option value="">Semua Tingkat</option>
                    <option value="kabupaten_kota">Kabupaten/Kota</option>
                    <option value="provinsi">Provinsi</option>
                    <option value="nasional">Nasional</option>
                    <option value="internasional">Internasional</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-surface rounded-2xl border border-border overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-surface-hover/70 text-text-secondary uppercase text-[10px] font-bold tracking-wider border-b border-border">
                    <tr>
                        <th class="p-3 w-12 text-center">No</th>
                        <th class="p-3">Nama Prestasi / Akreditasi</th>
                        <th class="p-3">Lembaga Penyelenggara</th>
                        <th class="p-3">Kategori & Tingkat</th>
                        <th class="p-3">Capaian / Predikat</th>
                        <th class="p-3">Tanggal & Masa Berlaku</th>
                        <th class="p-3 text-center">Bukti</th>
                        <th class="p-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($prestasis as $idx => $item)
                        <tr class="hover:bg-surface-hover/50 transition">
                            <td class="p-3 text-center text-text-muted">
                                {{ $prestasis->firstItem() + $idx }}
                            </td>
                            <td class="p-3">
                                <strong class="text-text-main block text-xs">{{ $item->nama_prestasi }}</strong>
                                @if($item->nomor_sk)
                                    <span class="text-[11px] text-text-secondary block">SK: {{ $item->nomor_sk }}</span>
                                @endif
                            </td>
                            <td class="p-3">
                                <span class="text-xs font-semibold text-text-main block">{{ $item->penyelenggara_nama }}</span>
                            </td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-purple-50 text-purple-700 dark:bg-purple-950 dark:text-purple-300 inline-block">
                                    {{ $item->kategori_label }}
                                </span>
                                <span class="text-[11px] text-text-muted block mt-0.5">Tingkat {{ $item->tingkat_label }}</span>
                            </td>
                            <td class="p-3">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-purple-50 text-purple-700 dark:bg-purple-950 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                                    {{ $item->capaian }}
                                </span>
                            </td>
                            <td class="p-3">
                                <span class="text-xs font-semibold text-text-main block">
                                    {{ $item->tanggal_perolehan ? $item->tanggal_perolehan->format('d/m/Y') : '-' }}
                                </span>
                                <span class="text-[11px] {{ $item->masa_berlaku_sampai && $item->masa_berlaku_sampai->isPast() ? 'text-rose-500 font-semibold' : 'text-text-muted' }} block">
                                    s/d {{ $item->masa_berlaku_sampai ? $item->masa_berlaku_sampai->format('d/m/Y') : 'Permanen' }}
                                </span>
                            </td>
                            <td class="p-3 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    @if($item->sertifikat_file_path)
                                        <a href="{{ Storage::url($item->sertifikat_file_path) }}" target="_blank" 
                                           class="p-1 rounded text-teal-600 hover:bg-teal-50 dark:hover:bg-teal-950" 
                                           title="Lihat Berkas SK / Sertifikat">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </a>
                                    @endif
                                    @if($item->foto_file_path)
                                        <a href="{{ Storage::url($item->foto_file_path) }}" target="_blank" 
                                           class="p-1 rounded text-purple-600 hover:bg-purple-50 dark:hover:bg-purple-950" 
                                           title="Lihat Foto Plakat / Piala">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </a>
                                    @endif
                                    @if(!$item->sertifikat_file_path && !$item->foto_file_path)
                                        <span class="text-text-muted text-xs">-</span>
                                    @endif
                                </div>
                            </td>
                            <td class="p-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button wire:click="openPreview({{ $item->id }})" 
                                            class="p-1.5 rounded-lg text-text-secondary hover:text-text-main hover:bg-surface-hover" 
                                            title="Detail Pratinjau">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>

                                    @can('prestasi.sekolah.edit')
                                    <button wire:click="openEditModal({{ $item->id }})" 
                                            class="p-1.5 rounded-lg text-primary hover:bg-primary-soft" 
                                            title="Edit Data">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    @endcan

                                    @can('prestasi.sekolah.hapus')
                                    <button wire:click="delete({{ $item->id }})" 
                                            wire:confirm="Yakin ingin menghapus prestasi sekolah ini?"
                                            class="p-1.5 rounded-lg text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950" 
                                            title="Hapus Data">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-text-muted text-xs">
                                Belum ada prestasi sekolah yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-border">
            {{ $prestasis->links() }}
        </div>
    </div>

    <!-- MODAL FORM CREATE / EDIT PRESTASI SEKOLAH -->
    @if($showFormModal)
    <div class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 overflow-hidden">
        <form wire:submit="save" class="bg-surface rounded-2xl border border-border w-full max-w-3xl shadow-2xl flex flex-col max-h-[88vh] overflow-hidden">
            <!-- Header (Fixed / Sticky) -->
            <div class="px-6 py-3.5 border-b border-border flex items-center justify-between bg-surface-hover/50 flex-none">
                <h3 class="font-bold text-text-main text-base flex items-center gap-2">
                    <span>🏫</span> {{ $prestasiSekolahId ? 'Edit Prestasi Sekolah' : 'Catat Prestasi Sekolah Baru' }}
                </h3>
                <button type="button" wire:click="$set('showFormModal', false)" 
                        class="w-8 h-8 rounded-lg flex items-center justify-center text-text-muted hover:text-text-main hover:bg-surface-hover text-lg font-bold transition">
                    &times;
                </button>
            </div>

            <!-- Body (Scrollable) -->
            <div class="p-5 sm:p-6 space-y-4 overflow-y-auto flex-1 text-xs">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-semibold text-text-main block mb-1">Nama Prestasi / Akreditasi *</label>
                        <input type="text" wire:model="namaPrestasi" placeholder="Misal: Akreditasi A BAN-SM, Juara 1 Sekolah Sehat" 
                               class="w-full text-xs rounded-xl border border-border bg-surface px-3 py-2">
                        @error('namaPrestasi') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-text-main block mb-1">Kategori Portofolio *</label>
                        <select wire:model="kategori" class="w-full text-xs rounded-xl border border-border bg-surface px-3 py-2">
                            <option value="akreditasi">Akreditasi Sekolah</option>
                            <option value="kejuaraan_institusi">Kejuaraan / Lomba Sekolah</option>
                            <option value="sertifikasi_iso_lsp">Sertifikasi Mutu (ISO/LSP)</option>
                            <option value="penghargaan_khusus">Penghargaan / Apresiasi Khusus</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-semibold text-text-main block mb-1">Lembaga / Badan Pemberi *</label>
                        <input type="text" wire:model="penyelenggaraNama" placeholder="Misal: BAN-SM, Kemendikbudristek, Kemenkes" 
                               class="w-full text-xs rounded-xl border border-border bg-surface px-3 py-2">
                        @error('penyelenggaraNama') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-text-main block mb-1">Tingkat Prestasi *</label>
                        <select wire:model="tingkat" class="w-full text-xs rounded-xl border border-border bg-surface px-3 py-2">
                            <option value="kabupaten_kota">Kabupaten/Kota</option>
                            <option value="provinsi">Provinsi</option>
                            <option value="nasional">Nasional</option>
                            <option value="internasional">Internasional</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-semibold text-text-main block mb-1">Capaian / Nilai / Predikat *</label>
                    <input type="text" wire:model="capaian" placeholder="Misal: Predikat A (Nilai 96), Juara 1, Terakreditasi ISO" 
                           class="w-full text-xs rounded-xl border border-border bg-surface px-3 py-2 font-bold text-purple-700 dark:text-purple-300">
                    @error('capaian') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                    <div class="flex flex-wrap gap-1 mt-1.5">
                        @foreach(['Predikat A (Unggul)', 'Juara 1 Nasional', 'Sekolah Adiwiyata Mandiri', 'Sekolah Ramah Anak', 'ISO 9001:2015', 'LSP-P1 Terlisensi'] as $pill)
                            <button type="button" wire:click="setCapaianTemplate('{{ $pill }}')" 
                                    class="px-2 py-0.5 rounded text-[10px] bg-surface-hover hover:bg-purple-500 hover:text-white transition">
                                {{ $pill }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="text-xs font-semibold text-text-main block mb-1">Tanggal Perolehan *</label>
                        <input type="date" wire:model="tanggalPerolehan" class="w-full text-xs rounded-xl border border-border bg-surface px-3 py-2">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-text-main block mb-1">Masa Berlaku Sampai</label>
                        <input type="date" wire:model="masaBerlakuSampai" class="w-full text-xs rounded-xl border border-border bg-surface px-3 py-2">
                        <span class="text-[10px] text-text-muted mt-0.5 block">Kosongkan jika permanen</span>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-text-main block mb-1">Nomor SK / Sertifikat</label>
                        <input type="text" wire:model="nomorSk" placeholder="Misal: 123/BAN-SM/SK/2026" class="w-full text-xs rounded-xl border border-border bg-surface px-3 py-2">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-border">
                    <div>
                        <label class="text-xs font-semibold text-text-main block mb-1">Berkas SK / Sertifikat (PDF/Gambar max 5MB)</label>
                        <input type="file" wire:model="sertifikatFile" class="file-input">
                        @if($existingSertifikatPath)
                            <span class="text-[11px] text-teal-600 block mt-1">✓ Berkas SK sebelumnya tersimpan</span>
                        @endif
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-text-main block mb-1">Foto Plakat / Piala (JPG/PNG max 5MB)</label>
                        <input type="file" wire:model="fotoFile" accept="image/*" class="file-input">
                        @if($existingFotoPath)
                            <span class="text-[11px] text-purple-600 block mt-1">✓ Foto piala sebelumnya tersimpan</span>
                        @endif
                    </div>
                </div>

                <div>
                    <label class="text-xs font-semibold text-text-main block mb-1">Deskripsi Tambahan</label>
                    <textarea wire:model="deskripsi" rows="2" placeholder="Keterangan singkat tentang penghargaan..." class="w-full text-xs rounded-xl border border-border bg-surface px-3 py-2"></textarea>
                </div>
            </div>

            <!-- Footer (Fixed / Sticky) -->
            <div class="px-6 py-3.5 border-t border-border bg-surface-hover/30 flex items-center justify-between flex-none">
                <span class="text-[11px] text-text-muted hidden sm:inline">*) Field bertanda bintang wajib diisi</span>
                <div class="flex items-center gap-2.5 ml-auto">
                    <button type="button" wire:click="$set('showFormModal', false)" class="btn btn-sm btn-outline border-border text-xs px-4">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary text-xs px-5 flex items-center gap-1.5 shadow-sm">
                        <svg wire:loading wire:target="save, sertifikatFile, fotoFile" class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        Simpan Prestasi Sekolah
                    </button>
                </div>
            </div>
        </form>
    </div>
    @endif

    <!-- MODAL PREVIEW DETAIL PRESTASI SEKOLAH -->
    @if($showPreviewModal && $previewItem)
    <div class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 overflow-hidden">
        <div class="bg-surface rounded-2xl border border-border w-full max-w-xl shadow-2xl flex flex-col max-h-[88vh] overflow-hidden">
            <!-- Header (Fixed / Sticky) -->
            <div class="px-6 py-3.5 border-b border-border flex items-center justify-between bg-surface-hover/50 flex-none">
                <h3 class="font-bold text-text-main text-base flex items-center gap-2">
                    <span>🎖️</span> Portofolio Prestasi Sekolah
                </h3>
                <button type="button" wire:click="$set('showPreviewModal', false)" 
                        class="w-8 h-8 rounded-lg flex items-center justify-center text-text-muted hover:text-text-main hover:bg-surface-hover text-lg font-bold transition">
                    &times;
                </button>
            </div>

            <!-- Body (Scrollable) -->
            <div class="p-6 space-y-4 overflow-y-auto flex-1 text-xs">
                <div class="p-4 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-bold text-purple-700 uppercase tracking-wider">Capaian Kelembagaan</span>
                        <h4 class="text-xl font-black text-purple-800 dark:text-purple-200">{{ $previewItem->capaian }}</h4>
                        <p class="text-xs text-text-secondary mt-0.5">{{ $previewItem->kategori_label }} &bull; Tingkat {{ $previewItem->tingkat_label }}</p>
                    </div>
                    <span class="text-3xl">🏫</span>
                </div>

                <div class="space-y-3">
                    <div>
                        <span class="text-text-muted block text-[11px]">Nama Prestasi / Akreditasi:</span>
                        <strong class="text-text-main text-sm">{{ $previewItem->nama_prestasi }}</strong>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-text-muted block text-[11px]">Lembaga / Badan Penerbit:</span>
                            <strong class="text-text-main">{{ $previewItem->penyelenggara_nama }}</strong>
                        </div>
                        <div>
                            <span class="text-text-muted block text-[11px]">Nomor SK / Sertifikat:</span>
                            <strong class="text-text-main">{{ $previewItem->nomor_sk ?: '-' }}</strong>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-text-muted block text-[11px]">Tanggal Penetapan:</span>
                            <strong class="text-text-main">{{ $previewItem->tanggal_perolehan ? $previewItem->tanggal_perolehan->format('d F Y') : '-' }}</strong>
                        </div>
                        <div>
                            <span class="text-text-muted block text-[11px]">Masa Berlaku:</span>
                            <strong class="text-text-main">{{ $previewItem->masa_berlaku_sampai ? $previewItem->masa_berlaku_sampai->format('d F Y') : 'Permanen' }}</strong>
                            <span class="text-text-secondary block">Status: {{ $previewItem->status_berlaku }}</span>
                        </div>
                    </div>

                    @if($previewItem->deskripsi)
                        <div>
                            <span class="text-text-muted block text-[11px]">Deskripsi:</span>
                            <p class="text-text-main mt-0.5">{{ $previewItem->deskripsi }}</p>
                        </div>
                    @endif

                    <div class="pt-3 border-t border-border grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @if($previewItem->sertifikat_file_path)
                            <div class="p-3 rounded-xl bg-surface border border-border text-center">
                                <span class="text-[11px] font-bold text-text-main block mb-2">Berkas SK / Sertifikat</span>
                                <a href="{{ Storage::url($previewItem->sertifikat_file_path) }}" target="_blank" class="btn btn-xs btn-outline border-border">
                                    📄 Buka Berkas SK
                                </a>
                            </div>
                        @endif

                        @if($previewItem->foto_file_path)
                            <div class="p-3 rounded-xl bg-surface border border-border text-center">
                                <span class="text-[11px] font-bold text-text-main block mb-2">Foto Plakat / Piala</span>
                                <img src="{{ Storage::url($previewItem->foto_file_path) }}" alt="Foto Piala" class="h-28 w-auto mx-auto rounded-lg object-cover">
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Footer (Fixed / Sticky) -->
            <div class="px-6 py-3 border-t border-border bg-surface-hover/30 flex justify-end flex-none">
                <button type="button" wire:click="$set('showPreviewModal', false)" class="btn btn-sm btn-outline border-border text-xs px-4">
                    Tutup
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
