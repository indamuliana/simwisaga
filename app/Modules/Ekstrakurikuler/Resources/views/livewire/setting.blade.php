<div class="space-y-6">
    <!-- Header Navigasi Sub-Modul -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-border pb-4">
        <div>
            <h2 class="text-2xl font-bold text-text-main flex items-center gap-2">
                <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Pengaturan UPD (Unit Pengembangan Diri)
            </h2>
            <p class="text-sm text-text-secondary mt-1">
                Sub-modul administrator untuk konfigurasi master UPD, penunjukan instruktur, jadwal patokan latihan, bobot rapor, dan batas nilai predikat.
            </p>
        </div>
        <!-- Tab Sub-Modul -->
        <div class="flex items-center gap-2 bg-surface-hover p-1 rounded-xl border border-border">
            <a href="{{ route('ekskul.index') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-text-secondary hover:text-text-main hover:bg-surface">
                Kegiatan UPD
            </a>
            <a href="{{ route('ekskul.anggota') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-text-secondary hover:text-text-main hover:bg-surface">
                Keanggotaan Siswa
            </a>
            <a href="{{ route('ekskul.rekap') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-text-secondary hover:text-text-main hover:bg-surface">
                Rekapitulasi UPD
            </a>
            <a href="{{ route('ekskul.setting') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-semibold bg-white dark:bg-gray-800 text-primary shadow-xs">
                Pengaturan UPD
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

    <!-- Toolbar: Search, Filter, Tombol Tambah -->
    <div class="flex flex-col sm:flex-row justify-between items-center gap-3 bg-surface p-3.5 rounded-xl border border-border">
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <div class="relative w-full sm:w-64">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama ekskul..."
                       class="input pl-9 w-full text-sm">
                <svg class="w-4 h-4 text-text-secondary absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <select wire:model.live="filterKategori" class="select text-sm w-40">
                <option value="all">Semua Kategori</option>
                <option value="wajib">Wajib Saja</option>
                <option value="pilihan">Pilihan Saja</option>
            </select>
        </div>

        <button wire:click="create" class="btn btn-primary text-sm flex items-center gap-2 w-full sm:w-auto justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Tambah Ekskul Baru</span>
        </button>
    </div>

    <!-- Tabel Pengaturan Ekskul -->
    <div class="card overflow-x-auto border border-border p-0">
        <table class="w-full text-left text-sm">
            <thead class="bg-surface-hover text-text-secondary text-xs uppercase tracking-wider border-b border-border">
                <tr>
                    <th class="p-3.5 text-center w-12">No</th>
                    <th class="p-3.5">Nama Ekskul</th>
                    <th class="p-3.5">Kategori</th>
                    <th class="p-3.5">Instruktur / Pembina</th>
                    <th class="p-3.5">Jadwal Patokan</th>
                    <th class="p-3.5 text-center">Bobot Rapor</th>
                    <th class="p-3.5 text-center">Ambang Batas Predikat</th>
                    <th class="p-3.5 text-center">Status</th>
                    <th class="p-3.5 text-center w-28">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border text-xs">
                @forelse($ekskuls as $idx => $e)
                    <tr class="hover:bg-surface-hover/50 transition {{ !$e->is_active ? 'opacity-60 bg-gray-50/50 dark:bg-gray-900/30' : '' }}">
                        <td class="p-3.5 text-center text-text-secondary">{{ $idx + 1 }}</td>
                        <td class="p-3.5">
                            <span class="font-bold text-text-main text-sm">{{ $e->nama }}</span>
                            @if($e->tempat)
                                <div class="text-[11px] text-text-secondary">Lokasi: {{ $e->tempat }}</div>
                            @endif
                        </td>
                        <td class="p-3.5">
                            @if($e->is_wajib)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300">
                                    Wajib (Tingkat X)
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300">
                                    Pilihan
                                </span>
                            @endif
                        </td>
                        <td class="p-3.5 font-medium text-text-main">
                            {{ $e->instruktur_display_name }}
                        </td>
                        <td class="p-3.5 text-text-secondary">
                            <div>{{ $e->hari_latihan ?? 'Jumat' }}</div>
                            <div class="text-[11px] font-mono">{{ $e->jam_mulai ? substr($e->jam_mulai,0,5) : '15:30' }} - {{ $e->jam_selesai ? substr($e->jam_selesai,0,5) : '17:00' }}</div>
                        </td>
                        <td class="p-3.5 text-center">
                            <span class="font-semibold text-primary">Presensi {{ $e->bobot_presensi }}%</span>
                            <div class="text-text-secondary text-[11px]">+ Asesmen {{ $e->bobot_penilaian }}%</div>
                        </td>
                        <td class="p-3.5 text-center font-mono text-[11px]">
                            <span class="text-emerald-600 font-bold">A&ge;{{ $e->threshold_a }}</span> &bull;
                            <span class="text-blue-600 font-bold">B&ge;{{ $e->threshold_b }}</span> &bull;
                            <span class="text-amber-600 font-bold">C&ge;{{ $e->threshold_c }}</span>
                        </td>
                        <td class="p-3.5 text-center">
                            <button wire:click="toggleStatus({{ $e->id }})" title="Klik untuk ubah status aktif/non-aktif"
                                    class="text-xs px-2.5 py-1 rounded-full font-medium transition {{ $e->is_active ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                {{ $e->is_active ? 'Aktif' : 'Non-Aktif' }}
                            </button>
                        </td>
                        <td class="p-3.5 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <button wire:click="edit({{ $e->id }})" class="btn btn-secondary text-xs py-1 px-2.5 font-medium text-primary">
                                    Edit
                                </button>
                                <button wire:click="delete({{ $e->id }})"
                                        onclick="confirm('Hapus ekstrakurikuler ini? Seluruh data anggota dan nilai akan ikut terhapus.') || event.stopImmediatePropagation()"
                                        class="btn btn-danger text-xs py-1 px-2">
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="p-10 text-center text-text-secondary">
                            Belum ada data ekstrakurikuler. Silakan klik "Tambah Ekskul Baru".
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Form Tambah / Edit Ekskul -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
            <div class="card max-w-2xl w-full p-6 space-y-5 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-border pb-3">
                    <h3 class="text-lg font-bold text-text-main">
                        {{ $editingId ? 'Edit Pengaturan Ekstrakurikuler' : 'Tambah Ekstrakurikuler Baru' }}
                    </h3>
                    <button wire:click="$set('showModal', false)" class="text-text-secondary hover:text-text-main text-lg">&times;</button>
                </div>

                <form wire:submit.prevent="save" class="space-y-4">
                    <!-- Nama Ekskul & Kategori -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold mb-1 text-text-main">Nama Ekstrakurikuler *</label>
                            <input type="text" wire:model="nama" class="input w-full text-sm" placeholder="Contoh: Pramuka, Basket, PMR...">
                            @error('nama') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold mb-1 text-text-main">Kategori Ekskul</label>
                            <select wire:model="is_wajib" class="select w-full text-sm">
                                <option value="0">Pilihan (Bakat & Minat)</option>
                                <option value="1">Wajib (Seluruh Tingkat X - Pramuka)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Penunjukan Instruktur -->
                    <div class="border border-border p-3.5 rounded-xl space-y-3 bg-surface-hover/30">
                        <label class="block text-xs font-bold text-text-main uppercase tracking-wider">Penunjukan Instruktur / Pembina</label>
                        <p class="text-xs text-text-secondary">Pilih instruktur dari guru/pegawai internal, akun peran 'Instruktur Ekskul', atau ketik nama pelatih luar.</p>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium mb-1">Dari Guru / Pegawai Internal</label>
                                <select wire:model="pembina_id" class="select w-full text-xs">
                                    <option value="">-- Pilih Guru Internal --</option>
                                    @foreach($pegawais as $peg)
                                        <option value="{{ $peg->id }}">{{ $peg->nama_lengkap }} ({{ $peg->jenis_gtk }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium mb-1">Dari Akun Instruktur Eksternal</label>
                                <select wire:model="instruktur_user_id" class="select w-full text-xs">
                                    <option value="">-- Pilih Akun Instruktur --</option>
                                    @foreach($instrukturUsers as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->username }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium mb-1">Nama Instruktur Manual (Jika belum punya akun)</label>
                            <input type="text" wire:model="instruktur_nama" class="input w-full text-xs" placeholder="Nama Pelatih / Instruktur Luar">
                        </div>
                    </div>

                    <!-- Jadwal Patokan & Lokasi -->
                    <div class="border border-border p-3.5 rounded-xl space-y-3 bg-surface-hover/30">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold text-text-main uppercase tracking-wider">Jadwal Patokan Latihan</label>
                            <span class="text-[11px] text-text-secondary italic">Presensi mingguan tetap fleksibel di lapangan</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                            <div>
                                <label class="block text-xs font-medium mb-1">Hari Patokan</label>
                                <select wire:model="hari_latihan" class="select w-full text-xs">
                                    <option value="Senin">Senin</option>
                                    <option value="Selasa">Selasa</option>
                                    <option value="Rabu">Rabu</option>
                                    <option value="Kamis">Kamis</option>
                                    <option value="Jumat">Jumat</option>
                                    <option value="Sabtu">Sabtu</option>
                                    <option value="Minggu">Minggu</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium mb-1">Jam Mulai</label>
                                <input type="time" wire:model="jam_mulai" class="input w-full text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-medium mb-1">Jam Selesai</label>
                                <input type="time" wire:model="jam_selesai" class="input w-full text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-medium mb-1">Lokasi Latihan</label>
                                <input type="text" wire:model="tempat" class="input w-full text-xs" placeholder="Lapangan / Ruang Praktik">
                            </div>
                        </div>
                    </div>

                    <!-- Formula Nilai Akhir & Rentang Predikat -->
                    <div class="border border-border p-3.5 rounded-xl space-y-3 bg-surface-hover/30">
                        <label class="block text-xs font-bold text-text-main uppercase tracking-wider">Formula Pembobotan & Rentang Predikat Rapor</label>
                        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                            <div>
                                <label class="block text-xs font-medium mb-1 text-emerald-600">Bobot Presensi (%)</label>
                                <input type="number" min="0" max="100" wire:model="bobot_presensi" class="input w-full text-xs text-center font-bold">
                            </div>
                            <div>
                                <label class="block text-xs font-medium mb-1 text-blue-600">Bobot Asesmen (%)</label>
                                <input type="number" min="0" max="100" wire:model="bobot_penilaian" class="input w-full text-xs text-center font-bold">
                            </div>
                            <div>
                                <label class="block text-xs font-medium mb-1">Batas A (&ge;)</label>
                                <input type="number" min="1" max="100" wire:model="threshold_a" class="input w-full text-xs text-center">
                            </div>
                            <div>
                                <label class="block text-xs font-medium mb-1">Batas B (&ge;)</label>
                                <input type="number" min="1" max="100" wire:model="threshold_b" class="input w-full text-xs text-center">
                            </div>
                            <div>
                                <label class="block text-xs font-medium mb-1">Batas C (&ge;)</label>
                                <input type="number" min="1" max="100" wire:model="threshold_c" class="input w-full text-xs text-center">
                            </div>
                        </div>
                    </div>

                    <!-- Deskripsi -->
                    <div>
                        <label class="block text-xs font-semibold mb-1 text-text-main">Deskripsi Ekskul</label>
                        <textarea wire:model="deskripsi" rows="2" class="input w-full text-xs" placeholder="Penjelasan singkat fokus pembinaan ekstrakurikuler ini..."></textarea>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-border">
                        <button type="button" wire:click="$set('showModal', false)" class="btn btn-secondary text-xs">Batal</button>
                        <button type="submit" class="btn btn-primary text-xs">Simpan Pengaturan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
