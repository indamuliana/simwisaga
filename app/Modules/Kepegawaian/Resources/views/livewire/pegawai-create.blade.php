<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-border">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('kepegawaian.pegawai') }}" class="text-sm text-text-secondary hover:text-primary transition">&larr; Kembali ke Data Pegawai</a>
                <span class="text-slate-300">/</span>
                <span class="text-xs font-semibold text-text-secondary">Input Pegawai Baru</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-text-main mt-1">Tambah Pegawai (GTK) Baru</h1>
            <p class="text-sm text-text-secondary">Lengkapi data profil guru dan karyawan, SK kepegawaian, riwayat, keluarga, dan 5 berkas foto.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('kepegawaian.pegawai') }}" class="btn btn-secondary text-xs">
                Batal
            </a>
            <button type="button" wire:click="save" wire:loading.attr="disabled" class="btn btn-primary text-xs px-5 shadow-xs transition disabled:opacity-50">
                <span wire:loading.remove wire:target="save">Simpan Pegawai</span>
                <span wire:loading wire:target="save">Menyimpan...</span>
            </button>
        </div>
    </div>

    <!-- Error Validation Alert -->
    @if ($errors->any())
        <div class="p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-300 dark:border-rose-800 rounded-xl">
            <div class="flex items-start">
                <svg class="h-5 w-5 text-rose-500 shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                <div class="ml-3">
                    <h3 class="text-sm font-semibold text-rose-800 dark:text-rose-300">Terdapat {{ $errors->count() }} isian yang perlu diperiksa:</h3>
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
        <button type="button" wire:click="setTab('identitas')" class="px-4 py-2 text-xs font-semibold rounded-xl transition {{ $activeTab === 'identitas' ? 'bg-primary text-white shadow-xs' : 'bg-surface hover:bg-surface-hover text-text-secondary border border-border' }}">
            1. Identitas Pribadi
        </button>
        <button type="button" wire:click="setTab('alamat')" class="px-4 py-2 text-xs font-semibold rounded-xl transition {{ $activeTab === 'alamat' ? 'bg-primary text-white shadow-xs' : 'bg-surface hover:bg-surface-hover text-text-secondary border border-border' }}">
            2. Alamat & Wilayah
        </button>
        <button type="button" wire:click="setTab('kepegawaian')" class="px-4 py-2 text-xs font-semibold rounded-xl transition {{ $activeTab === 'kepegawaian' ? 'bg-primary text-white shadow-xs' : 'bg-surface hover:bg-surface-hover text-text-secondary border border-border' }}">
            3. Kepegawaian & SK
        </button>
        <button type="button" wire:click="setTab('pajak')" class="px-4 py-2 text-xs font-semibold rounded-xl transition {{ $activeTab === 'pajak' ? 'bg-primary text-white shadow-xs' : 'bg-surface hover:bg-surface-hover text-text-secondary border border-border' }}">
            4. Pajak & BPJS
        </button>
        <button type="button" wire:click="setTab('pendidikan')" class="px-4 py-2 text-xs font-semibold rounded-xl transition {{ $activeTab === 'pendidikan' ? 'bg-primary text-white shadow-xs' : 'bg-surface hover:bg-surface-hover text-text-secondary border border-border' }}">
            5. Pendidikan & Pengalaman
        </button>
        <button type="button" wire:click="setTab('keluarga')" class="px-4 py-2 text-xs font-semibold rounded-xl transition {{ $activeTab === 'keluarga' ? 'bg-primary text-white shadow-xs' : 'bg-surface hover:bg-surface-hover text-text-secondary border border-border' }}">
            6. Data Keluarga
        </button>
        <button type="button" wire:click="setTab('foto')" class="px-4 py-2 text-xs font-semibold rounded-xl transition {{ $activeTab === 'foto' ? 'bg-primary text-white shadow-xs' : 'bg-surface hover:bg-surface-hover text-text-secondary border border-border' }}">
            7. Berkas Foto (5 Foto)
        </button>
        <button type="button" wire:click="setTab('akun')" class="px-4 py-2 text-xs font-semibold rounded-xl transition {{ $activeTab === 'akun' ? 'bg-primary text-white shadow-xs' : 'bg-surface hover:bg-surface-hover text-text-secondary border border-border' }}">
            8. Akun Login
        </button>
    </div>

    <!-- Form Body -->
    <div class="bg-surface border border-border rounded-2xl p-6 shadow-xs">
        <!-- 1. IDENTITAS PRIBADI -->
        @if($activeTab === 'identitas')
            <div class="space-y-6">
                <div class="border-b border-border pb-3">
                    <h2 class="text-base font-bold text-text-main">1. Identitas Pribadi Pegawai</h2>
                    <p class="text-xs text-text-secondary">Nama lengkap tanpa/dengan gelar, jenis kelamin, tanggal lahir, dan kontak resmi.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Gelar Depan</label>
                        <input type="text" wire:model="gelar_depan" placeholder="Drs. / Prof. / Dr." class="w-full text-sm rounded-xl border-border bg-surface text-text-main">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Nama Lengkap (Tanpa Gelar) <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model="nama_lengkap" placeholder="Contoh: Ahmad Hidayat" class="w-full text-sm rounded-xl border-border bg-surface text-text-main">
                        @error('nama_lengkap') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Gelar Belakang</label>
                        <input type="text" wire:model="gelar_belakang" placeholder="S.Kom., M.Pd." class="w-full text-sm rounded-xl border-border bg-surface text-text-main">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Jenis Kelamin <span class="text-rose-500">*</span></label>
                        <select wire:model="jenis_kelamin" class="w-full text-sm rounded-xl border-border bg-surface text-text-main">
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Tempat Lahir</label>
                        <input type="text" wire:model="tempat_lahir" placeholder="Kota / Kabupaten" class="w-full text-sm rounded-xl border-border bg-surface text-text-main">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Tanggal Lahir</label>
                        <input type="date" wire:model.live="tanggal_lahir" class="w-full text-sm rounded-xl border-border bg-surface text-text-main">
                        @if($tanggal_lahir)
                            <span class="text-xs text-primary font-medium mt-1 block">
                                Umur: {{ \Carbon\Carbon::parse($tanggal_lahir)->age }} Tahun (Otomatis)
                            </span>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">NIK (No. KTP)</label>
                        <input type="text" wire:model="nik" placeholder="16 digit NIK" maxlength="20" class="w-full text-sm font-mono rounded-xl border-border bg-surface text-text-main">
                        @error('nik') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Golongan Darah</label>
                        <select wire:model="golongan_darah" class="w-full text-sm rounded-xl border-border bg-surface text-text-main">
                            <option value="">-- Pilih --</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="AB">AB</option>
                            <option value="O">O</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">No. HP / WhatsApp</label>
                        <input type="text" wire:model="no_hp" placeholder="08xxxxxxxxxx" class="w-full text-sm rounded-xl border-border bg-surface text-text-main">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Email Resmi / Pribadi</label>
                        <input type="email" wire:model="email" placeholder="pegawai@wikrama.sch.id" class="w-full text-sm rounded-xl border-border bg-surface text-text-main">
                        @error('email') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        @endif

        <!-- 2. ALAMAT & WILAYAH -->
        @if($activeTab === 'alamat')
            <div class="space-y-6">
                <div class="border-b border-border pb-3">
                    <h2 class="text-base font-bold text-text-main">2. Alamat & Wilayah Domisili</h2>
                    <p class="text-xs text-text-secondary">Standarisasi Master Data Wilayah Kemendagri resmi dan opsi domisili Luar Negeri.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Cakupan Wilayah</label>
                        <select wire:model.live="jenis_wilayah" class="w-full text-sm rounded-xl border-border bg-surface text-text-main font-semibold">
                            <option value="dalam_negeri">🇮🇩 Dalam Negeri (Indonesia)</option>
                            <option value="luar_negeri">🌐 Luar Negeri</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Negara</label>
                        @if($jenis_wilayah === 'luar_negeri')
                            <select wire:model="negara" class="w-full text-sm rounded-xl border-border bg-surface text-text-main">
                                @foreach($negaraList as $neg)
                                    <option value="{{ $neg }}">{{ $neg }}</option>
                                @endforeach
                            </select>
                        @else
                            <input type="text" wire:model="negara" readonly class="w-full text-sm rounded-xl border-border bg-surface-ground text-text-secondary">
                        @endif
                    </div>
                </div>

                @if($jenis_wilayah === 'dalam_negeri')
                    <div class="p-4 rounded-xl bg-surface-ground/50 border border-border space-y-4">
                        <div class="text-xs font-bold text-text-main flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-primary"></span>
                            Pemilihan Wilayah Kemendagri (Cascading Otomatis)
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-text-secondary mb-1">Provinsi</label>
                                <input list="provinsi-options" wire:model.live="provinsi_nama" placeholder="Ketik / pilih Provinsi..." class="w-full text-sm rounded-xl border-border bg-surface text-text-main">
                                <datalist id="provinsi-options">
                                    @foreach($provinceList as $prov)
                                        <option value="{{ $prov }}"></option>
                                    @endforeach
                                </datalist>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-text-secondary mb-1">Kabupaten / Kota</label>
                                <input list="kabupaten-options" wire:model.live="kabupaten_nama" placeholder="Ketik / pilih Kab/Kota..." class="w-full text-sm rounded-xl border-border bg-surface text-text-main">
                                <datalist id="kabupaten-options">
                                    @foreach($kabupatenList as $kab)
                                        <option value="{{ $kab }}"></option>
                                    @endforeach
                                </datalist>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-text-secondary mb-1">Kecamatan</label>
                                <input list="kecamatan-options" wire:model.live="kecamatan_nama" placeholder="Ketik / pilih Kecamatan..." class="w-full text-sm rounded-xl border-border bg-surface text-text-main">
                                <datalist id="kecamatan-options">
                                    @foreach($kecamatanList as $kec)
                                        <option value="{{ $kec }}"></option>
                                    @endforeach
                                </datalist>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-text-secondary mb-1">Desa / Kelurahan</label>
                                <input list="desa-options" wire:model.live="desa_nama" placeholder="Ketik / pilih Desa/Kelurahan..." class="w-full text-sm rounded-xl border-border bg-surface text-text-main">
                                <datalist id="desa-options">
                                    @foreach($desaList as $desa)
                                        <option value="{{ $desa }}"></option>
                                    @endforeach
                                </datalist>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-text-secondary mb-1">Kode Pos</label>
                                <input type="text" wire:model="kode_pos" placeholder="5 digit kode pos" class="w-full text-sm font-mono rounded-xl border-border bg-surface text-text-main">
                            </div>
                        </div>
                    </div>
                @endif

                <div>
                    <label class="block text-xs font-semibold text-text-secondary mb-1">Alamat Lengkap (Jalan, RT/RW, Dusun, No. Rumah)</label>
                    <textarea wire:model="alamat" rows="3" placeholder="Contoh: Jl. Raya Wangun No. 21 RT 01 RW 04" class="w-full text-sm rounded-xl border-border bg-surface text-text-main"></textarea>
                </div>
            </div>
        @endif

        <!-- 3. KEPEGAWAIAN & SK -->
        @if($activeTab === 'kepegawaian')
            <div class="space-y-6">
                <div class="border-b border-border pb-3">
                    <h2 class="text-base font-bold text-text-main">3. Kepegawaian, SK & Sertifikasi</h2>
                    <p class="text-xs text-text-secondary">Klasifikasi jabatan, status GTK, nomor sertifikasi resmi, dan riwayat SK.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Jabatan (Jenis GTK) <span class="text-rose-500">*</span></label>
                        <select wire:model="jenis_gtk" class="w-full text-sm font-semibold rounded-xl border-border bg-surface text-text-main">
                            <option value="Guru">Guru</option>
                            <option value="Tendik">Tendik (Tenaga Kependidikan)</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Status Kepegawaian <span class="text-rose-500">*</span></label>
                        <select wire:model="status_kepegawaian" class="w-full text-sm font-semibold rounded-xl border-border bg-surface text-text-main">
                            <option value="GTY">GTY (Guru Tetap Yayasan)</option>
                            <option value="STY">STY (Staf Tetap Yayasan)</option>
                            <option value="GTT">GTT (Guru Tidak Tetap)</option>
                            <option value="STT">STT (Staf Tidak Tetap)</option>
                            <option value="IDS">IDS (Instruktur / Dosen Tamu)</option>
                            <option value="PNS DPK">PNS DPK (Diperbantukan)</option>
                            <option value="PPPK">PPPK</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Jabatan Struktural</label>
                        <input type="text" wire:model="jabatan_struktural" placeholder="Contoh: Kepala Sekolah / Waka Kurikulum" class="w-full text-sm rounded-xl border-border bg-surface text-text-main">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">NIP (Nomor Induk Pegawai)</label>
                        <input type="text" wire:model="nip" placeholder="18 digit atau strip" class="w-full text-sm font-mono rounded-xl border-border bg-surface text-text-main">
                        @error('nip') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">NUPTK (Nomor Unik Pendidik & Tendik)</label>
                        <input type="text" wire:model="nuptk" placeholder="16 digit NUPTK" class="w-full text-sm font-mono rounded-xl border-border bg-surface text-text-main">
                        @error('nuptk') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Nomor Peserta UKG (SIMPKB-ID)</label>
                        <input type="text" wire:model="nomor_peserta_ukg" placeholder="Nomor akun SIMPKB" class="w-full text-sm font-mono rounded-xl border-border bg-surface text-text-main">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Nomor Registrasi Guru (NRG)</label>
                        <input type="text" wire:model="nomor_registrasi_guru" placeholder="Nomor registrasi guru sertifikasi" class="w-full text-sm font-mono rounded-xl border-border bg-surface text-text-main">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Nomor Peserta Sertifikasi (Sertifikat Pendidik)</label>
                        <input type="text" wire:model="nomor_peserta_sertifikasi" placeholder="No. peserta serdik" class="w-full text-sm font-mono rounded-xl border-border bg-surface text-text-main">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Nomor Sertifikat Pendidik</label>
                        <input type="text" wire:model="nomor_sertifikat_pendidik" placeholder="Nomor blangko sertifikat" class="w-full text-sm font-mono rounded-xl border-border bg-surface text-text-main">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">TMT Awal Menjadi Guru</label>
                        <input type="date" wire:model="tmt_awal_guru" class="w-full text-sm rounded-xl border-border bg-surface text-text-main">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">No. SK Mulai Menjadi Guru</label>
                        <input type="text" wire:model="no_sk_mulai_guru" placeholder="Nomor SK pengangkatan awal" class="w-full text-sm rounded-xl border-border bg-surface text-text-main">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Tgl. Diangkat Menjadi Pegawai Tetap</label>
                        <input type="date" wire:model="tgl_diangkat_pegawai_tetap" class="w-full text-sm rounded-xl border-border bg-surface text-text-main">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">No. SK Tetap Yayasan</label>
                        <input type="text" wire:model="no_sk_tetap_yayasan" placeholder="Nomor SK tetap yayasan" class="w-full text-sm rounded-xl border-border bg-surface text-text-main">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 rounded-xl bg-surface-ground/50 border border-border items-center">
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Tgl. Awal Masuk Wikrama</label>
                        <input type="date" wire:model.live="tgl_awal_masuk_wikrama" class="w-full text-sm rounded-xl border-border bg-surface text-text-main">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Perhitungan Otomatis Masa Kerja</label>
                        <div class="text-sm font-semibold text-text-main">
                            @if($tgl_awal_masuk_wikrama)
                                @php
                                    $diff = \Carbon\Carbon::parse($tgl_awal_masuk_wikrama)->diff(now());
                                @endphp
                                <span class="text-primary">{{ $diff->y }} Tahun</span> • <span class="text-emerald-600 dark:text-emerald-400">{{ $diff->y }} Tahun {{ $diff->m }} Bulan</span>
                            @else
                                <span class="text-xs text-text-secondary italic">Isi tanggal masuk wikrama untuk kalkulasi otomatis</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" id="is_active" wire:model="is_active" class="rounded border-border text-primary focus:ring-primary">
                    <label for="is_active" class="text-xs font-semibold text-text-main cursor-pointer">Pegawai Aktif Bertugas di Sekolah</label>
                </div>
            </div>
        @endif

        <!-- 4. PAJAK & BPJS -->
        @if($activeTab === 'pajak')
            <div class="space-y-6">
                <div class="border-b border-border pb-3">
                    <h2 class="text-base font-bold text-text-main">4. Pajak & Jaminan Sosial (BPJS)</h2>
                    <p class="text-xs text-text-secondary">Nomor Pokok Wajib Pajak dan nomor kartu jaminan sosial kesehatan serta ketenagakerjaan.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">NPWP (Nomor Pokok Wajib Pajak)</label>
                        <input type="text" wire:model="npwp" placeholder="15 / 16 digit NPWP" class="w-full text-sm font-mono rounded-xl border-border bg-surface text-text-main">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Nama Wajib Pajak</label>
                        <input type="text" wire:model="nama_wajib_pajak" placeholder="Nama sesuai kartu NPWP" class="w-full text-sm rounded-xl border-border bg-surface text-text-main">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">No. Kartu BPJS Kesehatan</label>
                        <input type="text" wire:model="no_kartu_bpjs_kesehatan" placeholder="Nomor peserta BPJS Kesehatan" class="w-full text-sm font-mono rounded-xl border-border bg-surface text-text-main">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">No. Kartu BPJS Ketenagakerjaan</label>
                        <input type="text" wire:model="no_kartu_bpjs_ketenagakerjaan" placeholder="Nomor KPJ BPJS Ketenagakerjaan" class="w-full text-sm font-mono rounded-xl border-border bg-surface text-text-main">
                    </div>
                </div>
            </div>
        @endif

        <!-- 5. PENDIDIKAN & PENGALAMAN -->
        @if($activeTab === 'pendidikan')
            <div class="space-y-6">
                <div class="border-b border-border pb-3">
                    <h2 class="text-base font-bold text-text-main">5. Riwayat Pendidikan & Pengalaman Kerja</h2>
                    <p class="text-xs text-text-secondary">Pendidikan terakhir yang dituntaskan dan riwayat pengalaman kerja (maksimal 7 riwayat).</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 p-4 rounded-xl bg-surface-ground/50 border border-border">
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Pendidikan Terakhir</label>
                        <select wire:model="pendidikan_terakhir" class="w-full text-sm rounded-xl border-border bg-surface text-text-main">
                            <option value="">-- Pilih --</option>
                            <option value="SMA/SMK">SMA / SMK / MA</option>
                            <option value="D3">D3 (Diploma 3)</option>
                            <option value="D4">D4 (Diploma 4)</option>
                            <option value="S1">S1 (Sarjana)</option>
                            <option value="S2">S2 (Magister)</option>
                            <option value="S3">S3 (Doktor)</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Jurusan & Nama Instansi</label>
                        <input type="text" wire:model="jurusan_dan_instansi" placeholder="Format: Jurusan X - Universitas/Sekolah Y" class="w-full text-sm rounded-xl border-border bg-surface text-text-main">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Tahun Lulus</label>
                        <input type="text" wire:model="tahun_lulus_pendidikan_terakhir" placeholder="Contoh: 2018" maxlength="10" class="w-full text-sm font-mono rounded-xl border-border bg-surface text-text-main">
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="text-xs font-bold text-text-main flex items-center justify-between">
                        <span>Daftar Pengalaman Kerja (1 s/d 7)</span>
                        <span class="text-text-secondary font-normal">Isi sesuai riwayat yang ada</span>
                    </div>

                    @for($i = 0; $i < 7; $i++)
                        <div class="p-4 rounded-xl border border-border bg-surface-ground/30 space-y-2">
                            <span class="text-[11px] font-bold uppercase text-primary">Pengalaman Kerja {{ $i + 1 }}</span>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div>
                                    <input type="text" wire:model="pengalaman_kerja.{{ $i }}.lembaga" placeholder="Nama Lembaga / Perusahaan" class="w-full text-xs rounded-lg border-border bg-surface text-text-main">
                                </div>
                                <div>
                                    <input type="text" wire:model="pengalaman_kerja.{{ $i }}.posisi" placeholder="Posisi / Jabatan" class="w-full text-xs rounded-lg border-border bg-surface text-text-main">
                                </div>
                                <div>
                                    <input type="text" wire:model="pengalaman_kerja.{{ $i }}.periode" placeholder="Periode (Contoh: 2018 - 2021)" class="w-full text-xs rounded-lg border-border bg-surface text-text-main">
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        @endif

        <!-- 6. DATA KELUARGA -->
        @if($activeTab === 'keluarga')
            <div class="space-y-6">
                <div class="border-b border-border pb-3">
                    <h2 class="text-base font-bold text-text-main">6. Data Keluarga Pegawai</h2>
                    <p class="text-xs text-text-secondary">Status pernikahan, data pasangan, anak (1 s/d 4), dan orang tua kandung.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Status Pernikahan</label>
                        <select wire:model="status_pernikahan" class="w-full text-sm rounded-xl border-border bg-surface text-text-main">
                            <option value="Belum Menikah">Belum Menikah</option>
                            <option value="Menikah">Menikah</option>
                            <option value="Duda">Duda</option>
                            <option value="Janda">Janda</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Nama Istri / Suami</label>
                        <input type="text" wire:model="nama_pasangan" placeholder="Nama lengkap pasangan" class="w-full text-sm rounded-xl border-border bg-surface text-text-main">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Tgl. Lahir Istri / Suami</label>
                        <input type="date" wire:model="tgl_lahir_pasangan" class="w-full text-sm rounded-xl border-border bg-surface text-text-main">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Nama Ayah Kandung</label>
                        <input type="text" wire:model="nama_ayah" placeholder="Nama lengkap ayah" class="w-full text-sm rounded-xl border-border bg-surface text-text-main">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Nama Ibu Kandung</label>
                        <input type="text" wire:model="nama_ibu" placeholder="Nama lengkap ibu" class="w-full text-sm rounded-xl border-border bg-surface text-text-main">
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="text-xs font-bold text-text-main flex items-center justify-between">
                        <span>Data Anak (Slot 1 s/d 4)</span>
                        <span class="text-xs text-primary font-semibold">Jumlah Terdata: {{ $jumlah_anak }} Anak</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @for($i = 0; $i < 4; $i++)
                            <div class="p-4 rounded-xl border border-border bg-surface-ground/30 space-y-2">
                                <span class="text-[11px] font-bold uppercase text-primary">Anak {{ $i + 1 }}</span>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-[11px] text-text-secondary mb-1">Nama Anak</label>
                                        <input type="text" wire:model.live.debounce.300ms="data_anak.{{ $i }}.nama" placeholder="Nama anak ke-{{ $i + 1 }}" class="w-full text-xs rounded-lg border-border bg-surface text-text-main">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] text-text-secondary mb-1">Tanggal Lahir</label>
                                        <input type="date" wire:model="data_anak.{{ $i }}.tgl_lahir" class="w-full text-xs rounded-lg border-border bg-surface text-text-main">
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        @endif

        <!-- 7. BERKAS FOTO -->
        @if($activeTab === 'foto')
            <div class="space-y-6">
                <div class="border-b border-border pb-3">
                    <h2 class="text-base font-bold text-text-main">7. Berkas Foto Pegawai (1 Foto Utama & 4 Foto Tambahan)</h2>
                    <p class="text-xs text-text-secondary">Foto Profil Utama (Slot 1) bersifat wajib, sedangkan Slot 2 s/d 5 opsional untuk melengkapi arsip.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    <!-- Slot 1: Foto Utama (Wajib) -->
                    <div class="rounded-2xl border-2 border-primary/40 p-4 bg-primary/5 space-y-3 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold uppercase text-primary">★ Slot 1 (Utama) *</span>
                                <span class="text-[10px] px-1.5 py-0.5 rounded bg-rose-100 text-rose-700 font-bold">Wajib</span>
                            </div>
                            <div class="aspect-square rounded-xl border border-dashed border-primary/40 flex items-center justify-center overflow-hidden bg-surface relative">
                                @if($foto_1)
                                    <img src="{{ $foto_1->temporaryUrl() }}" class="w-full h-full object-cover">
                                @else
                                    <div class="text-center p-2 text-slate-400">
                                        <svg class="w-8 h-8 mx-auto mb-1 opacity-50 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        <span class="text-[10px]">Pilih Foto Profil</span>
                                    </div>
                                @endif
                            </div>
                            @error('foto_1') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-2">
                            <input type="file" wire:model="foto_1" accept="image/*" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[11px] file:bg-primary file:text-white cursor-pointer w-full">
                            <input type="text" wire:model="keterangan_1" placeholder="Keterangan Foto Utama" class="w-full text-xs rounded-lg border-border bg-surface text-text-main">
                        </div>
                    </div>

                    <!-- Slot 2 s/d 5: Foto Tambahan -->
                    @for($s = 2; $s <= 5; $s++)
                        @php
                            $propFoto = "foto_{$s}";
                            $propKet = "keterangan_{$s}";
                        @endphp
                        <div class="rounded-2xl border border-border p-4 bg-surface-ground/30 space-y-3 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-bold uppercase text-text-secondary">Slot {{ $s }} (Tambahan)</span>
                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-surface border border-border text-text-secondary">Opsional</span>
                                </div>
                                <div class="aspect-square rounded-xl border border-dashed border-border flex items-center justify-center overflow-hidden bg-surface relative">
                                    @if($$propFoto)
                                        <img src="{{ $$propFoto->temporaryUrl() }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="text-center p-2 text-slate-400">
                                            <svg class="w-8 h-8 mx-auto mb-1 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <span class="text-[10px]">Foto Tambahan {{ $s - 1 }}</span>
                                        </div>
                                    @endif
                                </div>
                                @error($propFoto) <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-2">
                                <input type="file" wire:model="{{ $propFoto }}" accept="image/*" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[11px] file:bg-surface-ground file:text-text-main cursor-pointer w-full">
                                <input type="text" wire:model="{{ $propKet }}" placeholder="Keterangan (opsional)" class="w-full text-xs rounded-lg border-border bg-surface text-text-main">
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        @endif

        <!-- 8. AKUN LOGIN -->
        @if($activeTab === 'akun')
            <div class="space-y-6 max-w-2xl">
                <div class="border-b border-border pb-3">
                    <h2 class="text-base font-bold text-text-main">8. Akun Login SIWARAGA</h2>
                    <p class="text-xs text-text-secondary">Opsi membuat akun akses login aplikasi untuk pegawai yang bersangkutan.</p>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" id="buat_akun" wire:model.live="buat_akun" class="rounded border-border text-primary focus:ring-primary">
                    <label for="buat_akun" class="text-xs font-semibold text-text-main cursor-pointer">Buat Akun Pengguna Sekarang</label>
                </div>

                @if($buat_akun)
                    <div class="p-4 rounded-xl border border-border bg-surface-ground/40 space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-text-secondary mb-1">Username Login <span class="text-rose-500">*</span></label>
                            <input type="text" wire:model="username" placeholder="Contoh: ahmad.hidayat" class="w-full text-sm rounded-xl border-border bg-surface text-text-main">
                            @error('username') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-text-secondary mb-1">Password Baru <span class="text-rose-500">*</span></label>
                            <input type="password" wire:model="password" placeholder="Minimal 6 karakter" class="w-full text-sm rounded-xl border-border bg-surface text-text-main">
                            @error('password') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-text-secondary mb-1">Role / Peran Akses <span class="text-rose-500">*</span></label>
                            <select wire:model="role_name" class="w-full text-sm rounded-xl border-border bg-surface text-text-main font-semibold">
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Bottom Actions -->
    <div class="flex items-center justify-between border-t border-border pt-4">
        <a href="{{ route('kepegawaian.pegawai') }}" class="btn btn-secondary text-xs">
            Batal
        </a>
        <button type="button" wire:click="save" wire:loading.attr="disabled" class="btn btn-primary text-xs px-6 shadow-xs disabled:opacity-50">
            <span wire:loading.remove wire:target="save">Simpan Pegawai</span>
            <span wire:loading wire:target="save">Menyimpan...</span>
        </button>
    </div>
</div>
