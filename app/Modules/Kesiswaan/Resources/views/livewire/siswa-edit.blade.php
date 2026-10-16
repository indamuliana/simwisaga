<div class="space-y-6">
 <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
 <div>
 <div class="flex items-center gap-2">
 <a href="{{ route('kesiswaan.siswa') }}" class="text-sm text-slate-400 hover:text-primary transition">&larr; Kembali ke Data Siswa</a>
 <span class="text-slate-300">/</span>
 <span class="text-xs font-mono font-semibold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 px-2 py-0.5 rounded">NIS: {{ $nipd ?: $nisn }}</span>
 </div>
 <h1 class="text-2xl font-bold tracking-tight text-text-main mt-1">{{ $nama }}</h1>
 <p class="text-sm text-text-secondary">Edit seluruh informasi biodata 66 kolom standar DAPODIK dan pas foto siswa.</p>
 </div>
 <div class="flex flex-wrap items-center gap-2">
 <!-- Dropdown Download Detail Siswa (Word, PDF, Excel) -->
 <div class="relative" x-data="{ openDownload: false }" @click.outside="openDownload = false">
 <button type="button" @click="openDownload = !openDownload"
 class="btn btn-secondary">
 <svg class="w-4 h-4 text-primary dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
 </svg>
 <span>Download Detail Siswa</span>
 <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': openDownload }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
 </svg>
 </button>

 <div x-show="openDownload" x-cloak x-transition
 class="absolute right-0 mt-2 w-64 rounded-xl bg-surface shadow-xl ring-1 ring-black/5 dark:ring-slate-700 z-50 py-1.5 divide-y divide-slate-100 dark:divide-slate-700">
 <div class="px-3.5 py-2 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
 Pilih Format Dokumen
 </div>
 <div class="py-1">
 <!-- PDF -->
 <button type="button" wire:click="downloadDetail('pdf')" @click="openDownload = false"
 class="flex items-center gap-3 w-full px-4 py-2.5 text-left text-text-main hover:bg-rose-50/70 hover:bg-surface-hover transition group">
 <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-rose-100 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 font-bold text-xs group-hover:scale-105 transition-transform">
 PDF
 </span>
 <div>
 <div class="font-semibold text-sm text-text-main group-hover:text-rose-600 dark:group-hover:text-rose-400">Dokumen PDF</div>
 <div class="text-[11px] text-text-secondary">Format cetak A4 & tanda tangan</div>
 </div>
 </button>

 <!-- Word -->
 <button type="button" wire:click="downloadDetail('word')" @click="openDownload = false"
 class="flex items-center gap-3 w-full px-4 py-2.5 text-left text-text-main hover:bg-blue-50/70 hover:bg-surface-hover transition group">
 <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-950/60 text-primary dark:text-blue-400 font-bold text-xs group-hover:scale-105 transition-transform">
 DOC
 </span>
 <div>
 <div class="font-semibold text-sm text-text-main group-hover:text-primary dark:group-hover:text-blue-400">Microsoft Word (.doc)</div>
 <div class="text-[11px] text-text-secondary">Dapat diedit & diubah formatnya</div>
 </div>
 </button>

 <!-- Excel -->
 <button type="button" wire:click="downloadDetail('excel')" @click="openDownload = false"
 class="flex items-center gap-3 w-full px-4 py-2.5 text-left text-text-main hover:bg-success-soft/70 hover:bg-surface-hover transition group">
 <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 font-bold text-xs group-hover:scale-105 transition-transform">
 XLS
 </span>
 <div>
 <div class="font-semibold text-sm text-text-main group-hover:text-emerald-600 dark:group-hover:text-emerald-400">Microsoft Excel (.xlsx)</div>
 <div class="text-[11px] text-text-secondary">Tabel data induk terstruktur</div>
 </div>
 </button>
 </div>
 </div>
 </div>

 @if($siswa->user)
 <button type="button" wire:click="resetPasswordDefault" wire:confirm="Reset password akun siswa {{ $siswa->user->username }} ke tanggal lahir (DDMMYYYY)?" class="px-3 py-2 text-xs font-semibold text-amber-700 bg-amber-50 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200 dark:border-amber-800 rounded-md hover:bg-amber-100 transition">
 &#128273; Reset Password (DDMMYYYY)
 </button>
 @endif
 <button wire:click="save" class="btn btn-primary px-5">
 Simpan Perubahan
 </button>
 </div>
 </div>

 <div wire:loading wire:target="downloadDetail" class="p-3.5 bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800 rounded-xl text-indigo-800 dark:text-indigo-300 text-xs flex items-center gap-2.5 shadow-xs">
 <svg class="animate-spin h-4 w-4 text-primary dark:text-indigo-400 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
 <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
 <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
 </svg>
 <span>Sedang menyiapkan berkas dokumen detail siswa... Unduhan akan dimulai secara otomatis sesaat lagi.</span>
 </div>

 @if (session('status'))
 <div class="p-4 text-sm text-success bg-success-soft rounded-md border border-success">
 {{ session('status') }}
 </div>
 @endif

 <!-- Tab Navigation -->
 <div class="flex flex-wrap gap-2 border-b border-border pb-2">
 <button type="button" wire:click="$set('activeTab', 'identitas')" class="btn btn-primary">
 1. Identitas & Foto
 </button>
 <button type="button" wire:click="$set('activeTab', 'alamat')" class="btn btn-primary">
 2. Alamat & Domisili
 </button>
 <button type="button" wire:click="$set('activeTab', 'orangtua')" class="btn btn-primary">
 3. Orang Tua & Wali
 </button>
 <button type="button" wire:click="$set('activeTab', 'dokumen')" class="btn btn-primary">
 4. Bantuan & Dokumen
 </button>
 <button type="button" wire:click="$set('activeTab', 'fisik')" class="btn btn-primary">
 5. Fisik & Kontak
 </button>
 </div>

 <!-- Tab 1: Identitas & Foto -->
 @if($activeTab === 'identitas')
 <div class="card p-6 space-y-6">
 <h3 class="text-base font-bold text-text-main border-b border-slate-100 border-border pb-3">
 Identitas Pribadi & Penempatan Akademik
 </h3>

 <!-- Foto Siswa -->
 <div class="flex items-center gap-6 p-4 bg-surface-hover bg-surface/40 rounded-xl border border-border">
 <div class="w-24 h-32 rounded-md bg-slate-200 bg-surface-hover overflow-hidden flex items-center justify-center border border-border dark:border-slate-600">
 @if($foto)
 <img src="{{ $foto->temporaryUrl() }}" class="w-full h-full object-cover">
 @elseif($existingFotoPath && file_exists(public_path('storage/' . $existingFotoPath)))
 <img src="{{ asset('storage/' . $existingFotoPath) }}" class="w-full h-full object-cover">
 @else
 <span class="text-xs text-slate-400 text-center px-2">Belum ada foto</span>
 @endif
 </div>
 <div class="space-y-2">
 <label class="block text-xs font-bold uppercase tracking-wider text-text-main text-text-secondary">
 Upload Pas Foto Siswa
 </label>
 <input type="file" wire:model="foto" accept="image/*" class="file-input">
 <p class="text-[11px] text-slate-400">Format: JPG, JPEG, PNG. Maksimal 2MB. Pas foto formal seragam sekolah.</p>
 @error('foto') <span class="text-xs text-rose-500 block">{{ $message }}</span> @enderror
 </div>
 </div>

 <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">
 NIS (Nomor Induk Siswa) *
 </label>
 <input type="text" wire:model="nipd" placeholder="Contoh: 12200760" class="w-full text-sm font-mono font-bold rounded-md border-border bg-surface text-text-main">
 <span class="text-[11px] text-slate-400">Nomor utama siswa (Username Login Siswa & Ortu).</span>
 @error('nipd') <span class="text-xs text-rose-500 block">{{ $message }}</span> @enderror
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">
 NISN (10 Digit) *
 </label>
 <input type="text" wire:model="nisn" class="w-full text-sm font-mono rounded-md border-border bg-surface text-text-main">
 @error('nisn') <span class="text-xs text-rose-500 block">{{ $message }}</span> @enderror
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">
 NIK Siswa (16 Digit)
 </label>
 <input type="text" wire:model="nik" class="w-full text-sm font-mono rounded-md border-border bg-surface text-text-main">
 </div>
 </div>

 <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
 <div class="sm:col-span-2">
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Nama Lengkap Siswa *</label>
 <input type="text" wire:model="nama" class="w-full text-sm font-bold rounded-md border-border bg-surface text-text-main">
 @error('nama') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Jenis Kelamin</label>
 <select wire:model="jk" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 <option value="L">Laki-laki</option>
 <option value="P">Perempuan</option>
 </select>
 </div>
 </div>

 <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Tempat Lahir</label>
 <input type="text" wire:model="tempat_lahir" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Tanggal Lahir (DD-MM-YYYY)</label>
 <input type="date" wire:model="tanggal_lahir" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 <span class="text-[11px] text-slate-400">Password default: DDMMYYYY</span>
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Agama</label>
 <input type="text" wire:model="agama" placeholder="Islam, Kristen, dll." class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 </div>
 </div>

 <!-- Penempatan Akademik & Program -->
 <div class="p-4 bg-indigo-50/40 bg-surface/60 rounded-xl border border-indigo-100 border-border space-y-4">
 <h4 class="text-xs font-bold uppercase tracking-wider text-indigo-700 dark:text-indigo-300">
 Penempatan Rombel, Rayon & Program
 </h4>
 <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Program</label>
 <select wire:model="program_id" class="w-full text-sm font-semibold rounded-md border-border bg-surface text-text-main">
 <option value="">-- Tanpa Program --</option>
 @foreach($programs as $pr)
 <option value="{{ $pr->id }}">{{ $pr->nama }} ({{ $pr->kode }})</option>
 @endforeach
 </select>
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Rombel (Kelas)</label>
 <select wire:model="rombel_id" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 <option value="">-- Pilih Rombel --</option>
 @foreach($rombels as $r)
 <option value="{{ $r->id }}">{{ $r->nama }}</option>
 @endforeach
 </select>
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Rayon Wilayah</label>
 <select wire:model="rayon_id" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 <option value="">-- Pilih Rayon --</option>
 @foreach($rayons as $ry)
 <option value="{{ $ry->id }}">{{ $ry->nama }}</option>
 @endforeach
 </select>
 </div>
 <div class="space-y-4">
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Status 
Keaktifan</label>
 <select wire:model="status" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 <option value="aktif">Aktif</option>
 <option value="lulus">Lulus</option>
 <option value="pindah">Pindah</option>
 <option value="dikeluarkan">Dikeluarkan</option>
 <option value="cuti">Cuti</option>
 </select>
 </div>
 <div>
 <label class="inline-flex items-center gap-2 cursor-pointer mt-2">
 <input type="checkbox" wire:model="is_active" class="w-4 h-4 rounded-md border-border text-primary focus:ring-indigo-500">
 <span class="text-sm font-semibold text-text-main text-text-secondary">Siswa Aktif Login / Tampil</span>
 </label>
 <p class="text-[11px] text-slate-400 mt-1">Siswa non-aktif tidak akan ditampilkan di daftar utama dan tidak bisa login.</p>
 </div>
 </div>
 </div>
 </div>
 </div>
 @endif

 <!-- Tab 2: Alamat & Domisili -->
 @if($activeTab === 'alamat')
 <div class="card p-6 space-y-4">
 <h3 class="text-base font-bold text-text-main border-b border-slate-100 border-border pb-3">
 Alamat Tempat Tinggal & Domisili Siswa
 </h3>

 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Alamat Jalan / Kampung</label>
 <textarea wire:model="alamat" rows="2" class="w-full text-sm rounded-md border-border bg-surface text-text-main"></textarea>
 </div>

 <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">RT</label>
 <input type="text" wire:model="rt" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">RW</label>
 <input type="text" wire:model="rw" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Dusun</label>
 <input type="text" wire:model="dusun" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Kode Pos</label>
 <input type="text" wire:model="kode_pos" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 </div>
 </div>

 <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Kelurahan / Desa</label>
 <input type="text" wire:model="kelurahan" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Kecamatan</label>
 <input type="text" wire:model="kecamatan" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 </div>
 </div>

 <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Jenis Tinggal</label>
 <input type="text" wire:model="jenis_tinggal" placeholder="Bersama orang tua / Asrama / Kost" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Alat Transportasi</label>
 <input type="text" wire:model="alat_transportasi" placeholder="Angkutan umum / Motor / Jalan kaki" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Jarak ke Sekolah (KM)</label>
 <input type="number" step="0.1" wire:model="jarak_ke_sekolah_km" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 </div>
 </div>

 <div class="grid grid-cols-2 gap-4">
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Lintang (Latitude)</label>
 <input type="text" wire:model="lintang" class="w-full text-sm font-mono rounded-md border-border bg-surface text-text-main">
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Bujur (Longitude)</label>
 <input type="text" wire:model="bujur" class="w-full text-sm font-mono rounded-md border-border bg-surface text-text-main">
 </div>
 </div>
 </div>
 @endif

 <!-- Tab 3: Orang Tua & Wali -->
 @if($activeTab === 'orangtua')
 <div class="space-y-6">
 <!-- Data Ayah -->
 <div class="card p-6 space-y-4">
 <h3 class="text-sm font-bold text-primary dark:text-indigo-400 uppercase tracking-wider">
 Data Ayah Kandung
 </h3>
 <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
 <div class="sm:col-span-2">
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Nama Ayah Kandung</label>
 <input type="text" wire:model="ayah_nama" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Tahun Lahir</label>
 <input type="number" wire:model="ayah_tahun_lahir" placeholder="Contoh: 1975" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Status Hidup</label>
 <select wire:model="ayah_status_hidup" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 <option value="Hidup">Masih Hidup</option>
 <option value="Wafat">Meninggal/Wafat</option>
 </select>
 </div>
 </div>
 <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Jenjang Pendidikan</label>
 <input type="text" wire:model="ayah_jenjang_pendidikan" placeholder="SMA / S1 / SMP" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Pekerjaan</label>
 <input type="text" wire:model="ayah_pekerjaan" placeholder="Wiraswasta / PNS / Buruh" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Penghasilan</label>
 <input type="text" wire:model="ayah_penghasilan" placeholder="Rentang penghasilan bulanan" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">No. Kontak / HP</label>
 <input type="text" wire:model="ayah_no_hp" placeholder="08xxxxxxxx" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 </div>
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">NIK Ayah (16 Digit)</label>
 <input type="text" wire:model="ayah_nik" class="w-full text-sm font-mono rounded-md border-border bg-surface text-text-main">
 </div>
 </div>

 <!-- Data Ibu -->
 <div class="card p-6 space-y-4">
 <h3 class="text-sm font-bold text-pink-600 dark:text-pink-400 uppercase tracking-wider">
 Data Ibu Kandung
 </h3>
 <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
 <div class="sm:col-span-2">
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Nama Ibu Kandung</label>
 <input type="text" wire:model="ibu_nama" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Tahun Lahir</label>
 <input type="number" wire:model="ibu_tahun_lahir" placeholder="Contoh: 1980" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Status Hidup</label>
 <select wire:model="ibu_status_hidup" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 <option value="Hidup">Masih Hidup</option>
 <option value="Wafat">Meninggal/Wafat</option>
 </select>
 </div>
 </div>
 <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Jenjang Pendidikan</label>
 <input type="text" wire:model="ibu_jenjang_pendidikan" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Pekerjaan</label>
 <input type="text" wire:model="ibu_pekerjaan" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Penghasilan</label>
 <input type="text" wire:model="ibu_penghasilan" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">No. Kontak / HP</label>
 <input type="text" wire:model="ibu_no_hp" placeholder="08xxxxxxxx" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 </div>
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">NIK Ibu (16 Digit)</label>
 <input type="text" wire:model="ibu_nik" class="w-full text-sm font-mono rounded-md border-border bg-surface text-text-main">
 </div>
 </div>

 <!-- Data Wali -->
 <div class="card p-6 space-y-4">
 <h3 class="text-sm font-bold text-text-secondary uppercase tracking-wider">
 Data Wali (Opsional / Jika Ada)
 </h3>
 <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
 <div class="sm:col-span-2">
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Nama Wali</label>
 <input type="text" wire:model="wali_nama" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Tahun Lahir</label>
 <input type="number" wire:model="wali_tahun_lahir" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 </div>
 </div>
 <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Jenjang Pendidikan</label>
 <input type="text" wire:model="wali_jenjang_pendidikan" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Pekerjaan</label>
 <input type="text" wire:model="wali_pekerjaan" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Penghasilan</label>
 <input type="text" wire:model="wali_penghasilan" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">No. Kontak / HP</label>
 <input type="text" wire:model="wali_no_hp" placeholder="08xxxxxxxx" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 </div>
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">NIK Wali</label>
 <input type="text" wire:model="wali_nik" class="w-full text-sm font-mono rounded-md border-border bg-surface text-text-main">
 </div>
 </div>
 </div>
 @endif

 <!-- Tab 4: Bantuan, Rekening & Dokumen -->
 @if($activeTab === 'dokumen')
 <div class="card p-6 space-y-6">
 <h3 class="text-base font-bold text-text-main border-b border-slate-100 border-border pb-3">
 Dokumen Resmi, Sekolah Asal, dan Bantuan Sosial
 </h3>

 <!-- Dokumen Berkas -->
 <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">No. KK (Kartu Keluarga)</label>
 <input type="text" wire:model="no_kk" class="w-full text-sm font-mono rounded-md border-border bg-surface text-text-main">
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">No. Registrasi Akta Lahir</label>
 <input type="text" wire:model="no_akta_lahir" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Sekolah Asal (SMP/MTs)</label>
 <input type="text" wire:model="sekolah_asal" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 </div>
 </div>

 <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">SKHUN</label>
 <input type="text" wire:model="skhun" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">No. Peserta UN</label>
 <input type="text" wire:model="no_peserta_un" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">No. Seri Ijazah</label>
 <input type="text" wire:model="no_seri_ijazah" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 </div>
 </div>

 <!-- Bantuan KPS, KIP, PIP, KKS -->
 <div class="p-4 bg-surface-hover bg-surface/40 rounded-xl border border-border space-y-4">
 <h4 class="text-xs font-bold uppercase tracking-wider text-text-main text-text-secondary">
 Bantuan Sosial & Rekening PIP
 </h4>

 <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
 <div class="space-y-2">
 <label class="inline-flex items-center gap-2 cursor-pointer">
 <input type="checkbox" wire:model.live="penerima_kps" class="rounded border-border text-primary focus:ring-indigo-500">
 <span class="text-xs font-semibold text-text-main text-text-secondary">Penerima KPS (Kartu Perlindungan Sosial)</span>
 </label>
 @if($penerima_kps)
 <input type="text" wire:model="no_kps" placeholder="Nomor KPS" class="w-full text-xs rounded-md border-border bg-surface text-text-main">
 @endif
 </div>
 <div class="space-y-2">
 <label class="inline-flex items-center gap-2 cursor-pointer">
 <input type="checkbox" wire:model.live="penerima_kip" class="rounded border-border text-primary focus:ring-indigo-500">
 <span class="text-xs font-semibold text-text-main text-text-secondary">Penerima KIP (Kartu Indonesia Pintar)</span>
 </label>
 @if($penerima_kip)
 <div class="grid grid-cols-2 gap-2">
 <input type="text" wire:model="nomor_kip" placeholder="Nomor KIP" class="w-full text-xs rounded-md border-border bg-surface text-text-main">
 <input type="text" wire:model="nama_di_kip" placeholder="Nama Tertera di KIP" class="w-full text-xs rounded-md border-border bg-surface text-text-main">
 </div>
 @endif
 </div>
 </div>

 <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Nomor KKS (Kartu Keluarga Sejahtera)</label>
 <input type="text" wire:model="nomor_kks" class="w-full text-xs rounded-md border-border bg-surface text-text-main">
 </div>
 <div class="space-y-2">
 <label class="inline-flex items-center gap-2 cursor-pointer">
 <input type="checkbox" wire:model.live="layak_pip" class="rounded border-border text-primary focus:ring-indigo-500">
 <span class="text-xs font-semibold text-text-main text-text-secondary">Layak PIP (Usulan Sekolah)</span>
 </label>
 @if($layak_pip)
 <input type="text" wire:model="alasan_layak_pip" placeholder="Alasan Layak PIP (misal: Pemegang PKH / Yatim Piatu)" class="w-full text-xs rounded-md border-border bg-surface text-text-main">
 @endif
 </div>
 </div>

 <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Nama Bank Penyalur</label>
 <input type="text" wire:model="bank" placeholder="BRI / BNI / Mandiri" class="w-full text-xs rounded-md border-border bg-surface text-text-main">
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Nomor Rekening Bank</label>
 <input type="text" wire:model="nomor_rekening_bank" class="w-full text-xs font-mono rounded-md border-border bg-surface text-text-main">
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Rekening Atas Nama</label>
 <input type="text" wire:model="rekening_atas_nama" class="w-full text-xs rounded-md border-border bg-surface text-text-main">
 </div>
 </div>
 </div>
 </div>
 @endif

 <!-- Tab 5: Fisik & Kontak -->
 @if($activeTab === 'fisik')
 <div class="card p-6 space-y-6">
 <h3 class="text-base font-bold text-text-main border-b border-slate-100 border-border pb-3">
 Data Fisik, Berkala, dan Informasi Kontak
 </h3>

 <!-- Data Fisik Siswa -->
 <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Berat Badan (KG)</label>
 <input type="number" wire:model="berat_badan" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Tinggi Badan (CM)</label>
 <input type="number" wire:model="tinggi_badan" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Lingkar Kepala (CM)</label>
 <input type="number" wire:model="lingkar_kepala" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Anak Ke-berapa</label>
 <input type="number" wire:model="anak_ke_berapa" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 </div>
 </div>

 <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Jumlah Saudara Kandung</label>
 <input type="number" wire:model="jml_saudara_kandung" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Kebutuhan Khusus</label>
 <input type="text" wire:model="kebutuhan_khusus" placeholder="Tidak ada / Tunanetra / Hiperaktif" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 </div>
 </div>

 <!-- Kontak -->
 <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">No. WhatsApp / HP</label>
 <input type="text" wire:model="hp" placeholder="08xxxxxxxxxx" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Telepon Rumah</label>
 <input type="text" wire:model="telepon" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Email Siswa</label>
 <input type="email" wire:model="email" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
 </div>
 </div>
 </div>
 @endif

 @if ($errors->any())
 <div class="p-4 mb-4 bg-rose-50 border-l-4 border-rose-500 rounded-r-md">
 <div class="flex">
 <div class="flex-shrink-0">
 <svg class="h-5 w-5 text-rose-500" viewBox="0 0 20 20" fill="currentColor">
 <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
 </svg>
 </div>
 <div class="ml-3">
 <h3 class="text-sm font-medium text-rose-800">Terdapat {{ $errors->count() }} kesalahan validasi:</h3>
 <div class="mt-2 text-sm text-rose-700">
 <ul class="list-disc pl-5 space-y-1">
 @foreach ($errors->all() as $error)
 <li>{{ $error }}</li>
 @endforeach
 </ul>
 </div>
 </div>
 </div>
 </div>
 @endif

 <div class="flex justify-end pt-4">
 <button wire:click="save" class="btn btn-primary px-6">
 Simpan Seluruh Data Siswa
 </button>
 </div>
</div>
