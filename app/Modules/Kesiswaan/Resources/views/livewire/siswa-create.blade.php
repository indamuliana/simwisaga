<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('kesiswaan.siswa') }}" class="text-sm text-slate-400 hover:text-primary transition">&larr; Kembali ke Data Siswa</a>
                <span class="text-slate-300">/</span>
                <span class="text-xs font-semibold text-text-secondary">Input Manual</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-text-main mt-1">Tambah Siswa Baru</h1>
            <p class="text-sm text-text-secondary">Input manual data profil siswa lengkap 66 kolom format standar DAPODIK SMK Wikrama 1 Garut.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('kesiswaan.siswa') }}" class="btn btn-secondary">
                Batal
            </a>
            <button type="button" wire:click="save(false)" wire:loading.attr="disabled" class="px-4 py-2 text-sm font-semibold text-primary bg-primary/10 hover:bg-primary/20 border border-primary/30 rounded-lg transition disabled:opacity-50">
                <span wire:loading.remove wire:target="save(false)">Simpan & Tambah Lagi</span>
                <span wire:loading wire:target="save(false)">Menyimpan...</span>
            </button>
            <button type="button" wire:click="save(true)" wire:loading.attr="disabled" class="btn btn-primary px-5 disabled:opacity-50">
                <span wire:loading.remove wire:target="save(true)">Simpan & Selesai</span>
                <span wire:loading wire:target="save(true)">Menyimpan...</span>
            </button>
        </div>
    </div>

    <!-- Alert Status -->
    @if (session('status'))
    <div class="p-4 text-sm text-success bg-success-soft rounded-lg border border-success flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        <span>{{ session('status') }}</span>
    </div>
    @endif

    <!-- Error Validation Alert -->
    @if ($errors->any())
    <div class="p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-300 dark:border-rose-800 rounded-lg">
        <div class="flex items-start">
            <svg class="h-5 w-5 text-rose-500 shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
            <div class="ml-3">
                <h3 class="text-sm font-semibold text-rose-800 dark:text-rose-300">Terdapat {{ $errors->count() }} kolom yang perlu diperbaiki:</h3>
                <ul class="mt-1 text-xs text-rose-700 dark:text-rose-400 list-disc pl-5 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <!-- Tab Navigation -->
    <div class="flex flex-wrap gap-2 border-b border-border pb-2">
        <button type="button" wire:click="$set('activeTab', 'identitas')" class="px-4 py-2 text-xs font-semibold rounded-lg transition {{ $activeTab === 'identitas' ? 'bg-primary text-white shadow-xs' : 'bg-surface hover:bg-surface-hover text-text-secondary border border-border' }}">
            1. Identitas & Foto
        </button>
        <button type="button" wire:click="$set('activeTab', 'alamat')" class="px-4 py-2 text-xs font-semibold rounded-lg transition {{ $activeTab === 'alamat' ? 'bg-primary text-white shadow-xs' : 'bg-surface hover:bg-surface-hover text-text-secondary border border-border' }}">
            2. Alamat & Domisili
        </button>
        <button type="button" wire:click="$set('activeTab', 'orangtua')" class="px-4 py-2 text-xs font-semibold rounded-lg transition {{ $activeTab === 'orangtua' ? 'bg-primary text-white shadow-xs' : 'bg-surface hover:bg-surface-hover text-text-secondary border border-border' }}">
            3. Orang Tua & Wali
        </button>
        <button type="button" wire:click="$set('activeTab', 'dokumen')" class="px-4 py-2 text-xs font-semibold rounded-lg transition {{ $activeTab === 'dokumen' ? 'bg-primary text-white shadow-xs' : 'bg-surface hover:bg-surface-hover text-text-secondary border border-border' }}">
            4. Bantuan & Dokumen
        </button>
        <button type="button" wire:click="$set('activeTab', 'fisik')" class="px-4 py-2 text-xs font-semibold rounded-lg transition {{ $activeTab === 'fisik' ? 'bg-primary text-white shadow-xs' : 'bg-surface hover:bg-surface-hover text-text-secondary border border-border' }}">
            5. Fisik & Kontak
        </button>
    </div>

    <!-- Tab 1: Identitas & Foto -->
    @if($activeTab === 'identitas')
    <div class="card p-6 space-y-6">
        <h3 class="text-base font-bold text-text-main border-b border-border pb-3">
            Identitas Pribadi & Penempatan Akademik
        </h3>

        <!-- Foto Siswa -->
        <div class="flex items-center gap-6 p-4 bg-surface-hover/50 rounded-xl border border-border">
            <div class="w-24 h-32 rounded-lg bg-slate-200 dark:bg-slate-800 overflow-hidden flex items-center justify-center border border-border shrink-0">
                @if($foto)
                    <img src="{{ $foto->temporaryUrl() }}" class="w-full h-full object-cover">
                @else
                    <span class="text-xs text-slate-400 text-center px-2">Pas Foto (Opsional)</span>
                @endif
            </div>
            <div class="space-y-2">
                <label class="block text-xs font-bold uppercase tracking-wider text-text-main">
                    Upload Pas Foto Siswa
                </label>
                <input type="file" wire:model="foto" accept="image/*" class="file-input">
                <p class="text-[11px] text-text-secondary">Format: JPG, JPEG, PNG. Maksimal 2MB. Pas foto formal seragam sekolah.</p>
                @error('foto') <span class="text-xs text-rose-500 block">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-text-main mb-1">
                    NIS (Nomor Induk Siswa)
                </label>
                <input type="text" wire:model="nipd" placeholder="Contoh: 12200760" class="w-full text-sm font-mono font-bold rounded-lg border-border bg-surface text-text-main">
                <span class="text-[11px] text-text-secondary">Username login Siswa & Ortu (jika kosong, menggunakan NISN).</span>
                @error('nipd') <span class="text-xs text-rose-500 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-text-main mb-1">
                    NISN (10 Digit) <span class="text-rose-500">*</span>
                </label>
                <input type="text" wire:model="nisn" placeholder="Contoh: 0061234567" class="w-full text-sm font-mono rounded-lg border-border bg-surface text-text-main" maxlength="20">
                @error('nisn') <span class="text-xs text-rose-500 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-text-main mb-1">
                    NIK Siswa (16 Digit)
                </label>
                <input type="text" wire:model="nik" placeholder="Contoh: 320501xxxxxxxxxx" class="w-full text-sm font-mono rounded-lg border-border bg-surface text-text-main" maxlength="20">
                @error('nik') <span class="text-xs text-rose-500 block">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-text-main mb-1">
                    Nama Lengkap Siswa <span class="text-rose-500">*</span>
                </label>
                <input type="text" wire:model="nama" placeholder="Nama lengkap sesuai ijazah/akta" class="w-full text-sm font-bold rounded-lg border-border bg-surface text-text-main">
                @error('nama') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-text-main mb-1">
                    Jenis Kelamin <span class="text-rose-500">*</span>
                </label>
                <select wire:model="jk" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>
                @error('jk') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-text-main mb-1">Tempat Lahir</label>
                <input type="text" wire:model="tempat_lahir" placeholder="Contoh: Garut" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
                @error('tempat_lahir') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-text-main mb-1">Tanggal Lahir</label>
                <input type="date" wire:model="tanggal_lahir" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
                <span class="text-[11px] text-text-secondary">Password awal akun login: DDMMYYYY</span>
                @error('tanggal_lahir') <span class="text-xs text-rose-500 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-text-main mb-1">Agama</label>
                <input type="text" wire:model="agama" placeholder="Islam, Kristen, dll." class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
                @error('agama') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Penempatan Akademik & Program -->
        <div class="p-4 bg-primary/5 dark:bg-primary/10 rounded-xl border border-primary/20 space-y-4">
            <h4 class="text-xs font-bold uppercase tracking-wider text-primary">
                Penempatan Program, Rombel & Rayon
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-text-main mb-1">Program Siswa</label>
                    <select wire:model="program_id" class="w-full text-sm font-semibold rounded-lg border-border bg-surface text-text-main">
                        <option value="">-- Tanpa Program --</option>
                        @foreach($programs as $pr)
                            <option value="{{ $pr->id }}">{{ $pr->nama }} ({{ $pr->kode }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-main mb-1">Rombel (Kelas)</label>
                    <select wire:model="rombel_id" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
                        <option value="">-- Pilih Rombel --</option>
                        @foreach($rombels as $r)
                            <option value="{{ $r->id }}">{{ $r->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-main mb-1">Rayon Wilayah</label>
                    <select wire:model="rayon_id" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
                        <option value="">-- Pilih Rayon --</option>
                        @foreach($rayons as $ry)
                            <option value="{{ $ry->id }}">{{ $ry->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-text-main mb-1">Status Keaktifan</label>
                        <select wire:model="status" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
                            <option value="aktif">Aktif</option>
                            <option value="lulus">Lulus</option>
                            <option value="pindah">Pindah</option>
                            <option value="dikeluarkan">Dikeluarkan</option>
                            <option value="cuti">Cuti</option>
                        </select>
                    </div>
                    <div>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="is_active" class="w-4 h-4 rounded border-border text-primary focus:ring-primary">
                            <span class="text-xs font-semibold text-text-main">Akun Aktif (Dapat Login)</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Tab 2: Alamat & Domisili -->
    @if($activeTab === 'alamat')
    <div class="card p-6 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-border pb-4">
            <div>
                <h3 class="text-base font-bold text-text-main">
                    Alamat Tempat Tinggal & Domisili Siswa
                </h3>
                <p class="text-xs text-text-muted mt-0.5">Tentukan cakupan wilayah tempat tinggal siswa (Dalam Negeri atau Luar Negeri).</p>
            </div>

            {{-- Segmented Toggle Dalam Negeri / Luar Negeri --}}
            <div class="flex items-center gap-1.5 p-1 bg-surface-subtle border border-border rounded-xl shrink-0">
                <button type="button" 
                        wire:click="$set('cakupan_wilayah', 'dalam_negeri')" 
                        class="px-3.5 py-1.5 text-xs font-semibold rounded-lg transition-all flex items-center gap-1.5 {{ $cakupan_wilayah === 'dalam_negeri' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main' }}">
                    <span>🇮🇩</span>
                    <span>Dalam Negeri</span>
                </button>
                <button type="button" 
                        wire:click="$set('cakupan_wilayah', 'luar_negeri')" 
                        class="px-3.5 py-1.5 text-xs font-semibold rounded-lg transition-all flex items-center gap-1.5 {{ $cakupan_wilayah === 'luar_negeri' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main' }}">
                    <span>🌐</span>
                    <span>Luar Negeri</span>
                </button>
            </div>
        </div>

        @if($cakupan_wilayah === 'dalam_negeri')
            {{-- MODE DALAM NEGERI (STANDAR KEMENDAGRI) --}}
            <div class="space-y-4">
                {{-- Alamat Jalan / Kampung --}}
                <div>
                    <label class="block text-xs font-semibold text-text-secondary mb-1">Alamat Jalan / Kampung / No. Rumah</label>
                    <textarea wire:model="alamat" rows="2" placeholder="Contoh: Jl. Merdeka No. 45 / Kp. Babakan Sukamaju" class="w-full text-sm rounded-md border-border bg-surface text-text-main"></textarea>
                </div>

                {{-- RT, RW, Dusun, Kode Pos --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">RT</label>
                        <input type="text" wire:model="rt" placeholder="Contoh: 01" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">RW</label>
                        <input type="text" wire:model="rw" placeholder="Contoh: 03" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Dusun / Lingkungan</label>
                        <input type="text" wire:model="dusun" placeholder="Contoh: Dusun Cikole" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Kode Pos</label>
                        <input type="text" wire:model="kode_pos" placeholder="Contoh: 44151" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
                    </div>
                </div>

                {{-- Provinsi & Kabupaten/Kota (Cascading Hybrid) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-semibold text-text-secondary">
                                Provinsi <span class="text-rose-500">*</span>
                            </label>
                            <button type="button" wire:click="$toggle('isManualProvinsi')" class="text-[11px] font-medium text-primary hover:underline">
                                {{ $isManualProvinsi ? '📋 Pilih Daftar' : '✏️ Ketik Manual' }}
                            </button>
                        </div>
                        @if ($isManualProvinsi)
                            <input type="text" wire:model.live="provinsi" placeholder="Contoh: Jawa Barat" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
                        @else
                            <select wire:model.live="provinsi" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
                                <option value="">-- Pilih Provinsi --</option>
                                @if ($provinsi && !in_array($provinsi, $provinceList))
                                    <option value="{{ $provinsi }}">{{ $provinsi }} (Saat Ini)</option>
                                @endif
                                @foreach ($provinceList as $p)
                                    <option value="{{ $p }}">{{ $p }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-semibold text-text-secondary">
                                Kabupaten / Kota <span class="text-rose-500">*</span>
                            </label>
                            <button type="button" wire:click="$toggle('isManualKabupaten')" class="text-[11px] font-medium text-primary hover:underline">
                                {{ $isManualKabupaten ? '📋 Pilih Daftar' : '✏️ Ketik Manual' }}
                            </button>
                        </div>
                        @if ($isManualKabupaten)
                            <input type="text" wire:model.live="kabupaten_kota" placeholder="Contoh: Kab. Garut" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
                        @else
                            <select wire:model.live="kabupaten_kota" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
                                <option value="">-- Pilih Kabupaten / Kota --</option>
                                @if ($kabupaten_kota && !in_array($kabupaten_kota, $kabupatenList))
                                    <option value="{{ $kabupaten_kota }}">{{ $kabupaten_kota }} (Saat Ini)</option>
                                @endif
                                @foreach ($kabupatenList as $k)
                                    <option value="{{ $k }}">{{ $k }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                </div>

                {{-- Kecamatan & Desa/Kelurahan (Cascading Hybrid) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-semibold text-text-secondary">
                                Kecamatan
                            </label>
                            <button type="button" wire:click="$toggle('isManualKecamatan')" class="text-[11px] font-medium text-primary hover:underline">
                                {{ $isManualKecamatan ? '📋 Pilih Daftar' : '✏️ Ketik Manual' }}
                            </button>
                        </div>
                        @if ($isManualKecamatan || empty($kecamatanList))
                            <input type="text" wire:model.live.debounce.300ms="kecamatan" placeholder="Contoh: Tarogong Kaler" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
                        @else
                            <select wire:model.live="kecamatan" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
                                <option value="">-- Pilih Kecamatan --</option>
                                @if ($kecamatan && !in_array($kecamatan, $kecamatanList))
                                    <option value="{{ $kecamatan }}">{{ $kecamatan }} (Saat Ini)</option>
                                @endif
                                @foreach ($kecamatanList as $kec)
                                    <option value="{{ $kec }}">{{ $kec }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-semibold text-text-secondary">
                                Kelurahan / Desa
                            </label>
                            <button type="button" wire:click="$toggle('isManualKelurahan')" class="text-[11px] font-medium text-primary hover:underline">
                                {{ $isManualKelurahan ? '📋 Pilih Daftar' : '✏️ Ketik Manual' }}
                            </button>
                        </div>
                        @if ($isManualKelurahan || empty($desaList))
                            <input type="text" wire:model="kelurahan" placeholder="Contoh: Pananjung" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
                        @else
                            <select wire:model="kelurahan" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
                                <option value="">-- Pilih Desa / Kelurahan --</option>
                                @if ($kelurahan && !in_array($kelurahan, $desaList))
                                    <option value="{{ $kelurahan }}">{{ $kelurahan }} (Saat Ini)</option>
                                @endif
                                @foreach ($desaList as $d)
                                    <option value="{{ $d }}">{{ $d }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                </div>
            </div>
        @else
            {{-- MODE LUAR NEGERI --}}
            <div class="p-4 rounded-xl border border-amber-200 dark:border-amber-800/50 bg-amber-50/50 dark:bg-amber-950/20 space-y-4">
                <div class="text-xs text-amber-800 dark:text-amber-300 font-medium flex items-center gap-1.5">
                    <span>💡</span>
                    <span>Siswa berdomisili di luar negeri. Masukkan negara, provinsi/state, kota, dan rincian alamat manual.</span>
                </div>

                {{-- Negara & Provinsi/State --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Negara <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model="negara" list="listNegaraLuarCreate" placeholder="Contoh: Malaysia, Arab Saudi, dll." class="w-full text-sm rounded-md border-border bg-surface text-text-main">
                        <datalist id="listNegaraLuarCreate">
                            @foreach($negaraList as $nl)
                                <option value="{{ $nl }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Provinsi / State / Negeri Bagian</label>
                        <input type="text" wire:model="provinsi" placeholder="Contoh: Sabah, Sarawak, Mekkah, dll." class="w-full text-sm rounded-md border-border bg-surface text-text-main">
                    </div>
                </div>

                {{-- Kota & Kode Pos Luar Negeri --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Kota / Distrik / Daerah</label>
                        <input type="text" wire:model="kabupaten_kota" placeholder="Contoh: Tawau, Sandakan, Jeddah, dll." class="w-full text-sm rounded-md border-border bg-surface text-text-main">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Kode Pos Luar Negeri (Opsional)</label>
                        <input type="text" wire:model="kode_pos" placeholder="Contoh: 91000" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
                    </div>
                </div>

                {{-- Alamat Manual Luar Negeri --}}
                <div>
                    <label class="block text-xs font-semibold text-text-secondary mb-1">Alamat Lengkap / Keterangan Asal</label>
                    <textarea wire:model="alamat" rows="2" placeholder="Contoh: CLC Ladang Holyrood, Km 15 Jalan Apas / Konsulat RI Tawau" class="w-full text-sm rounded-md border-border bg-surface text-text-main"></textarea>
                </div>
            </div>
        @endif

        {{-- DATA DOMISILI & TRANSPORTASI --}}
        <div class="pt-4 border-t border-border space-y-4">
            <h4 class="text-xs font-bold text-text-muted uppercase tracking-wider">Informasi Domisili & Transportasi</h4>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-text-secondary mb-1">Jenis Tinggal</label>
                    <input type="text" wire:model="jenis_tinggal" placeholder="Pesantren / Asrama / Bersama Orang Tua / Kost" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-secondary mb-1">Alat Transportasi</label>
                    <input type="text" wire:model="alat_transportasi" placeholder="Jalan kaki / Motor / Angkutan umum" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-secondary mb-1">Jarak ke Sekolah (KM)</label>
                    <input type="number" step="0.1" wire:model="jarak_ke_sekolah_km" placeholder="Misal: 3.5" class="w-full text-sm rounded-md border-border bg-surface text-text-main">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-text-secondary mb-1">Lintang (Latitude)</label>
                    <input type="text" wire:model="lintang" placeholder="-7.213xxx" class="w-full text-sm font-mono rounded-md border-border bg-surface text-text-main">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-secondary mb-1">Bujur (Longitude)</label>
                    <input type="text" wire:model="bujur" placeholder="107.901xxx" class="w-full text-sm font-mono rounded-md border-border bg-surface text-text-main">
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Tab 3: Orang Tua & Wali -->
    @if($activeTab === 'orangtua')
    <div class="space-y-6">
        <!-- Data Ayah -->
        <div class="card p-6 space-y-4">
            <h3 class="text-sm font-bold text-primary uppercase tracking-wider">
                Data Ayah Kandung
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-text-main mb-1">Nama Ayah Kandung</label>
                    <input type="text" wire:model="ayah_nama" placeholder="Nama lengkap ayah" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-main mb-1">Tahun Lahir</label>
                    <input type="number" wire:model="ayah_tahun_lahir" placeholder="Contoh: 1975" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-text-main mb-1">Jenjang Pendidikan</label>
                    <input type="text" wire:model="ayah_jenjang_pendidikan" placeholder="SMA / S1 / SMP" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-main mb-1">Pekerjaan</label>
                    <input type="text" wire:model="ayah_pekerjaan" placeholder="Wiraswasta / PNS / Karyawan" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-main mb-1">Penghasilan Bulanan</label>
                    <input type="text" wire:model="ayah_penghasilan" placeholder="Misal: Rp 2.000.000 - Rp 5.000.000" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-text-main mb-1">NIK Ayah (16 Digit)</label>
                <input type="text" wire:model="ayah_nik" placeholder="3205xxxxxxxxxxxx" class="w-full text-sm font-mono rounded-lg border-border bg-surface text-text-main" maxlength="20">
            </div>
        </div>

        <!-- Data Ibu -->
        <div class="card p-6 space-y-4">
            <h3 class="text-sm font-bold text-pink-600 dark:text-pink-400 uppercase tracking-wider">
                Data Ibu Kandung
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-text-main mb-1">Nama Ibu Kandung</label>
                    <input type="text" wire:model="ibu_nama" placeholder="Nama lengkap ibu" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-main mb-1">Tahun Lahir</label>
                    <input type="number" wire:model="ibu_tahun_lahir" placeholder="Contoh: 1980" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-text-main mb-1">Jenjang Pendidikan</label>
                    <input type="text" wire:model="ibu_jenjang_pendidikan" placeholder="SMA / S1 / SMP" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-main mb-1">Pekerjaan</label>
                    <input type="text" wire:model="ibu_pekerjaan" placeholder="Ibu Rumah Tangga / Wiraswasta" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-main mb-1">Penghasilan Bulanan</label>
                    <input type="text" wire:model="ibu_penghasilan" placeholder="Misal: Tidak Berpenghasilan" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-text-main mb-1">NIK Ibu (16 Digit)</label>
                <input type="text" wire:model="ibu_nik" placeholder="3205xxxxxxxxxxxx" class="w-full text-sm font-mono rounded-lg border-border bg-surface text-text-main" maxlength="20">
            </div>
        </div>

        <!-- Data Wali -->
        <div class="card p-6 space-y-4">
            <h3 class="text-sm font-bold text-text-secondary uppercase tracking-wider">
                Data Wali (Opsional / Jika Ada)
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-text-main mb-1">Nama Wali</label>
                    <input type="text" wire:model="wali_nama" placeholder="Nama wali jika tinggal bersama wali" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-main mb-1">Tahun Lahir</label>
                    <input type="number" wire:model="wali_tahun_lahir" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-text-main mb-1">Jenjang Pendidikan</label>
                    <input type="text" wire:model="wali_jenjang_pendidikan" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-main mb-1">Pekerjaan</label>
                    <input type="text" wire:model="wali_pekerjaan" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-main mb-1">Penghasilan</label>
                    <input type="text" wire:model="wali_penghasilan" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-text-main mb-1">NIK Wali (16 Digit)</label>
                <input type="text" wire:model="wali_nik" class="w-full text-sm font-mono rounded-lg border-border bg-surface text-text-main" maxlength="20">
            </div>
        </div>
    </div>
    @endif

    <!-- Tab 4: Bantuan, Rekening & Dokumen -->
    @if($activeTab === 'dokumen')
    <div class="card p-6 space-y-6">
        <h3 class="text-base font-bold text-text-main border-b border-border pb-3">
            Dokumen Resmi, Sekolah Asal, dan Bantuan Sosial
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-text-main mb-1">No. KK (Kartu Keluarga)</label>
                <input type="text" wire:model="no_kk" placeholder="Nomor KK 16 digit" class="w-full text-sm font-mono rounded-lg border-border bg-surface text-text-main" maxlength="20">
            </div>
            <div>
                <label class="block text-xs font-semibold text-text-main mb-1">No. Registrasi Akta Lahir</label>
                <input type="text" wire:model="no_akta_lahir" placeholder="Nomor registrasi akta kelahiran" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
            </div>
            <div>
                <label class="block text-xs font-semibold text-text-main mb-1">Sekolah Asal (SMP/MTs)</label>
                <input type="text" wire:model="sekolah_asal" placeholder="SMPN 1 Garut / MTs..." class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-text-main mb-1">SKHUN</label>
                <input type="text" wire:model="skhun" placeholder="Nomor SKHUN SMP" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
            </div>
            <div>
                <label class="block text-xs font-semibold text-text-main mb-1">No. Peserta UN</label>
                <input type="text" wire:model="no_peserta_un" placeholder="Nomor peserta UN SMP" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
            </div>
            <div>
                <label class="block text-xs font-semibold text-text-main mb-1">No. Seri Ijazah SMP</label>
                <input type="text" wire:model="no_seri_ijazah" placeholder="DN-xx/xxxx..." class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
            </div>
        </div>

        <!-- Bantuan KPS, KIP, PIP, KKS -->
        <div class="p-4 bg-surface-hover/50 rounded-xl border border-border space-y-4">
            <h4 class="text-xs font-bold uppercase tracking-wider text-text-main">
                Bantuan Sosial & Rekening PIP
            </h4>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model.live="penerima_kps" class="rounded border-border text-primary focus:ring-primary">
                        <span class="text-xs font-semibold text-text-main">Penerima KPS (Kartu Perlindungan Sosial)</span>
                    </label>
                    @if($penerima_kps)
                    <input type="text" wire:model="no_kps" placeholder="Nomor KPS" class="w-full text-xs rounded-lg border-border bg-surface text-text-main">
                    @endif
                </div>
                <div class="space-y-2">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model.live="penerima_kip" class="rounded border-border text-primary focus:ring-primary">
                        <span class="text-xs font-semibold text-text-main">Penerima KIP (Kartu Indonesia Pintar)</span>
                    </label>
                    @if($penerima_kip)
                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" wire:model="nomor_kip" placeholder="Nomor KIP" class="w-full text-xs rounded-lg border-border bg-surface text-text-main">
                        <input type="text" wire:model="nama_di_kip" placeholder="Nama Tertera di KIP" class="w-full text-xs rounded-lg border-border bg-surface text-text-main">
                    </div>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-text-main mb-1">Nomor KKS (Kartu Keluarga Sejahtera)</label>
                    <input type="text" wire:model="nomor_kks" placeholder="Nomor KKS" class="w-full text-xs rounded-lg border-border bg-surface text-text-main">
                </div>
                <div class="space-y-2">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model.live="layak_pip" class="rounded border-border text-primary focus:ring-primary">
                        <span class="text-xs font-semibold text-text-main">Layak PIP (Usulan Sekolah)</span>
                    </label>
                    @if($layak_pip)
                    <input type="text" wire:model="alasan_layak_pip" placeholder="Alasan Layak PIP (misal: Pemegang PKH / Yatim Piatu)" class="w-full text-xs rounded-lg border-border bg-surface text-text-main">
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-semibold text-text-main mb-1">Nama Bank Penyalur</label>
                    <input type="text" wire:model="bank" placeholder="BRI / BNI / Mandiri" class="w-full text-xs rounded-lg border-border bg-surface text-text-main">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-main mb-1">Nomor Rekening Bank</label>
                    <input type="text" wire:model="nomor_rekening_bank" placeholder="Nomor rekening" class="w-full text-xs font-mono rounded-lg border-border bg-surface text-text-main">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-main mb-1">Rekening Atas Nama</label>
                    <input type="text" wire:model="rekening_atas_nama" placeholder="Nama pemilik rekening" class="w-full text-xs rounded-lg border-border bg-surface text-text-main">
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Tab 5: Fisik & Kontak -->
    @if($activeTab === 'fisik')
    <div class="card p-6 space-y-6">
        <h3 class="text-base font-bold text-text-main border-b border-border pb-3">
            Data Fisik, Berkala, dan Informasi Kontak
        </h3>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-semibold text-text-main mb-1">Berat Badan (KG)</label>
                <input type="number" wire:model="berat_badan" placeholder="Contoh: 50" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
            </div>
            <div>
                <label class="block text-xs font-semibold text-text-main mb-1">Tinggi Badan (CM)</label>
                <input type="number" wire:model="tinggi_badan" placeholder="Contoh: 165" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
            </div>
            <div>
                <label class="block text-xs font-semibold text-text-main mb-1">Lingkar Kepala (CM)</label>
                <input type="number" wire:model="lingkar_kepala" placeholder="Contoh: 54" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
            </div>
            <div>
                <label class="block text-xs font-semibold text-text-main mb-1">Anak Ke-berapa</label>
                <input type="number" wire:model="anak_ke_berapa" placeholder="Contoh: 1" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-text-main mb-1">Jumlah Saudara Kandung</label>
                <input type="number" wire:model="jml_saudara_kandung" placeholder="Contoh: 2" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
            </div>
            <div>
                <label class="block text-xs font-semibold text-text-main mb-1">Kebutuhan Khusus</label>
                <input type="text" wire:model="kebutuhan_khusus" placeholder="Tidak ada / Tunanetra / dll." class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
            </div>
        </div>

        <!-- Kontak -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
            <div>
                <label class="block text-xs font-semibold text-text-main mb-1">No. WhatsApp / HP</label>
                <input type="text" wire:model="hp" placeholder="08xxxxxxxxxx" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
            </div>
            <div>
                <label class="block text-xs font-semibold text-text-main mb-1">Telepon Rumah</label>
                <input type="text" wire:model="telepon" placeholder="0262xxxxxx" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
            </div>
            <div>
                <label class="block text-xs font-semibold text-text-main mb-1">Email Siswa</label>
                <input type="email" wire:model="email" placeholder="nama@email.com" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
            </div>
        </div>
    </div>
    @endif

    <!-- Action Buttons Footer -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-4 border-t border-border">
        <a href="{{ route('kesiswaan.siswa') }}" class="btn btn-secondary w-full sm:w-auto text-center">
            &larr; Batal & Kembali
        </a>
        <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto justify-end">
            <button type="button" wire:click="save(false)" wire:loading.attr="disabled" class="w-full sm:w-auto px-5 py-2.5 text-sm font-semibold text-primary bg-primary/10 hover:bg-primary/20 border border-primary/30 rounded-lg transition disabled:opacity-50">
                <span wire:loading.remove wire:target="save(false)">Simpan & Tambah Lagi</span>
                <span wire:loading wire:target="save(false)">Menyimpan...</span>
            </button>
            <button type="button" wire:click="save(true)" wire:loading.attr="disabled" class="w-full sm:w-auto btn btn-primary px-6 py-2.5 disabled:opacity-50">
                <span wire:loading.remove wire:target="save(true)">Simpan & Selesai</span>
                <span wire:loading wire:target="save(true)">Menyimpan...</span>
            </button>
        </div>
    </div>
</div>
