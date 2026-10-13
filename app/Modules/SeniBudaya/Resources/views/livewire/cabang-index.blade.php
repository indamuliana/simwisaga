<div class="space-y-6">
    <!-- Header Navigasi -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-border pb-4">
        <div>
            <h2 class="text-2xl font-bold text-text-main flex items-center gap-2">
                <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Master Cabang Seni Budaya
            </h2>
            <p class="text-sm text-text-secondary mt-1">
                Kelola daftar cabang (Seni Tari, Seni Suara, Seni Rupa, dll), penugasan guru, serta pembobotan nilai & presensi.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-1.5 bg-surface-hover p-1 rounded-xl border border-border">
            <a href="{{ route('seni-budaya.index') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-text-secondary hover:text-text-main hover:bg-surface">
                Dashboard
            </a>
            <a href="{{ route('seni-budaya.cabang') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-semibold bg-white dark:bg-gray-800 text-primary shadow-xs">
                Cabang Seni
            </a>
            <a href="{{ route('seni-budaya.anggota') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-text-secondary hover:text-text-main hover:bg-surface">
                Keanggotaan Siswa
            </a>
            <a href="{{ route('seni-budaya.tp') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-text-secondary hover:text-text-main hover:bg-surface">
                Tujuan Pembelajaran (TP)
            </a>
            <a href="{{ route('seni-budaya.rekap') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-text-secondary hover:text-text-main hover:bg-surface">
                Rekapitulasi Nilai
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

    <!-- Action Toolbar -->
    <div class="flex justify-between items-center">
        <p class="text-sm text-text-secondary">Total: <span class="font-bold text-text-main">{{ $cabangs->count() }} cabang</span> terdaftar</p>
        <button wire:click="openCreateModal" class="btn-primary text-sm flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Cabang Seni
        </button>
    </div>

    <!-- Tabel Daftar Cabang -->
    <div class="card overflow-hidden border border-border">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-surface-hover text-text-secondary text-xs uppercase tracking-wider border-b border-border">
                    <tr>
                        <th class="px-4 py-3 text-center w-12">No</th>
                        <th class="px-4 py-3">Kode</th>
                        <th class="px-4 py-3">Nama Cabang</th>
                        <th class="px-4 py-3">Guru / Pengampu</th>
                        <th class="px-4 py-3 text-center">KKM</th>
                        <th class="px-4 py-3 text-center">Bobot (Presensi : Asesmen)</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border text-text-main">
                    @forelse($cabangs as $index => $c)
                        <tr class="hover:bg-surface-hover transition-colors">
                            <td class="px-4 py-3 text-center text-text-muted">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 font-semibold text-primary">{{ $c->kode }}</td>
                            <td class="px-4 py-3 font-medium">{{ $c->nama }}</td>
                            <td class="px-4 py-3 text-text-secondary">{{ $c->guru_display_name }}</td>
                            <td class="px-4 py-3 text-center font-bold">{{ $c->kkm }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded text-xs bg-surface-hover border border-border">
                                    {{ $c->bobot_presensi }}% : {{ $c->bobot_penilaian }}%
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button type="button"
                                        wire:click="toggleActive({{ $c->id }})"
                                        class="px-2.5 py-1 rounded-full text-xs font-semibold cursor-pointer transition-colors {{ $c->is_active ? 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300' : 'bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300' }}">
                                    {{ $c->is_active ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button wire:click="openEditModal({{ $c->id }})" class="p-1.5 text-text-secondary hover:text-primary rounded-md hover:bg-surface" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button wire:click="delete({{ $c->id }})" wire:confirm="Yakin ingin menghapus cabang '{{ $c->nama }}'? Data keanggotaan dan nilai yang terkait akan terhapus." class="p-1.5 text-text-secondary hover:text-rose-600 rounded-md hover:bg-surface" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-text-muted">
                                Belum ada cabang Seni Budaya yang ditambahkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form Tambah / Edit -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs" x-cloak>
            <div class="card max-w-lg w-full p-6 space-y-4 shadow-xl border border-border">
                <div class="flex justify-between items-center border-b border-border pb-3">
                    <h3 class="text-lg font-bold text-text-main">
                        {{ $editingId ? 'Edit Cabang Seni Budaya' : 'Tambah Cabang Seni Budaya Baru' }}
                    </h3>
                    <button type="button" wire:click="$set('showModal', false)" class="text-text-muted hover:text-text-main text-xl leading-none">&times;</button>
                </div>

                <form wire:submit="save" class="space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Kode Cabang <span class="text-rose-500">*</span></label>
                            <input type="text" wire:model="kode" placeholder="misal: SB-TARI" class="input text-sm w-full">
                            @error('kode') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">KKM Default <span class="text-rose-500">*</span></label>
                            <input type="number" wire:model="kkm" min="0" max="100" class="input text-sm w-full">
                            @error('kkm') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Nama Cabang Seni Budaya <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model="nama" placeholder="misal: Seni Tari, Seni Suara, Seni Lukis" class="input text-sm w-full">
                        @error('nama') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Guru Pengampu Utama</label>
                        <select wire:model="guru_id" class="input text-sm w-full">
                            <option value="">-- Pilih Guru Pegawai (Opsional) --</option>
                            @foreach($pegawais as $p)
                                <option value="{{ $p->id }}">{{ $p->nama_lengkap }} ({{ $p->nip ?? '-' }})</option>
                            @endforeach
                        </select>
                        @error('guru_id') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Bobot Presensi (%) <span class="text-rose-500">*</span></label>
                            <input type="number" wire:model="bobot_presensi" min="0" max="100" class="input text-sm w-full">
                            @error('bobot_presensi') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Bobot Asesmen (%) <span class="text-rose-500">*</span></label>
                            <input type="number" wire:model="bobot_penilaian" min="0" max="100" class="input text-sm w-full">
                            @error('bobot_penilaian') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Hari Latihan</label>
                            <input type="text" wire:model="hari_latihan" placeholder="misal: Rabu / Jumat" class="input text-sm w-full">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Tempat / Ruang</label>
                            <input type="text" wire:model="tempat" placeholder="misal: Ruang Tari / Lab Musik" class="input text-sm w-full">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Deskripsi / Keterangan</label>
                        <textarea wire:model="deskripsi" rows="2" placeholder="Catatan atau deskripsi materi cabang..." class="input text-sm w-full"></textarea>
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <input type="checkbox" id="is_active_cb" wire:model="is_active" class="rounded border-border text-primary focus:ring-primary">
                        <label for="is_active_cb" class="text-sm font-medium text-text-main cursor-pointer">Status Aktif</label>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-border">
                        <button type="button" wire:click="$set('showModal', false)" class="btn-secondary text-sm">Batal</button>
                        <button type="submit" class="btn-primary text-sm flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Simpan Cabang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
