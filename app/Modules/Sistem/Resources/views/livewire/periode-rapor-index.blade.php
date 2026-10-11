<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-text-main">Pengaturan Titi Mangsa & Periode Rapor</h1>
            <p class="text-sm text-text-secondary">Kelola periode rapor (misal: 9 Minggu ke-1 s.d. ke-4), rentang tanggal pelaksanaan, dan titi mangsa tanggal pembagian rapor per Tahun Ajaran.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @can('sistem.titimangsa.tambah')
                <button wire:click="openCopyModal" class="btn btn-secondary text-xs flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                    <span>Salin dari T.A. Lain</span>
                </button>
                <button wire:click="openCreateModal" class="btn btn-primary text-xs flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>+ Tambah Periode Rapor</span>
                </button>
            @endcan
        </div>
    </div>

    <!-- Flash Notifications -->
    @if (session('status'))
        <div class="p-4 text-sm text-emerald-700 bg-emerald-50 dark:bg-emerald-950/50 dark:text-emerald-300 rounded-xl border border-emerald-200 dark:border-emerald-800 flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('status') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div class="p-4 text-sm text-rose-700 bg-rose-50 dark:bg-rose-950/50 dark:text-rose-300 rounded-xl border border-rose-200 dark:border-rose-800 flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Filter Bar: Tahun Ajaran -->
    <div class="card p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <label class="text-xs font-semibold text-text-secondary whitespace-nowrap">Tahun Ajaran:</label>
            <select wire:model.live="filterTahunAjaranId" class="text-sm rounded-lg border-border bg-surface text-text-main py-1.5 px-3 focus:ring-primary focus:border-primary">
                @foreach($tahunAjarans as $ta)
                    <option value="{{ $ta->id }}">
                        {{ $ta->nama }} {{ $ta->is_active ? '(Aktif)' : '' }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="text-xs text-text-secondary flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            <span>Periode dengan tanda centang hijau otomatis dipilih saat menu cetak rapor dibuka.</span>
        </div>
    </div>

    <!-- Table Daftar Periode Rapor -->
    <div class="card p-0 overflow-hidden shadow-2xs">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-border text-left text-xs">
                <thead class="bg-surface-hover/60 text-text-secondary uppercase font-semibold tracking-wider text-[11px]">
                    <tr>
                        <th class="px-4 py-3 w-12 text-center">Urutan</th>
                        <th class="px-4 py-3 w-28">Semester</th>
                        <th class="px-4 py-3">Nama Resmi</th>
                        <th class="px-4 py-3">Istilah Sekolah</th>
                        <th class="px-4 py-3">Rentang Pelaksanaan</th>
                        <th class="px-4 py-3">Titi Mangsa (Cetak)</th>
                        <th class="px-4 py-3 text-center w-28">Periode Aktif</th>
                        <th class="px-4 py-3 text-right w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border text-text-main">
                    @forelse($periodes as $p)
                        <tr class="hover:bg-surface-hover/40 transition {{ $p->is_active ? 'bg-primary-soft/10' : '' }}">
                            <td class="px-4 py-3.5 text-center font-mono text-text-secondary">{{ $p->urutan }}</td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold uppercase {{ $p->semester === 'ganjil' ? 'bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200 dark:border-blue-800' : 'bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300 border border-purple-200 dark:border-purple-800' }}">
                                    {{ ucfirst($p->semester) }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-text-main">{{ $p->nama }}</div>
                                <div class="text-[10px] text-text-secondary font-mono">{{ $p->kode }}</div>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-50 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                    {{ $p->sebutan }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-text-secondary">
                                @if($p->tanggal_mulai && $p->tanggal_selesai)
                                    <span>{{ $p->tanggal_mulai->translatedFormat('d M Y') }} – {{ $p->tanggal_selesai->translatedFormat('d M Y') }}</span>
                                @elseif($p->tanggal_mulai)
                                    <span>Mulai {{ $p->tanggal_mulai->translatedFormat('d M Y') }}</span>
                                @else
                                    <span class="text-text-muted italic">Tidak ditentukan</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 font-medium">
                                <div class="text-text-main flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span>{{ $p->titimangsa_lengkap }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                @can('sistem.titimangsa.edit')
                                    <button wire:click="toggleActive({{ $p->id }})" title="Klik untuk mengubah status aktif" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold transition cursor-pointer {{ $p->is_active ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300 hover:bg-emerald-200' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 hover:bg-slate-200' }}">
                                        @if($p->is_active)
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <span>Aktif</span>
                                        @else
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                            <span>Nonaktif</span>
                                        @endif
                                    </button>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium {{ $p->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $p->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                @endcan
                            </td>
                            <td class="px-4 py-3.5 text-right space-x-2 whitespace-nowrap">
                                @can('sistem.titimangsa.edit')
                                    <button wire:click="edit({{ $p->id }})" class="text-xs font-semibold text-primary hover:underline">Edit</button>
                                @endcan
                                @can('sistem.titimangsa.hapus')
                                    <button wire:click="delete({{ $p->id }})" wire:confirm="Hapus periode '{{ $p->sebutan }}'?" class="text-xs font-semibold text-rose-600 hover:underline">Hapus</button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-text-secondary">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <p class="text-sm font-medium">Belum ada periode rapor yang dibuat untuk Tahun Ajaran ini.</p>
                                    @can('sistem.titimangsa.tambah')
                                        <div class="flex items-center gap-3 pt-2">
                                            <button wire:click="openCreateModal" class="btn btn-primary text-xs">+ Tambah Periode Manual</button>
                                            <button wire:click="openCopyModal" class="btn btn-secondary text-xs">Salin dari T.A. Lain</button>
                                        </div>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL CREATE / EDIT PERIODE RAPOR -->
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div class="w-full max-w-lg bg-surface rounded-2xl shadow-xl border border-border overflow-hidden my-8">
            <div class="px-6 py-4 border-b border-border flex justify-between items-center bg-surface-hover/30">
                <h2 class="text-lg font-bold text-text-main flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>{{ $periodeId ? 'Edit Periode Rapor & Titi Mangsa' : 'Tambah Periode Rapor Baru' }}</span>
                </h2>
                <button wire:click="$set('showModal', false)" class="text-text-secondary hover:text-text-main text-lg">&times;</button>
            </div>

            <form wire:submit="save" class="p-6 space-y-4 text-xs">
                <!-- Semester & Urutan -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-text-main mb-1">Semester <span class="text-rose-500">*</span></label>
                        <select wire:model="semester" class="w-full text-xs rounded-lg border-border bg-surface text-text-main">
                            <option value="ganjil">Semester Ganjil</option>
                            <option value="genap">Semester Genap</option>
                        </select>
                        @error('semester') <span class="text-rose-500 block mt-0.5">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block font-semibold text-text-main mb-1">Nomor Urutan <span class="text-rose-500">*</span></label>
                        <input type="number" min="1" wire:model="urutan" class="w-full text-xs rounded-lg border-border bg-surface text-text-main text-center font-bold">
                        @error('urutan') <span class="text-rose-500 block mt-0.5">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Kode & Nama Resmi -->
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block font-semibold text-text-main mb-1">Kode Unik <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model="kode" placeholder="misal: 9_minggu_1" class="w-full text-xs rounded-lg border-border bg-surface text-text-main font-mono">
                        @error('kode') <span class="text-rose-500 block mt-0.5">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-span-2">
                        <label class="block font-semibold text-text-main mb-1">Nama Resmi Periode <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model="nama" placeholder="misal: Rapor Tengah Semester Ganjil" class="w-full text-xs rounded-lg border-border bg-surface text-text-main">
                        @error('nama') <span class="text-rose-500 block mt-0.5">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Istilah / Sebutan Sekolah -->
                <div>
                    <label class="block font-semibold text-text-main mb-1">Istilah / Sebutan Sekolah <span class="text-rose-500">*</span></label>
                    <input type="text" wire:model="sebutan" placeholder="misal: 9 Minggu ke-1" class="w-full text-xs rounded-lg border-border bg-surface text-text-main font-bold text-primary">
                    <p class="text-[10px] text-text-secondary mt-1">Istilah ini yang akan dicantumkan di identitas laporan hasil belajar rapor siswa (misal: "9 Minggu ke-1").</p>
                    @error('sebutan') <span class="text-rose-500 block mt-0.5">{{ $message }}</span> @enderror
                </div>

                <!-- Rentang Pelaksanaan -->
                <div class="p-3 bg-surface-hover/30 rounded-xl border border-border space-y-2">
                    <label class="block font-semibold text-text-secondary">Rentang Tanggal Pelaksanaan (Opsional):</label>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <span class="text-[11px] text-text-secondary block mb-1">Tanggal Mulai</span>
                            <input type="date" wire:model="tanggal_mulai" class="w-full text-xs rounded-lg border-border bg-surface text-text-main">
                            @error('tanggal_mulai') <span class="text-rose-500 block mt-0.5">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <span class="text-[11px] text-text-secondary block mb-1">Tanggal Selesai</span>
                            <input type="date" wire:model="tanggal_selesai" class="w-full text-xs rounded-lg border-border bg-surface text-text-main">
                            @error('tanggal_selesai') <span class="text-rose-500 block mt-0.5">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Titi Mangsa Pembagian Rapor -->
                <div class="p-3 bg-primary-soft/10 rounded-xl border border-primary/20 space-y-2">
                    <label class="block font-semibold text-text-main">Titi Mangsa Penerbitan Rapor:</label>
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <span class="text-[11px] text-text-secondary block mb-1">Tempat <span class="text-rose-500">*</span></span>
                            <input type="text" wire:model="tempat" placeholder="Garut" class="w-full text-xs rounded-lg border-border bg-surface text-text-main font-semibold">
                            @error('tempat') <span class="text-rose-500 block mt-0.5">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-span-2">
                            <span class="text-[11px] text-text-secondary block mb-1">Tanggal Pembagian Rapor <span class="text-rose-500">*</span></span>
                            <input type="date" wire:model="tanggal_bagi" class="w-full text-xs rounded-lg border-border bg-surface text-text-main font-bold">
                            @error('tanggal_bagi') <span class="text-rose-500 block mt-0.5">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <p class="text-[10px] text-text-secondary">Akan tercetak di atas tanda tangan Kepala Sekolah: <strong class="text-text-main">{{ $tempat ?: 'Garut' }}, {{ $tanggal_bagi ? \Carbon\Carbon::parse($tanggal_bagi)->translatedFormat('d F Y') : '-' }}</strong></p>
                </div>

                <!-- Status Aktif -->
                <div class="pt-1">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="is_active" class="rounded border-border text-primary focus:ring-primary">
                        <span class="font-medium text-text-main">Jadikan sebagai Periode Rapor yang Sedang Aktif</span>
                    </label>
                    <p class="text-[10px] text-text-secondary pl-6">Jika dicentang, periode ini otomatis terpilih sebagai default saat menu Cetak Rapor dibuka.</p>
                </div>

                <!-- Actions -->
                <div class="pt-4 flex justify-end space-x-3 border-t border-border">
                    <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 text-text-secondary hover:bg-surface-hover rounded-lg">Batal</button>
                    <button type="submit" class="btn btn-primary font-semibold">Simpan Periode</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- MODAL COPY DARI TAHUN AJARAN LAIN -->
    @if($showCopyModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="w-full max-w-md bg-surface rounded-2xl shadow-xl border border-border overflow-hidden">
            <div class="px-6 py-4 border-b border-border flex justify-between items-center bg-surface-hover/30">
                <h2 class="text-lg font-bold text-text-main">Salin Periode dari Tahun Ajaran Lain</h2>
                <button wire:click="$set('showCopyModal', false)" class="text-text-secondary hover:text-text-main text-lg">&times;</button>
            </div>
            <form wire:submit="executeCopy" class="p-6 space-y-4 text-xs">
                <p class="text-text-secondary">Fitur ini akan menyalin seluruh nama periode, istilah sebutan (misal: 9 Minggu ke-1 s.d. 4), tempat, dan urutan dari Tahun Ajaran sumber ke Tahun Ajaran target saat ini.</p>

                <div>
                    <label class="block font-semibold text-text-main mb-1">Tahun Ajaran Sumber (Asal)</label>
                    <select wire:model="sourceTahunAjaranId" class="w-full text-xs rounded-lg border-border bg-surface text-text-main">
                        <option value="">-- Pilih Tahun Ajaran Sumber --</option>
                        @foreach($tahunAjarans as $ta)
                            @if($ta->id != $filterTahunAjaranId)
                                <option value="{{ $ta->id }}">{{ $ta->nama }}</option>
                            @endif
                        @endforeach
                    </select>
                    @error('sourceTahunAjaranId') <span class="text-rose-500 block mt-0.5">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block font-semibold text-text-secondary mb-1">Tahun Ajaran Tujuan (Target)</label>
                    <input type="text" value="{{ $tahunAjarans->firstWhere('id', $filterTahunAjaranId)?->nama }}" disabled class="w-full text-xs rounded-lg border-border bg-surface-hover/60 text-text-main cursor-not-allowed font-bold">
                </div>

                <div class="pt-4 flex justify-end space-x-3 border-t border-border">
                    <button type="button" wire:click="$set('showCopyModal', false)" class="px-4 py-2 text-text-secondary hover:bg-surface-hover rounded-lg">Batal</button>
                    <button type="submit" class="btn btn-primary font-semibold">Salin Periode Rapor</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
