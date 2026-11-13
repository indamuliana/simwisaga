<div class="space-y-6">
    <!-- Header Navigasi Sub-Modul Kepesantrenan -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-border pb-4">
        <div>
            <h2 class="text-2xl font-bold text-text-main flex items-center gap-2">
                <svg class="w-7 h-7 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                Kamar & Asrama Santri
            </h2>
            <p class="text-sm text-text-secondary mt-1">
                Pengelolaan asrama, musrif pembimbing, dan plotting kamar santri Program Unggulan beserta penunjukan Mudabbir.
            </p>
        </div>

        <!-- Tab Navigasi Submodul -->
        <div class="flex items-center gap-1.5 bg-surface-hover p-1.5 rounded-xl border border-border overflow-x-auto max-w-full">
            <a href="{{ route('pesantren.kamar') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-semibold bg-white dark:bg-gray-800 text-emerald-600 dark:text-emerald-400 shadow-xs whitespace-nowrap">
                Kamar & Asrama
            </a>
            <a href="{{ route('pesantren.hafalan') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-text-secondary hover:text-text-main hover:bg-surface whitespace-nowrap">
                Input Hafalan
            </a>
            <a href="{{ route('pesantren.rekap') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-text-secondary hover:text-text-main hover:bg-surface whitespace-nowrap">
                Rekap & Rank MLBB
            </a>
            <a href="{{ route('pesantren.halaqoh') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-text-secondary hover:text-text-main hover:bg-surface whitespace-nowrap">
                Halaqoh & Presensi
            </a>
        </div>
    </div>

    <!-- Alert Notifikasi -->
    @if(session()->has('message'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-xl text-emerald-800 dark:text-emerald-200 text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('message') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
        </div>
    @endif
    @if(session()->has('error'))
        <div class="p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-xl text-rose-800 dark:text-rose-200 text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">&times;</button>
        </div>
    @endif

    <!-- Toolbar Filter & Tambah Kamar -->
    <div class="flex flex-col md:flex-row justify-between items-stretch md:items-center gap-3 bg-surface p-4 rounded-xl border border-border">
        <div class="flex flex-wrap items-center gap-3">
            <div class="relative w-full sm:w-64">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari kamar / musrif..."
                       class="input pl-9 w-full text-sm">
                <svg class="w-4 h-4 text-text-secondary absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <select wire:model.live="filterGender" class="select text-sm w-36">
                <option value="all">Semua Gender</option>
                <option value="L">Ikhwan (Putra)</option>
                <option value="P">Akhwat (Putri)</option>
            </select>
            <select wire:model.live="filterTahunAjaranId" class="select text-sm w-44">
                @foreach($tahunAjarans as $ta)
                    <option value="{{ $ta->id }}">{{ $ta->nama }} {{ $ta->is_active ? '(Aktif)' : '' }}</option>
                @endforeach
            </select>
        </div>

        @can('pesantren.kamar.kelola')
            <button wire:click="createKamar" class="btn btn-primary text-sm flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Kamar
            </button>
        @endcan
    </div>

    <!-- Grid Kartu Kamar -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($kamars as $kamar)
            <div class="bg-surface rounded-2xl border border-border shadow-xs hover:border-emerald-500/50 transition-all flex flex-col justify-between overflow-hidden">
                <div class="p-5 space-y-4">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold uppercase tracking-wider {{ $kamar->jenis_kelamin === 'L' ? 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300' : 'bg-pink-100 text-pink-800 dark:bg-pink-950 dark:text-pink-300' }}">
                                    {{ $kamar->jenis_kelamin === 'L' ? 'Putra (Ikhwan)' : 'Putri (Akhwat)' }}
                                </span>
                                @if($kamar->gedung)
                                    <span class="text-xs text-text-muted">{{ $kamar->gedung }}</span>
                                @endif
                            </div>
                            <h3 class="text-lg font-bold text-text-main mt-1">{{ $kamar->nama_kamar }}</h3>
                        </div>

                        <!-- Badge Kapasitas -->
                        <div class="text-right">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $kamar->kamar_siswas_count >= $kamar->kapasitas ? 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' }}">
                                {{ $kamar->kamar_siswas_count }} / {{ $kamar->kapasitas }} Santri
                            </span>
                        </div>
                    </div>

                    <!-- Musrif & Keterangan -->
                    <div class="text-xs space-y-1.5 pt-2 border-t border-border">
                        <div class="flex items-center justify-between text-text-secondary">
                            <span class="text-text-muted">Musrif / PJ:</span>
                            <span class="font-semibold text-text-main truncate max-w-[180px]">{{ $kamar->musrif?->nama_lengkap ?? 'Belum ada' }}</span>
                        </div>
                        @if($kamar->keterangan)
                            <p class="text-text-muted italic text-[11px] line-clamp-1">{{ $kamar->keterangan }}</p>
                        @endif
                    </div>
                </div>

                <!-- Footer Aksi -->
                <div class="px-5 py-3 bg-surface-hover/50 border-t border-border flex items-center justify-between gap-2">
                    <button wire:click="openPlotting({{ $kamar->id }})" class="btn btn-secondary text-xs flex items-center gap-1.5 py-1.5 px-3">
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span>Kelola Santri ({{ $kamar->kamar_siswas_count }})</span>
                    </button>

                    @can('pesantren.kamar.kelola')
                        <div class="flex items-center gap-1">
                            <button wire:click="editKamar({{ $kamar->id }})" class="p-1.5 text-text-muted hover:text-primary transition-colors rounded" title="Edit Kamar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <button wire:click="deleteKamar({{ $kamar->id }})" wire:confirm="Hapus kamar ini beserta daftar santri di dalamnya?" class="p-1.5 text-text-muted hover:text-rose-600 transition-colors rounded" title="Hapus Kamar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    @endcan
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center text-text-muted bg-surface rounded-2xl border border-dashed border-border">
                <svg class="w-12 h-12 mx-auto text-text-muted/50 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <p class="font-medium">Belum ada data kamar asrama.</p>
                <p class="text-xs mt-1">Silakan klik "Tambah Kamar" untuk memulai.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $kamars->links() }}
    </div>

    <!-- Modal Form Kamar -->
    @if($showKamarModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-surface rounded-2xl border border-border shadow-xl w-full max-w-lg overflow-hidden animate-in fade-in zoom-in-95 duration-200">
                <div class="px-6 py-4 border-b border-border flex items-center justify-between">
                    <h3 class="text-lg font-bold text-text-main">
                        {{ $editingKamarId ? 'Edit Data Kamar' : 'Tambah Kamar Baru' }}
                    </h3>
                    <button wire:click="$set('showKamarModal', false)" class="text-text-muted hover:text-text-main text-xl">&times;</button>
                </div>
                <form wire:submit="saveKamar" class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Nama Kamar *</label>
                        <input type="text" wire:model="nama_kamar" placeholder="Contoh: Al-Fatih 01" class="input w-full">
                        @error('nama_kamar') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Gedung / Asrama</label>
                            <input type="text" wire:model="gedung" placeholder="Contoh: Asrama Putra Lt. 2" class="input w-full">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Kategori Gender *</label>
                            <select wire:model="jenis_kelamin" class="select w-full">
                                <option value="L">Ikhwan (Putra)</option>
                                <option value="P">Akhwat (Putri)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Kapasitas (Santri) *</label>
                            <input type="number" wire:model="kapasitas" min="1" max="50" class="input w-full">
                            @error('kapasitas') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Musrif / PJ (Guru / Non-Guru)</label>
                            <select wire:model="musrif_id" class="select w-full">
                                <option value="">-- Pilih Musrif / PJ --</option>
                                @foreach($musrifList as $pegawai)
                                    <option value="{{ $pegawai->id }}">{{ $pegawai->nama_lengkap }} ({{ ucfirst($pegawai->jenis_gtk) }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Keterangan / Fasilitas</label>
                        <textarea wire:model="keterangan" rows="2" placeholder="Catatan fasilitas kamar..." class="input w-full"></textarea>
                    </div>

                    <div class="pt-4 border-t border-border flex justify-end gap-2">
                        <button type="button" wire:click="$set('showKamarModal', false)" class="btn btn-secondary text-sm">Batal</button>
                        <button type="submit" class="btn btn-primary text-sm">Simpan Kamar</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal Kelola / Plotting Santri Kamar -->
    @if($showPlottingModal && $activeKamar)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-surface rounded-2xl border border-border shadow-2xl w-full max-w-4xl flex flex-col overflow-hidden animate-in fade-in zoom-in-95 duration-200" style="max-height: 90vh;">
                <div class="px-6 py-4 border-b border-border flex items-center justify-between bg-surface-hover/50 shrink-0">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-lg font-bold text-text-main">Santri Kamar: {{ $activeKamar->nama_kamar }}</h3>
                            <span class="px-2 py-0.5 rounded text-xs font-bold {{ $activeKamar->jenis_kelamin === 'L' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800' }}">
                                {{ $activeKamar->jenis_kelamin === 'L' ? 'Putra' : 'Putri' }}
                            </span>
                        </div>
                        <p class="text-xs text-text-secondary mt-0.5">
                            Kapasitas: {{ $activeKamar->siswas->count() }} / {{ $activeKamar->kapasitas }} Santri | Musrif: {{ $activeKamar->musrif?->nama_lengkap ?? '-' }}
                        </p>
                    </div>
                    <button wire:click="$set('showPlottingModal', false)" class="text-text-muted hover:text-text-main text-2xl">&times;</button>
                </div>

                <div class="p-6 space-y-6 overflow-y-auto flex-1">
                    <!-- Form Tambah Santri ke Kamar (Hanya Santri Unggulan) -->
                    @can('pesantren.kamar.kelola')
                        <div class="p-4 bg-surface-hover rounded-xl border border-border space-y-3">
                            <div class="flex items-center justify-between">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-text-secondary">Tambah Santri Unggulan ke Kamar</h4>
                                <span class="text-[11px] text-text-muted">Kapasitas: {{ $activeKamar->siswas->count() }} / {{ $activeKamar->kapasitas }} Santri</span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                                <div class="col-span-1 md:col-span-3">
                                    <label class="block text-[11px] font-semibold text-text-muted mb-1">Cari / Filter Nama</label>
                                    <input type="text" wire:model.live.debounce.300ms="searchSiswa" placeholder="Ketik nama / NISN..." class="input w-full text-xs">
                                </div>
                                <div class="col-span-1 md:col-span-4">
                                    <label class="block text-[11px] font-semibold text-text-muted mb-1">Pilih Santri Unggulan ({{ $availableSiswas->count() }})</label>
                                    <select wire:model="selectedSiswaId" class="select w-full text-xs">
                                        <option value="">-- Pilih dari {{ $availableSiswas->count() }} santri --</option>
                                        @foreach($availableSiswas as $s)
                                            <option value="{{ $s->id }}">{{ $s->nama }} ({{ $s->rombel?->nama ?? '-' }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-span-1 md:col-span-3">
                                    <label class="block text-[11px] font-semibold text-text-muted mb-1">Peran di Kamar</label>
                                    <select wire:model="selectedPeran" class="select w-full text-xs">
                                        <option value="anggota">Anggota Biasa</option>
                                        <option value="mudabbir">MUDABBIR (Ketua/PJ)</option>
                                    </select>
                                </div>
                                <div class="col-span-1 md:col-span-2">
                                    <button wire:click="addSiswaToKamar" 
                                            class="btn btn-primary text-xs w-full py-2 flex items-center justify-center gap-1.5" 
                                            {{ $activeKamar->siswas->count() >= $activeKamar->kapasitas ? 'disabled' : '' }}>
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        <span class="truncate">Tambahkan</span>
                                    </button>
                                </div>
                            </div>
                            @if($activeKamar->siswas->count() >= $activeKamar->kapasitas)
                                <p class="text-xs text-rose-500 font-medium">Kapasitas kamar sudah penuh ({{ $activeKamar->kapasitas }} santri).</p>
                            @endif
                        </div>
                    @endcan

                    <!-- Daftar Santri di Kamar Ini -->
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-text-secondary mb-3">Daftar Penghuni Kamar Aktif</h4>
                        <div class="border border-border rounded-xl overflow-x-auto">
                            <table class="w-full text-xs text-left min-w-[500px] md:min-w-full">
                                <thead class="bg-surface-hover/80 text-text-secondary border-b border-border">
                                    <tr>
                                        <th class="p-3">Santri</th>
                                        <th class="p-3">Rombel / Kelas</th>
                                        <th class="p-3">Peran Kamar</th>
                                        <th class="p-3 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-border">
                                    @forelse($activeKamar->siswas as $siswa)
                                        @php
                                            $isMudabbir = $siswa->pivot->peran === 'mudabbir';
                                        @endphp
                                        <tr class="hover:bg-surface-hover/50 {{ $isMudabbir ? 'bg-amber-50/50 dark:bg-amber-950/20' : '' }}">
                                            <td class="p-3 font-semibold text-text-main">
                                                <div class="flex items-center gap-2">
                                                    @if($isMudabbir)
                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-black bg-amber-500 text-white shadow-xs" title="Mudabbir Kamar">
                                                            ★ MUDABBIR
                                                        </span>
                                                    @endif
                                                    <span>{{ $siswa->nama }}</span>
                                                </div>
                                                <div class="text-[10px] text-text-muted">NISN: {{ $siswa->nisn ?? '-' }}</div>
                                            </td>
                                            <td class="p-3 text-text-secondary">
                                                {{ $siswa->rombel?->nama ?? '-' }}
                                            </td>
                                            <td class="p-3">
                                                <button wire:click="togglePeran({{ $siswa->pivot->id ?? 'null' }}, {{ $siswa->id }})" class="cursor-pointer text-[11px] font-medium px-2 py-0.5 rounded border {{ $isMudabbir ? 'border-amber-400 bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' : 'border-border text-text-muted hover:text-text-main' }}" title="Klik untuk ubah peran">
                                                    {{ $isMudabbir ? 'Mudabbir' : 'Anggota Biasa' }}
                                                </button>
                                            </td>
                                            <td class="p-3 text-right">
                                                @can('pesantren.kamar.kelola')
                                                    <button wire:click="removeSiswaFromKamar({{ $siswa->pivot->id ?? 'null' }}, {{ $siswa->id }})" wire:confirm="Keluarkan santri {{ $siswa->nama }} dari kamar ini?" class="text-rose-500 hover:text-rose-700 font-semibold p-1" title="Keluarkan dari kamar">
                                                        Keluarkan
                                                    </button>
                                                @endcan
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="p-6 text-center text-text-muted">
                                                Kamar ini belum memiliki santri. Tambahkan santri di atas.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-3.5 bg-surface-hover/50 border-t border-border flex justify-end shrink-0">
                    <button wire:click="$set('showPlottingModal', false)" class="btn btn-secondary text-sm">Tutup</button>
                </div>
            </div>
        </div>
    @endif
</div>
