<div class="space-y-6">
    @include('prestasi::partials.sub-nav', ['active' => 'guru'])

    @if (session()->has('message'))
        <div class="p-4 rounded-xl bg-blue-500/10 border border-blue-500/20 text-blue-700 dark:text-blue-300 text-sm flex items-center justify-between">
            <span class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('message') }}
            </span>
            <button type="button" @click="$el.parentElement.remove()" class="text-blue-700 hover:opacity-75">&times;</button>
        </div>
    @endif

    <!-- Toolbar & Filter -->
    <div class="bg-surface rounded-2xl border border-border p-4 space-y-4">
        <div class="flex flex-col md:flex-row justify-between items-stretch md:items-center gap-3">
            <div class="relative flex-1 max-w-md">
                <input type="text" wire:model.live.debounce.300ms="search" 
                       placeholder="Cari guru, nama kejuaraan, sertifikasi..." 
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

                @can('prestasi.guru.tambah')
                <button wire:click="openCreateModal" class="btn btn-sm btn-primary flex items-center gap-1.5 text-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    + Catat Prestasi Guru / GTK
                </button>
                @endcan
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 border-t border-border/60">
            <div>
                <label class="text-[10px] uppercase font-bold text-text-muted block mb-1">Tahun Ajaran</label>
                <select wire:model.live="filterTahunAjaranId" class="w-full text-xs rounded-xl border border-border bg-surface px-2.5 py-1.5">
                    <option value="">Semua Tahun Ajaran</option>
                    @foreach($tahunAjarans as $ta)
                        <option value="{{ $ta->id }}">{{ $ta->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-[10px] uppercase font-bold text-text-muted block mb-1">Kategori</label>
                <select wire:model.live="filterKategori" class="w-full text-xs rounded-xl border border-border bg-surface px-2.5 py-1.5">
                    <option value="">Semua Kategori</option>
                    <option value="kejuaraan_gtk">Lomba / Kejuaraan GTK</option>
                    <option value="karya_ilmiah">Karya Ilmiah / Inovasi</option>
                    <option value="sertifikasi_keahlian">Sertifikasi Keahlian / Asesor</option>
                    <option value="penghargaan">Penghargaan / Tanda Kehormatan</option>
                </select>
            </div>
            <div>
                <label class="text-[10px] uppercase font-bold text-text-muted block mb-1">Tingkat</label>
                <select wire:model.live="filterTingkat" class="w-full text-xs rounded-xl border border-border bg-surface px-2.5 py-1.5">
                    <option value="">Semua Tingkat</option>
                    <option value="sekolah">Sekolah</option>
                    <option value="kecamatan">Kecamatan</option>
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
                        <th class="p-3">Guru / Tim GTK</th>
                        <th class="p-3">Nama Prestasi / Sertifikasi</th>
                        <th class="p-3">Penyelenggara & Tingkat</th>
                        <th class="p-3">Peringkat / Capaian</th>
                        <th class="p-3">Tanggal & TA</th>
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
                                @if($item->tipe_peserta === 'kelompok')
                                    <div class="flex items-center gap-1.5">
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-blue-50 text-blue-700 dark:bg-blue-950 dark:text-blue-300">
                                            TIM ({{ $item->pegawais->count() }})
                                        </span>
                                        <strong class="text-text-main">{{ $item->nama_tim ?: 'Tim GTK' }}</strong>
                                    </div>
                                    <div class="text-[11px] text-text-secondary line-clamp-1 mt-0.5">
                                        {{ $item->daftar_guru_nama }}
                                    </div>
                                @else
                                    <strong class="text-text-main block">{{ $item->daftar_guru_nama }}</strong>
                                    <span class="text-[11px] text-text-secondary block">
                                        NIP: {{ $item->pegawais->first()?->nip ?: '-' }}
                                    </span>
                                @endif
                            </td>
                            <td class="p-3">
                                <strong class="text-text-main block text-xs">{{ $item->nama_prestasi }}</strong>
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-surface-hover text-text-secondary inline-block mt-0.5">
                                    {{ $item->kategori_label }}
                                </span>
                            </td>
                            <td class="p-3">
                                <span class="text-xs font-semibold text-text-main block">{{ $item->penyelenggara_nama }}</span>
                                <span class="text-[11px] text-text-muted block">Tingkat {{ $item->tingkat_label }}</span>
                            </td>
                            <td class="p-3">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-blue-50 text-blue-700 dark:bg-blue-950 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                    {{ $item->peringkat }}
                                </span>
                            </td>
                            <td class="p-3">
                                <span class="text-xs font-semibold text-text-main block">
                                    {{ $item->tanggal_perolehan ? $item->tanggal_perolehan->format('d/m/Y') : '-' }}
                                </span>
                                <span class="text-[11px] text-text-muted block">
                                    {{ $item->tahunAjaran?->nama ?? '-' }}
                                </span>
                            </td>
                            <td class="p-3 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    @if($item->sertifikat_file_path)
                                        <a href="{{ Storage::url($item->sertifikat_file_path) }}" target="_blank" 
                                           class="p-1 rounded text-teal-600 hover:bg-teal-50 dark:hover:bg-teal-950" 
                                           title="Lihat Berkas Sertifikat (PDF/Scan)">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </a>
                                    @endif
                                    @if($item->foto_file_path)
                                        <a href="{{ Storage::url($item->foto_file_path) }}" target="_blank" 
                                           class="p-1 rounded text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-950" 
                                           title="Lihat Foto Dokumentasi">
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

                                    @can('prestasi.guru.edit')
                                    <button wire:click="openEditModal({{ $item->id }})" 
                                            class="p-1.5 rounded-lg text-primary hover:bg-primary-soft" 
                                            title="Edit Data">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    @endcan

                                    @can('prestasi.guru.hapus')
                                    <button wire:click="delete({{ $item->id }})" 
                                            wire:confirm="Yakin ingin menghapus data prestasi guru ini?"
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
                                Belum ada data prestasi Guru & Tendik yang tercatat.
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

    <!-- MODAL FORM CREATE / EDIT PRESTASI GURU -->
    @if($showFormModal)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-surface rounded-2xl border border-border w-full max-w-2xl shadow-xl overflow-hidden my-8">
            <div class="px-6 py-4 border-b border-border flex items-center justify-between bg-surface-hover/50">
                <h3 class="font-bold text-text-main text-base flex items-center gap-2">
                    <span>👨‍🏫</span> {{ $prestasiGuruId ? 'Edit Prestasi Guru / GTK' : 'Catat Prestasi Guru & Tendik Baru' }}
                </h3>
                <button type="button" wire:click="$set('showFormModal', false)" class="text-text-muted hover:text-text-main text-xl font-bold">&times;</button>
            </div>

            <form wire:submit="save" class="p-6 space-y-4 max-h-[80vh] overflow-y-auto">
                <!-- Tipe Peserta -->
                <div class="bg-surface-hover/40 p-4 rounded-xl border border-border space-y-3">
                    <label class="text-xs font-bold text-text-main block">Tipe Kepesertaan GTK</label>
                    <div class="flex items-center gap-6 text-xs">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" wire:model.live="tipePeserta" value="individu" class="text-primary focus:ring-primary">
                            <span>Individu (1 Guru/Karyawan)</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" wire:model.live="tipePeserta" value="kelompok" class="text-primary focus:ring-primary">
                            <span>Tim / Kelompok GTK</span>
                        </label>
                    </div>

                    @if($tipePeserta === 'kelompok')
                        <div>
                            <label class="text-[11px] font-semibold text-text-secondary block mb-1">Nama Tim / Kelompok</label>
                            <input type="text" wire:model="namaTim" placeholder="Misal: Tim Inovasi Pendidik Wikrama" 
                                   class="w-full text-xs rounded-xl border border-border bg-surface px-3 py-2">
                        </div>

                        <!-- Pilih Banyak Pegawai -->
                        <div class="space-y-2">
                            <label class="text-[11px] font-semibold text-text-secondary block">Tambah Anggota Tim Guru:</label>
                            <select wire:change="addPegawai($event.target.value); $event.target.value='';" class="w-full text-xs rounded-xl border border-border bg-surface px-3 py-2">
                                <option value="">+ Pilih Guru / Karyawan untuk ditambahkan...</option>
                                @foreach($pegawais as $pg)
                                    <option value="{{ $pg->id }}">{{ $pg->nama_gelar }} ({{ $pg->nip ?: 'Non-NIP' }})</option>
                                @endforeach
                            </select>

                            <div class="space-y-1.5 pt-1">
                                @foreach($selectedPegawaiIds as $pIdx => $selP)
                                    <div class="flex items-center justify-between p-2 rounded-lg bg-surface border border-border text-xs">
                                        <span class="font-medium text-text-main flex items-center gap-2">
                                            <span class="w-5 h-5 rounded-full bg-blue-500/10 text-blue-600 flex items-center justify-center font-bold text-[10px]">{{ $pIdx + 1 }}</span>
                                            {{ $selP['nama'] }}
                                        </span>
                                        <div class="flex items-center gap-2">
                                            <select wire:model="selectedPegawaiIds.{{ $pIdx }}.peran" class="text-[11px] rounded-lg border border-border bg-surface px-2 py-1">
                                                <option value="ketua">Ketua Tim</option>
                                                <option value="anggota">Anggota</option>
                                            </select>
                                            <button type="button" wire:click="removePegawai({{ $pIdx }})" class="text-rose-500 hover:text-rose-700 font-bold px-1.5 py-0.5">&times;</button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div>
                            <label class="text-[11px] font-semibold text-text-secondary block mb-1">Pilih Guru / Tenaga Kependidikan *</label>
                            <select wire:model.live="quickSinglePegawaiId" class="w-full text-xs rounded-xl border border-border bg-surface px-3 py-2">
                                <option value="">-- Pilih Guru / Pegawai --</option>
                                @foreach($pegawais as $pg)
                                    <option value="{{ $pg->id }}">{{ $pg->nama_gelar }} ({{ $pg->nip ?: 'Non-NIP' }})</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    @error('selectedPegawaiIds') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                </div>

                <!-- Info Prestasi & Kategori -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-semibold text-text-main block mb-1">Nama Prestasi / Sertifikasi *</label>
                        <input type="text" wire:model="namaPrestasi" placeholder="Misal: Sertifikasi Asesor BNSP, Guru Inovatif" 
                               class="w-full text-xs rounded-xl border border-border bg-surface px-3 py-2">
                        @error('namaPrestasi') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-text-main block mb-1">Kategori Prestasi GTK *</label>
                        <select wire:model="kategori" class="w-full text-xs rounded-xl border border-border bg-surface px-3 py-2">
                            <option value="kejuaraan_gtk">Lomba / Kejuaraan GTK</option>
                            <option value="karya_ilmiah">Karya Ilmiah / Inovasi</option>
                            <option value="sertifikasi_keahlian">Sertifikasi Keahlian / Asesor</option>
                            <option value="penghargaan">Penghargaan / Tanda Kehormatan</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-semibold text-text-main block mb-1">Penyelenggara / Lembaga Pemberi *</label>
                        <input type="text" wire:model="penyelenggaraNama" placeholder="Misal: Kemendikbudristek, BNSP, Pemprov" 
                               class="w-full text-xs rounded-xl border border-border bg-surface px-3 py-2">
                        @error('penyelenggaraNama') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-text-main block mb-1">Tingkat Prestasi *</label>
                        <select wire:model="tingkat" class="w-full text-xs rounded-xl border border-border bg-surface px-3 py-2">
                            <option value="sekolah">Sekolah</option>
                            <option value="kecamatan">Kecamatan</option>
                            <option value="kabupaten_kota">Kabupaten/Kota</option>
                            <option value="provinsi">Provinsi</option>
                            <option value="nasional">Nasional</option>
                            <option value="internasional">Internasional</option>
                        </select>
                    </div>
                </div>

                <!-- Peringkat / Capaian -->
                <div>
                    <label class="text-xs font-semibold text-text-main block mb-1">Peringkat / Capaian (Bisa Pilih atau Ketik Bebas) *</label>
                    <input type="text" wire:model="peringkat" placeholder="Misal: Juara 1, Peringkat Terbaik, Lulus Asesor" 
                           class="w-full text-xs rounded-xl border border-border bg-surface px-3 py-2 font-bold text-blue-700 dark:text-blue-300">
                    @error('peringkat') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                    <div class="flex flex-wrap gap-1 mt-1.5">
                        @foreach(['Juara 1', 'Juara 2', 'Juara 3', 'Harapan 1', 'Pendidik Teladan', 'Lulus Asesor BNSP', 'Karya Terbaik', 'Penerima Satyalancana'] as $pill)
                            <button type="button" wire:click="setPeringkatTemplate('{{ $pill }}')" 
                                    class="px-2 py-0.5 rounded text-[10px] bg-surface-hover hover:bg-blue-500 hover:text-white transition">
                                {{ $pill }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Waktu & Tempat -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-semibold text-text-main block mb-1">Tanggal Perolehan *</label>
                        <input type="date" wire:model="tanggalPerolehan" class="w-full text-xs rounded-xl border border-border bg-surface px-3 py-2">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-text-main block mb-1">Tempat / Lokasi</label>
                        <input type="text" wire:model="tempat" placeholder="Misal: Jakarta / Daring" class="w-full text-xs rounded-xl border border-border bg-surface px-3 py-2">
                    </div>
                </div>

                <!-- Upload Ganda -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-border">
                    <div>
                        <label class="text-xs font-semibold text-text-main block mb-1">Berkas Sertifikat / SK (PDF/Gambar max 5MB)</label>
                        <input type="file" wire:model="sertifikatFile" class="w-full text-xs text-text-secondary file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-500/10 file:text-blue-700">
                        @if($existingSertifikatPath)
                            <span class="text-[11px] text-teal-600 block mt-1">✓ Berkas sertifikat sebelumnya tersimpan</span>
                        @endif
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-text-main block mb-1">Foto Dokumentasi (JPG/PNG max 5MB)</label>
                        <input type="file" wire:model="fotoFile" accept="image/*" class="w-full text-xs text-text-secondary file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-500/10 file:text-blue-700">
                        @if($existingFotoPath)
                            <span class="text-[11px] text-blue-600 block mt-1">✓ Foto dokumentasi sebelumnya tersimpan</span>
                        @endif
                    </div>
                </div>

                <div>
                    <label class="text-xs font-semibold text-text-main block mb-1">Deskripsi Tambahan</label>
                    <textarea wire:model="deskripsi" rows="2" placeholder="Keterangan singkat..." class="w-full text-xs rounded-xl border border-border bg-surface px-3 py-2"></textarea>
                </div>

                <div class="pt-4 border-t border-border flex justify-end gap-3">
                    <button type="button" wire:click="$set('showFormModal', false)" class="btn btn-sm btn-outline border-border text-xs">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary text-xs flex items-center gap-1.5">
                        <svg wire:loading class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        Simpan Prestasi GTK
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- MODAL PREVIEW DETAIL PRESTASI GURU -->
    @if($showPreviewModal && $previewItem)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-surface rounded-2xl border border-border w-full max-w-xl shadow-xl overflow-hidden my-8">
            <div class="px-6 py-4 border-b border-border flex items-center justify-between bg-surface-hover/50">
                <h3 class="font-bold text-text-main text-base flex items-center gap-2">
                    <span>🎖️</span> Portofolio Prestasi Guru & GTK
                </h3>
                <button type="button" wire:click="$set('showPreviewModal', false)" class="text-text-muted hover:text-text-main text-xl font-bold">&times;</button>
            </div>

            <div class="p-6 space-y-4">
                <div class="p-4 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-bold text-blue-700 uppercase tracking-wider">Capaian Prestasi</span>
                        <h4 class="text-xl font-black text-blue-800 dark:text-blue-200">{{ $previewItem->peringkat }}</h4>
                        <p class="text-xs text-text-secondary mt-0.5">{{ $previewItem->kategori_label }} &bull; Tingkat {{ $previewItem->tingkat_label }}</p>
                    </div>
                    <span class="text-3xl">👨‍🏫</span>
                </div>

                <div class="space-y-3 text-xs">
                    <div>
                        <span class="text-text-muted block text-[11px]">Nama Prestasi / Sertifikasi:</span>
                        <strong class="text-text-main text-sm">{{ $previewItem->nama_prestasi }}</strong>
                    </div>

                    <div class="p-3 rounded-xl bg-surface-hover/60 border border-border/60">
                        <span class="text-text-muted block text-[11px] mb-1 font-semibold">Pendidik / Tenaga Kependidikan Penerima:</span>
                        <strong class="text-text-main text-sm block">{{ $previewItem->daftar_guru_nama }}</strong>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-text-muted block text-[11px]">Penyelenggara:</span>
                            <strong class="text-text-main">{{ $previewItem->penyelenggara_nama }}</strong>
                        </div>
                        <div>
                            <span class="text-text-muted block text-[11px]">Tanggal & Tahun Ajaran:</span>
                            <strong class="text-text-main">{{ $previewItem->tanggal_perolehan ? $previewItem->tanggal_perolehan->format('d F Y') : '-' }}</strong>
                            <span class="text-text-secondary block">TA {{ $previewItem->tahunAjaran?->nama ?? '-' }}</span>
                        </div>
                    </div>

                    @if($previewItem->deskripsi)
                        <div>
                            <span class="text-text-muted block text-[11px]">Deskripsi:</span>
                            <p class="text-text-main mt-0.5">{{ $previewItem->deskripsi }}</p>
                        </div>
                    @endif

                    <!-- Dokumen & Foto -->
                    <div class="pt-3 border-t border-border grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @if($previewItem->sertifikat_file_path)
                            <div class="p-3 rounded-xl bg-surface border border-border text-center">
                                <span class="text-[11px] font-bold text-text-main block mb-2">Berkas Sertifikat</span>
                                <a href="{{ Storage::url($previewItem->sertifikat_file_path) }}" target="_blank" class="btn btn-xs btn-outline border-border">
                                    📄 Buka Sertifikat
                                </a>
                            </div>
                        @endif

                        @if($previewItem->foto_file_path)
                            <div class="p-3 rounded-xl bg-surface border border-border text-center">
                                <span class="text-[11px] font-bold text-text-main block mb-2">Foto Dokumentasi</span>
                                <img src="{{ Storage::url($previewItem->foto_file_path) }}" alt="Foto Dokumentasi" class="h-28 w-auto mx-auto rounded-lg object-cover">
                            </div>
                        @endif
                    </div>
                </div>

                <div class="pt-4 border-t border-border flex justify-end">
                    <button type="button" wire:click="$set('showPreviewModal', false)" class="btn btn-sm btn-outline border-border text-xs">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
