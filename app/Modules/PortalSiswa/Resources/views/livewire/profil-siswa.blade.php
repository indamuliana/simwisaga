<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-surface p-6 rounded-2xl border border-border shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-primary uppercase tracking-wider mb-1">
                <span>👤 Portal Siswa</span>
                <span>&bull;</span>
                <span>Profil & Akun</span>
            </div>
            <h1 class="text-2xl font-black text-text-main tracking-tight">Data Diri & Pengajuan Perbaikan</h1>
            <p class="text-xs text-text-muted mt-1">
                Lihat informasi identitas diri Anda yang tercatat di sistem atau ajukan permohonan koreksi data ke bagian kesiswaan.
            </p>
        </div>
        <div>
            <button type="button" wire:click="openPengajuanModal" class="px-4 py-2.5 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs font-bold shadow-xs transition-all flex items-center gap-2">
                <span>✍️</span>
                <span>Ajukan Perbaikan Data</span>
            </button>
        </div>
    </div>

    @if (session('success_pengajuan'))
        <div class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 p-4 text-xs font-medium text-emerald-800 dark:text-emerald-300 flex items-center gap-2 shadow-2xs">
            <span>✅</span>
            <span>{{ session('success_pengajuan') }}</span>
        </div>
    @endif
    @if (session('success_password'))
        <div class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 p-4 text-xs font-medium text-emerald-800 dark:text-emerald-300 flex items-center gap-2 shadow-2xs">
            <span>🔐</span>
            <span>{{ session('success_password') }}</span>
        </div>
    @endif

    <!-- Section Navigation Tabs -->
    <div class="border-b border-border bg-surface p-2 rounded-2xl border shadow-xs">
        <nav class="flex flex-wrap items-center gap-1.5">
            <button type="button" wire:click="$set('activeSection', 'biodata')" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $activeSection === 'biodata' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
                <span>📋 Biodata & Akademik</span>
            </button>
            <button type="button" wire:click="$set('activeSection', 'orang_tua')" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $activeSection === 'orang_tua' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
                <span>👨‍👩‍👧 Orang Tua / Wali</span>
            </button>
            <button type="button" wire:click="$set('activeSection', 'pengajuan')" class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 {{ $activeSection === 'pengajuan' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
                <span>📬 Riwayat Pengajuan</span>
                @if ($riwayatPengajuan->isNotEmpty())
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $activeSection === 'pengajuan' ? 'bg-white/20 text-white' : 'bg-surface-raised text-text-muted border border-border' }}">
                        {{ $riwayatPengajuan->count() }}
                    </span>
                @endif
            </button>
            <button type="button" wire:click="$set('activeSection', 'keamanan')" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $activeSection === 'keamanan' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
                <span>🔒 Ganti Kata Sandi</span>
            </button>
        </nav>
    </div>

    <!-- Section 1: Biodata & Akademik -->
    @if ($activeSection === 'biodata')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left: Foto & Badge Ringkas -->
            <div class="card p-6 flex flex-col items-center text-center">
                <div class="h-28 w-28 rounded-2xl border-2 border-primary/20 bg-primary-soft shadow-inner overflow-hidden flex items-center justify-center mb-4">
                    @if ($siswa && $siswa->foto)
                        <img src="{{ asset('storage/' . $siswa->foto) }}" alt="{{ $siswa->nama }}" class="h-full w-full object-cover">
                    @else
                        <span class="text-4xl font-black text-primary">{{ strtoupper(substr($siswa?->nama ?? auth()->user()->name, 0, 2)) }}</span>
                    @endif
                </div>
                <h3 class="text-lg font-bold text-text-main">{{ $siswa?->nama ?? auth()->user()->name }}</h3>
                <span class="text-xs text-text-muted mt-0.5">NISN: {{ $siswa?->nisn ?: '-' }} &bull; NIPD: {{ $siswa?->nipd ?: '-' }}</span>

                <div class="w-full mt-6 pt-6 border-t border-border space-y-2 text-left text-xs">
                    <div class="flex justify-between py-1">
                        <span class="text-text-muted">Rombel</span>
                        <span class="font-bold text-text-main">{{ $siswa?->rombel?->nama ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span class="text-text-muted">Wali Kelas</span>
                        <span class="font-bold text-text-main truncate max-w-[150px]">{{ $siswa?->rombel?->waliKelas?->nama_lengkap ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span class="text-text-muted">Rayon</span>
                        <span class="font-bold text-text-main">{{ $siswa?->rayon?->nama ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span class="text-text-muted">Pembimbing</span>
                        <span class="font-bold text-text-main truncate max-w-[150px]">{{ $siswa?->rayon?->pembimbing?->nama_lengkap ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span class="text-text-muted">Program</span>
                        <span class="font-bold text-primary">{{ $siswa?->program?->nama ?: 'Reguler' }}</span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span class="text-text-muted">Status</span>
                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-600">
                            {{ ucfirst($siswa?->status_siswa ?? 'Aktif') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Right: Detail Biodata & Kontak -->
            <div class="lg:col-span-2 space-y-6">
                <div class="card p-6 space-y-4">
                    <h3 class="text-sm font-bold text-text-main flex items-center gap-2 border-b border-border pb-3">
                        <span>🆔 Identitas Pribadi</span>
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="text-text-muted block">Nama Lengkap</span>
                            <span class="font-semibold text-text-main text-sm mt-0.5 block">{{ $siswa?->nama }}</span>
                        </div>
                        <div>
                            <span class="text-text-muted block">Nomor Induk Kependudukan (NIK)</span>
                            <span class="font-mono font-semibold text-text-main mt-0.5 block">{{ $siswa?->nik ?: '-' }}</span>
                        </div>
                        <div>
                            <span class="text-text-muted block">Tempat, Tanggal Lahir</span>
                            <span class="font-medium text-text-main mt-0.5 block">
                                {{ $siswa?->tempat_lahir ?: '-' }}, {{ $siswa?->tanggal_lahir ? $siswa->tanggal_lahir->format('d F Y') : '-' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-text-muted block">Jenis Kelamin</span>
                            <span class="font-medium text-text-main mt-0.5 block">
                                {{ ($siswa?->jk ?? $siswa?->jenis_kelamin) === 'L' ? 'Laki-laki' : ((($siswa?->jk ?? $siswa?->jenis_kelamin) === 'P') ? 'Perempuan' : '-') }}
                            </span>
                        </div>
                        <div>
                            <span class="text-text-muted block">Agama</span>
                            <span class="font-medium text-text-main mt-0.5 block">{{ $siswa?->agama ?: '-' }}</span>
                        </div>
                        <div>
                            <span class="text-text-muted block">Asal Sekolah SMP/MTs</span>
                            <span class="font-medium text-text-main mt-0.5 block">{{ $siswa?->sekolah_asal ?: '-' }}</span>
                        </div>
                    </div>
                </div>

                <div class="card p-6 space-y-4">
                    <h3 class="text-sm font-bold text-text-main flex items-center gap-2 border-b border-border pb-3">
                        <span>📍 Alamat & Kontak Siswa</span>
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div class="sm:col-span-2">
                            <span class="text-text-muted block">Alamat Domisili</span>
                            <span class="font-medium text-text-main mt-0.5 block">{{ $siswa?->alamat ?: '-' }}</span>
                        </div>
                        <div>
                            <span class="text-text-muted block">Nomor HP / WhatsApp</span>
                            <span class="font-mono font-semibold text-text-main mt-0.5 block">{{ $siswa?->hp ?: ($siswa?->telepon ?: '-') }}</span>
                        </div>
                        <div>
                            <span class="text-text-muted block">Email Terdaftar</span>
                            <span class="font-medium text-text-main mt-0.5 block">{{ $siswa?->email ?: (auth()->user()->email ?: '-') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Section 2: Orang Tua / Wali -->
    @if ($activeSection === 'orang_tua')
        @php
            $ortu = $siswa?->orangTuas?->first();
        @endphp
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Data Ayah -->
            <div class="card p-6 space-y-4">
                <h3 class="text-sm font-bold text-text-main flex items-center gap-2 border-b border-border pb-3">
                    <span>👨 Data Ayah Kandung</span>
                </h3>
                <div class="space-y-3 text-xs">
                    <div>
                        <span class="text-text-muted block">Nama Ayah</span>
                        <span class="font-semibold text-text-main text-sm mt-0.5 block">{{ $ortu?->nama_ayah ?: '-' }}</span>
                    </div>
                    <div>
                        <span class="text-text-muted block">Pekerjaan</span>
                        <span class="font-medium text-text-main mt-0.5 block">{{ $ortu?->pekerjaan_ayah ?: '-' }}</span>
                    </div>
                    <div>
                        <span class="text-text-muted block">Penghasilan Bulanan</span>
                        <span class="font-medium text-text-main mt-0.5 block">{{ $ortu?->penghasilan_ayah ?: '-' }}</span>
                    </div>
                    <div>
                        <span class="text-text-muted block">Pendidikan Terakhir</span>
                        <span class="font-medium text-text-main mt-0.5 block">{{ $ortu?->pendidikan_ayah ?: '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- Data Ibu -->
            <div class="card p-6 space-y-4">
                <h3 class="text-sm font-bold text-text-main flex items-center gap-2 border-b border-border pb-3">
                    <span>👩 Data Ibu Kandung</span>
                </h3>
                <div class="space-y-3 text-xs">
                    <div>
                        <span class="text-text-muted block">Nama Ibu</span>
                        <span class="font-semibold text-text-main text-sm mt-0.5 block">{{ $ortu?->nama_ibu ?: '-' }}</span>
                    </div>
                    <div>
                        <span class="text-text-muted block">Pekerjaan</span>
                        <span class="font-medium text-text-main mt-0.5 block">{{ $ortu?->pekerjaan_ibu ?: '-' }}</span>
                    </div>
                    <div>
                        <span class="text-text-muted block">Penghasilan Bulanan</span>
                        <span class="font-medium text-text-main mt-0.5 block">{{ $ortu?->penghasilan_ibu ?: '-' }}</span>
                    </div>
                    <div>
                        <span class="text-text-muted block">Pendidikan Terakhir</span>
                        <span class="font-medium text-text-main mt-0.5 block">{{ $ortu?->pendidikan_ibu ?: '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- Kontak Orang Tua / Wali -->
            <div class="card p-6 md:col-span-2 space-y-4">
                <h3 class="text-sm font-bold text-text-main flex items-center gap-2 border-b border-border pb-3">
                    <span>📞 Kontak & Wali</span>
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                    <div>
                        <span class="text-text-muted block">Nomor Telepon / WA Orang Tua</span>
                        <span class="font-mono font-semibold text-text-main mt-0.5 block">{{ $ortu?->telepon_ortu ?: '-' }}</span>
                    </div>
                    <div>
                        <span class="text-text-muted block">Nama Wali (Jika Ada)</span>
                        <span class="font-medium text-text-main mt-0.5 block">{{ $ortu?->nama_wali ?: '-' }}</span>
                    </div>
                    <div>
                        <span class="text-text-muted block">Telepon Wali</span>
                        <span class="font-mono font-medium text-text-main mt-0.5 block">{{ $ortu?->telepon_wali ?: '-' }}</span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Section 3: Riwayat Pengajuan Perbaikan Data -->
    @if ($activeSection === 'pengajuan')
        <div class="card p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-border pb-4">
                <div>
                    <h3 class="text-sm font-bold text-text-main">Riwayat Permohonan Koreksi Data</h3>
                    <p class="text-xs text-text-muted">Daftar pengajuan perbaikan identitas diri yang pernah Anda kirimkan.</p>
                </div>
                <button type="button" wire:click="openPengajuanModal" class="px-3 py-1.5 rounded-lg bg-primary hover:bg-primary-hover text-white text-xs font-bold transition-all flex items-center gap-1.5">
                    <span>➕ Ajukan Baru</span>
                </button>
            </div>

            @if ($riwayatPengajuan->isEmpty())
                <div class="p-8 text-center rounded-xl border border-dashed border-border text-xs text-text-muted">
                    Belum ada pengajuan perbaikan data yang dikirimkan.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-surface-raised text-text-muted uppercase text-[10px] tracking-wider border-b border-border">
                            <tr>
                                <th class="p-3">Tanggal</th>
                                <th class="p-3">Kategori & Rincian</th>
                                <th class="p-3">Nilai Lama</th>
                                <th class="p-3">Usulan Baru</th>
                                <th class="p-3">Alasan</th>
                                <th class="p-3">Status</th>
                                <th class="p-3">Catatan Admin</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach ($riwayatPengajuan as $p)
                                @php
                                    $firstChange = collect($p->rincian_perubahan)->first() ?? [];
                                    $fieldName = $firstChange['field'] ?? array_key_first($p->rincian_perubahan ?? []);
                                    $lama = $firstChange['lama'] ?? '-';
                                    $baru = $firstChange['baru'] ?? '-';
                                @endphp
                                <tr class="hover:bg-surface-hover/50 transition-colors">
                                    <td class="p-3 whitespace-nowrap text-text-secondary">{{ $p->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="p-3">
                                        <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-semibold bg-surface-raised text-text-secondary border border-border uppercase">
                                            {{ $p->kategori }}
                                        </span>
                                        <div class="font-bold text-text-main mt-0.5">{{ ucwords(str_replace('_', ' ', $fieldName)) }}</div>
                                    </td>
                                    <td class="p-3 text-rose-500 font-mono line-through">{{ $lama }}</td>
                                    <td class="p-3 text-emerald-600 font-mono font-bold">{{ $baru }}</td>
                                    <td class="p-3 max-w-[200px] truncate text-text-secondary" title="{{ $p->alasan }}">{{ $p->alasan }}</td>
                                    <td class="p-3 whitespace-nowrap">
                                        @if ($p->status === 'menunggu')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-600 border border-amber-500/20">
                                                ⏳ Menunggu
                                            </span>
                                        @elseif ($p->status === 'disetujui')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-600 border border-emerald-500/20">
                                                ✅ Disetujui
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-600 border border-rose-500/20">
                                                ❌ Ditolak
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-text-muted">
                                        {{ $p->catatan_admin ?: '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif

    <!-- Section 4: Ganti Kata Sandi -->
    @if ($activeSection === 'keamanan')
        <div class="card p-6 max-w-xl mx-auto space-y-4">
            <h3 class="text-sm font-bold text-text-main border-b border-border pb-3 flex items-center gap-2">
                <span>🔐 Perbarui Kata Sandi Akun</span>
            </h3>
            <p class="text-xs text-text-muted">
                Pastikan menggunakan kata sandi yang kuat dan tidak dibagikan kepada siapa pun.
            </p>

            <form wire:submit.prevent="updatePassword" class="space-y-4 text-xs">
                <div>
                    <label class="block font-medium text-text-secondary mb-1">Kata Sandi Saat Ini</label>
                    <input type="password" wire:model="current_password" class="w-full rounded-xl border border-border bg-surface px-3 py-2 text-xs text-text-main focus:border-primary focus:outline-hidden">
                    @error('current_password') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block font-medium text-text-secondary mb-1">Kata Sandi Baru (Min. 8 karakter & angka)</label>
                    <input type="password" wire:model="new_password" class="w-full rounded-xl border border-border bg-surface px-3 py-2 text-xs text-text-main focus:border-primary focus:outline-hidden">
                    @error('new_password') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block font-medium text-text-secondary mb-1">Konfirmasi Kata Sandi Baru</label>
                    <input type="password" wire:model="new_password_confirmation" class="w-full rounded-xl border border-border bg-surface px-3 py-2 text-xs text-text-main focus:border-primary focus:outline-hidden">
                </div>

                <div class="pt-2">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs font-bold shadow-xs transition-all flex items-center gap-2" wire:loading.attr="disabled">
                        <span wire:loading.remove>Simpan Password Baru</span>
                        <span wire:loading>Memproses...</span>
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Modal Form Ajukan Perbaikan Data -->
    @if ($showPengajuanModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
            <div class="bg-surface rounded-2xl border border-border shadow-xl max-w-lg w-full p-6 space-y-4 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-border pb-3">
                    <h3 class="text-sm font-bold text-text-main flex items-center gap-2">
                        <span>✍️ Formulir Pengajuan Perbaikan Data</span>
                    </h3>
                    <button type="button" wire:click="closePengajuanModal" class="text-text-muted hover:text-text-main text-lg font-bold">&times;</button>
                </div>

                <form wire:submit.prevent="submitPengajuan" class="space-y-4 text-xs">
                    <div>
                        <label class="block font-medium text-text-secondary mb-1">Kategori Perubahan</label>
                        <select wire:model.live="kategori" class="w-full rounded-xl border border-border bg-surface px-3 py-2 text-xs text-text-main focus:border-primary focus:outline-hidden">
                            <option value="biodata">Identitas Pribadi (Nama, NIK, TTL, dll)</option>
                            <option value="kontak">Kontak & Domisili (Alamat, No HP)</option>
                            <option value="orang_tua">Data Orang Tua / Wali</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-medium text-text-secondary mb-1">Bagian / Field Data yang Ingin Diperbaiki</label>
                        <select wire:model="namaField" class="w-full rounded-xl border border-border bg-surface px-3 py-2 text-xs text-text-main focus:border-primary focus:outline-hidden">
                            @if ($kategori === 'biodata')
                                <option value="nama">Nama Lengkap Siswa</option>
                                <option value="nik">Nomor Induk Kependudukan (NIK)</option>
                                <option value="nisn">Nomor Induk Siswa Nasional (NISN)</option>
                                <option value="tempat_lahir">Tempat Lahir</option>
                                <option value="tanggal_lahir">Tanggal Lahir (Format: YYYY-MM-DD)</option>
                                <option value="agama">Agama</option>
                            @elseif ($kategori === 'kontak')
                                <option value="alamat">Alamat Domisili Lengkap</option>
                                <option value="hp">Nomor HP / WhatsApp</option>
                                <option value="email">Email</option>
                            @elseif ($kategori === 'orang_tua')
                                <option value="nama_ayah">Nama Ayah Kandung</option>
                                <option value="pekerjaan_ayah">Pekerjaan Ayah</option>
                                <option value="nama_ibu">Nama Ibu Kandung</option>
                                <option value="pekerjaan_ibu">Pekerjaan Ibu</option>
                            @else
                                <option value="catatan_lainnya">Lainnya</option>
                            @endif
                        </select>
                        @error('namaField') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-medium text-text-secondary mb-1">Nilai Usulan yang Benar (Nilai Baru)</label>
                        <input type="text" wire:model="nilaiBaru" placeholder="Masukkan perbaikan data yang benar..." class="w-full rounded-xl border border-border bg-surface px-3 py-2 text-xs text-text-main focus:border-primary focus:outline-hidden">
                        @error('nilaiBaru') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-medium text-text-secondary mb-1">Alasan Perubahan</label>
                        <textarea wire:model="alasan" rows="3" placeholder="Contoh: Kesalahan penulisan nama sesuai Akta Kelahiran..." class="w-full rounded-xl border border-border bg-surface px-3 py-2 text-xs text-text-main focus:border-primary focus:outline-hidden"></textarea>
                        @error('alasan') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-medium text-text-secondary mb-1">Upload Berkas Pendukung (Opsional: Foto Kartu Keluarga / Akta / KTP, Maks: 2MB)</label>
                        <input type="file" wire:model="lampiranFile" accept=".jpg,.jpeg,.png,.pdf" class="w-full text-xs text-text-secondary file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-soft file:text-primary hover:file:bg-primary/20">
                        @error('lampiranFile') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-border">
                        <button type="button" wire:click="closePengajuanModal" class="px-4 py-2 rounded-xl border border-border hover:bg-surface-hover text-text-secondary font-bold text-xs">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-primary hover:bg-primary-hover text-white font-bold text-xs shadow-xs" wire:loading.attr="disabled">
                            <span wire:loading.remove>Kirim Pengajuan</span>
                            <span wire:loading>Mengirim...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
