<div class="space-y-6">
    {{-- Header Modul --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-text-main flex items-center gap-2">
                <span>Struktur Organisasi</span>
            </h1>
            <p class="text-xs sm:text-sm text-text-secondary mt-0.5">Kelola susunan hierarki jabatan, bobot jam tugas tambahan, dan penugasan pejabat sekolah</p>
        </div>

        {{-- Quick Stat Badges --}}
        <div class="flex items-center gap-2 flex-wrap self-start sm:self-auto">
            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-surface border border-border text-xs text-text-secondary shadow-2xs">
                <svg class="w-3.5 h-3.5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <span>Jabatan:</span>
                <strong class="text-text-main font-bold">{{ $totalJabatan }}</strong>
            </div>
            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-surface border border-border text-xs text-text-secondary shadow-2xs">
                <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Total Beban:</span>
                <strong class="text-indigo-600 dark:text-indigo-400 font-bold">{{ $totalBobotJam }} Jam</strong>
            </div>
            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-surface border border-border text-xs text-text-secondary shadow-2xs">
                <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span>Pejabat:</span>
                <strong class="text-emerald-600 dark:text-emerald-400 font-bold">{{ $totalPejabat }} Orang</strong>
            </div>
        </div>
    </div>

    {{-- Flash Notifications --}}
    @if(session()->has('success'))
        <div class="p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-xs sm:text-sm flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif
    @if(session()->has('error'))
        <div class="p-3.5 rounded-xl bg-red-50 dark:bg-red-950/60 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 text-xs sm:text-sm flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    {{-- Tab Bar (Daftar Jabatan vs Bagan Organigram) --}}
    <div class="flex items-center justify-between gap-3 flex-wrap">
        <div class="inline-flex items-center gap-1.5 p-1 rounded-xl bg-surface-hover/80 border border-border">
            <button type="button"
                wire:click="setTab('daftar')"
                class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 {{ $activeTab === 'daftar' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main hover:bg-surface/80' }}">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                </svg>
                <span>Daftar Jabatan</span>
            </button>
            <button type="button"
                wire:click="setTab('bagan')"
                class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 {{ $activeTab === 'bagan' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main hover:bg-surface/80' }}">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11" />
                </svg>
                <span>Bagan Organigram</span>
            </button>
        </div>

        @can('kepegawaian.struktur_organisasi.kelola')
            <button type="button"
                wire:click="openCreateModal()"
                class="btn btn-primary text-xs py-2 px-3.5 flex items-center gap-1.5 shadow-xs">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Jabatan Baru</span>
            </button>
        @endcan
    </div>

    {{-- TAB 1: DAFTAR JABATAN HIERARKI --}}
    @if($activeTab === 'daftar')
        <div class="space-y-4">
            {{-- Filter & Search Bar --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="w-full sm:w-72">
                    <input type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari nama jabatan, kode, atau pejabat..."
                        class="w-full px-3 py-1.5 text-xs rounded-lg border border-border bg-surface text-text-main focus:border-primary focus:ring-1 focus:ring-primary shadow-xs">
                </div>
                <div class="text-[11px] text-text-muted flex items-center gap-2">
                    <span>Gunakan tombol <strong class="text-text-main">▲</strong> dan <strong class="text-text-main">▼</strong> untuk menaikkan/menurunkan urutan posisi jabatan.</span>
                </div>
            </div>

            {{-- Table Hierarki --}}
            <div class="bg-surface rounded-xl shadow-xs border border-border overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-text-secondary">
                        <thead class="bg-surface-hover text-xs uppercase text-text-main">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Jabatan & Struktur</th>
                                <th class="px-4 py-3 text-center font-semibold">Bobot Jam</th>
                                <th class="px-4 py-3 font-semibold">Atasan Langsung</th>
                                <th class="px-4 py-3 font-semibold">Pejabat Pengisi</th>
                                <th class="px-4 py-3 text-center font-semibold">Posisi</th>
                                <th class="px-4 py-3 text-center font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @forelse($treeData as $row)
                                @php
                                    $j = $row['jabatan'];
                                    $level = $row['level'];
                                    $isFirst = $row['is_first'];
                                    $isLast = $row['is_last'];
                                @endphp
                                <tr class="hover:bg-surface-hover/60 transition-colors group">
                                    {{-- Kolom 1: Hierarki Jabatan --}}
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-1.5" style="padding-left: {{ $level * 22 }}px;">
                                            @if($level > 0)
                                                <span class="text-text-muted/60 font-mono text-xs select-none">
                                                    {{ $isLast ? '└──' : '├──' }}
                                                </span>
                                            @endif
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="font-bold text-text-main text-xs sm:text-sm">{{ $j->nama }}</span>
                                                @if($j->kode)
                                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-mono font-bold bg-surface-hover text-text-muted border border-border">
                                                        {{ $j->kode }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        @if($j->keterangan)
                                            <div class="text-[11px] text-text-muted mt-0.5" style="padding-left: {{ $level * 22 + ($level > 0 ? 24 : 0) }}px;">
                                                {{ $j->keterangan }}
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Kolom 2: Bobot Jam --}}
                                    <td class="px-4 py-3 text-center">
                                        @if($j->bobot_jam > 0)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-900">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                {{ number_format($j->bobot_jam, 1) }} Jam
                                            </span>
                                        @else
                                            <span class="text-xs text-text-muted">-</span>
                                        @endif
                                    </td>

                                    {{-- Kolom 3: Atasan Langsung --}}
                                    <td class="px-4 py-3 text-xs">
                                        @if($j->parent)
                                            <span class="font-medium text-text-main">{{ $j->parent->nama }}</span>
                                        @else
                                            <span class="text-text-muted italic flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Puncak Struktur
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Kolom 4: Pejabat Pengisi --}}
                                    <td class="px-4 py-3">
                                        @if($j->pejabats->count() > 0)
                                            <div class="space-y-1">
                                                @foreach($j->pejabats as $p)
                                                    <div class="flex items-center gap-2 text-xs">
                                                        <div class="w-5 h-5 rounded-full {{ $p->isInternal() ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/60 dark:text-blue-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/60 dark:text-amber-300' }} flex items-center justify-center font-bold text-[10px] shrink-0">
                                                            {{ substr($p->nama_tampil, 0, 1) }}
                                                        </div>
                                                        <span class="font-medium text-text-main truncate max-w-[200px]" title="{{ $p->nama_tampil }}">
                                                            {{ $p->nama_tampil }}
                                                        </span>
                                                        @if(!$p->isInternal())
                                                            <span class="px-1 py-0.2 rounded text-[9px] bg-amber-50 text-amber-600 dark:bg-amber-950 border border-amber-200 dark:border-amber-800">
                                                                Luar
                                                            </span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-xs text-text-muted italic">Belum ditentukan</span>
                                        @endif
                                    </td>

                                    {{-- Kolom 5: Urutan & Tombol Naik/Turun --}}
                                    <td class="px-4 py-3 text-center">
                                        @can('kepegawaian.struktur_organisasi.kelola')
                                            <div class="inline-flex items-center gap-1 bg-surface-hover p-1 rounded-lg border border-border">
                                                <button type="button"
                                                    wire:click="moveUp({{ $j->id }})"
                                                    @if($isFirst) disabled @endif
                                                    class="p-1 rounded text-text-secondary hover:text-text-main hover:bg-surface disabled:opacity-30 disabled:cursor-not-allowed transition-colors"
                                                    title="Pindahkan ke Atas">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                                    </svg>
                                                </button>
                                                <button type="button"
                                                    wire:click="moveDown({{ $j->id }})"
                                                    @if($isLast) disabled @endif
                                                    class="p-1 rounded text-text-secondary hover:text-text-main hover:bg-surface disabled:opacity-30 disabled:cursor-not-allowed transition-colors"
                                                    title="Pindahkan ke Bawah">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-xs text-text-muted">{{ $j->urutan }}</span>
                                        @endcan
                                    </td>

                                    {{-- Kolom 6: Aksi --}}
                                    <td class="px-4 py-3 text-center">
                                        <div class="inline-flex items-center gap-1">
                                            @can('kepegawaian.struktur_organisasi.kelola')
                                                {{-- Tambah Sub-Jabatan Langsung --}}
                                                <button type="button"
                                                    wire:click="openCreateModal({{ $j->id }})"
                                                    class="p-1.5 rounded-lg text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/60 transition-colors"
                                                    title="Tambah Sub-Jabatan di bawah {{ $j->nama }}">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </button>

                                                {{-- Edit Jabatan --}}
                                                <button type="button"
                                                    wire:click="openEditModal({{ $j->id }})"
                                                    class="p-1.5 rounded-lg text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-950/60 transition-colors"
                                                    title="Edit Jabatan">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>

                                                {{-- Hapus Jabatan --}}
                                                <button type="button"
                                                    wire:click="delete({{ $j->id }})"
                                                    wire:confirm="Apakah Anda yakin ingin menghapus jabatan '{{ $j->nama }}'?"
                                                    class="p-1.5 rounded-lg text-red-600 hover:bg-red-50 dark:hover:bg-red-950/60 transition-colors"
                                                    title="Hapus Jabatan">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-12 text-center text-text-secondary">
                                        <div class="max-w-sm mx-auto flex flex-col items-center">
                                            <div class="w-12 h-12 rounded-full bg-surface-hover flex items-center justify-center text-text-muted mb-3">
                                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                            </div>
                                            <h3 class="text-sm font-bold text-text-main">Belum Ada Struktur Jabatan</h3>
                                            <p class="text-xs text-text-muted mt-1 text-center">Silakan klik tombol "Tambah Jabatan Baru" untuk menyusun struktur organisasi sekolah.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    {{-- TAB 2: BAGAN ORGANIGRAM VISUAL --}}
    @if($activeTab === 'bagan')
        <div class="space-y-4" x-data="{ zoom: 1, reset() { this.zoom = 1; }, zoomIn() { if (this.zoom < 1.6) this.zoom += 0.1; }, zoomOut() { if (this.zoom > 0.6) this.zoom -= 0.1; } }">
            {{-- Toolbar Kontrol Bagan --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-3 bg-surface rounded-xl border border-border">
                <div class="flex items-center gap-2 text-xs text-text-secondary">
                    <span class="font-semibold text-text-main">Navigasi Bagan:</span>
                    <span>Gunakan tombol perbesar/perkecil atau geser secara horizontal.</span>
                </div>
                <div class="flex items-center gap-1.5 self-start sm:self-auto">
                    <button type="button" @click="zoomOut()" class="p-1.5 rounded-lg border border-border bg-surface-hover text-text-main hover:bg-surface transition" title="Perkecil (-)">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" /></svg>
                    </button>
                    <span class="text-xs font-mono px-2 text-text-muted" x-text="Math.round(zoom * 100) + '%'">100%</span>
                    <button type="button" @click="zoomIn()" class="p-1.5 rounded-lg border border-border bg-surface-hover text-text-main hover:bg-surface transition" title="Perbesar (+)">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    </button>
                    <button type="button" @click="reset()" class="btn btn-secondary !py-1 !px-2.5 !text-xs ml-1">
                        Reset
                    </button>
                </div>
            </div>

            {{-- Area Kanvas Pohon Organigram --}}
            <div class="bg-surface rounded-xl border border-border p-6 overflow-x-auto min-h-[460px] shadow-xs">
                @if(empty($nestedTree))
                    <div class="py-16 text-center text-text-secondary">
                        <div class="max-w-sm mx-auto flex flex-col items-center">
                            <div class="w-12 h-12 rounded-full bg-surface-hover flex items-center justify-center text-text-muted mb-3">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11" /></svg>
                            </div>
                            <h3 class="text-sm font-bold text-text-main">Belum Ada Data Bagan</h3>
                            <p class="text-xs text-text-muted mt-1">Tambahkan jabatan pada tab "Daftar Jabatan" untuk melihat visualisasi bagan organigram.</p>
                        </div>
                    </div>
                @else
                    <div class="inline-block min-w-full text-center transition-transform origin-top" :style="'transform: scale(' + zoom + ')'">
                        <div class="organigram-tree flex justify-center gap-8">
                            @foreach($nestedTree as $rootNode)
                                @include('kepegawaian::livewire.partials.organigram-node', ['node' => $rootNode])
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- MODAL TAMBAH / EDIT JABATAN --}}
    @if($isModalOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-surface rounded-2xl border border-border shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto" @click.away="$wire.closeModal()">
                {{-- Modal Header --}}
                <div class="px-5 py-4 border-b border-border flex items-center justify-between sticky top-0 bg-surface z-10">
                    <h3 class="text-base font-bold text-text-main flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span>{{ $editingJabatanId ? 'Edit Jabatan' : 'Tambah Jabatan Baru' }}</span>
                    </h3>
                    <button type="button" wire:click="closeModal" class="p-1 rounded-lg text-text-muted hover:text-text-main hover:bg-surface-hover transition">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="p-5 space-y-4">
                    {{-- Nama & Kode Jabatan --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-text-main mb-1">
                                Nama Jabatan <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                wire:model="nama"
                                placeholder="Misal: Wakasek Kurikulum, Kepala Lab"
                                class="w-full px-3 py-2 text-xs rounded-lg border border-border bg-surface text-text-main focus:border-primary focus:ring-1 focus:ring-primary shadow-xs">
                            @error('nama') <span class="text-[11px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-text-main mb-1">Kode Singkat</label>
                            <input type="text"
                                wire:model="kode"
                                placeholder="Misal: WAKAKUR"
                                class="w-full px-3 py-2 text-xs rounded-lg border border-border bg-surface text-text-main focus:border-primary focus:ring-1 focus:ring-primary shadow-xs font-mono uppercase">
                            @error('kode') <span class="text-[11px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Atasan Langsung & Bobot Jam --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-text-main mb-1">Atasan Langsung (Parent)</label>
                            <select wire:model="parent_id" class="w-full px-3 py-2 text-xs rounded-lg border border-border bg-surface text-text-main focus:border-primary focus:ring-1 focus:ring-primary shadow-xs">
                                <option value="">-- Puncak Struktur (Tidak Memiliki Atasan) --</option>
                                @foreach($parentOptions as $po)
                                    <option value="{{ $po->id }}">{{ $po->nama }} {{ $po->kode ? '('.$po->kode.')' : '' }}</option>
                                @endforeach
                            </select>
                            <span class="text-[10px] text-text-muted mt-1 block">Pilih jabatan yang menjadi atasan langsung posisi ini.</span>
                            @error('parent_id') <span class="text-[11px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-text-main mb-1">Bobot Jam (Ekuivalensi)</label>
                            <div class="relative">
                                <input type="number"
                                    step="0.5"
                                    wire:model="bobot_jam"
                                    placeholder="0"
                                    class="w-full px-3 py-2 text-xs rounded-lg border border-border bg-surface text-text-main focus:border-primary focus:ring-1 focus:ring-primary shadow-xs pr-12">
                                <span class="absolute right-3 top-2 text-xs text-text-muted font-semibold">Jam</span>
                            </div>
                            @error('bobot_jam') <span class="text-[11px] text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Keterangan --}}
                    <div>
                        <label class="block text-xs font-semibold text-text-main mb-1">Keterangan / Ruang Lingkup</label>
                        <textarea wire:model="keterangan" rows="2" placeholder="Catatan tugas atau bidang koordinasi jabatan..." class="w-full px-3 py-2 text-xs rounded-lg border border-border bg-surface text-text-main focus:border-primary focus:ring-1 focus:ring-primary shadow-xs"></textarea>
                    </div>

                    {{-- Pejabat Pengisi (Repeater) --}}
                    <div class="pt-3 border-t border-border space-y-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-xs font-bold text-text-main flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    Pejabat Pengisi Jabatan
                                </h4>
                                <p class="text-[11px] text-text-muted">Dapat diisi dari Pegawai terdata atau nama manual pihak luar (eksternal).</p>
                            </div>
                            <button type="button"
                                wire:click="addPejabatRow"
                                class="btn btn-secondary !py-1 !px-2.5 !text-xs flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                <span>Tambah Pejabat</span>
                            </button>
                        </div>

                        <div class="space-y-2.5">
                            @foreach($pejabatList as $idx => $pejabat)
                                <div class="p-3 rounded-xl border border-border bg-surface-hover/40 space-y-2">
                                    <div class="flex items-center justify-between text-[11px] font-semibold text-text-secondary">
                                        <span>Pejabat #{{ $idx + 1 }}</span>
                                        @if(count($pejabatList) > 1)
                                            <button type="button" wire:click="removePejabatRow({{ $idx }})" class="text-red-500 hover:text-red-700 font-bold transition">
                                                Hapus Baris
                                            </button>
                                        @endif
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-[10px] text-text-muted mb-0.5">Pegawai Internal (Terdata di GTK)</label>
                                            <select wire:model="pejabatList.{{ $idx }}.pegawai_id" class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-border bg-surface text-text-main shadow-2xs">
                                                <option value="">-- Pilih Pegawai Terdaftar --</option>
                                                @foreach($pegawaiOptions as $p)
                                                    <option value="{{ $p->id }}">{{ $p->nama_bergelar ?: $p->nama_lengkap }} ({{ $p->jenis_gtk }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] text-text-muted mb-0.5">ATAU Nama Pejabat Luar (Eksternal)</label>
                                            <input type="text"
                                                wire:model="pejabatList.{{ $idx }}.nama_pejabat"
                                                placeholder="Isi jika pihak eksternal/non-GTK..."
                                                class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-border bg-surface text-text-main shadow-2xs">
                                        </div>
                                    </div>
                                    <div>
                                        <input type="text"
                                            wire:model="pejabatList.{{ $idx }}.keterangan"
                                            placeholder="Catatan tambahan pejabat (misal: Periode 2026/2027, Pembina 1)..."
                                            class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-border bg-surface text-text-main shadow-2xs">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="px-5 py-3.5 border-t border-border bg-surface-hover/30 flex items-center justify-end gap-2 sticky bottom-0 bg-surface z-10">
                    <button type="button" wire:click="closeModal" class="btn btn-secondary text-xs py-2 px-3.5">
                        Batal
                    </button>
                    <button type="button" wire:click="save" class="btn btn-primary text-xs py-2 px-4 font-bold shadow-xs">
                        Simpan Jabatan
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
