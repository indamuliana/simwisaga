<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-border">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('kepegawaian.pegawai') }}" class="text-sm text-text-secondary hover:text-primary transition">&larr; Kembali ke Data Pegawai</a>
                <span class="text-slate-300">/</span>
                <a href="{{ route('kepegawaian.pegawai.show', $pegawai->id) }}" class="text-xs text-text-secondary hover:underline">Detail Pegawai</a>
                <span class="text-slate-300">/</span>
                <span class="text-xs font-semibold text-text-secondary">Ubah Data</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-text-main mt-1">Ubah Data Pegawai: {{ $pegawai->nama_gelar }}</h1>
            <p class="text-sm text-text-secondary">Perbarui rincian profil, status kepegawaian, riwayat pendidikan, keluarga, dan berkas foto.</p>
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

    <!-- Form Body (Satu Kontainer Kartu Raksasa Utuh) -->
    <div class="bg-surface border border-border rounded-2xl p-6 sm:p-8 shadow-xs space-y-8">
        
        <!-- 1. IDENTITAS PRIBADI -->
        <section class="space-y-6">
            <div class="border-b border-border pb-3">
                <h2 class="text-base font-bold text-text-main flex items-center gap-2">
                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-primary/10 text-primary text-xs font-bold">1</span>
                    Identitas Pribadi Pegawai
                </h2>
                <p class="text-xs text-text-secondary mt-0.5">Nama lengkap tanpa/dengan gelar, jenis kelamin, tanggal lahir, dan kontak resmi.</p>
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

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-text-secondary mb-1">NIY (No. Induk Yayasan)</label>
                    <input type="text" wire:model="niy" placeholder="PRW..." class="w-full text-sm font-mono rounded-xl border-border bg-surface text-text-main">
                    @error('niy') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
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
        </section>

        <!-- 2. ALAMAT & WILAYAH -->
        <section class="border-t border-border pt-6 space-y-6">
            <div class="border-b border-border pb-3">
                <h2 class="text-base font-bold text-text-main flex items-center gap-2">
                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-primary/10 text-primary text-xs font-bold">2</span>
                    Alamat & Wilayah Domisili
                </h2>
                <p class="text-xs text-text-secondary mt-0.5">Standarisasi data wilayah domisili resmi dan opsi domisili Luar Negeri.</p>
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
                <!-- Dropdown Wilayah Kemendagri Menyatu Langsung Dalam Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Provinsi</label>
                        <select wire:model.live="provinsi_nama" class="w-full text-sm rounded-xl border-border bg-surface text-text-main">
                            <option value="">-- Pilih Provinsi --</option>
                            @foreach($provinceList as $prov)
                                <option value="{{ $prov }}">{{ $prov }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Kabupaten / Kota</label>
                        <select wire:model.live="kabupaten_nama" class="w-full text-sm rounded-xl border-border bg-surface text-text-main" {{ empty($provinsi_nama) ? 'disabled' : '' }}>
                            <option value="">-- Pilih Kab / Kota --</option>
                            @foreach($kabupatenList as $kab)
                                <option value="{{ $kab }}">{{ $kab }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Kecamatan</label>
                        <select wire:model.live="kecamatan_nama" class="w-full text-sm rounded-xl border-border bg-surface text-text-main" {{ empty($kabupaten_nama) ? 'disabled' : '' }}>
                            <option value="">-- Pilih Kecamatan --</option>
                            @foreach($kecamatanList as $kec)
                                <option value="{{ $kec }}">{{ $kec }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Desa / Kelurahan</label>
                        <select wire:model.live="desa_nama" class="w-full text-sm rounded-xl border-border bg-surface text-text-main" {{ empty($kecamatan_nama) ? 'disabled' : '' }}>
                            <option value="">-- Pilih Desa / Kelurahan --</option>
                            @foreach($desaList as $desa)
                                <option value="{{ $desa }}">{{ $desa }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Kode Pos</label>
                        <input type="text" wire:model="kode_pos" placeholder="5 digit kode pos" class="w-full text-sm font-mono rounded-xl border-border bg-surface text-text-main">
                    </div>
                </div>
            @endif

            <div>
                <label class="block text-xs font-semibold text-text-secondary mb-1">Alamat Lengkap (Jalan, RT/RW, Dusun, No. Rumah)</label>
                <textarea wire:model="alamat" rows="3" placeholder="Contoh: Jl. Raya Wangun No. 21 RT 01 RW 04" class="w-full text-sm rounded-xl border-border bg-surface text-text-main"></textarea>
            </div>
        </section>

        <!-- 3. KEPEGAWAIAN, SK & KONTRAK KERJA -->
        <section class="border-t border-border pt-6 space-y-6">
            <div class="border-b border-border pb-3">
                <h2 class="text-base font-bold text-text-main flex items-center gap-2">
                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-primary/10 text-primary text-xs font-bold">3</span>
                    Kepegawaian, SK & Kontrak Kerja
                </h2>
                <p class="text-xs text-text-secondary mt-0.5">Klasifikasi jabatan, status GTK, nomor sertifikasi resmi, riwayat SK, dan masa kontrak.</p>
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
                    <label class="block text-xs font-semibold text-text-secondary mb-1">Mata Pelajaran Diampu</label>
                    <input type="text" wire:model="mapel_diampu" placeholder="Contoh: Matematika, Bahasa Indonesia" class="w-full text-sm rounded-xl border-border bg-surface text-text-main">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-secondary mb-1">Jam Kerja</label>
                    <input type="text" wire:model="jam_kerja" placeholder="Contoh: Penuh Waktu / 07:00 - 16:00" class="w-full text-sm rounded-xl border-border bg-surface text-text-main">
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

            <!-- Rincian Kontrak Kerja -->
            <div class="p-4 rounded-xl bg-surface-ground/30 border border-border space-y-4">
                <div class="text-xs font-bold text-text-main flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-primary"></span>
                    Informasi Kontrak Kerja & Masa Berlaku
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Tgl. Pertama Kontrak</label>
                        <input type="date" wire:model="tgl_pertama_kontrak" class="w-full text-sm rounded-xl border-border bg-surface text-text-main">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">No. SK Kontrak Pertama</label>
                        <input type="text" wire:model="no_sk_kontrak_pertama" placeholder="Nomor SK kontrak pertama" class="w-full text-sm rounded-xl border-border bg-surface text-text-main">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Habis Kontrak</label>
                        <input type="date" wire:model="habis_kontrak" class="w-full text-sm rounded-xl border-border bg-surface text-text-main">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Lama Kontrak Selanjutnya</label>
                        <input type="text" wire:model="lama_kontrak_selanjutnya" placeholder="Contoh: 1 Tahun" class="w-full text-sm rounded-xl border-border bg-surface text-text-main">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Keterangan Perpanjang</label>
                        <input type="text" wire:model="keterangan_perpanjang" placeholder="Catatan perpanjangan kontrak" class="w-full text-sm rounded-xl border-border bg-surface text-text-main">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 rounded-xl bg-surface-ground/30 border border-border items-center">
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
                <input type="checkbox" id="is_active_edit" wire:model="is_active" class="rounded border-border text-primary focus:ring-primary">
                <label for="is_active_edit" class="text-xs font-semibold text-text-main cursor-pointer">Pegawai Aktif Bertugas di Sekolah</label>
            </div>
        </section>

        <!-- 4. PAJAK & BPJS -->
        <section class="border-t border-border pt-6 space-y-6">
            <div class="border-b border-border pb-3">
                <h2 class="text-base font-bold text-text-main flex items-center gap-2">
                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-primary/10 text-primary text-xs font-bold">4</span>
                    Pajak & Jaminan Sosial (BPJS)
                </h2>
                <p class="text-xs text-text-secondary mt-0.5">Nomor Pokok Wajib Pajak dan nomor kartu jaminan sosial kesehatan serta ketenagakerjaan.</p>
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
        </section>

        <!-- 5. PENDIDIKAN & PENGALAMAN -->
        <section class="border-t border-border pt-6 space-y-6">
            <div class="border-b border-border pb-3">
                <h2 class="text-base font-bold text-text-main flex items-center gap-2">
                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-primary/10 text-primary text-xs font-bold">5</span>
                    Riwayat Pendidikan & Pengalaman Kerja
                </h2>
                <p class="text-xs text-text-secondary mt-0.5">Pendidikan terakhir yang dituntaskan dan riwayat pengalaman kerja (maksimal 7 riwayat).</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 p-4 rounded-xl bg-surface-ground/30 border border-border">
                <div>
                    <label class="block text-xs font-semibold text-text-secondary mb-1">Jenjang Pendidikan</label>
                    <input type="text" wire:model="jenjang_pendidikan" placeholder="Contoh: S1 / D3 / SMA" class="w-full text-sm rounded-xl border-border bg-surface text-text-main">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-secondary mb-1">Pendidikan Terakhir</label>
                    <input type="text" wire:model="pendidikan_terakhir" placeholder="Gelar / Pendidikan Terakhir" class="w-full text-sm rounded-xl border-border bg-surface text-text-main">
                </div>
                <div>
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
        </section>

        <!-- 6. DATA KELUARGA -->
        <section class="border-t border-border pt-6 space-y-6">
            <div class="border-b border-border pb-3">
                <h2 class="text-base font-bold text-text-main flex items-center gap-2">
                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-primary/10 text-primary text-xs font-bold">6</span>
                    Data Keluarga Pegawai
                </h2>
                <p class="text-xs text-text-secondary mt-0.5">Status pernikahan, data pasangan, anak (1 s/d 4), dan orang tua kandung.</p>
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
        </section>

        <!-- 7. BERKAS FOTO -->
        <section class="border-t border-border pt-6 space-y-6">
            <div class="border-b border-border pb-3">
                <h2 class="text-base font-bold text-text-main flex items-center gap-2">
                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-primary/10 text-primary text-xs font-bold">7</span>
                    Berkas Foto Pegawai (1 Foto Profil Utama & 4 Foto Tambahan)
                </h2>
                <p class="text-xs text-text-secondary mt-0.5">Foto Profil Utama (Slot 1) bersifat wajib. Unggah berkas baru untuk memperbarui foto yang sudah ada.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Slot 1: Foto Utama (Wajib) -->
                <div class="rounded-2xl border-2 border-primary/40 p-4 bg-primary/5 space-y-3 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold uppercase text-primary">★ Slot 1 (Utama) *</span>
                            @if($foto_1 || $existingFoto1)
                                <span class="text-[10px] px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-700 font-bold">Ada</span>
                            @else
                                <span class="text-[10px] px-1.5 py-0.5 rounded bg-rose-100 text-rose-700 font-bold">Wajib</span>
                            @endif
                        </div>
                        <div class="aspect-square rounded-xl border border-dashed border-primary/40 flex items-center justify-center overflow-hidden bg-surface relative">
                            @if($foto_1)
                                <img src="{{ $foto_1->temporaryUrl() }}" class="w-full h-full object-cover">
                            @elseif($existingFoto1 && file_exists(public_path('storage/' . $existingFoto1)))
                                <img src="{{ asset('storage/' . $existingFoto1) }}" class="w-full h-full object-cover">
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
                        $propExist = "existingFoto{$s}";
                    @endphp
                    <div class="rounded-2xl border border-border p-4 bg-surface-ground/30 space-y-3 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold uppercase text-text-secondary">Slot {{ $s }} (Tambahan)</span>
                                @if($$propFoto || $$propExist)
                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-700 font-bold">Ada</span>
                                @else
                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-surface border border-border text-text-secondary">Kosong</span>
                                @endif
                            </div>
                            <div class="aspect-square rounded-xl border border-dashed border-border flex items-center justify-center overflow-hidden bg-surface relative">
                                @if($$propFoto)
                                    <img src="{{ $$propFoto->temporaryUrl() }}" class="w-full h-full object-cover">
                                @elseif($$propExist && file_exists(public_path('storage/' . $$propExist)))
                                    <img src="{{ asset('storage/' . $$propExist) }}" class="w-full h-full object-cover">
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
        </section>

        <!-- 8. AKUN LOGIN SISTEM -->
        <section class="border-t border-border pt-6 space-y-6">
            <div class="border-b border-border pb-3">
                <h2 class="text-base font-bold text-text-main flex items-center gap-2">
                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-primary/10 text-primary text-xs font-bold">8</span>
                    Akun Login SIWARAGA
                </h2>
                <p class="text-xs text-text-secondary mt-0.5">Kelola username, kata sandi, dan peran hak akses untuk pegawai ini.</p>
            </div>

            @if($linkedUser)
                <div class="p-5 rounded-xl border border-border bg-surface-ground/30 space-y-4 max-w-2xl">
                    <div class="flex items-center justify-between pb-3 border-b border-border">
                        <div>
                            <span class="text-xs text-text-secondary block">Status Akun Login</span>
                            <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400">Terhubung dengan Akun Pengguna Aktif</span>
                        </div>
                        <span class="px-2.5 py-1 text-xs font-mono font-bold bg-surface border border-border rounded-lg text-primary">
                            {{ $linkedUser->username }}
                        </span>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Ganti Password (Kosongkan jika tidak ingin mengubah)</label>
                        <input type="password" wire:model="password" placeholder="Ketik kata sandi baru..." class="w-full text-sm rounded-xl border-border bg-surface text-text-main">
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
            @else
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="buat_akun_edit" wire:model.live="buat_akun" class="rounded border-border text-primary focus:ring-primary">
                    <label for="buat_akun_edit" class="text-xs font-semibold text-text-main cursor-pointer">Buat Akun Pengguna Sekarang</label>
                </div>

                @if($buat_akun)
                    <div class="p-5 rounded-xl border border-border bg-surface-ground/30 space-y-4 max-w-2xl">
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
            @endif
        </section>

        <!-- FOOTER AKSI (Hanya di Bagian Paling Bawah) -->
        <div class="border-t border-border pt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-xs text-text-secondary">
                Pastikan seluruh isian bertanda bintang (<span class="text-rose-500">*</span>) telah terisi dengan benar.
            </div>
            <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                <a href="{{ route('kepegawaian.pegawai.show', $pegawai->id) }}" class="btn btn-secondary text-xs px-5 py-2.5">
                    Batal
                </a>
                <button type="button" wire:click="save" wire:loading.attr="disabled" class="btn btn-primary text-xs px-6 py-2.5 shadow-xs transition disabled:opacity-50 flex items-center gap-2">
                    <span wire:loading.remove wire:target="save">Simpan Perubahan</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </button>
            </div>
        </div>

    </div>
</div>
