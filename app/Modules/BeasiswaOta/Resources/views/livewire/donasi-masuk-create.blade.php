<div class="space-y-6 max-w-7xl mx-auto pb-16">
    @include('beasiswaota::partials.sub-nav', ['active' => 'donasi-masuk'])

    <!-- Header & Tombol Navigasi Kembali -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-border">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('beasiswa-ota.donasi-masuk') }}" wire:navigate class="btn btn-ghost btn-sm text-xs gap-1.5 text-text-secondary hover:text-text-main px-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali ke Daftar Donasi
                </a>
            </div>
            <h1 class="text-2xl font-black text-text-main tracking-tight">Catat Penerimaan Donasi Masuk</h1>
            <p class="text-sm text-text-secondary">Formulir lengkap pencatatan donasi perorangan atau kolektif kelompok untuk beberapa bulan sekaligus.</p>
        </div>
    </div>

    <!-- Alert Notifikasi Error -->
    @if(session()->has('error'))
        <div class="p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-2xl text-rose-800 dark:text-rose-200 text-sm flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 text-lg font-bold">&times;</button>
        </div>
    @endif

    <!-- Card 1: Pemilihan Tahun Ajaran & Mode Input -->
    <div class="card border border-border rounded-2xl p-5 bg-surface shadow-xs space-y-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <!-- Pilihan Tahun Ajaran -->
            <div class="flex flex-wrap items-center gap-3">
                <span class="text-xs font-bold text-text-secondary uppercase tracking-wider flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Tahun Ajaran:
                </span>
                <select wire:model.live="tahunAjaranId" class="select select-bordered select-sm text-sm font-bold w-48">
                    @foreach($tahunAjarans as $ta)
                        <option value="{{ $ta->id }}">{{ $ta->nama }} {{ $ta->is_active ? '(Aktif)' : '' }}</option>
                    @endforeach
                </select>
                <span class="text-xs text-text-secondary">Siklus: <strong>Juli s.d. Juni</strong></span>
            </div>

            <!-- Mode Switcher -->
            <div class="flex p-1 bg-surface-hover/80 rounded-xl border border-border">
                <button type="button" 
                        wire:click="$set('modeDonasi', 'perorangan')" 
                        class="py-2 px-4 rounded-lg text-xs font-bold transition flex items-center gap-2 {{ $modeDonasi === 'perorangan' ? 'bg-surface text-primary shadow-xs border border-border' : 'text-text-secondary hover:text-text-main' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Donasi Perorangan (Individu)
                </button>
                <button type="button" 
                        wire:click="$set('modeDonasi', 'kelompok')" 
                        class="py-2 px-4 rounded-lg text-xs font-bold transition flex items-center gap-2 {{ $modeDonasi === 'kelompok' ? 'bg-surface text-primary shadow-xs border border-border' : 'text-text-secondary hover:text-text-main' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Per Kelompok (via Koordinator)
                </button>
            </div>
        </div>
    </div>

    <!-- ================= MODE 1: DONASI PERORANGAN (INDIVIDU) ================= -->
    @if($modeDonasi === 'perorangan')
        <div class="card border border-border rounded-2xl p-6 bg-surface shadow-xs space-y-6">
            <div class="border-b border-border pb-3">
                <h2 class="text-base font-bold text-text-main">Data Donasi Perorangan</h2>
                <p class="text-xs text-text-secondary">Penyetoran langsung oleh donatur perorangan untuk iuran rutin kelompok atau donasi insidental.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-text-secondary uppercase mb-1">
                        Pilih Donatur <span class="text-rose-500">*</span>
                    </label>
                    <select wire:model.live="donaturId" class="select select-bordered w-full text-sm font-semibold">
                        <option value="">-- Pilih Donatur --</option>
                        @foreach($donaturs as $don)
                            <option value="{{ $don->id }}">
                                {{ $don->nama }} ({{ ucfirst($don->tipe) }}) {{ $don->nomor_telepon ? '- ' . $don->nomor_telepon : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('donaturId') <span class="text-xs text-rose-500 block mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-text-secondary uppercase mb-1">
                        Pos Kas / Kelompok Penerima
                    </label>
                    <select wire:model="kelompokId" class="select select-bordered w-full text-sm">
                        <option value="">-- Kas Bebas / Insidental --</option>
                        @foreach($kelompoks as $klp)
                            <option value="{{ $klp->id }}">
                                {{ $klp->nama_kelompok }} ({{ $klp->kode_kelompok }})
                            </option>
                        @endforeach
                    </select>
                    <span class="text-[11px] text-text-secondary mt-1 block">
                        Jika donatur rutin kelompok, otomatis dialokasikan ke kas kelompok terkait.
                    </span>
                </div>
            </div>

            <!-- PEMILIHAN BULAN MATRIX PERORANGAN (JIKA DONATUR RUTIN) -->
            @if($komitmenBulananPerorangan > 0)
                <div class="p-5 bg-surface-hover/50 rounded-2xl border border-border space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2 border-b border-border">
                        <div>
                            <div class="text-sm font-bold text-text-main flex items-center gap-2">
                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Checklist Periode Bulan yang Disetor
                            </div>
                            <div class="text-xs text-text-secondary mt-0.5">
                                Iuran Komitmen: <strong class="text-primary">Rp {{ number_format($komitmenBulananPerorangan, 0, ',', '.') }} / bulan</strong>. Bulan bertanda <span class="text-emerald-600 font-bold">Lunas</span> telah disetorkan sebelumnya.
                            </div>
                        </div>

                        <!-- Tombol Pintas Preset Cepat -->
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-[11px] font-bold text-text-secondary uppercase">Preset Cepat:</span>
                            <button type="button" wire:click="applyPresetPerorangan('ganjil')" class="btn btn-xs btn-outline border-blue-300 text-blue-700 dark:text-blue-300 hover:bg-blue-50 font-semibold">
                                + Sem. Ganjil (6 Bln)
                            </button>
                            <button type="button" wire:click="applyPresetPerorangan('genap')" class="btn btn-xs btn-outline border-indigo-300 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-50 font-semibold">
                                + Sem. Genap (6 Bln)
                            </button>
                            <button type="button" wire:click="applyPresetPerorangan('full')" class="btn btn-xs btn-outline border-purple-300 text-purple-700 dark:text-purple-300 hover:bg-purple-50 font-semibold">
                                + 1 Th Penuh (12 Bln)
                            </button>
                            <button type="button" wire:click="applyPresetPerorangan('clear')" class="btn btn-xs btn-ghost text-rose-600 hover:bg-rose-50" title="Bersihkan pilihan bulan">
                                Reset
                            </button>
                        </div>
                    </div>

                    <!-- 12 Bulan Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Semester Ganjil -->
                        <div class="p-3 bg-surface rounded-xl border border-border space-y-2">
                            <div class="text-xs font-bold text-text-secondary uppercase tracking-wider flex items-center justify-between">
                                <span>Semester Ganjil (Juli - Desember)</span>
                                <span class="text-[10px] text-primary">T.A. {{ $this->availableMonths['tahun_ajaran']?->nama }}</span>
                            </div>
                            <div class="grid grid-cols-3 gap-2">
                                @foreach($this->availableMonths['ganjil'] as $bm)
                                    @php
                                        $isPaid = in_array($bm['key'], $paidMonthsPerorangan);
                                        $isSelected = in_array($bm['key'], $selectedBulanPerorangan);
                                    @endphp
                                    <button type="button" 
                                            wire:click="toggleBulanPerorangan('{{ $bm['key'] }}')"
                                            @disabled($isPaid)
                                            class="py-2.5 px-2 rounded-xl text-xs font-bold text-center border transition flex flex-col items-center justify-center gap-0.5
                                            {{ $isPaid ? 'bg-emerald-50 border-emerald-200 text-emerald-700 dark:bg-emerald-950/40 dark:border-emerald-800 dark:text-emerald-300 cursor-not-allowed opacity-80' : ($isSelected ? 'bg-primary text-white border-primary shadow-xs ring-2 ring-primary/20' : 'bg-surface hover:bg-surface-hover border-border text-text-main') }}">
                                        <span class="text-sm font-black">{{ $bm['short'] }}</span>
                                        <span class="text-[10px] font-normal {{ $isSelected ? 'text-white/80' : 'text-text-secondary' }}">{{ $bm['year'] }}</span>
                                        <span class="text-[9px] font-semibold mt-0.5 {{ $isPaid ? 'text-emerald-700 dark:text-emerald-300 font-bold' : ($isSelected ? 'text-white' : 'text-primary') }}">
                                            {{ $isPaid ? '✓ LUNAS' : ($isSelected ? '✓ TERPILIH' : '+ PILIH') }}
                                        </span>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- Semester Genap -->
                        <div class="p-3 bg-surface rounded-xl border border-border space-y-2">
                            <div class="text-xs font-bold text-text-secondary uppercase tracking-wider flex items-center justify-between">
                                <span>Semester Genap (Januari - Juni)</span>
                                <span class="text-[10px] text-primary">T.A. {{ $this->availableMonths['tahun_ajaran']?->nama }}</span>
                            </div>
                            <div class="grid grid-cols-3 gap-2">
                                @foreach($this->availableMonths['genap'] as $bm)
                                    @php
                                        $isPaid = in_array($bm['key'], $paidMonthsPerorangan);
                                        $isSelected = in_array($bm['key'], $selectedBulanPerorangan);
                                    @endphp
                                    <button type="button" 
                                            wire:click="toggleBulanPerorangan('{{ $bm['key'] }}')"
                                            @disabled($isPaid)
                                            class="py-2.5 px-2 rounded-xl text-xs font-bold text-center border transition flex flex-col items-center justify-center gap-0.5
                                            {{ $isPaid ? 'bg-emerald-50 border-emerald-200 text-emerald-700 dark:bg-emerald-950/40 dark:border-emerald-800 dark:text-emerald-300 cursor-not-allowed opacity-80' : ($isSelected ? 'bg-primary text-white border-primary shadow-xs ring-2 ring-primary/20' : 'bg-surface hover:bg-surface-hover border-border text-text-main') }}">
                                        <span class="text-sm font-black">{{ $bm['short'] }}</span>
                                        <span class="text-[10px] font-normal {{ $isSelected ? 'text-white/80' : 'text-text-secondary' }}">{{ $bm['year'] }}</span>
                                        <span class="text-[9px] font-semibold mt-0.5 {{ $isPaid ? 'text-emerald-700 dark:text-emerald-300 font-bold' : ($isSelected ? 'text-white' : 'text-primary') }}">
                                            {{ $isPaid ? '✓ LUNAS' : ($isSelected ? '✓ TERPILIH' : '+ PILIH') }}
                                        </span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Ringkasan Periode Terpilih -->
                    @if(!empty($selectedBulanPerorangan))
                        <div class="p-3 bg-primary/10 border border-primary/20 rounded-xl text-xs text-primary flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <span class="font-bold">Periode Terpilih: {{ count($selectedBulanPerorangan) }} Bulan</span>
                                <div class="text-[11px] opacity-80 mt-0.5">
                                    {{ self::formatPeriodeKeterangan($selectedBulanPerorangan, $tahunAjarans->firstWhere('id', $tahunAjaranId)) }}
                                </div>
                            </div>
                            <div class="text-sm font-extrabold text-primary shrink-0">
                                {{ count($selectedBulanPerorangan) }} &times; Rp {{ number_format($komitmenBulananPerorangan, 0, ',', '.') }} = Rp {{ number_format(count($selectedBulanPerorangan) * $komitmenBulananPerorangan, 0, ',', '.') }}
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-text-secondary uppercase mb-1">
                        Tanggal Penyetoran <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" wire:model="tanggalDonasi" class="input input-bordered w-full text-sm">
                    @error('tanggalDonasi') <span class="text-xs text-rose-500 block mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-text-secondary uppercase mb-1">
                        Total Nominal Donasi (Rp) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" inputmode="numeric" 
                           wire:model.live.debounce.300ms="nominal" 
                           class="input input-bordered w-full text-sm font-bold text-emerald-600 dark:text-emerald-400" 
                           placeholder="Misal: 150000">
                    @if(!empty($nominal))
                        <span class="text-[11px] text-primary font-bold block mt-1">
                            = Rp {{ number_format((float) \App\Modules\BeasiswaOta\Livewire\DonasiMasukCreate::cleanNominal($nominal), 0, ',', '.') }}
                        </span>
                    @endif
                    @error('nominal') <span class="text-xs text-rose-500 block mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-text-secondary uppercase mb-1">Keterangan / Catatan Transaksi</label>
                <textarea wire:model="keterangan" rows="2" class="textarea textarea-bordered w-full text-sm" placeholder="Misal: Iuran Semester Ganjil, Titipan Donasi, dll."></textarea>
            </div>
        </div>
    @endif

    <!-- ================= MODE 2: PER KELOMPOK (KOLEKTIF VIA KOORDINATOR) ================= -->
    @if($modeDonasi === 'kelompok')
        <div class="card border border-border rounded-2xl p-6 bg-surface shadow-xs space-y-6">
            <div class="border-b border-border pb-3">
                <h2 class="text-base font-bold text-text-main">Setoran Kolektif Kelompok Donatur</h2>
                <p class="text-xs text-text-secondary">Penyetoran iuran bersama seluruh anggota kelompok yang dikumpulkan melalui Koordinator.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-text-secondary uppercase mb-1">
                        Pilih Kelompok Donatur <span class="text-rose-500">*</span>
                    </label>
                    <select wire:model.live="kelompokKolektifId" class="select select-bordered w-full text-sm font-bold">
                        <option value="">-- Pilih Kelompok --</option>
                        @foreach($kelompoks as $klp)
                            <option value="{{ $klp->id }}">
                                {{ $klp->nama_kelompok }} (Target: Rp {{ number_format($klp->target_nominal, 0, ',', '.') }})
                            </option>
                        @endforeach
                    </select>
                    @error('kelompokKolektifId') <span class="text-xs text-rose-500 block mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-text-secondary uppercase mb-1">
                        Tanggal Penyetoran <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" wire:model="tanggalDonasiKolektif" class="input input-bordered w-full text-sm">
                    @error('tanggalDonasiKolektif') <span class="text-xs text-rose-500 block mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Input Koordinator Penyetor -->
            <div class="p-4 bg-surface-hover/50 rounded-2xl border border-border space-y-3">
                <div>
                    <span class="text-xs font-bold text-text-main uppercase tracking-wider">
                        Koordinator / PIC Penyetor Kelompok <span class="text-rose-500">*</span>
                    </span>
                    <p class="text-[11px] text-text-secondary">Koordinator yang mengumpulkan dan menyerahkan dana donatur ke sekolah.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 items-center">
                    @if(!empty($anggotaItems))
                        <div>
                            <label class="block text-[11px] text-text-secondary mb-1">Pilih dari Anggota Kelompok:</label>
                            <select wire:model.live="koordinatorDonaturId" class="select select-bordered select-sm w-full text-xs">
                                <option value="">-- Ketik Nama Manual --</option>
                                @foreach($anggotaItems as $ai)
                                    <option value="{{ $ai['donatur_id'] }}">{{ $ai['nama'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div>
                        <label class="block text-[11px] text-text-secondary mb-1">Nama Koordinator / Penyetor Resmi:</label>
                        <input type="text" wire:model="koordinatorNama" class="input input-bordered input-sm w-full text-xs font-semibold" placeholder="Nama Koordinator...">
                        @error('koordinatorNama') <span class="text-xs text-rose-500 block mt-0.5">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            @if(!empty($anggotaItems))
                <!-- TOOLBAR PRESET MASSAL KELOMPOK -->
                <div class="p-4 bg-indigo-50/70 dark:bg-indigo-950/30 border border-indigo-200 dark:border-indigo-800 rounded-2xl flex flex-col md:flex-row md:items-center justify-between gap-3">
                    <div>
                        <div class="text-xs font-bold text-indigo-900 dark:text-indigo-200 uppercase tracking-wider flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Terapkan Periode Bulan ke Semua Anggota Sekaligus:
                        </div>
                        <p class="text-[11px] text-indigo-700 dark:text-indigo-300 mt-0.5">
                            Klik tombol pintas di samping untuk mengisi matrix seluruh anggota yang dicentang secara serentak.
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 shrink-0">
                        <button type="button" wire:click="applyBulkMonths('ganjil')" class="btn btn-xs btn-primary font-semibold">
                            + Sem. Ganjil (6 Bln)
                        </button>
                        <button type="button" wire:click="applyBulkMonths('genap')" class="btn btn-xs btn-primary font-semibold">
                            + Sem. Genap (6 Bln)
                        </button>
                        <button type="button" wire:click="applyBulkMonths('full')" class="btn btn-xs btn-primary font-semibold">
                            + 1 Th Penuh (12 Bln)
                        </button>
                        <button type="button" wire:click="applyBulkMonths('clear')" class="btn btn-xs btn-ghost text-rose-600 hover:bg-rose-50 font-semibold">
                            Reset Semua
                        </button>
                    </div>
                </div>

                <!-- TABEL MATRIX SPREADSHEET 12 BULAN -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="font-bold text-xs text-text-main uppercase tracking-wider">
                            Matrix Rincian Iuran & Bulan per Anggota Donatur:
                        </div>
                        <label class="flex items-center gap-1.5 text-xs text-text-secondary cursor-pointer shrink-0 select-none">
                            <input type="checkbox" wire:model.live="selectAllAnggotaKolektif" class="checkbox checkbox-primary checkbox-xs">
                            <span class="font-semibold">Centang Semua Anggota ({{ count($anggotaItems) }})</span>
                        </label>
                    </div>

                    <div class="border border-border rounded-2xl overflow-x-auto shadow-xs">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-surface-hover/80 text-text-secondary uppercase border-b border-border text-[11px]">
                                    <th rowspan="2" class="py-2.5 px-3 text-center border-r border-border" style="width: 35px;">✓</th>
                                    <th rowspan="2" class="py-2.5 px-3 border-r border-border min-w-[200px]">Donatur & Komitmen</th>
                                    <th rowspan="2" class="py-2.5 px-2 text-center border-r border-border min-w-[150px]">Pintas Baris</th>
                                    <th colspan="6" class="py-1 px-2 text-center bg-blue-50/50 dark:bg-blue-950/20 border-r border-border font-bold text-blue-900 dark:text-blue-200">
                                        Semester Ganjil (T.A. {{ $this->availableMonths['tahun_ajaran']?->nama }})
                                    </th>
                                    <th colspan="6" class="py-1 px-2 text-center bg-indigo-50/50 dark:bg-indigo-950/20 border-r border-border font-bold text-indigo-900 dark:text-indigo-200">
                                        Semester Genap (T.A. {{ $this->availableMonths['tahun_ajaran']?->nama }})
                                    </th>
                                    <th rowspan="2" class="py-2.5 px-2 text-center border-r border-border min-w-[65px]">Bulan</th>
                                    <th rowspan="2" class="py-2.5 px-3 text-right min-w-[130px]">Nominal (Rp)</th>
                                </tr>
                                <tr class="bg-surface-hover/50 text-[10px] text-text-secondary border-b border-border">
                                    <!-- Kolom Ganjil: Jul, Agt, Sep, Okt, Nov, Des -->
                                    @foreach($this->availableMonths['ganjil'] as $bm)
                                        <th class="py-1.5 px-1 text-center border-r border-border/60 bg-blue-50/30 dark:bg-blue-950/10 min-w-[38px]">{{ $bm['short'] }}</th>
                                    @endforeach
                                    <!-- Kolom Genap: Jan, Feb, Mar, Apr, Mei, Jun -->
                                    @foreach($this->availableMonths['genap'] as $bm)
                                        <th class="py-1.5 px-1 text-center border-r border-border/60 bg-indigo-50/30 dark:bg-indigo-950/10 min-w-[38px]">{{ $bm['short'] }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                @foreach($anggotaItems as $idx => $item)
                                    @php
                                        $isSelected = !empty($item['is_selected']);
                                        $selCount = count($item['selected_bulan'] ?? []);
                                    @endphp
                                    <tr class="hover:bg-surface-hover/40 transition {{ $isSelected ? 'bg-surface' : 'opacity-50 bg-surface-hover/20' }}">
                                        <!-- Checkbox Pilih -->
                                        <td class="py-2 px-3 text-center border-r border-border">
                                            <input type="checkbox" wire:model.live="anggotaItems.{{ $idx }}.is_selected" class="checkbox checkbox-primary checkbox-xs">
                                        </td>

                                        <!-- Info Donatur -->
                                        <td class="py-2 px-3 border-r border-border">
                                            <div class="font-bold text-text-main text-xs">{{ $item['nama'] }}</div>
                                            <div class="text-[10px] text-text-secondary flex items-center gap-1.5">
                                                <span>Komitmen: <strong class="text-primary">Rp {{ number_format($item['nominal_komitmen'], 0, ',', '.') }}</strong>/bln</span>
                                                @if(!empty($item['nomor_telepon']))
                                                    &bull; <span>{{ $item['nomor_telepon'] }}</span>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Pintas Baris -->
                                        <td class="py-2 px-2 text-center border-r border-border">
                                            <div class="flex items-center justify-center gap-1">
                                                <button type="button" wire:click="applyMemberPreset({{ $idx }}, 'ganjil')" class="btn btn-xs btn-outline text-[9px] px-1.5 py-0 h-5 min-h-0 border-blue-200 text-blue-700 hover:bg-blue-50" title="Centang 6 Bulan Ganjil">Ganjil</button>
                                                <button type="button" wire:click="applyMemberPreset({{ $idx }}, 'genap')" class="btn btn-xs btn-outline text-[9px] px-1.5 py-0 h-5 min-h-0 border-indigo-200 text-indigo-700 hover:bg-indigo-50" title="Centang 6 Bulan Genap">Genap</button>
                                                <button type="button" wire:click="applyMemberPreset({{ $idx }}, 'full')" class="btn btn-xs btn-outline text-[9px] px-1.5 py-0 h-5 min-h-0 border-purple-200 text-purple-700 hover:bg-purple-50" title="Centang 12 Bulan Penuh">1 Th</button>
                                                <button type="button" wire:click="applyMemberPreset({{ $idx }}, 'clear')" class="btn btn-xs btn-ghost text-[9px] px-1 py-0 h-5 min-h-0 text-rose-500 hover:bg-rose-50" title="Bersihkan">✕</button>
                                            </div>
                                        </td>

                                        <!-- Sel Bulan Ganjil: Jul, Agt, Sep, Okt, Nov, Des -->
                                        @foreach($this->availableMonths['ganjil'] as $bm)
                                            @php
                                                $isPaid = in_array($bm['key'], $item['paid_bulan'] ?? []);
                                                $isMonthSelected = in_array($bm['key'], $item['selected_bulan'] ?? []);
                                            @endphp
                                            <td class="py-1 px-1 text-center border-r border-border/60">
                                                @if($isPaid)
                                                    <span class="inline-block px-1 py-0.5 rounded text-[8px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 cursor-not-allowed select-none" title="{{ $bm['name'] }} sudah Lunas">
                                                        Lunas
                                                    </span>
                                                @else
                                                    <button type="button" 
                                                            wire:click="toggleMemberMonth({{ $idx }}, '{{ $bm['key'] }}')"
                                                            class="w-7 h-7 rounded-lg text-[10px] font-bold border transition flex items-center justify-center mx-auto
                                                            {{ $isMonthSelected ? 'bg-emerald-600 text-white border-emerald-600 shadow-xs ring-1 ring-emerald-400' : 'bg-surface hover:bg-primary/20 border-border text-text-secondary hover:text-primary' }}"
                                                            title="{{ $bm['label'] }}: {{ $isMonthSelected ? 'Dicentang (Klik untuk batalkan)' : 'Belum dipilih (Klik untuk centang)' }}">
                                                        {{ $isMonthSelected ? '✓' : $bm['short'] }}
                                                    </button>
                                                @endif
                                            </td>
                                        @endforeach

                                        <!-- Sel Bulan Genap: Jan, Feb, Mar, Apr, Mei, Jun -->
                                        @foreach($this->availableMonths['genap'] as $bm)
                                            @php
                                                $isPaid = in_array($bm['key'], $item['paid_bulan'] ?? []);
                                                $isMonthSelected = in_array($bm['key'], $item['selected_bulan'] ?? []);
                                            @endphp
                                            <td class="py-1 px-1 text-center border-r border-border/60">
                                                @if($isPaid)
                                                    <span class="inline-block px-1 py-0.5 rounded text-[8px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 cursor-not-allowed select-none" title="{{ $bm['name'] }} sudah Lunas">
                                                        Lunas
                                                    </span>
                                                @else
                                                    <button type="button" 
                                                            wire:click="toggleMemberMonth({{ $idx }}, '{{ $bm['key'] }}')"
                                                            class="w-7 h-7 rounded-lg text-[10px] font-bold border transition flex items-center justify-center mx-auto
                                                            {{ $isMonthSelected ? 'bg-emerald-600 text-white border-emerald-600 shadow-xs ring-1 ring-emerald-400' : 'bg-surface hover:bg-primary/20 border-border text-text-secondary hover:text-primary' }}"
                                                            title="{{ $bm['label'] }}: {{ $isMonthSelected ? 'Dicentang (Klik untuk batalkan)' : 'Belum dipilih (Klik untuk centang)' }}">
                                                        {{ $isMonthSelected ? '✓' : $bm['short'] }}
                                                    </button>
                                                @endif
                                            </td>
                                        @endforeach

                                        <!-- Jumlah Bulan -->
                                        <td class="py-2 px-2 text-center border-r border-border">
                                            @if($selCount > 0)
                                                <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-extrabold bg-primary/10 text-primary">
                                                    {{ $selCount }} Bln
                                                </span>
                                            @else
                                                <span class="text-[10px] text-text-secondary italic">0</span>
                                            @endif
                                        </td>

                                        <!-- Input Nominal -->
                                        <td class="py-2 px-3 text-right">
                                            <input type="text" inputmode="numeric" 
                                                   wire:model.live.debounce.300ms="anggotaItems.{{ $idx }}.nominal" 
                                                   class="input input-bordered input-xs w-full text-right font-bold text-emerald-600 dark:text-emerald-400" 
                                                   placeholder="150000"
                                                   @disabled(!$isSelected)>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- PANEL RINGKASAN SETORAN KOLEKTIF -->
                @php
                    $selectedCount = collect($anggotaItems)->where('is_selected', true)->count();
                    $selectedKlp = $kelompoks->firstWhere('id', $kelompokKolektifId);
                    $targetKlp = (float) ($selectedKlp?->target_nominal ?? 0);
                    $selisih = $this->totalKolektif - $targetKlp;
                @endphp
                <div class="p-4 bg-indigo-50/70 dark:bg-indigo-950/30 border border-indigo-200 dark:border-indigo-800 rounded-2xl flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 text-xs text-indigo-900 dark:text-indigo-200">
                    <div>
                        <div class="font-bold text-sm">
                            <span class="text-primary">{{ $selectedCount }} dari {{ count($anggotaItems) }} Donatur Anggota</span> disetorkan via Koordinator <strong class="text-text-main">{{ $koordinatorNama ?: '-' }}</strong>.
                        </div>
                        <div class="text-xs text-text-secondary mt-1">
                            Target Kelompok: <strong>Rp {{ number_format($targetKlp, 0, ',', '.') }}</strong>
                            @if($selisih >= 0)
                                <span class="text-emerald-600 font-bold ml-1.5">(Surplus +Rp {{ number_format($selisih, 0, ',', '.') }})</span>
                            @else
                                <span class="text-amber-600 font-bold ml-1.5">(Kurang Rp {{ number_format(abs($selisih), 0, ',', '.') }})</span>
                            @endif
                        </div>
                    </div>
                    <div class="text-left sm:text-right">
                        <div class="text-[11px] uppercase font-bold text-text-secondary">Total Setoran Kolektif:</div>
                        <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight">
                            Rp {{ number_format($this->totalKolektif, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            @elseif($kelompokKolektifId)
                <div class="p-8 text-center text-sm text-text-secondary border border-dashed border-border rounded-2xl">
                    Kelompok ini belum memiliki anggota donatur rutin. Tambahkan anggota terlebih dahulu pada menu Donatur & Kelompok.
                </div>
            @endif

            <div>
                <label class="block text-xs font-bold text-text-secondary uppercase mb-1">Catatan Setoran Kolektif</label>
                <textarea wire:model="keteranganKolektif" rows="2" class="textarea textarea-bordered w-full text-sm" placeholder="Misal: Penyetoran iuran semester ganjil dari 14 donatur kelompok via Koordinator..."></textarea>
            </div>
        </div>
    @endif

    <!-- ================= AREA TOMBOL SIMPAN DI PALING BAWAH FORMULIR ================= -->
    <div class="card border border-border rounded-2xl p-5 bg-surface shadow-xs flex flex-col sm:flex-row justify-between items-center gap-4">
        <div>
            <a href="{{ route('beasiswa-ota.donasi-masuk') }}" wire:navigate class="btn btn-ghost text-sm text-text-secondary hover:text-text-main">
                &larr; Batalkan & Kembali ke Daftar
            </a>
        </div>

        <div class="flex items-center gap-3">
            @if($modeDonasi === 'perorangan')
                <button type="button" wire:click="save" class="btn btn-primary text-sm px-6 flex items-center gap-2" wire:loading.attr="disabled">
                    <span wire:loading wire:target="save" class="loading loading-spinner loading-xs"></span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan Donasi Perorangan
                </button>
            @else
                <button type="button" wire:click="save" 
                        class="btn btn-primary text-sm px-6 flex items-center gap-2" 
                        wire:loading.attr="disabled"
                        @disabled(empty($anggotaItems) || $this->totalKolektif <= 0)>
                    <span wire:loading wire:target="save" class="loading loading-spinner loading-xs"></span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan Donasi Kolektif (Rp {{ number_format($this->totalKolektif, 0, ',', '.') }})
                </button>
            @endif
        </div>
    </div>
</div>
