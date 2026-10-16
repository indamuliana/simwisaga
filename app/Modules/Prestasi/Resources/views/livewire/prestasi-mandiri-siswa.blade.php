<div class="space-y-6">
    @include('prestasi::partials.sub-nav', ['active' => 'mandiri'])

    @if (session()->has('message'))
        <div class="p-4 rounded-xl bg-teal-500/10 border border-teal-500/20 text-teal-700 dark:text-teal-300 text-sm flex items-center justify-between">
            <span class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('message') }}
            </span>
            <button type="button" @click="$el.parentElement.remove()" class="text-teal-700 hover:opacity-75">&times;</button>
        </div>
    @endif

    <!-- Profile Header & Action -->
    <div class="bg-surface rounded-2xl border border-border p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-teal-500/10 text-teal-600 flex items-center justify-center text-2xl font-black">
                🎓
            </div>
            <div>
                <span class="text-xs font-bold text-text-secondary uppercase tracking-wider">Portofolio Prestasi Peserta Didik</span>
                <h3 class="text-xl font-bold text-text-main mt-0.5">{{ $currentSiswa?->nama ?? 'Siswa' }}</h3>
                <p class="text-xs text-text-muted mt-0.5">
                    NISN: {{ $currentSiswa?->nisn ?: '-' }} &bull; Rombel: {{ $currentSiswa?->rombel?->nama ?? '-' }} &bull; Rayon: {{ $currentSiswa?->rayon?->nama ?? '-' }}
                </p>
            </div>
        </div>

        <button wire:click="openSubmitModal" class="btn btn-sm btn-primary flex items-center gap-2 text-xs">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            + Ajukan Prestasi Saya
        </button>
    </div>

    <!-- Daftar Riwayat Prestasiku -->
    <div class="space-y-4">
        <h4 class="font-bold text-text-main text-sm flex items-center gap-2">
            <span>🎖️</span> Riwayat Seluruh Capaian & Pengajuan Prestasi
        </h4>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($myPrestasis as $p)
                <div class="bg-surface rounded-2xl border border-border p-5 space-y-3 relative overflow-hidden shadow-xs hover:border-primary/50 transition">
                    <div class="flex items-start justify-between gap-2">
                        <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-teal-50 text-teal-700 dark:bg-teal-950 dark:text-teal-300 border border-teal-200">
                            {{ $p->peringkat }}
                        </span>

                        @if($p->status_verifikasi === 'approved')
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-teal-50 text-teal-700 dark:bg-teal-950 dark:text-teal-300 border border-teal-200">
                                ✓ Disetujui (Rapor)
                            </span>
                        @elseif($p->status_verifikasi === 'pending')
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 dark:bg-amber-950 dark:text-amber-300 border border-amber-200">
                                ⏳ Menunggu Verifikasi
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 dark:bg-rose-950 dark:text-rose-300 border border-rose-200">
                                ✕ Perlu Revisi
                            </span>
                        @endif
                    </div>

                    <div>
                        <h5 class="font-bold text-sm text-text-main line-clamp-2">{{ $p->nama_prestasi }}</h5>
                        <p class="text-xs text-text-secondary mt-1">Penyelenggara: <strong>{{ $p->penyelenggara_nama }}</strong></p>
                        <p class="text-[11px] text-text-muted mt-0.5">Tingkat {{ $p->tingkat_label }} &bull; {{ $p->tanggal_perolehan ? $p->tanggal_perolehan->format('d M Y') : '-' }}</p>
                    </div>

                    @if($p->pembimbing_nama !== '-')
                        <div class="text-[11px] text-text-secondary pt-2 border-t border-border">
                            Guru Pendamping: <strong class="text-text-main">{{ $p->pembimbing_nama }}</strong>
                        </div>
                    @endif

                    @if($p->catatan_verifikasi)
                        <div class="p-2.5 rounded-lg bg-surface-hover/70 border border-border text-[11px]">
                            <span class="text-text-muted block font-semibold">Catatan Verifikator:</span>
                            <span class="text-text-main">{{ $p->catatan_verifikasi }}</span>
                        </div>
                    @endif

                    <div class="pt-2 border-t border-border flex justify-between items-center text-xs">
                        <span class="text-[11px] text-text-muted">Target: {{ $p->periodeRapor?->nama ?? 'Semester' }}</span>
                        @if($p->sertifikat_file_path)
                            <a href="{{ \Illuminate\Support\Facades\Storage::url($p->sertifikat_file_path) }}" target="_blank" class="text-primary hover:underline font-semibold flex items-center gap-1">
                                📄 Bukti Sertifikat
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-surface rounded-2xl border border-border p-8 text-center text-text-muted text-xs">
                    <p class="text-base mb-1">Belum ada portofolio prestasi.</p>
                    <p>Pernah menjuarai lomba, kompetisi, atau memiliki sertifikasi keahlian? Klik tombol <strong>+ Ajukan Prestasi Saya</strong> di atas untuk mendaftarkannya.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- MODAL PENGAJUAN MANDIRI -->
    @if($showSubmitModal)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-surface rounded-2xl border border-border w-full max-w-2xl shadow-xl overflow-hidden my-8">
            <div class="px-6 py-4 border-b border-border flex items-center justify-between bg-surface-hover/50">
                <h3 class="font-bold text-text-main text-base flex items-center gap-2">
                    <span>📝</span> Formulir Pengajuan Prestasi Mandiri
                </h3>
                <button type="button" wire:click="$set('showSubmitModal', false)" class="text-text-muted hover:text-text-main text-xl font-bold">&times;</button>
            </div>

            <form wire:submit="submitPengajuan" class="p-6 space-y-4 max-h-[80vh] overflow-y-auto">
                <div class="p-3 rounded-xl bg-teal-500/10 border border-teal-500/20 text-xs text-teal-800 dark:text-teal-200">
                    💡 Pengajuan prestasi yang Anda kirimkan akan ditinjau oleh tim Kesiswaan / Wali Kelas. Setelah disetujui, prestasi akan resmi tercantum di catatan rapor dan portofolio Anda.
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-semibold text-text-main block mb-1">Nama Prestasi / Lomba / Sertifikasi *</label>
                        <input type="text" wire:model="namaPrestasi" placeholder="Misal: Juara 1 LKS Web Technologies" 
                               class="w-full text-xs rounded-xl border border-border bg-surface px-3 py-2">
                        @error('namaPrestasi') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-text-main block mb-1">Pihak Penyelenggara *</label>
                        <input type="text" wire:model="penyelenggaraNama" placeholder="Misal: Kemendikbudristek, Telkom, OSIS" 
                               class="w-full text-xs rounded-xl border border-border bg-surface px-3 py-2">
                        @error('penyelenggaraNama') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="text-xs font-semibold text-text-main block mb-1">Kategori Prestasi *</label>
                        <select wire:model="kategori" class="w-full text-xs rounded-xl border border-border bg-surface px-3 py-2">
                            <option value="non_akademik">Non-Akademik (Kejuaraan)</option>
                            <option value="akademik">Akademik</option>
                            <option value="sertifikasi">Sertifikasi Keahlian</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-text-main block mb-1">Lingkup Penyelenggara</label>
                        <select wire:model="penyelenggaraLingkup" class="w-full text-xs rounded-xl border border-border bg-surface px-3 py-2">
                            <option value="luar_sekolah">Eksternal (Luar Sekolah)</option>
                            <option value="dalam_sekolah">Internal (Dalam Sekolah)</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-text-main block mb-1">Tingkat Prestasi *</label>
                        <select wire:model="tingkat" class="w-full text-xs rounded-xl border border-border bg-surface px-3 py-2">
                            <option value="sekolah">Tingkat Sekolah</option>
                            <option value="kecamatan">Tingkat Kecamatan</option>
                            <option value="kabupaten_kota">Tingkat Kabupaten/Kota</option>
                            <option value="provinsi">Tingkat Provinsi</option>
                            <option value="nasional">Tingkat Nasional</option>
                            <option value="internasional">Tingkat Internasional</option>
                        </select>
                    </div>
                </div>

                <!-- Peringkat -->
                <div>
                    <label class="text-xs font-semibold text-text-main block mb-1">Peringkat / Capaian yang Diraih *</label>
                    <input type="text" wire:model="peringkat" placeholder="Misal: Juara 1, Medali Emas, Best Speaker" 
                           class="w-full text-xs rounded-xl border border-border bg-surface px-3 py-2 font-bold text-teal-700 dark:text-teal-300">
                    @error('peringkat') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                    <div class="flex flex-wrap gap-1 mt-1.5">
                        @foreach(['Juara 1', 'Juara 2', 'Juara 3', 'Harapan 1', 'Medali Emas', 'Medali Perak', 'Medali Perunggu', 'Finalis', 'Lulus Sertifikasi'] as $pill)
                            <button type="button" wire:click="setPeringkatTemplate('{{ $pill }}')" 
                                    class="px-2 py-0.5 rounded text-[10px] bg-surface-hover hover:bg-teal-500 hover:text-white transition">
                                {{ $pill }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Guru Pembimbing -->
                <div class="bg-surface-hover/40 p-3 rounded-xl border border-border space-y-2">
                    <label class="text-xs font-bold text-text-main block">Guru Pembimbing / Pendamping (Bila Ada)</label>
                    <div class="flex items-center gap-4 text-xs">
                        <label class="flex items-center gap-1.5 cursor-pointer">
                            <input type="radio" wire:model.live="pembimbingType" value="pegawai" class="text-primary">
                            <span>Guru / Pegawai Sekolah</span>
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer">
                            <input type="radio" wire:model.live="pembimbingType" value="manual" class="text-primary">
                            <span>Tulis Manual (Pelatih Luar)</span>
                        </label>
                    </div>
                    @if($pembimbingType === 'pegawai')
                        <select wire:model="pembimbingPegawaiId" class="w-full text-xs rounded-xl border border-border bg-surface px-3 py-2">
                            <option value="">-- Pilih Guru Pembimbing --</option>
                            @foreach($pegawais as $pg)
                                <option value="{{ $pg->id }}">{{ $pg->nama_gelar }}</option>
                            @endforeach
                        </select>
                    @else
                        <input type="text" wire:model="pembimbingNamaManual" placeholder="Nama pembimbing/pelatih luar..." 
                               class="w-full text-xs rounded-xl border border-border bg-surface px-3 py-2">
                    @endif
                </div>

                <!-- Waktu & Tempat -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-semibold text-text-main block mb-1">Tanggal Perolehan *</label>
                        <input type="date" wire:model="tanggalPerolehan" class="w-full text-xs rounded-xl border border-border bg-surface px-3 py-2">
                        @error('tanggalPerolehan') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-text-main block mb-1">Tempat / Kota</label>
                        <input type="text" wire:model="tempat" placeholder="Misal: Bandung / Jakarta" class="w-full text-xs rounded-xl border border-border bg-surface px-3 py-2">
                    </div>
                </div>

                <!-- Upload Berkas & Foto -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-border">
                    <div>
                        <label class="text-xs font-semibold text-text-main block mb-1">Scan Sertifikat / Piagam (Wajib PDF/Gambar max 5MB) *</label>
                        <input type="file" wire:model="sertifikatFile" class="w-full text-xs text-text-secondary file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-teal-500/10 file:text-teal-700">
                        @error('sertifikatFile') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-text-main block mb-1">Foto Dokumentasi / Piala (JPG/PNG max 5MB)</label>
                        <input type="file" wire:model="fotoFile" accept="image/*" class="w-full text-xs text-text-secondary file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-500/10 file:text-blue-700">
                        @error('fotoFile') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="text-xs font-semibold text-text-main block mb-1">Keterangan / Cerita Prestasi</label>
                    <textarea wire:model="deskripsi" rows="2" placeholder="Ceritakan singkat tentang pencapaian Anda..." 
                              class="w-full text-xs rounded-xl border border-border bg-surface px-3 py-2"></textarea>
                </div>

                <div class="pt-4 border-t border-border flex justify-end gap-3">
                    <button type="button" wire:click="$set('showSubmitModal', false)" class="btn btn-sm btn-outline border-border text-xs">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary text-xs flex items-center gap-1.5">
                        <svg wire:loading class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        Kirim Pengajuan Prestasi
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
