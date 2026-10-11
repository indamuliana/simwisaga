<div class="space-y-6">
 {{-- Header Halaman & Action Buttons --}}
 <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 print:hidden">
 <div>
 <h1 class="text-2xl font-bold tracking-tight text-text-main flex items-center gap-2">
 <svg class="w-7 h-7 text-primary dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
 </svg>
 Cetak Absensi / Daftar Hadir Murid
 </h1>
 <p class="text-sm text-text-secondary">
 Format resmi Portrait A4 standar SIRAGA dengan Kop Surat resmi, 30 kolom kotak presensi kosong, dan tanda tangan manual.
 </p>
 </div>
 <div class="flex flex-wrap items-center gap-2">
 @if($siswas->count() > 0)
 <button wire:click="cetakAbsensi"
 type="button"
 class="btn btn-primary gap-2">
 <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
 </svg>
 <span>PDF</span>
 </button>

 <button onclick="window.print()"
 type="button"
 class="btn btn-secondary">
 <svg class="w-4 h-4 text-text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
 </svg>
 <span>Browser</span>
 </button>

 <button wire:click="cetakWord"
 type="button"
 class="inline-flex items-center gap-2 px-3.5 py-2.5 text-sm font-semibold text-sky-700 dark:text-sky-300 bg-sky-50 dark:bg-sky-950/40 border border-sky-200 dark:border-sky-800 rounded-lg hover:bg-sky-100 dark:hover:bg-sky-900/50 shadow-sm transition">
 <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
 </svg>
 <span>.Docx</span>
 </button>

 <button wire:click="cetakExcel"
 type="button"
 class="inline-flex items-center gap-2 px-3.5 py-2.5 text-sm font-semibold text-emerald-700 text-success bg-success-soft dark:bg-emerald-950/40 border border-success rounded-lg hover:bg-emerald-100 dark:hover:bg-emerald-900/50 shadow-sm transition">
 <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
 </svg>
 <span>.xlsx</span>
 </button>
 @endif
 </div>
 </div>

 {{-- Loading indicator --}}
 <div wire:loading wire:target="cetakAbsensi, cetakExcel, cetakWord, cetakMassalRombel, cetakMassalRombelGender, cetakMassalProgram, cetakMassalProgramGender" class="p-3 bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800 rounded-xl text-indigo-800 dark:text-indigo-300 text-xs flex items-center gap-2.5 shadow-xs print:hidden">
 <svg class="animate-spin h-4 w-4 text-primary dark:text-indigo-400 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
 <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
 <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
 </svg>
 <span>Sedang menyiapkan berkas dokumen Daftar Hadir Portrait... Unduhan akan dimulai otomatis.</span>
 </div>

 @if (session('error'))
 <div class="p-4 text-sm text-rose-800 bg-rose-50 rounded-lg dark:bg-rose-950/50 dark:text-rose-300 border border-rose-200 dark:border-rose-800 print:hidden">
 {{ session('error') }}
 </div>
 @endif

 {{-- Barisan Tombol Cetak Praktis Massal (Horizontal Pills) --}}
 <div class="p-3.5 bg-surface border border-border rounded-xl shadow-xs print:hidden space-y-2">
 <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
 <div class="flex items-center gap-2">
 <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-indigo-500/10 text-primary dark:text-indigo-400">
 <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
 </svg>
 </span>
 <div>
 <h4 class="text-xs font-bold text-text-main uppercase tracking-wider">Cetak Massal:</h4>
 </div>
 </div>

 <div class="flex flex-wrap items-center gap-2">
 {{-- 1. Cetak Per Rombel --}}
 <button wire:click="cetakMassalRombel"
 wire:loading.attr="disabled"
 type="button"
 class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-text-main bg-surface-hover hover:bg-primary hover:text-white border border-border hover:border-primary rounded-lg transition-all shadow-2xs group"
 title="Cetak seluruh siswa per rombel (masing-masing rombel 1 lembar)">
 <svg class="w-3.5 h-3.5 text-primary group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
 </svg>
 <span>1. Per Rombel</span>
 </button>

 {{-- 2. Cetak Per Rombel per Gender --}}
 <button wire:click="cetakMassalRombelGender"
 wire:loading.attr="disabled"
 type="button"
 class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-text-main bg-surface-hover hover:bg-primary hover:text-white border border-border hover:border-primary rounded-lg transition-all shadow-2xs group"
 title="Cetak seluruh siswa per rombel dipisah per gender (Putra & Putri terpisah)">
 <svg class="w-3.5 h-3.5 text-sky-400 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
 </svg>
 <span>2. Per Rombel (L/P)</span>
 </button>

 {{-- 3. Cetak Per Program --}}
 <button wire:click="cetakMassalProgram"
 wire:loading.attr="disabled"
 type="button"
 class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-text-main bg-surface-hover hover:bg-primary hover:text-white border border-border hover:border-primary rounded-lg transition-all shadow-2xs group"
 title="Cetak seluruh siswa per program (Reguler & Unggulan)">
 <svg class="w-3.5 h-3.5 text-amber-400 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
 </svg>
 <span>3. Per Program</span>
 </button>

 {{-- 4. Cetak Per Program per Gender --}}
 <button wire:click="cetakMassalProgramGender"
 wire:loading.attr="disabled"
 type="button"
 class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-text-main bg-surface-hover hover:bg-primary hover:text-white border border-border hover:border-primary rounded-lg transition-all shadow-2xs group"
 title="Cetak seluruh siswa per program dipisah per gender">
 <svg class="w-3.5 h-3.5 text-pink-400 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
 </svg>
 <span>4. Per Program (L/P)</span>
 </button>
 </div>
 </div>
 </div>

 {{-- Card Filter & Opsi Pengaturan --}}
 <div class="card p-5 space-y-4 print:hidden">
 <div class="flex items-center justify-between border-b border-slate-100 border-border pb-3">
 <div class="flex items-center gap-2">
 <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
 </svg>
 <h3 class="text-sm font-bold text-text-main dark:text-slate-100 uppercase tracking-wide">Filter & Kriteria Siswa</h3>
 </div>
 <button wire:click="resetFilters" type="button" class="text-xs font-medium text-text-secondary hover:text-rose-600 transition flex items-center gap-1">
 <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
 </svg>
 <span>Reset Filter</span>
 </button>
 </div>

 {{-- Filter Row 1: Kriteria Utama --}}
 <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3">
 {{-- Tingkat / Kelas --}}
 <div>
 <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Tingkat / Kelas</label>
 <select wire:model.live="filterTingkat" class="w-full text-xs rounded-lg border-border bg-surface text-text-main focus:ring-indigo-500">
 <option value="">-- Semua Tingkat --</option>
 <option value="X">Kelas X</option>
 <option value="XI">Kelas XI</option>
 <option value="XII">Kelas XII</option>
 </select>
 </div>

 {{-- Jurusan --}}
 <div>
 <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Jurusan / Keahlian</label>
 <select wire:model.live="filterJurusan" class="w-full text-xs rounded-lg border-border bg-surface text-text-main focus:ring-indigo-500">
 <option value="">-- Semua Jurusan --</option>
 @foreach($jurusans as $j)
 <option value="{{ $j->id }}">{{ $j->kode }} ({{ $j->nama }})</option>
 @endforeach
 </select>
 </div>

 {{-- Rombel --}}
 <div>
 <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Rombel (Kelas)</label>
 <select wire:model.live="filterRombel" class="w-full text-xs rounded-lg border-border bg-surface text-text-main focus:ring-indigo-500">
 <option value="">-- Semua Rombel --</option>
 @foreach($rombels as $r)
 <option value="{{ $r->id }}">{{ $r->nama }}</option>
 @endforeach
 </select>
 </div>

 {{-- Rayon --}}
 <div>
 <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Rayon Wilayah</label>
 <select wire:model.live="filterRayon" class="w-full text-xs rounded-lg border-border bg-surface text-text-main focus:ring-indigo-500">
 <option value="">-- Semua Rayon --</option>
 @foreach($rayons as $ry)
 <option value="{{ $ry->id }}">{{ $ry->nama }}</option>
 @endforeach
 </select>
 </div>

 {{-- Jenis Kelamin (Putra / Putri) --}}
 <div>
 <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Putra / Putri (JK)</label>
 <select wire:model.live="filterJk" class="w-full text-xs rounded-lg border-border bg-surface text-text-main focus:ring-indigo-500">
 <option value="">-- Semua (Putra & Putri) --</option>
 <option value="L">Putra (L)</option>
 <option value="P">Putri (P)</option>
 </select>
 </div>

 {{-- Program (Unggulan / Reguler) --}}
 <div>
 <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Program Siswa</label>
 <select wire:model.live="filterProgram" class="w-full text-xs rounded-lg border-border bg-surface text-text-main focus:ring-indigo-500">
 <option value="">-- Semua Program --</option>
 @foreach($programs as $pr)
 <option value="{{ $pr->id }}">{{ $pr->nama }}</option>
 @endforeach
 </select>
 </div>
 </div>

 {{-- Filter Row 2: Shortir by & Judul / Lembar Metadata --}}
 <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-2 border-t border-slate-100 border-border">
 {{-- Shortir by --}}
 <div>
 <label class="block text-xs font-semibold text-primary dark:text-indigo-400 uppercase tracking-wider mb-1 flex items-center gap-1">
 <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
 </svg>
 <span>Shortir By (Urutan)</span>
 </label>
 <select wire:model.live="sortBy" class="w-full text-xs rounded-lg border-border bg-surface text-text-main font-medium focus:ring-indigo-500">
 <option value="nipd_asc">NIS / NIPD (Kecil ke Besar)</option>
 <option value="nipd_desc">NIS / NIPD (Besar ke Kecil)</option>
 <option value="nama_asc">Nama Siswa (A &ndash; Z)</option>
 <option value="nama_desc">Nama Siswa (Z &ndash; A)</option>
 <option value="rombel_asc">Rombel (Kelas) Lalu Nama</option>
 <option value="rayon_asc">Rayon Lalu Nama</option>
 <option value="jk_asc">Jenis Kelamin (Putra Dulu, Lalu Putri)</option>
 </select>
 </div>

 {{-- Mapel (Mata Pelajaran) --}}
 <div>
 <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">
 Mata Pelajaran (Mapel)
 </label>
 <input type="text"
 wire:model.live.debounce.300ms="mapel"
 placeholder="Kosongkan jika diisi manual di kertas"
 class="w-full text-xs rounded-lg border-border bg-surface text-text-main placeholder:text-slate-400 focus:ring-indigo-500" />
 </div>

 {{-- Tahun Pelajaran --}}
 <div>
 <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">
 Tahun Pelajaran
 </label>
 <input type="text"
 wire:model.live.debounce.300ms="tahunPelajaran"
 placeholder="Contoh: 2026-2027"
 class="w-full text-xs rounded-lg border-border bg-surface text-text-main focus:ring-indigo-500" />
 </div>

 {{-- Jumlah Kolom --}}
 <div>
 <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">
 Jumlah Kolom Tanggal
 </label>
 <input type="number" min="1" max="50"
 wire:model.live.debounce.300ms="jumlahKolom"
 class="w-full text-xs rounded-lg border-border bg-surface text-text-main focus:ring-indigo-500" />
 </div>
 </div>
 </div>

 {{-- Kriteria & Status Bar --}}
 <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 px-1 print:hidden">
 <div class="text-sm text-text-secondary">
 Kriteria Aktif:
 <span class="font-bold text-text-main bg-slate-100 bg-surface px-2 py-0.5 rounded-md text-xs">{{ $filterLabel }}</span>
 &mdash;
 <span class="font-bold text-primary dark:text-indigo-400">{{ $siswas->count() }}</span> siswa ditemukan
 <span class="text-xs text-slate-400">
 (Putra: <b class="text-primary">{{ $siswas->where('jk', 'L')->count() }}</b>,
 Putri: <b class="text-pink-600">{{ $siswas->where('jk', 'P')->count() }}</b>)
 </span>
 </div>
 <div class="text-xs text-slate-400 dark:text-text-secondary flex items-center gap-2">
 <span>Tahun Pelajaran: <strong class="text-text-main text-text-secondary">{{ $tahunPelajaran }}</strong></span>
 <span>&middot;</span>
 <span class="text-primary dark:text-indigo-400 font-medium">Format: Portrait A4</span>
 </div>
 </div>

 {{-- Lembar Preview Daftar Hadir Murid Sesuai Template Rekap Absensi SIRAGA --}}
 @if($siswas->count() > 0)
 @php
 $school = \App\Modules\Sistem\Models\School::current();
 @endphp
 <div class="card overflow-hidden print:border-none print:shadow-none print:p-0">
 
 {{-- Kop Surat Resmi Sesuai Template rekap_absensi_rombel --}}
 <div class="p-6 pb-3 border-b-2 border-slate-900 dark:border-slate-600 text-center">
 @if(!empty($school->kop_surat_path) && file_exists(public_path('storage/' . $school->kop_surat_path)))
 <img src="{{ asset('storage/' . $school->kop_surat_path) }}" class="w-full h-auto object-contain mx-auto block" alt="Kop Surat">
 @else
 <p class="text-xs font-semibold text-text-secondary uppercase tracking-wider">YAYASAN PRAWITAMA</p>
 <h1 class="text-xl font-extrabold text-text-main uppercase tracking-wide">{{ $school->nama ?? 'SMK WIKRAMA 1 GARUT' }}</h1>
 <p class="text-[11px] text-text-secondary">
 {{ $school->alamat ?? 'Jl. Otto Iskandardinata, Tarogong Kaler, Garut' }} | Telp: {{ $school->telepon ?? '-' }} | Email: {{ $school->email ?? '-' }}
 </p>
 @endif
 </div>

 {{-- Header Judul Dokumen --}}
 <div class="pt-8 pb-6 text-center space-y-1">
 <h1 class="text-base sm:text-lg font-bold text-text-main uppercase tracking-wider">
 DAFTAR HADIR MURID
 </h1>
 <h1 class="text-base sm:text-lg font-bold text-text-main uppercase tracking-wider">
 SMKS WIKRAMA 1 GARUT
 </h1>
 <h1 class="text-base sm:text-lg font-bold text-text-main uppercase tracking-wider">
 TAHUN PELAJARAN {{ $tahunPelajaran }}
 </h1>

 {{-- Bar Informasi Filter & Mapel (Tanpa Bulan, Tanpa Semester, Tanpa Urutan) --}}
 <div class="pt-3 px-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-slate-100 border-border pb-2" style="font-family: 'Times New Roman', Times, serif; font-size: 15px;">
 <div>
 <span class="font-bold text-text-main">Kriteria / Kelas:</span>
 <span class="text-text-main ml-1">{{ $filterLabel }}</span>
 </div>
 <div>
 <span class="font-bold text-text-main">Mata Pelajaran:</span>
 <span class="text-text-main ml-1">{{ $mapel ?: '........................................................' }}</span>
 </div>
 </div>
 </div>

 {{-- Tabel Absensi Portrait dengan 30 Kolom Kotak Kosong --}}
 <div class="overflow-x-auto p-4 pt-2">
 <table class="w-full text-left border-collapse border border-slate-900 border-border text-xs table-auto">
 <thead>
 <tr class="bg-slate-100 bg-surface text-text-main font-bold border-b border-slate-900 border-border" style="font-family: Tahoma, Geneva, sans-serif;">
 <!-- Lebar kolom No -->
 <th rowspan="2" class="p-1.5 text-center border-r border-slate-900 border-border text-[11px] whitespace-nowrap" style="width: 20px;">No</th>
 <!-- Lebar kolom NIS -->
 <th rowspan="2" class="p-1.5 text-center border-r border-slate-900 border-border text-[11px] whitespace-nowrap" style="width: 60px;">NIS</th>
 <!-- Lebar kolom Nama Siswa -->
 <th rowspan="2" class="p-1.5 text-center border-r border-slate-900 border-border text-[11px] whitespace-nowrap" style="width: 140px;">Nama Siswa</th>
 <!-- Lebar kolom JK -->
 <th rowspan="2" class="p-1.5 text-center border-r border-slate-900 border-border text-[11px] whitespace-nowrap" style="width: 20px;">JK</th>
 <!-- Lebar kolom Prog -->
 <th rowspan="2" class="p-1.5 text-center border-r border-slate-900 border-border text-[11px] whitespace-nowrap" title="Program: U = Unggulan, R = Reguler" style="width: 25px;">Prog</th>
 <!-- Lebar kolom Rayon -->
 <th rowspan="2" class="p-1.5 text-center border-r border-slate-900 border-border text-[11px] whitespace-nowrap" style="width: 65px;">Rayon</th>
 <th colspan="{{ $jumlahKolom }}" class="p-1 text-center border-r border-slate-900 border-border text-[10px] font-normal tracking-wider bg-surface-hover dark:bg-slate-850">
 ........................................................
 </th>
 </tr>
 <tr class="bg-surface border-b border-slate-900 border-border">
 @for($d = 1; $d <= $jumlahKolom; $d++)
 <th class="min-w-[15px] text-center p-0 border-r border-slate-900 border-border h-4 font-normal text-[9px] text-slate-300">&nbsp;</th>
 @endfor
 </tr>
 </thead>
 <tbody class="divide-y divide-slate-900 dark:divide-slate-700 text-text-main">
 @foreach($siswas as $idx => $s)
 @php
 $progNama = strtolower($s->program?->nama ?? '');
 $progSingkat = str_contains($progNama, 'unggul') ? 'U' : (str_contains($progNama, 'regul') ? 'R' : ($s->program ? substr($s->program->nama, 0, 1) : '-'));
 @endphp
 <tr class="hover:bg-surface-hover/70 dark:hover:bg-slate-800/40 transition" style="font-family: Tahoma, Geneva, sans-serif; font-weight: normal;">
 <td class="p-1 text-center text-[10px] border-r border-slate-900 border-border">{{ $idx + 1 }}</td>
 <td class="p-1 text-center text-[10px] text-text-main border-r border-slate-900 border-border">{{ $s->nipd ?: $s->nisn }}</td>
 <td class="p-1 text-text-main border-r border-slate-900 border-border whitespace-nowrap text-xs">
 {{ $s->nama }}
 </td>
 <td class="p-1 text-center border-r border-slate-900 border-border text-[10px]">
 {{ $s->jk }}
 </td>
 <td class="p-1 text-center border-r border-slate-900 border-border text-[10px]">
 {{ $progSingkat }}
 </td>
 <td class="p-1 text-center text-[10px] text-text-secondary border-r border-slate-900 border-border whitespace-nowrap">
 {{ $s->rayon?->nama ?? '-' }}
 </td>
 @for($d = 1; $d <= $jumlahKolom; $d++)
 <td class="p-0 text-center border-r border-slate-900 border-border text-slate-300">&nbsp;</td>
 @endfor
 </tr>
 @endforeach
 </tbody>
 </table>
 </div>

 {{-- Di Bawah Tabel --}}
 <div class="px-6 py-4">
 {{-- Jumlah Siswa Rata Kiri 1 Enter Setelah Tabel --}}
 <div class="pt-4 text-text-main text-left" style="font-family: 'Times New Roman', Times, serif; font-size: 15px;">
 <div>
 Jumlah: <b>{{ $siswas->count() }}</b> Siswa
 (L: <b>{{ $siswas->where('jk', 'L')->count() }}</b>,
 P: <b>{{ $siswas->where('jk', 'P')->count() }}</b>)
 </div>
 <div class="text-[11px] text-text-secondary mt-0.5" style="font-family: Arial, Helvetica, sans-serif;">* Program: <b>U</b> = Unggulan, <b>R</b> = Reguler</div>
 </div>

 {{-- Tanda Tangan Manual Guru Mata Pelajaran Rata Kanan (di bawahnya, tidak sejajar) --}}
 <div class="mt-8 text-right">
 <div class="inline-block text-center min-w-[250px] text-text-main" style="font-family: 'Times New Roman', Times, serif; font-size: 15px;">
 <p class="mb-1">Garut, ........................... ({{ date('Y') }})</p>
 <p>Guru Mata Pelajaran,</p>
 <div class="h-20"></div>
 <p class="mt-1 font-bold">
 {{ $namaGuru ?: '( ................................................ )' }}
 </p>
 <p>
 NIP. {{ $nipGuru ?: '..........................................' }}
 </p>
 </div>
 </div>
 </div>

 {{-- Catatan Kaki Dokumen --}}
 <div class="px-6 py-2.5 bg-surface-hover bg-surface/50 border-t border-border flex justify-between text-[10px] text-slate-400">
 <span>Dicetak dari Sistem Informasi Pengelolaan Sekolah (SIRAGA) SMKS Wikrama 1 Garut</span>
 <span>Tanggal Cetak: {{ now()->translatedFormat('d/m/Y H:i') }}</span>
 </div>
 </div>
 @else
 <div class="card p-12 text-center print:hidden">
 <div class="w-14 h-14 mx-auto mb-3 text-slate-300 dark:text-text-secondary">
 <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
 </svg>
 </div>
 <h3 class="text-base font-bold text-text-main mb-1">Tidak Ada Data Siswa</h3>
 <p class="text-xs text-slate-400 dark:text-text-secondary max-w-sm mx-auto">
 Tidak ada data siswa yang cocok dengan kombinasi filter yang Anda pilih. Silakan atur kembali filter di atas.
 </p>
 <div class="mt-4">
 <button wire:click="resetFilters" type="button" class="px-4 py-2 text-xs font-semibold text-primary bg-indigo-50 hover:bg-indigo-100 rounded-lg dark:bg-indigo-950/50 dark:text-indigo-400 transition">
 Kembalikan ke Semua Siswa
 </button>
 </div>
 </div>
 @endif
</div>
