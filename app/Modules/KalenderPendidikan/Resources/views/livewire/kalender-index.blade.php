<div class="space-y-4">
    {{-- Header Minimalis & Aksi Cepat --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between pb-2 border-b border-border">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-text-main flex items-center gap-2">
                <svg class="w-6 h-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Kalender Pendidikan & Agenda Sekolah
            </h1>
            <p class="text-xs text-text-muted mt-0.5">
                Pusat visualisasi jadwal KBM, asesmen, kesiswaan, kedinasan, dan penetapan hari libur sekolah.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @can('kalender.tambah')
            <button type="button" 
                    wire:click="openCreateModal()" 
                    class="btn btn-primary inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold shadow-xs">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Agenda
            </button>
            @endcan

            <a href="{{ route('kalender.cetak-pdf') }}" 
               target="_blank"
               class="btn btn-secondary inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium">
                <svg class="w-3.5 h-3.5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak PDF
            </a>

            @can('kalender.kategori.kelola')
            <a href="{{ route('kalender.kategori') }}" class="btn btn-secondary px-2.5 py-1.5 text-xs font-medium text-text-secondary" title="Kelola Kategori">
                Kategori
            </a>
            @endcan

            @can('kalender.libur.kelola')
            <a href="{{ route('kalender.libur') }}" class="btn btn-secondary px-2.5 py-1.5 text-xs font-medium text-text-secondary" title="Pengaturan Hari Libur">
                Hari Libur
            </a>
            @endcan
        </div>
    </div>

    {{-- Layout 2 Kolom: Kalender (3/4 Kolom) & Sidebar Filter + Agenda Terdekat (1/4 Kolom) --}}
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 items-start"
         x-data="kalenderApp({ events: {{ $eventsJson }} })"
         x-init="initCalendar()">

        {{-- Kolom Kiri: Kalender Utama (Menempati 3/4 Kolom = lg:col-span-3) --}}
        <div class="lg:col-span-3 min-w-0 card p-3 sm:p-4 bg-surface border border-border rounded-xl shadow-xs space-y-2 overflow-hidden">
            {{-- Legenda Kategori Minimalis --}}
            <div class="flex flex-wrap items-center gap-2 text-[11px] pb-2 border-b border-border/70">
                <span class="text-text-muted font-medium text-[10px] uppercase tracking-wider">Kategori:</span>
                @foreach($kategoris as $k)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-white text-[10px] font-medium" style="background-color: {{ $k->warna }}">
                        {{ $k->nama }}
                    </span>
                @endforeach
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-red-600 text-white text-[10px] font-medium">
                    🏖 Libur
                </span>
            </div>

            {{-- Container Kalender FullCalendar --}}
            <div id="fullcalendar-container" class="w-full min-w-0" wire:ignore></div>
        </div>

        {{-- Kolom Kanan: Filter & Tabel List Agenda Terdekat (Menempati 1/4 Kolom = lg:col-span-1) --}}
        <div class="lg:col-span-1 min-w-0 space-y-3">
            {{-- Panel Filter Minimalis --}}
            <div class="card p-3 bg-surface border border-border rounded-xl shadow-xs space-y-2.5">
                <div class="flex items-center justify-between pb-1.5 border-b border-border">
                    <span class="text-xs font-bold uppercase tracking-wider text-text-muted flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Filter Agenda
                    </span>
                    @if($filterKategori || $filterStatus || $filterFinal !== '' || $filterAudiens)
                    <button type="button" 
                            wire:click="$set('filterKategori', ''); $set('filterStatus', ''); $set('filterFinal', ''); $set('filterAudiens', '')"
                            class="text-[11px] text-rose-500 hover:underline">
                        Reset
                    </button>
                    @endif
                </div>

                <div class="space-y-2 text-xs">
                    <div>
                        <label class="block font-semibold text-text-muted mb-0.5">Kategori</label>
                        <select wire:model.live="filterKategori" class="input input-sm w-full py-1 text-xs">
                            <option value="">Semua Kategori</option>
                            @foreach($kategoris as $k)
                                <option value="{{ $k->id }}">{{ $k->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-semibold text-text-muted mb-0.5">Status</label>
                            <select wire:model.live="filterStatus" class="input input-sm w-full py-1 text-xs">
                                <option value="">Semua</option>
                                <option value="publik">Publik</option>
                                <option value="internal">Internal</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-semibold text-text-muted mb-0.5">Flag Final</label>
                            <select wire:model.live="filterFinal" class="input input-sm w-full py-1 text-xs">
                                <option value="">Semua</option>
                                <option value="1">FINAL</option>
                                <option value="0">Belum</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-text-muted mb-0.5">Sasaran Audiens</label>
                        <select wire:model.live="filterAudiens" class="input input-sm w-full py-1 text-xs">
                            <option value="">Semua Sasaran</option>
                            <option value="semua">Semua Warga</option>
                            <option value="guru">Guru & Staf</option>
                            <option value="kelas">Siswa/Kelas</option>
                            <option value="ortu">Orang Tua</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Panel List Agenda Terdekat Tersortir --}}
            <div class="card p-3 bg-surface border border-border rounded-xl shadow-xs flex flex-col">
                <div class="flex items-center justify-between pb-2 border-b border-border">
                    <span class="text-xs font-bold uppercase tracking-wider text-text-muted flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Agenda Terdekat ({{ $upcomingAgendas->count() }})
                    </span>
                    <span class="text-[10px] text-text-muted">Klik utk detail</span>
                </div>

                <div class="mt-2 space-y-1.5 max-h-[380px] overflow-y-auto pr-1">
                    @forelse($upcomingAgendas as $item)
                    <div @click="selectAgenda({{ $item->id }}, '{{ $item->tgl_mulai->format('Y-m-d') }}')"
                         class="group p-2 rounded-lg border border-border/80 bg-surface-hover/20 hover:bg-surface-hover hover:border-primary/40 cursor-pointer transition-all">
                        
                        <div class="flex items-start justify-between gap-1">
                            <div class="flex items-center gap-1.5 flex-wrap min-w-0">
                                <span class="w-2 h-2 rounded-full flex-shrink-0" style="background-color: {{ $item->display_warna }}"></span>
                                <span class="text-[10px] font-semibold text-text-muted uppercase truncate max-w-[110px]">
                                    {{ $item->kategori?->nama ?? 'Kegiatan' }}
                                </span>
                            </div>
                            @if($item->is_final)
                                <span class="text-[9px] font-bold text-emerald-600 bg-emerald-50 dark:bg-emerald-950 px-1 py-0.2 rounded border border-emerald-300 flex-shrink-0">
                                    ✓ FINAL
                                </span>
                            @endif
                        </div>

                        <h4 class="text-xs font-semibold text-text-main line-clamp-1 group-hover:text-primary transition-colors mt-0.5">
                            {{ $item->judul }}
                        </h4>

                        @if($item->koordinator)
                        <div class="text-[10px] text-text-muted truncate mt-0.5 flex items-center gap-1">
                            <span class="font-medium text-text-secondary">PJ:</span>
                            <span class="truncate">{{ $item->koordinator }}</span>
                        </div>
                        @endif

                        <div class="flex items-center justify-between text-[10px] text-text-muted mt-1">
                            <span>{{ $item->tgl_mulai->translatedFormat('d M') }}{{ $item->tgl_mulai->format('Y-m-d') !== $item->tgl_selesai->format('Y-m-d') ? ' - '.$item->tgl_selesai->translatedFormat('d M') : '' }}</span>
                            <span class="truncate max-w-[95px]">{{ $item->lokasi ?: ($item->is_all_day ? 'Sepanjang Hari' : substr($item->jam_mulai,0,5)) }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="py-6 text-center text-text-muted text-xs">
                        Tidak ada agenda mendatang sesuai filter.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Form Tambah / Edit Agenda --}}
    @if($showFormModal)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="relative w-full max-w-2xl bg-surface border border-border rounded-2xl shadow-xl overflow-hidden p-6 space-y-4 animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center justify-between pb-3 border-b border-border">
                <h3 class="text-base font-bold text-text-main flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    {{ $isEditing ? 'Edit Agenda Kegiatan' : 'Tambah Agenda Kegiatan Baru' }}
                </h3>
                <button type="button" wire:click="$set('showFormModal', false)" class="text-text-muted hover:text-text-main p-1 rounded-lg">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <form wire:submit="saveAgenda" class="space-y-3.5">
                <div>
                    <label class="block text-xs font-semibold text-text-muted mb-1">Nama / Judul Kegiatan <span class="text-rose-500">*</span></label>
                    <input type="text" wire:model="judul" placeholder="Contoh: Penilaian Tengah Semester (PTS) Gasal" class="input w-full @error('judul') border-rose-500 @enderror">
                    @error('judul') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Pembuat (Default Akun Login) & Pelaksana/Koordinator --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-text-muted mb-1">
                            Pembuat Agenda <span class="text-rose-500">*</span>
                        </label>
                        <select wire:model="created_by" class="input w-full @error('created_by') border-rose-500 @enderror">
                            @foreach($usersList as $u)
                                <option value="{{ $u->id }}">
                                    {{ $u->name }} {{ $u->id === auth()->id() ? '(Akun Anda)' : '' }}
                                </option>
                            @endforeach
                        </select>
                        <span class="text-[10px] text-text-muted mt-0.5 block">
                            Default diisi otomatis oleh akun Anda
                        </span>
                        @error('created_by') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-text-muted mb-1">
                            Pelaksana / Koordinator
                        </label>
                        <input type="text" 
                               wire:model="koordinator" 
                               placeholder="Contoh: Waka Kesiswaan / Budi Santoso, S.Pd" 
                               class="input w-full @error('koordinator') border-rose-500 @enderror">
                        <span class="text-[10px] text-text-muted mt-0.5 block">
                            Dapat diisi manual nama atau jabatan
                        </span>
                        @error('koordinator') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-text-muted mb-1">Kategori <span class="text-rose-500">*</span></label>
                        <select wire:model="kategori_id" class="input w-full">
                            @foreach($kategoris as $k)
                                <option value="{{ $k->id }}">{{ $k->nama }}</option>
                            @endforeach
                        </select>
                        @error('kategori_id') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-text-muted mb-1">Visibilitas / Status <span class="text-rose-500">*</span></label>
                        <select wire:model="status" class="input w-full">
                            <option value="internal">Internal (Guru, Tendik, Staff)</option>
                            <option value="publik">Publik (Landing Page & Siswa/Ortu)</option>
                            <option value="draft">Draft (Belum Dipublikasikan)</option>
                        </select>
                        @error('status') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-text-muted mb-1">Tanggal Mulai <span class="text-rose-500">*</span></label>
                        <input type="date" wire:model="tgl_mulai" class="input w-full @error('tgl_mulai') border-rose-500 @enderror">
                        @error('tgl_mulai') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-text-muted mb-1">Tanggal Selesai <span class="text-rose-500">*</span></label>
                        <input type="date" wire:model="tgl_selesai" class="input w-full @error('tgl_selesai') border-rose-500 @enderror">
                        @error('tgl_selesai') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="p-2.5 bg-surface-hover/50 rounded-xl border border-border space-y-2">
                    <label class="flex items-center gap-2 cursor-pointer text-xs font-medium text-text-main">
                        <input type="checkbox" wire:model.live="is_all_day" class="checkbox">
                        Kegiatan Sepanjang Hari (All Day)
                    </label>

                    @if(! $is_all_day)
                    <div class="grid grid-cols-2 gap-3 pt-1">
                        <div>
                            <label class="block text-xs font-semibold text-text-muted mb-1">Jam Mulai</label>
                            <input type="time" wire:model="jam_mulai" class="input w-full">
                            @error('jam_mulai') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-text-muted mb-1">Jam Selesai</label>
                            <input type="time" wire:model="jam_selesai" class="input w-full">
                            @error('jam_selesai') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-text-muted mb-1">Lokasi / Ruangan</label>
                        <input type="text" wire:model="lokasi" placeholder="Misal: Aula Utama / Lab Komputer" class="input w-full">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-text-muted mb-1">Sasaran Audiens</label>
                        <select wire:model="audiens" class="input w-full">
                            <option value="semua">Semua Warga Sekolah</option>
                            <option value="guru">Khusus Guru & Tendik</option>
                            <option value="kelas">Siswa / Rombel</option>
                            <option value="ortu">Orang Tua / Wali Siswa</option>
                        </select>
                    </div>
                </div>

                <div class="p-2.5 bg-surface-hover/50 rounded-xl border border-border space-y-2">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-text-muted mb-1">Pola Pengulangan Rutin</label>
                            <select wire:model.live="recurrence_type" class="input w-full">
                                <option value="none">Tidak Berulang (Sekali Kegiatan)</option>
                                <option value="daily">Setiap Hari</option>
                                <option value="weekly">Setiap Pekan (Mingguan)</option>
                                <option value="monthly">Setiap Bulan</option>
                            </select>
                        </div>

                        @if($recurrence_type !== 'none')
                        <div>
                            <label class="block text-xs font-semibold text-text-muted mb-1">Ulangi Sampai Tanggal <span class="text-rose-500">*</span></label>
                            <input type="date" wire:model="recurrence_until" class="input w-full @error('recurrence_until') border-rose-500 @enderror">
                            @error('recurrence_until') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        @endif
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-text-muted mb-1">Keterangan / Deskripsi Kegiatan</label>
                    <textarea wire:model="deskripsi" rows="2" placeholder="Informasi detail kegiatan..." class="input w-full py-1.5"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-border">
                    <button type="button" wire:click="$set('showFormModal', false)" class="btn btn-secondary px-3 py-1.5 text-xs">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary px-4 py-1.5 text-xs font-semibold inline-flex items-center gap-1.5">
                        <svg wire:loading class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        Simpan Agenda
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Modal Detail Agenda --}}
    @if($showDetailModal && $selectedAgenda)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="relative w-full max-w-lg bg-surface border border-border rounded-2xl shadow-xl overflow-hidden p-5 space-y-4 animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-start justify-between pb-2.5 border-b border-border">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold text-white shadow-xs" style="background-color: {{ $selectedAgenda->display_warna }}">
                            {{ $selectedAgenda->kategori?->nama ?? 'Kegiatan' }}
                        </span>

                        @if($selectedAgenda->is_final)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 inline-flex items-center gap-1 border border-emerald-300">
                                ✓ FINAL
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 border border-amber-300">
                                Usulan / Belum Final
                            </span>
                        @endif

                        <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-surface-hover text-text-muted uppercase">
                            {{ $selectedAgenda->status }}
                        </span>
                    </div>
                    <h2 class="text-lg font-bold text-text-main pt-0.5">{{ $selectedAgenda->judul }}</h2>
                </div>

                <button type="button" wire:click="$set('showDetailModal', false)" class="text-text-muted hover:text-text-main p-1 rounded-lg">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <div class="space-y-2.5 text-xs">
                <div class="flex items-center gap-2.5 text-text-secondary">
                    <svg class="w-4 h-4 text-text-muted flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <div>
                        <p class="font-medium text-text-main">
                            {{ $selectedAgenda->tgl_mulai->translatedFormat('l, d F Y') }}
                            @if($selectedAgenda->tgl_mulai->format('Y-m-d') !== $selectedAgenda->tgl_selesai->format('Y-m-d'))
                                &mdash; {{ $selectedAgenda->tgl_selesai->translatedFormat('l, d F Y') }}
                            @endif
                        </p>
                        <p class="text-[11px] text-text-muted">
                            @if($selectedAgenda->is_all_day)
                                Sepanjang Hari
                            @else
                                {{ substr($selectedAgenda->jam_mulai, 0, 5) }} &ndash; {{ substr($selectedAgenda->jam_selesai, 0, 5) }} WIB
                            @endif
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 pt-1">
                    <div class="flex items-center gap-1.5 text-text-secondary truncate">
                        <svg class="w-3.5 h-3.5 text-text-muted flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        <span class="truncate">{{ $selectedAgenda->lokasi ?: 'Lokasi belum ditentukan' }}</span>
                    </div>

                    <div class="flex items-center gap-1.5 text-text-secondary truncate">
                        <svg class="w-3.5 h-3.5 text-text-muted flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        <span class="truncate">Audiens: <strong>{{ ucfirst($selectedAgenda->audiens) }}</strong></span>
                    </div>
                </div>

                @if($selectedAgenda->deskripsi)
                <div class="p-2.5 bg-surface-hover/40 rounded-xl border border-border text-[11px] text-text-secondary leading-relaxed">
                    {{ $selectedAgenda->deskripsi }}
                </div>
                @endif

                <div class="pt-2 border-t border-border grid grid-cols-1 sm:grid-cols-2 gap-2 text-[11px] text-text-muted">
                    <div>
                        <span class="font-medium text-text-main">Pembuat (Akun):</span>
                        <span class="text-text-secondary">{{ $selectedAgenda->creator?->name ?? 'Sistem' }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-text-main">Pelaksana / Koordinator:</span>
                        <span class="text-text-secondary">{{ $selectedAgenda->koordinator ?: '-' }}</span>
                    </div>
                    @if($selectedAgenda->is_final && $selectedAgenda->validator)
                    <div class="sm:col-span-2">
                        <span class="font-medium text-emerald-600">Diverifikasi oleh:</span> {{ $selectedAgenda->validator->name }}
                        <span class="text-[10px]">({{ $selectedAgenda->finalized_at?->translatedFormat('d M Y H:i') }})</span>
                    </div>
                    @endif
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-2 pt-3 border-t border-border">
                <div class="flex items-center gap-1.5">
                    @if($canManageFlag)
                    <button type="button" 
                            wire:click="toggleFlagFinal({{ $selectedAgenda->id }})" 
                            class="btn {{ $selectedAgenda->is_final ? 'btn-secondary text-amber-600' : 'btn-primary bg-emerald-600 hover:bg-emerald-700 text-white' }} px-2.5 py-1 text-xs font-semibold inline-flex items-center gap-1 shadow-xs">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ $selectedAgenda->is_final ? 'Cabut Final' : 'Flag FINAL' }}
                    </button>
                    @endif

                    <a href="{{ route('kalender.export-ics', $selectedAgenda->id) }}" 
                       class="btn btn-secondary px-2.5 py-1 text-xs font-medium inline-flex items-center gap-1" 
                       title="Download iCal (.ics)">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                        iCal (.ics)
                    </a>
                </div>

                @if($selectedAgenda->created_by === auth()->id() || $canManageFlag)
                <div class="flex items-center gap-1.5">
                    <button type="button" wire:click="editSelectedAgenda" class="btn btn-secondary px-2.5 py-1 text-xs font-medium text-text-main">
                        Edit
                    </button>
                    <button type="button" 
                            wire:confirm="Yakin ingin menghapus agenda ini?"
                            wire:click="deleteAgenda({{ $selectedAgenda->id }})" 
                            class="btn btn-secondary px-2.5 py-1 text-xs font-medium text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30">
                        Hapus
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>

