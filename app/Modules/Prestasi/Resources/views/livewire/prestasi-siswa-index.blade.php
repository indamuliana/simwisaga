<div class="space-y-6">
    @include('prestasi::partials.sub-nav', ['active' => 'siswa'])

    @if (session()->has('message'))
        <div class="p-4 rounded-xl bg-teal-500/10 border border-teal-500/20 text-teal-700 dark:text-teal-300 text-sm flex items-center justify-between">
            <span class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('message') }}
            </span>
            <button type="button" @click="$el.parentElement.remove()" class="text-teal-700 hover:opacity-75">&times;</button>
        </div>
    @endif

    <!-- Toolbar & Filter -->
    <div class="bg-surface rounded-2xl border border-border p-4 space-y-4">
        <div class="flex flex-col md:flex-row justify-between items-stretch md:items-center gap-3">
            <div class="relative flex-1 max-w-md">
                <input type="text" wire:model.live.debounce.300ms="search" 
                       placeholder="Cari siswa, nama kejuaraan, penyelenggara..." 
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

                @can('prestasi.tambah')
                <button wire:click="openCreateModal" class="btn btn-sm btn-primary flex items-center gap-1.5 text-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    + Catat Prestasi Siswa
                </button>
                @endcan
            </div>
        </div>

        <!-- Filter Dropdowns -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2.5 pt-2 border-t border-border/60">
            <div>
                <label class="text-[10px] uppercase font-bold text-text-muted block mb-1">Tahun Ajaran</label>
                <select wire:model.live="filterTahunAjaranId" class="w-full text-xs rounded-xl border border-border bg-surface px-2.5 py-1.5">
                    <option value="">Semua TA</option>
                    @foreach($tahunAjarans as $ta)
                        <option value="{{ $ta->id }}">{{ $ta->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-[10px] uppercase font-bold text-text-muted block mb-1">Semester</label>
                <select wire:model.live="filterSemesterId" class="w-full text-xs rounded-xl border border-border bg-surface px-2.5 py-1.5">
                    <option value="">Semua Semester</option>
                    @foreach($semesters as $sem)
                        <option value="{{ $sem->id }}">{{ $sem->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-[10px] uppercase font-bold text-text-muted block mb-1">Rombel</label>
                <select wire:model.live="filterRombelId" class="w-full text-xs rounded-xl border border-border bg-surface px-2.5 py-1.5">
                    <option value="">Semua Rombel</option>
                    @foreach($rombels as $r)
                        <option value="{{ $r->id }}">{{ $r->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-[10px] uppercase font-bold text-text-muted block mb-1">Rayon</label>
                <select wire:model.live="filterRayonId" class="w-full text-xs rounded-xl border border-border bg-surface px-2.5 py-1.5">
                    <option value="">Semua Rayon</option>
                    @foreach($rayons as $ry)
                        <option value="{{ $ry->id }}">{{ $ry->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-[10px] uppercase font-bold text-text-muted block mb-1">Kategori</label>
                <select wire:model.live="filterKategori" class="w-full text-xs rounded-xl border border-border bg-surface px-2.5 py-1.5">
                    <option value="">Semua Kategori</option>
                    <option value="non_akademik">Non-Akademik (Lomba)</option>
                    <option value="akademik">Akademik</option>
                    <option value="sertifikasi">Sertifikasi Keahlian</option>
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
                        <th class="p-3">Siswa / Tim</th>
                        <th class="p-3">Prestasi & Penyelenggara</th>
                        <th class="p-3">Kategori & Tingkat</th>
                        <th class="p-3">Peringkat / Capaian</th>
                        <th class="p-3">Guru Pembimbing</th>
                        <th class="p-3">Periode Rapor</th>
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
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-purple-50 text-purple-700 dark:bg-purple-950 dark:text-purple-300">
                                            TIM ({{ $item->siswas->count() }})
                                        </span>
                                        <strong class="text-text-main">{{ $item->nama_tim ?: 'Tim Beregu' }}</strong>
                                    </div>
                                    <div class="text-[11px] text-text-secondary line-clamp-1 mt-0.5" title="{{ $item->daftar_siswa_nama }}">
                                        {{ $item->daftar_siswa_nama }}
                                    </div>
                                @else
                                    @php $s = $item->siswas->first(); @endphp
                                    <strong class="text-text-main block">{{ $s?->nama ?? '-' }}</strong>
                                    <span class="text-[11px] text-text-secondary block">
                                        {{ $s?->rombel?->nama ?? '-' }} &bull; {{ $s?->rayon?->nama ?? '-' }}
                                    </span>
                                @endif
                            </td>
                            <td class="p-3">
                                <strong class="text-text-main block text-xs">{{ $item->nama_prestasi }}</strong>
                                <span class="text-[11px] text-text-secondary block">
                                    {{ $item->penyelenggara_nama }} ({{ $item->penyelenggara_lingkup === 'dalam_sekolah' ? 'Internal' : 'Eksternal' }})
                                </span>
                            </td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-surface-hover text-text-main inline-block">
                                    {{ $item->kategori_label }}
                                </span>
                                <span class="text-[11px] text-text-muted block mt-0.5">
                                    Tingkat {{ $item->tingkat_label }}
                                </span>
                            </td>
                            <td class="p-3">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-teal-50 text-teal-700 dark:bg-teal-950 dark:text-teal-300 border border-teal-200 dark:border-teal-800">
                                    {{ $item->peringkat }}
                                </span>
                            </td>
                            <td class="p-3">
                                <span class="text-xs text-text-main block font-medium">
                                    {{ $item->pembimbing_nama }}
                                </span>
                            </td>
                            <td class="p-3">
                                <span class="text-xs font-semibold text-text-main block">
                                    {{ $item->periodeRapor?->nama ?? 'Umum Semester' }}
                                </span>
                                <span class="text-[11px] text-text-muted block">
                                    {{ $item->tanggal_perolehan ? $item->tanggal_perolehan->format('d/m/Y') : '-' }}
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

                                    @can('prestasi.edit')
                                    <button wire:click="openEditModal({{ $item->id }})" 
                                            class="p-1.5 rounded-lg text-primary hover:bg-primary-soft" 
                                            title="Edit Data">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    @endcan

                                    @can('prestasi.hapus')
                                    <button wire:click="delete({{ $item->id }})" 
                                            wire:confirm="Yakin ingin menghapus data prestasi ini?"
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
                            <td colspan="9" class="p-8 text-center text-text-muted text-xs">
                                Tidak ada data prestasi siswa ditemukan.
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

    <!-- MODAL FORM CREATE / EDIT PRESTASI SISWA -->
    @if($showFormModal)
    <div class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 overflow-hidden">
        <form wire:submit="save" class="bg-surface rounded-2xl border border-border w-full max-w-4xl shadow-2xl flex flex-col max-h-[88vh] overflow-hidden">
            <!-- Header (Fixed / Sticky) -->
            <div class="px-6 py-3.5 border-b border-border flex items-center justify-between bg-surface-hover/50 flex-none">
                <h3 class="font-bold text-text-main text-base flex items-center gap-2">
                    <span>🏆</span> {{ $prestasiId ? 'Edit Data Prestasi Siswa' : 'Catat Prestasi Siswa Baru' }}
                </h3>
                <button type="button" wire:click="$set('showFormModal', false)" 
                        class="w-8 h-8 rounded-lg flex items-center justify-center text-text-muted hover:text-text-main hover:bg-surface-hover text-lg font-bold transition">
                    &times;
                </button>
            </div>

            <!-- Body (Scrollable 2-Column Responsive Layout) -->
            <div class="p-5 sm:p-6 overflow-y-auto flex-1">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 text-xs">
                    
                    <!-- KOLOM KIRI: Informasi Event, Penyelenggara & Capaian -->
                    <div class="space-y-4">
                        <div class="font-bold text-[11px] uppercase tracking-wider text-text-muted flex items-center gap-1.5 pb-1 border-b border-border">
                            <span>📌</span> Informasi Kejuaraan / Event
                        </div>

                        <!-- Nama Prestasi & Bidang -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="sm:col-span-2">
                                <label class="text-xs font-semibold text-text-main block mb-1">Nama Prestasi / Event *</label>
                                <input type="text" wire:model="namaPrestasi" placeholder="Misal: LKS Web Technologies, O2SN Renang" 
                                       class="w-full text-xs rounded-xl border border-border bg-surface px-3 py-2">
                                @error('namaPrestasi') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                            </div>
                            <div class="sm:col-span-2">
                                <label class="text-xs font-semibold text-text-main block mb-1">Bidang Prestasi</label>
                                <input type="text" wire:model="bidang" placeholder="Misal: IT & Coding, Olahraga, Seni, Bahasa" 
                                       class="w-full text-xs rounded-xl border border-border bg-surface px-3 py-2">
                            </div>
                        </div>

                        <!-- Kategori & Lingkup Penyelenggara -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="text-xs font-semibold text-text-main block mb-1">Kategori Prestasi *</label>
                                <select wire:model="kategori" class="w-full text-xs rounded-xl border border-border bg-surface px-3 py-2">
                                    <option value="non_akademik">Non-Akademik (Kejuaraan)</option>
                                    <option value="akademik">Akademik</option>
                                    <option value="sertifikasi">Sertifikasi Keahlian</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-text-main block mb-1">Lingkup Penyelenggara *</label>
                                <select wire:model="penyelenggaraLingkup" class="w-full text-xs rounded-xl border border-border bg-surface px-3 py-2">
                                    <option value="luar_sekolah">Eksternal (Luar Sekolah)</option>
                                    <option value="dalam_sekolah">Internal (Dalam Sekolah)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Nama Penyelenggara -->
                        <div>
                            <label class="text-xs font-semibold text-text-main block mb-1">Nama Lembaga Penyelenggara *</label>
                            <input type="text" wire:model="penyelenggaraNama" placeholder="Misal: Kemendikbudristek, Telkom Univ, Disdik" 
                                   class="w-full text-xs rounded-xl border border-border bg-surface px-3 py-2">
                            @error('penyelenggaraNama') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                        </div>

                        <!-- Tingkat Prestasi -->
                        <div>
                            <label class="text-xs font-semibold text-text-main block mb-1">Tingkat Prestasi *</label>
                            <select wire:model="tingkat" class="w-full text-xs rounded-xl border border-border bg-surface px-3 py-2">
                                <option value="sekolah">Tingkat Sekolah</option>
                                <option value="kecamatan">Tingkat Kecamatan</option>
                                <option value="kabupaten_kota">Tingkat Kabupaten / Kota</option>
                                <option value="provinsi">Tingkat Provinsi</option>
                                <option value="nasional">Tingkat Nasional</option>
                                <option value="internasional">Tingkat Internasional</option>
                            </select>
                        </div>

                        <!-- Peringkat / Capaian & Template Pills -->
                        <div>
                            <label class="text-xs font-semibold text-text-main block mb-1">Peringkat / Capaian (Bisa Pilih atau Ketik Bebas) *</label>
                            <input type="text" wire:model="peringkat" placeholder="Misal: Juara 1, Best Speaker, Medali Emas" 
                                   class="w-full text-xs rounded-xl border border-border bg-surface px-3 py-2 font-bold text-teal-700 dark:text-teal-300">
                            @error('peringkat') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                            
                            <div class="flex flex-wrap gap-1 mt-1.5">
                                @foreach(['Juara 1', 'Juara 2', 'Juara 3', 'Harapan 1', 'Medali Emas', 'Medali Perak', 'Medali Perunggu', 'Finalis', 'Best Speaker', 'Lulus Sertifikasi'] as $pill)
                                    <button type="button" wire:click="setPeringkatTemplate('{{ $pill }}')" 
                                            class="px-2 py-0.5 rounded text-[10px] bg-surface-hover hover:bg-teal-500 hover:text-white transition">
                                        {{ $pill }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- Waktu & Tempat & Rapor -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-border">
                            <div>
                                <label class="text-xs font-semibold text-text-main block mb-1">Tanggal Perolehan *</label>
                                <input type="date" wire:model.live="tanggalPerolehan" 
                                       class="w-full text-xs rounded-xl border border-border bg-surface px-3 py-2">
                                @error('tanggalPerolehan') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-text-main block mb-1">Tempat / Lokasi</label>
                                <input type="text" wire:model="tempat" placeholder="Misal: Jakarta Convention Center" 
                                       class="w-full text-xs rounded-xl border border-border bg-surface px-3 py-2">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="text-xs font-semibold text-text-main block mb-1">Periode Rapor (Target Integrasi Rapor)</label>
                                <select wire:model="periodeRaporId" class="w-full text-xs rounded-xl border border-border bg-surface px-3 py-2">
                                    <option value="">-- Umum Satu Semester --</option>
                                    @foreach($formPeriodes as $fp)
                                        <option value="{{ $fp->id }}">{{ $fp->nama }} ({{ $fp->sebutan }})</option>
                                    @endforeach
                                </select>
                                <span class="text-[10px] text-text-muted mt-0.5 block">Otomatis terhubung ke catatan rapor siswa pada periode ini</span>
                            </div>
                        </div>
                    </div>

                    <!-- KOLOM KANAN: Penerima Prestasi, Pembimbing, Berkas Bukti & Catatan -->
                    <div class="space-y-4">
                        <div class="font-bold text-[11px] uppercase tracking-wider text-text-muted flex items-center gap-1.5 pb-1 border-b border-border">
                            <span>👥</span> Penerima Prestasi & Pendamping
                        </div>

                        <!-- Tipe Peserta & Pemilihan Siswa -->
                        <div class="bg-surface-hover/40 p-3.5 rounded-xl border border-border space-y-3">
                            <label class="text-xs font-bold text-text-main block">Tipe Kepesertaan Prestasi</label>
                            <div class="flex items-center gap-6 text-xs">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" wire:model.live="tipePeserta" value="individu" class="text-primary focus:ring-primary">
                                    <span>Individu (1 Siswa)</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" wire:model.live="tipePeserta" value="kelompok" class="text-primary focus:ring-primary">
                                    <span>Regu / Tim</span>
                                </label>
                            </div>

                            @if($tipePeserta === 'kelompok')
                                <div>
                                    <label class="text-[11px] font-semibold text-text-secondary block mb-1">Nama Tim / Kelompok (Opsional)</label>
                                    <input type="text" wire:model="namaTim" placeholder="Misal: Tim Robotik Wikrama, Regu LKS Web Tech" 
                                           class="w-full text-xs rounded-xl border border-border bg-surface px-3 py-2">
                                </div>
                            @endif

                            <!-- Autocomplete Search -->
                            <div class="space-y-2">
                                <label class="text-[11px] font-semibold text-text-secondary block">
                                    Pilih Siswa Penerima Prestasi:
                                </label>

                                <div class="relative">
                                    <input type="text" wire:model.live.debounce.250ms="siswaSearchQuery" 
                                           placeholder="Ketik minimal 2 huruf nama atau NISN siswa..." 
                                           class="w-full text-xs rounded-xl border border-border bg-surface px-3 py-2">
                                    
                                    @if(!empty($siswaSearchResults))
                                        <div class="absolute left-0 right-0 mt-1 bg-surface border border-border rounded-xl shadow-lg z-20 max-h-44 overflow-y-auto divide-y divide-border">
                                            @foreach($siswaSearchResults as $sr)
                                                <div wire:click="addSiswa({{ $sr->id }})" 
                                                     class="p-2.5 hover:bg-surface-hover cursor-pointer text-xs flex justify-between items-center">
                                                    <div>
                                                        <strong class="text-text-main">{{ $sr->nama }}</strong>
                                                        <span class="text-[10px] text-text-muted block">NISN: {{ $sr->nisn ?: '-' }} &bull; Rombel: {{ $sr->rombel?->nama ?? '-' }}</span>
                                                    </div>
                                                    <span class="text-[10px] font-bold text-primary">+ Tambahkan</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <!-- Chips Siswa Terpilih -->
                                <div class="space-y-1.5 pt-1 max-h-36 overflow-y-auto">
                                    @foreach($selectedSiswaIds as $sIdx => $sel)
                                        <div class="flex items-center justify-between p-2 rounded-lg bg-surface border border-border text-xs">
                                            <span class="font-medium text-text-main flex items-center gap-2">
                                                <span class="w-5 h-5 rounded-full bg-teal-500/10 text-teal-600 flex items-center justify-center font-bold text-[10px]">{{ $sIdx + 1 }}</span>
                                                {{ $sel['nama'] }}
                                            </span>
                                            <div class="flex items-center gap-2">
                                                @if($tipePeserta === 'kelompok')
                                                    <select wire:model="selectedSiswaIds.{{ $sIdx }}.peran" class="text-[11px] rounded-lg border border-border bg-surface px-2 py-1">
                                                        <option value="ketua">Ketua Tim</option>
                                                        <option value="anggota">Anggota</option>
                                                    </select>
                                                @endif
                                                <button type="button" wire:click="removeSiswa({{ $sIdx }})" class="text-rose-500 hover:text-rose-700 font-bold px-1.5 py-0.5">&times;</button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('selectedSiswaIds') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Guru Pembimbing -->
                        <div class="bg-surface-hover/40 p-3.5 rounded-xl border border-border space-y-2.5">
                            <label class="text-xs font-bold text-text-main block">Guru Pembimbing / Pendamping Siswa</label>
                            <div class="flex items-center gap-6 text-xs">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" wire:model.live="pembimbingType" value="pegawai" class="text-primary focus:ring-primary">
                                    <span>Master Data Pegawai</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" wire:model.live="pembimbingType" value="manual" class="text-primary focus:ring-primary">
                                    <span>Tulis Manual</span>
                                </label>
                            </div>

                            @if($pembimbingType === 'pegawai')
                                <select wire:model="pembimbingPegawaiId" class="w-full text-xs rounded-xl border border-border bg-surface px-3 py-2">
                                    <option value="">-- Pilih Guru / GTK Pembimbing --</option>
                                    @foreach($pegawais as $pg)
                                        <option value="{{ $pg->id }}">{{ $pg->nama_gelar }} ({{ $pg->nip ?: 'Non-NIP' }})</option>
                                    @endforeach
                                </select>
                            @else
                                <input type="text" wire:model="pembimbingNamaManual" placeholder="Tuliskan nama lengkap guru/pelatih luar..." 
                                       class="w-full text-xs rounded-xl border border-border bg-surface px-3 py-2">
                            @endif
                        </div>

                        <!-- Upload Ganda: Sertifikat & Foto -->
                        <div class="font-bold text-[11px] uppercase tracking-wider text-text-muted flex items-center gap-1.5 pt-2 border-t border-border">
                            <span>📎</span> Berkas Bukti & Catatan
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="text-xs font-semibold text-text-main block mb-1">Berkas Sertifikat / SK (PDF/Img max 5MB)</label>
                                <input type="file" wire:model="sertifikatFile" class="w-full text-xs text-text-secondary file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-teal-500/10 file:text-teal-700">
                                @error('sertifikatFile') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                                @if($existingSertifikatPath)
                                    <span class="text-[11px] text-teal-600 block mt-1">✓ Berkas tersimpan</span>
                                @endif
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-text-main block mb-1">Foto Dokumentasi (JPG/PNG max 5MB)</label>
                                <input type="file" wire:model="fotoFile" accept="image/*" class="w-full text-xs text-text-secondary file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-blue-500/10 file:text-blue-700">
                                @error('fotoFile') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                                @if($existingFotoPath)
                                    <span class="text-[11px] text-blue-600 block mt-1">✓ Foto tersimpan</span>
                                @endif
                            </div>
                        </div>

                        <div>
                            <label class="text-xs font-semibold text-text-main block mb-1">Catatan / Deskripsi Tambahan</label>
                            <textarea wire:model="deskripsi" rows="2" placeholder="Catatan singkat tentang kompetisi atau karya yang dilombakan..." 
                                      class="w-full text-xs rounded-xl border border-border bg-surface px-3 py-2"></textarea>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Footer (Fixed / Sticky at bottom) -->
            <div class="px-6 py-3.5 border-t border-border bg-surface-hover/30 flex items-center justify-between flex-none">
                <span class="text-[11px] text-text-muted hidden sm:inline">*) Field bertanda bintang wajib diisi</span>
                <div class="flex items-center gap-2.5 ml-auto">
                    <button type="button" wire:click="$set('showFormModal', false)" class="btn btn-sm btn-outline border-border text-xs px-4">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-sm btn-primary text-xs px-5 flex items-center gap-1.5 shadow-sm">
                        <svg wire:loading wire:target="save, sertifikatFile, fotoFile" class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span>Simpan Data Prestasi</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
    @endif

    <!-- MODAL PREVIEW DETAIL PRESTASI SISWA -->
    @if($showPreviewModal && $previewItem)
    <div class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 overflow-hidden">
        <div class="bg-surface rounded-2xl border border-border w-full max-w-2xl shadow-2xl flex flex-col max-h-[88vh] overflow-hidden">
            <!-- Header (Fixed / Sticky) -->
            <div class="px-6 py-3.5 border-b border-border flex items-center justify-between bg-surface-hover/50 flex-none">
                <h3 class="font-bold text-text-main text-base flex items-center gap-2">
                    <span>🎖️</span> Detail Portofolio Prestasi
                </h3>
                <button type="button" wire:click="$set('showPreviewModal', false)" 
                        class="w-8 h-8 rounded-lg flex items-center justify-center text-text-muted hover:text-text-main hover:bg-surface-hover text-lg font-bold transition">
                    &times;
                </button>
            </div>

            <!-- Body (Scrollable) -->
            <div class="p-6 space-y-4 overflow-y-auto flex-1 text-xs">
                <div class="p-4 rounded-xl bg-teal-500/10 border border-teal-500/20 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-bold text-teal-700 uppercase tracking-wider">Capaian / Peringkat</span>
                        <h4 class="text-2xl font-black text-teal-800 dark:text-teal-200">{{ $previewItem->peringkat }}</h4>
                        <p class="text-xs text-text-secondary mt-0.5">Tingkat {{ $previewItem->tingkat_label }} &bull; {{ $previewItem->kategori_label }}</p>
                    </div>
                    <span class="text-3xl">🏆</span>
                </div>

                <div class="space-y-3">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-text-muted block text-[11px]">Nama Prestasi / Event:</span>
                            <strong class="text-text-main text-sm">{{ $previewItem->nama_prestasi }}</strong>
                        </div>
                        <div>
                            <span class="text-text-muted block text-[11px]">Penyelenggara:</span>
                            <strong class="text-text-main">{{ $previewItem->penyelenggara_nama }}</strong>
                            <span class="text-[11px] text-text-secondary">({{ $previewItem->penyelenggara_lingkup === 'dalam_sekolah' ? 'Dalam Sekolah' : 'Luar Sekolah' }})</span>
                        </div>
                    </div>

                    <div class="p-3 rounded-xl bg-surface-hover/60 border border-border/60">
                        <span class="text-text-muted block text-[11px] mb-1 font-semibold">
                            {{ $previewItem->tipe_peserta === 'kelompok' ? 'Anggota Tim (' . ($previewItem->nama_tim ?: 'Beregu') . '):' : 'Siswa Penerima Prestasi:' }}
                        </span>
                        <div class="divide-y divide-border/60">
                            @foreach($previewItem->siswas as $ps)
                                <div class="py-1.5 flex justify-between items-center">
                                    <div>
                                        <strong class="text-text-main">{{ $ps->nama }}</strong>
                                        <span class="text-text-secondary text-[11px]"> &bull; {{ $ps->rombel?->nama ?? '-' }} (Rayon {{ $ps->rayon?->nama ?? '-' }})</span>
                                    </div>
                                    @if($previewItem->tipe_peserta === 'kelompok')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $ps->pivot->peran === 'ketua' ? 'bg-amber-500 text-white' : 'bg-surface text-text-secondary border border-border' }}">
                                            {{ ucfirst($ps->pivot->peran) }}
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-text-muted block text-[11px]">Guru Pembimbing / Pendamping:</span>
                            <strong class="text-text-main">{{ $previewItem->pembimbing_nama }}</strong>
                        </div>
                        <div>
                            <span class="text-text-muted block text-[11px]">Periode Rapor & Tanggal:</span>
                            <strong class="text-text-main">{{ $previewItem->periodeRapor?->nama ?? 'Semester Penuh' }}</strong>
                            <span class="text-text-secondary block">{{ $previewItem->tanggal_perolehan ? $previewItem->tanggal_perolehan->format('d F Y') : '-' }}</span>
                        </div>
                    </div>

                    @if($previewItem->deskripsi)
                        <div>
                            <span class="text-text-muted block text-[11px]">Deskripsi / Keterangan:</span>
                            <p class="text-text-main mt-0.5">{{ $previewItem->deskripsi }}</p>
                        </div>
                    @endif

                    <!-- Dokumen & Foto -->
                    <div class="pt-3 border-t border-border grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @if($previewItem->sertifikat_file_path)
                            <div class="p-3 rounded-xl bg-surface border border-border text-center">
                                <span class="text-[11px] font-bold text-text-main block mb-2">Berkas Sertifikat</span>
                                <a href="{{ Storage::url($previewItem->sertifikat_file_path) }}" target="_blank" class="btn btn-xs btn-outline border-border">
                                    📄 Buka Dokumen Sertifikat
                                </a>
                            </div>
                        @endif

                        @if($previewItem->foto_file_path)
                            <div class="p-3 rounded-xl bg-surface border border-border text-center">
                                <span class="text-[11px] font-bold text-text-main block mb-2">Foto Dokumentasi</span>
                                <img src="{{ Storage::url($previewItem->foto_file_path) }}" alt="Foto Dokumentasi" class="h-32 w-auto mx-auto rounded-lg object-cover">
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
