<div class="space-y-6">
    @include('beasiswaota::partials.sub-nav', ['active' => 'setting'])

    <!-- Alert Notifikasi -->
    @if(session()->has('message'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-xl text-emerald-800 dark:text-emerald-200 text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('message') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
        </div>
    @endif

    <div class="card border border-border rounded-2xl p-6 bg-surface shadow-xs space-y-6 max-w-4xl">
        <div>
            <h3 class="text-lg font-bold text-text-main flex items-center gap-2">
                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Pengaturan Modul & Template Sertifikat Donatur
            </h3>
            <p class="text-xs text-text-secondary mt-0.5">
                Konfigurasi penandatangan resmi dokumen OTA dan pengunggahan gambar template background sertifikat donatur kustom Anda.
            </p>
        </div>

        <form wire:submit.prevent="saveSettings" class="space-y-6">
            <!-- SECTION 1: UPLOAD TEMPLATE BACKGROUND SERTIFIKAT -->
            <div class="border border-border rounded-xl p-5 bg-surface-hover/30 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="font-bold text-sm text-text-main flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Desain Background Template Sertifikat Donatur
                        </h4>
                        <p class="text-xs text-text-secondary mt-0.5">
                            Unggah desain piagam Anda (Format JPG/PNG dengan orientasi A4 Landscape, rasio 297mm x 210mm).
                        </p>
                    </div>

                    @if($currentBgPath)
                        <button type="button" wire:confirm="Hapus background kustom dan kembali ke desain standar?" wire:click="removeTemplateBg" class="text-xs text-rose-500 hover:underline font-semibold">
                            Hapus Template Kustom
                        </button>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
                    <div>
                        <input type="file" wire:model="certificateBgFile" accept="image/png,image/jpeg" class="file-input file-input-bordered file-input-sm w-full text-xs">
                        <div wire:loading wire:target="certificateBgFile" class="text-xs text-primary mt-1">Mengunggah gambar...</div>
                        <span class="text-[11px] text-text-secondary mt-1 block">
                            Ukuran maksimal 5MB. Jika tidak diunggah, sistem akan menggunakan desain berbingkai elegan default.
                        </span>
                        @error('certificateBgFile') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        @if($certificateBgFile)
                            <div class="border border-emerald-300 rounded-lg p-2 bg-emerald-50 text-center">
                                <span class="text-xs font-semibold text-emerald-800">Preview Gambar Baru:</span>
                                <img src="{{ $certificateBgFile->temporaryUrl() }}" class="mt-1 h-28 object-contain mx-auto rounded border shadow-xs" alt="Preview Baru">
                            </div>
                        @elseif($currentBgPath)
                            <div class="border border-border rounded-lg p-2 bg-surface text-center">
                                <span class="text-xs font-semibold text-text-secondary">Template Aktif Saat Ini:</span>
                                <img src="{{ asset('storage/' . $currentBgPath) }}" class="mt-1 h-28 object-contain mx-auto rounded border shadow-xs" alt="Template Aktif">
                            </div>
                        @else
                            <div class="border border-dashed border-border rounded-lg p-4 text-center text-xs text-text-secondary">
                                Menggunakan desain sertifikat bawaan formal SIRAGA (Bingkai Emas & Biru Tua Elegan).
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- SECTION 2: TARGET & PENANDATANGAN DOKUMEN -->
            <div class="space-y-4">
                <h4 class="font-bold text-sm text-text-main">Pengaturan Default & Pejabat Penandatangan</h4>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">
                            Default Target Pembiayaan Kelompok (Rp)
                        </label>
                        <input type="number" wire:model="defaultTargetKelompok" class="input input-bordered w-full text-sm font-bold" placeholder="2000000">
                        <span class="text-[11px] text-text-secondary mt-1 block">Nominal acuan awal per semester saat membuat kelompok baru.</span>
                        @error('defaultTargetKelompok') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">
                            Titimangsa Kota
                        </label>
                        <input type="text" wire:model="titimangsaKota" class="input input-bordered w-full text-sm" placeholder="Garut">
                        @error('titimangsaKota') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">
                            Nama Kepala Sekolah (Penanda Tangan Sertifikat & Kwitansi)
                        </label>
                        <input type="text" wire:model="kepsekNama" class="input input-bordered w-full text-sm" placeholder="H. Dedik, M.Pd.">
                        @error('kepsekNama') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">
                            NIP Kepala Sekolah
                        </label>
                        <input type="text" wire:model="kepsekNip" class="input input-bordered w-full text-sm" placeholder="197xxxxxxxxx">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">
                            Nama Koordinator / Ketua Program OTA
                        </label>
                        <input type="text" wire:model="ketuaOtaNama" class="input input-bordered w-full text-sm" placeholder="Koordinator Program OTA">
                        @error('ketuaOtaNama') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">
                            Nama Bendahara / Pengelola Keuangan OTA
                        </label>
                        <input type="text" wire:model="bendaharaNama" class="input input-bordered w-full text-sm" placeholder="Bendahara Program OTA">
                        @error('bendaharaNama') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-3 border-t border-border">
                <button type="submit" class="btn btn-primary text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan Seluruh Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>