@push('styles')
<style>
    /* Styling Kalender Minimalis & Mengurangi Tinggi Sel Setengah dari Standar */
    .fc .fc-daygrid-day-frame {
        min-height: 48px !important;
        padding: 1px 2px !important;
    }
    .fc .fc-daygrid-day-top {
        flex-direction: row;
        padding: 1px 3px !important;
    }
    .fc .fc-daygrid-day-number {
        font-size: 0.72rem !important;
        padding: 1px 3px !important;
        font-weight: 600;
        line-height: 1.1;
    }
    .fc .fc-daygrid-event {
        font-size: 0.68rem !important;
        line-height: 1.15;
        margin-top: 1px !important;
        margin-bottom: 1px !important;
        padding: 1px 3px !important;
        border-radius: 2px !important;
    }
    .fc .fc-col-header-cell-cushion {
        font-size: 0.72rem !important;
        padding: 4px 2px !important;
        font-weight: 700;
        text-transform: uppercase;
    }
    .fc .fc-toolbar-title {
        font-size: 1.05rem !important;
        font-weight: 700;
    }
    .fc .fc-button {
        font-size: 0.72rem !important;
        padding: 0.2rem 0.5rem !important;
        font-weight: 600;
    }
    /* Pastikan kalender selalu memenuhi 100% lebar container dan membagi 7 hari secara proporsional */
    #fullcalendar-container {
        width: 100% !important;
        max-width: 100% !important;
        overflow-x: auto;
    }
    .fc {
        width: 100% !important;
        max-width: 100% !important;
    }
    .fc-scrollgrid,
    .fc-theme-standard .fc-scrollgrid,
    .fc .fc-col-header,
    .fc .fc-daygrid-body,
    .fc .fc-daygrid-body table,
    .fc .fc-scrollgrid-sync-table {
        width: 100% !important;
        table-layout: fixed !important;
    }

    /* ========================================================
       KOLOM SABTU & MINGGU (WEEKEND) - MERAH MUDA TERANG
       Harmonis dengan tema Terang (Light) & Gelap (Dark)
       ======================================================== */
    /* Light Mode */
    .fc .fc-day-sat,
    .fc .fc-day-sun {
        background-color: #FFF1F2 !important; /* Soft rose / light pink */
    }
    .fc .fc-col-header-cell.fc-day-sat,
    .fc .fc-col-header-cell.fc-day-sun {
        background-color: #FFE4E6 !important; /* Deeper soft rose header */
    }
    .fc .fc-col-header-cell.fc-day-sat .fc-col-header-cell-cushion,
    .fc .fc-col-header-cell.fc-day-sun .fc-col-header-cell-cushion {
        color: #E11D48 !important; /* Rose-600 */
    }
    .fc .fc-day-sat .fc-daygrid-day-number,
    .fc .fc-day-sun .fc-daygrid-day-number {
        color: #E11D48 !important; /* Rose-600 */
        font-weight: 700;
    }
    .fc .fc-day-other.fc-day-sat,
    .fc .fc-day-other.fc-day-sun {
        background-color: #FFF8F8 !important;
        opacity: 0.75;
    }
    .fc .fc-day-other.fc-day-sat .fc-daygrid-day-number,
    .fc .fc-day-other.fc-day-sun .fc-daygrid-day-number {
        color: #FDA4AF !important; /* Rose-300 */
    }
    .fc .fc-day-today.fc-day-sat,
    .fc .fc-day-today.fc-day-sun {
        background-color: #FCE7F3 !important; /* Sedikit aksen lebih cerah jika hari ini jatuh di akhir pekan */
    }

    /* Dark Mode (Midnight Navy Theme) */
    .dark .fc-theme-standard td,
    .dark .fc-theme-standard th,
    .dark .fc-theme-standard .fc-scrollgrid {
        border-color: #1E2D4A !important;
    }
    .dark .fc .fc-toolbar-title {
        color: #F8FAFC !important;
    }
    .dark .fc .fc-col-header-cell-cushion {
        color: #94A3B8 !important;
    }
    .dark .fc .fc-daygrid-day-number {
        color: #94A3B8 !important;
    }
    .dark .fc .fc-day-other .fc-daygrid-day-number {
        color: #475569 !important;
    }
    .dark .fc .fc-button-primary {
        background-color: #1A2846 !important;
        border-color: #1E2D4A !important;
        color: #F8FAFC !important;
    }
    .dark .fc .fc-button-primary:hover {
        background-color: #1E2E50 !important;
    }
    .dark .fc .fc-button-primary:not(:disabled).fc-button-active,
    .dark .fc .fc-button-primary:not(:disabled):active {
        background-color: #2563EB !important;
        border-color: #2563EB !important;
    }
    .dark .fc .fc-day-sat,
    .dark .fc .fc-day-sun {
        background-color: rgba(244, 63, 94, 0.09) !important; /* Soft rose glow on dark surface */
    }
    .dark .fc .fc-col-header-cell.fc-day-sat,
    .dark .fc .fc-col-header-cell.fc-day-sun {
        background-color: rgba(244, 63, 94, 0.16) !important;
    }
    .dark .fc .fc-col-header-cell.fc-day-sat .fc-col-header-cell-cushion,
    .dark .fc .fc-col-header-cell.fc-day-sun .fc-col-header-cell-cushion {
        color: #FB7185 !important; /* Rose-400 */
    }
    .dark .fc .fc-day-sat .fc-daygrid-day-number,
    .dark .fc .fc-day-sun .fc-daygrid-day-number {
        color: #FB7185 !important; /* Rose-400 */
        font-weight: 700;
    }
    .dark .fc .fc-day-other.fc-day-sat,
    .dark .fc .fc-day-other.fc-day-sun {
        background-color: rgba(244, 63, 94, 0.04) !important;
        opacity: 0.65;
    }
    .dark .fc .fc-day-other.fc-day-sat .fc-daygrid-day-number,
    .dark .fc .fc-day-other.fc-day-sun .fc-daygrid-day-number {
        color: #9F1239 !important; /* Rose-800 */
    }
    .dark .fc .fc-day-today.fc-day-sat,
    .dark .fc .fc-day-today.fc-day-sun {
        background-color: rgba(244, 63, 94, 0.22) !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script>
    function kalenderApp(config) {
        return {
            calendar: null,
            initCalendar() {
                const calendarEl = document.getElementById('fullcalendar-container');
                if (!calendarEl) return;

                const parseEvents = (raw) => {
                    try {
                        return typeof raw === 'string' ? JSON.parse(raw) : (raw || []);
                    } catch (e) {
                        return [];
                    }
                };

                this.calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    firstDay: 1, // Dimulai hari Senin
                    locale: 'id',
                    timeZone: 'local',
                    contentHeight: 'auto',
                    aspectRatio: 1.75, // Mengatur rasio agar sel tidak terlalu tinggi
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,listMonth'
                    },
                    buttonText: {
                        today: 'Hari Ini',
                        month: 'Bulan',
                        week: 'Minggu',
                        list: 'Daftar'
                    },
                    dayMaxEvents: 2,
                    eventTimeFormat: {
                        hour: '2-digit',
                        minute: '2-digit',
                        meridiem: false,
                        hour12: false
                    },
                    events: parseEvents(this.$wire.eventsJson || (config && config.events)),
                    dateClick: (info) => {
                        this.$wire.openCreateModal(info.dateStr);
                    },
                    eventClick: (info) => {
                        const props = info.event.extendedProps;
                        if (props.is_libur) {
                            alert('🏖 HARI LIBUR: ' + props.judul + '\nTanggal: ' + props.tgl_mulai + (props.tgl_mulai !== props.tgl_selesai ? ' s/d ' + props.tgl_selesai : '') + '\n' + (props.keterangan || ''));
                            return;
                        }
                        this.$wire.openDetailModal(info.event.id);
                    }
                });

                this.calendar.render();

                // Deteksi perubahan ukuran container (misal saat sidebar di-pin/unhide)
                if (window.ResizeObserver) {
                    const resizeObserver = new ResizeObserver(() => {
                        if (this.calendar) {
                            this.calendar.updateSize();
                        }
                    });
                    resizeObserver.observe(calendarEl);
                }

                // Sinkronisasi Reaktif: FullCalendar otomatis update saat filter berubah atau agenda disimpan
                this.$watch('$wire.eventsJson', (newVal) => {
                    if (this.calendar && newVal) {
                        this.calendar.removeAllEvents();
                        this.calendar.addEventSource(parseEvents(newVal));
                    }
                });
            },
            selectAgenda(id, dateStr) {
                if (this.calendar && dateStr) {
                    this.calendar.gotoDate(dateStr);
                }
                this.$wire.openDetailModal(id);
            }
        }
    }
</script>
@endpush
