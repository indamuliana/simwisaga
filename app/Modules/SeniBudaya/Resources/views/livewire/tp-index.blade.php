<div class="space-y-6">
    <!-- Header Navigasi -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-border pb-4">
        <div>
            <h2 class="text-2xl font-bold text-text-main flex items-center gap-2">
                <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                Tujuan Pembelajaran (TP) Seni Budaya
            </h2>
            <p class="text-sm text-text-secondary mt-1">
                Alokasikan Tujuan Pembelajaran ke periode asesmen (STS 1, SAS, STS 2, SAT). Capaian rapor akan dinamis menyesuaikan periode cetak rapor yang dipilih.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-1.5 bg-surface-hover p-1 rounded-xl border border-border">
            <a href="{{ route('seni-budaya.index') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-text-secondary hover:text-text-main hover:bg-surface">
                Dashboard
            </a>
            <a href="{{ route('seni-budaya.cabang') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-text-secondary hover:text-text-main hover:bg-surface">
                Cabang Seni
            </a>
            <a href="{{ route('seni-budaya.anggota') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-text-secondary hover:text-text-main hover:bg-surface">
                Keanggotaan Siswa
            </a>
            <a href="{{ route('seni-budaya.tp') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-semibold bg-white dark:bg-gray-800 text-primary shadow-xs">
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

    <!-- Filter Cabang, Semester & Alokasi Asesmen -->
    <div class="card p-4 border border-border flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 w-full md:w-auto flex-1">
            <div>
                <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Pilih Cabang Seni</label>
                <select wire:model.live="selectedCabangId" class="input text-sm w-full">
                    @foreach($cabangs as $c)
                        <option value="{{ $c->id }}">{{ $c->nama }} ({{ $c->kode }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Pilih Semester</label>
                <select wire:model.live="selectedSemesterId" class="input text-sm w-full">
                    @foreach($semesters as $s)
                        <option value="{{ $s->id }}">{{ $s->tahunAjaran?->nama }} - {{ ucfirst($s->semester) }} {{ $s->is_active ? '(Aktif)' : '' }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Filter Target Asesmen</label>
                <select wire:model.live="filterTargetAsesmen" class="input text-sm w-full">
                    <option value="all">Semua Target Asesmen</option>
                    @if($isGenap)
                        <option value="STS 2">Khusus STS 2</option>
                        <option value="SAT">Khusus SAT</option>
                    @else
                        <option value="STS 1">Khusus STS 1</option>
                        <option value="SAS">Khusus SAS</option>
                    @endif
                </select>
            </div>
        </div>

        <button wire:click="openCreateModal" class="btn-primary text-sm flex items-center gap-1.5 shrink-0 self-end md:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah TP
        </button>
    </div>

    <!-- Tabel Daftar TP -->
    <div class="card overflow-hidden border border-border">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-surface-hover text-text-secondary text-xs uppercase tracking-wider border-b border-border">
                    <tr>
                        <th class="px-4 py-3 text-center w-14">Urutan</th>
                        <th class="px-4 py-3 w-28">Kode TP</th>
                        <th class="px-4 py-3">Deskripsi Capaian Pembelajaran</th>
                        <th class="px-4 py-3 text-center w-48">Alokasi Asesmen</th>
                        <th class="px-4 py-3 text-center w-24">Status</th>
                        <th class="px-4 py-3 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border text-text-main">
                    @forelse($tps as $tp)
                        <tr class="hover:bg-surface-hover transition-colors">
                            <td class="px-4 py-3 text-center text-text-muted font-bold">{{ $tp->urutan }}</td>
                            <td class="px-4 py-3 font-semibold text-primary">{{ $tp->kode }}</td>
                            <td class="px-4 py-3">{{ $tp->deskripsi }}</td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $target = $tp->target_asesmen ?? 'semua';
                                    $badgeColor = match($target) {
                                        'STS 1' => 'bg-blue-100 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 border-blue-200',
                                        'SAS' => 'bg-purple-100 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300 border-purple-200',
                                        'STS 2' => 'bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border-amber-200',
                                        'SAT' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border-emerald-200',
                                        default => 'bg-surface-hover text-text-secondary border-border',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-md text-xs font-semibold border {{ $badgeColor }}">
                                    {{ $tp->target_asesmen_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $tp->is_active ? 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $tp->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button wire:click="openEditModal({{ $tp->id }})" class="p-1.5 text-text-secondary hover:text-primary rounded-md hover:bg-surface" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button wire:click="delete({{ $tp->id }})" wire:confirm="Hapus TP '{{ $tp->kode }}'?" class="p-1.5 text-text-secondary hover:text-rose-600 rounded-md hover:bg-surface" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-text-muted">
                                Belum ada Tujuan Pembelajaran (TP) untuk cabang dan kriteria filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form TP -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs" x-cloak>
            <div class="card max-w-lg w-full p-6 space-y-4 shadow-xl border border-border">
                <div class="flex justify-between items-center border-b border-border pb-3">
                    <h3 class="text-lg font-bold text-text-main">
                        {{ $editingId ? 'Edit Tujuan Pembelajaran' : 'Tambah Tujuan Pembelajaran Baru' }}
                    </h3>
                    <button type="button" wire:click="$set('showModal', false)" class="text-text-muted hover:text-text-main text-xl leading-none">&times;</button>
                </div>

                <form wire:submit="save" class="space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Kode TP <span class="text-rose-500">*</span></label>
                            <input type="text" wire:model="kode" placeholder="misal: TP 1" class="input text-sm w-full">
                            @error('kode') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Nomor Urut <span class="text-rose-500">*</span></label>
                            <input type="number" wire:model="urutan" min="1" class="input text-sm w-full">
                            @error('urutan') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Pilihan Target Alokasi Asesmen -->
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Alokasi Target Asesmen <span class="text-rose-500">*</span></label>
                        <select wire:model="target_asesmen" class="input text-sm w-full font-medium">
                            @foreach($targetOptions as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-text-muted mt-1">
                            Pilih apakah TP ini dinilai saat <strong>{{ $isGenap ? 'STS 2, SAT, atau Keduanya' : 'STS 1, SAS, atau Keduanya' }}</strong>. Deskripsi rapor akan otomatis dinamis menyesuaikan periode rapor yang dicetak.
                        </p>
                        @error('target_asesmen') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Deskripsi Tujuan Pembelajaran <span class="text-rose-500">*</span></label>
                        <p class="text-xs text-text-muted mb-1">Gunakan kata kerja operasional (contoh: <em>mengeksplorasi teknik vokal dan artikulasi lagu daerah</em>).</p>
                        <textarea wire:model="deskripsi" rows="3" placeholder="Tulis deskripsi capaian..." class="input text-sm w-full"></textarea>
                        @error('deskripsi') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <input type="checkbox" id="tp_active_cb" wire:model="is_active" class="rounded border-border text-primary focus:ring-primary">
                        <label for="tp_active_cb" class="text-sm font-medium text-text-main cursor-pointer">Status Aktif</label>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-border">
                        <button type="button" wire:click="$set('showModal', false)" class="btn-secondary text-sm">Batal</button>
                        <button type="submit" class="btn-primary text-sm">Simpan TP</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
