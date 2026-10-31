<div class="mx-auto max-w-5xl space-y-6 pb-12">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-text-main">Pengaturan Halaman Depan (Landing Page)</h1>
            <p class="text-sm text-text-secondary mt-1">
                Kelola pengumuman SPMB, teks hero section, showcase 3 pilar kejuruan, dan filosofi pendidikan secara dinamis.
            </p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ url('/') }}" target="_blank" class="btn btn-sm btn-outline flex items-center gap-1.5 text-xs">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                <span>Lihat Tampilan Web</span>
            </a>
            <button type="button" 
                    wire:click="resetToDefault" 
                    wire:confirm="Yakin ingin mereset seluruh pengaturan ke konfigurasi bawaan file landing.php?" 
                    class="btn btn-sm btn-danger text-xs">
                Reset ke Default
            </button>
        </div>
    </div>

    <!-- Flash Message -->
    @if (session('message'))
    <div class="rounded-xl border border-emerald-500/20 bg-emerald-50 dark:bg-emerald-950/30 p-4 text-sm font-semibold text-emerald-700 dark:text-emerald-300 flex items-center gap-2">
        <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <span>{{ session('message') }}</span>
    </div>
    @endif

    <!-- Navigation Tabs -->
    <div class="flex border-b border-border space-x-1 sm:space-x-3 overflow-x-auto pb-px">
        <button type="button" 
                wire:click="$set('activeTab', 'announcement')" 
                class="py-2.5 px-4 text-xs sm:text-sm font-bold rounded-t-xl transition-all border-b-2 {{ $activeTab === 'announcement' ? 'border-primary text-primary bg-surface' : 'border-transparent text-text-secondary hover:text-text-main' }}">
            📢 Pengumuman &amp; SPMB
        </button>
        <button type="button" 
                wire:click="$set('activeTab', 'hero')" 
                class="py-2.5 px-4 text-xs sm:text-sm font-bold rounded-t-xl transition-all border-b-2 {{ $activeTab === 'hero' ? 'border-primary text-primary bg-surface' : 'border-transparent text-text-secondary hover:text-text-main' }}">
            🌟 Hero Section &amp; Teks
        </button>
        <button type="button" 
                wire:click="$set('activeTab', 'slides')" 
                class="py-2.5 px-4 text-xs sm:text-sm font-bold rounded-t-xl transition-all border-b-2 {{ $activeTab === 'slides' ? 'border-primary text-primary bg-surface' : 'border-transparent text-text-secondary hover:text-text-main' }}">
            🖼️ Showcase 3 Pilar &amp; Slide
        </button>
        <button type="button" 
                wire:click="$set('activeTab', 'welcome')" 
                class="py-2.5 px-4 text-xs sm:text-sm font-bold rounded-t-xl transition-all border-b-2 {{ $activeTab === 'welcome' ? 'border-primary text-primary bg-surface' : 'border-transparent text-text-secondary hover:text-text-main' }}">
            📖 Filosofi &amp; Manifesto
        </button>
        <button type="button" 
                wire:click="$set('activeTab', 'student_moments')" 
                class="py-2.5 px-4 text-xs sm:text-sm font-bold rounded-t-xl transition-all border-b-2 {{ $activeTab === 'student_moments' ? 'border-primary text-primary bg-surface' : 'border-transparent text-text-secondary hover:text-text-main' }}">
            🎓 Dinamika Siswa
        </button>
        <button type="button" 
                wire:click="$set('activeTab', 'mitra')" 
                class="py-2.5 px-4 text-xs sm:text-sm font-bold rounded-t-xl transition-all border-b-2 {{ $activeTab === 'mitra' ? 'border-primary text-primary bg-surface' : 'border-transparent text-text-secondary hover:text-text-main' }}">
            🤝 Mitra Link &amp; Match (BMW)
        </button>
    </div>

    <form wire:submit="save" class="space-y-6">

        {{-- ── TAB 1: PENGUMUMAN & SPMB ── --}}
        @if ($activeTab === 'announcement')
        <div class="card space-y-5">
            <div class="border-b border-border pb-3">
                <h2 class="text-base font-bold text-text-main">Pengumuman Banner Atas &amp; Link SPMB</h2>
                <p class="text-xs text-text-secondary">Banner pemberitahuan di bar paling atas landing page.</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2 flex items-center gap-3 p-3 rounded-xl bg-surface-hover border border-border">
                    <input type="checkbox" id="announcement_enabled" wire:model="announcement.enabled" class="rounded border-border text-primary focus:ring-primary w-4 h-4">
                    <label for="announcement_enabled" class="text-sm font-bold text-text-main cursor-pointer">
                        Aktifkan Banner Pengumuman di Bar Paling Atas
                    </label>
                </div>

                <div>
                    <label class="label">Badge Pengumuman</label>
                    <input type="text" wire:model="announcement.badge" class="input" placeholder="Contoh: SPMB 2026/2027">
                </div>
                <div>
                    <label class="label">Teks Pengumuman</label>
                    <input type="text" wire:model="announcement.text" class="input" placeholder="Penerimaan Peserta Didik Baru Resmi Dibuka!">
                </div>

                <div>
                    <label class="label">Nomor WhatsApp / Hotline (Tampilan)</label>
                    <input type="text" wire:model="announcement.phone_display" class="input" placeholder="0813-2331-4430">
                </div>
                <div>
                    <label class="label">URL Telepon / WA (Format tel: atau wa.me)</label>
                    <input type="text" wire:model="announcement.phone_url" class="input" placeholder="tel:081323314430">
                </div>

                <div>
                    <label class="label">Teks Tombol Aksi</label>
                    <input type="text" wire:model="announcement.cta_text" class="input" placeholder="Daftar SPMB Online">
                </div>
                <div>
                    <label class="label">URL Tombol Pendaftaran SPMB Online</label>
                    <input type="text" wire:model="announcement.cta_url" class="input" placeholder="https://spmb.smkwikrama1garut.sch.id/">
                </div>

                <div class="sm:col-span-2 pt-3 border-t border-border">
                    <h3 class="text-sm font-bold text-text-main mb-3">Tautan Eksternal Branding &amp; Brosur</h3>
                </div>

                <div>
                    <label class="label">URL Utama Pendaftaran SPMB</label>
                    <input type="text" wire:model="branding.spmb_url" class="input" placeholder="https://spmb.smkwikrama1garut.sch.id/">
                </div>
                <div>
                    <label class="label">URL Unduh Brosur Resmi</label>
                    <input type="text" wire:model="branding.brochure_url" class="input" placeholder="http://brosur.smkwikrama1garut.sch.id/">
                </div>
            </div>
        </div>
        @endif

        {{-- ── TAB 2: HERO SECTION ── --}}
        @if ($activeTab === 'hero')
        <div class="card space-y-5">
            <div class="border-b border-border pb-3">
                <h2 class="text-base font-bold text-text-main">Hero Section &amp; Tipografi</h2>
                <p class="text-xs text-text-secondary">Headline utama, gambar latar belakang, highlight gradasi, efek mesin tik, dan deskripsi.</p>
            </div>

            {{-- ── Pengaturan Gambar Latar Belakang Hero (Dinamis) ── --}}
            <div class="p-4 rounded-2xl bg-surface-hover border border-border space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <h3 class="text-sm font-bold text-text-main">Gambar Latar Belakang (Hero Background)</h3>
                        <p class="text-xs text-text-secondary">Foto beresolusi tinggi yang menjadi latar utama section pertama landing page.</p>
                    </div>
                    @if(!empty($hero['background_image']) && $hero['background_image'] !== 'images/web/campus-environment.jpg')
                        <button type="button" 
                                wire:click="resetHeroBackground" 
                                wire:confirm="Kembalikan gambar latar ke foto bawaan kampus?"
                                class="btn-ghost text-xs text-danger hover:bg-danger/10 py-1.5 px-3 self-start sm:self-auto">
                            <svg class="w-4 h-4 mr-1 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Reset ke Foto Bawaan
                        </button>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-5 items-start">
                    {{-- Pratinjau Gambar Dinamis dengan Live Feedback Slider --}}
                    <div class="md:col-span-6 space-y-3">
                        <div class="relative rounded-xl overflow-hidden aspect-[16/9] bg-slate-950 border border-border shadow-inner group">
                            <img src="{{ $this->heroBgPreview }}" 
                                 alt="Pratinjau Latar Belakang" 
                                 class="w-full h-full object-cover object-center"
                                 style="filter: brightness({{ (($hero['image_brightness'] ?? 40) / 100) }}) contrast(1.15);">
                            <div class="absolute inset-0" 
                                 style="background-color: rgba(2, 6, 23, {{ (($hero['overlay_opacity'] ?? 70) / 100) }}); backdrop-filter: blur(1px);"></div>
                            <div class="absolute inset-0 flex flex-col justify-center items-center text-center p-4 z-10 pointer-events-none">
                                <span class="text-[10px] font-mono font-bold text-amber-400 bg-slate-950/80 px-2.5 py-0.5 rounded-full border border-white/10 mb-2">
                                    Simulasi: {{ $hero['overlay_opacity'] ?? 70 }}% Gelap • {{ $hero['image_brightness'] ?? 40 }}% Kecerahan
                                </span>
                                <p class="text-white font-extrabold text-sm sm:text-base drop-shadow-sm leading-tight">
                                    {{ $hero['title_line1'] ?? 'Solusi Pendidikan Akhlaq Berkualitas' }}
                                </p>
                                <span class="text-[11px] text-slate-300 mt-1">Teks putih terlihat jelas &amp; kontras</span>
                            </div>
                        </div>

                        {{-- Tombol Preset Cepat --}}
                        <div>
                            <span class="text-[11px] font-bold text-text-secondary block mb-1.5">Preset Cepat:</span>
                            <div class="flex flex-wrap gap-1.5">
                                <button type="button" 
                                        wire:click="setHeroPreset(70, 40)" 
                                        class="px-2.5 py-1 rounded-lg text-[11px] font-semibold transition-colors {{ ($hero['overlay_opacity'] ?? 70) == 70 && ($hero['image_brightness'] ?? 40) == 40 ? 'bg-primary text-white' : 'bg-surface border border-border text-text-main hover:bg-surface-hover' }}">
                                    Seimbang (70% / 40%)
                                </button>
                                <button type="button" 
                                        wire:click="setHeroPreset(60, 50)" 
                                        class="px-2.5 py-1 rounded-lg text-[11px] font-semibold transition-colors {{ ($hero['overlay_opacity'] ?? 70) == 60 && ($hero['image_brightness'] ?? 40) == 50 ? 'bg-primary text-white' : 'bg-surface border border-border text-text-main hover:bg-surface-hover' }}">
                                    Sedang (60% / 50%)
                                </button>
                                <button type="button" 
                                        wire:click="setHeroPreset(50, 60)" 
                                        class="px-2.5 py-1 rounded-lg text-[11px] font-semibold transition-colors {{ ($hero['overlay_opacity'] ?? 70) == 50 && ($hero['image_brightness'] ?? 40) == 60 ? 'bg-primary text-white' : 'bg-surface border border-border text-text-main hover:bg-surface-hover' }}">
                                    Terang (50% / 60%)
                                </button>
                                <button type="button" 
                                        wire:click="setHeroPreset(85, 20)" 
                                        class="px-2.5 py-1 rounded-lg text-[11px] font-semibold transition-colors {{ ($hero['overlay_opacity'] ?? 70) == 85 && ($hero['image_brightness'] ?? 40) == 20 ? 'bg-primary text-white' : 'bg-surface border border-border text-text-main hover:bg-surface-hover' }}">
                                    Pekat (85% / 20%)
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Form Upload & Slider Kontrol Kegelapan --}}
                    <div class="md:col-span-6 space-y-4">
                        <div>
                            <label class="label">Pilih Foto Pengganti (Maks. 5 MB)</label>
                            <input type="file" 
                                   wire:model="hero_bg_file" 
                                   accept="image/png,image/jpeg,image/webp,image/jpg" 
                                   class="file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-hover text-xs text-text-secondary cursor-pointer border border-border rounded-xl p-2 w-full bg-surface">
                            <div wire:loading wire:target="hero_bg_file" class="text-xs text-primary font-semibold mt-1">
                                <svg class="animate-spin -ml-1 mr-2 h-3.5 w-3.5 inline text-primary" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                                Mengunggah dan memproses pratinjau gambar...
                            </div>
                            @error('hero_bg_file') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- Slider 1: Kegelapan Overlay Gelap --}}
                        <div class="p-3 rounded-xl bg-surface border border-border space-y-1.5">
                            <div class="flex items-center justify-between text-xs">
                                <label class="font-bold text-text-main">Tingkat Kegelapan Lapisan (Dark Overlay)</label>
                                <span class="font-mono font-bold text-primary px-2 py-0.5 rounded bg-primary/10">{{ $hero['overlay_opacity'] ?? 70 }}%</span>
                            </div>
                            <input type="range" 
                                   min="0" 
                                   max="100" 
                                   step="5" 
                                   wire:model.live="hero.overlay_opacity" 
                                   class="w-full h-2 bg-surface-hover rounded-lg appearance-none cursor-pointer accent-primary">
                            <div class="flex justify-between text-[10px] text-text-secondary">
                                <span>0% (Transparan)</span>
                                <span>Default 70%</span>
                                <span>100% (Hitam Pekat)</span>
                            </div>
                        </div>

                        {{-- Slider 2: Kecerahan Foto Latar Belakang --}}
                        <div class="p-3 rounded-xl bg-surface border border-border space-y-1.5">
                            <div class="flex items-center justify-between text-xs">
                                <label class="font-bold text-text-main">Kecerahan Foto Latar (Dimming Filter)</label>
                                <span class="font-mono font-bold text-primary px-2 py-0.5 rounded bg-primary/10">{{ $hero['image_brightness'] ?? 40 }}%</span>
                            </div>
                            <input type="range" 
                                   min="10" 
                                   max="100" 
                                   step="5" 
                                   wire:model.live="hero.image_brightness" 
                                   class="w-full h-2 bg-surface-hover rounded-lg appearance-none cursor-pointer accent-primary">
                            <div class="flex justify-between text-[10px] text-text-secondary">
                                <span>10% (Sangat Redup)</span>
                                <span>Default 40%</span>
                                <span>100% (Asli)</span>
                            </div>
                        </div>

                        <div class="text-[11px] text-text-secondary space-y-1 pt-1">
                            <p class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-success inline shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Rekomendasi: Overlay 60% - 75% untuk keseimbangan estetika foto dan keterbacaan teks.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="label">Pill Tag / Lencana Atas</label>
                    <input type="text" wire:model="hero.tag" class="input" placeholder="SMK PUSAT KEUNGGULAN • ISLAMIC BOARDING SCHOOL">
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="label">Baris Judul 1 (Teks Putih/Gelap)</label>
                        <input type="text" wire:model="hero.title_line1" class="input" placeholder="Mendidik Generasi Juara untuk Menjadi">
                    </div>
                    <div>
                        <label class="label">Highlight Judul (Gradasi Berwarna)</label>
                        <input type="text" wire:model="hero.title_highlight" class="input" placeholder="Terbaik bagi Masa Depan">
                    </div>
                </div>

                <div>
                    <label class="label">Daftar Kata Efek Mesin Tik (Ketik 1 kata/frasa per baris)</label>
                    <textarea wire:model="typewriterInput" rows="5" class="input font-mono text-xs" placeholder="Teknologi &amp; AI&#10;Bisnis Digital&#10;Hospitaliti Bintang 5&#10;Karakter Qur'ani&#10;Kepemimpinan Global"></textarea>
                    <p class="text-xs text-text-secondary mt-1">Kata-kata ini akan berganti secara otomatis dengan animasi mesin tik halus.</p>
                </div>

                <div>
                    <label class="label">Deskripsi Pembuka Hero</label>
                    <textarea wire:model="hero.description" rows="3" class="input"></textarea>
                </div>

                <!-- 4 Statistik Hero -->
                <div class="pt-4 border-t border-border">
                    <h3 class="text-sm font-bold text-text-main mb-3">4 Metrik Capaian Hero Strip</h3>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        @for ($i = 0; $i < 4; $i++)
                        <div class="p-3 rounded-xl bg-surface-hover border border-border space-y-2">
                            <span class="text-[10px] font-mono font-bold text-primary">KARTU METRIK #{{ $i + 1 }}</span>
                            <div>
                                <label class="text-[11px] font-semibold text-text-secondary">Angka / Nilai</label>
                                <input type="text" wire:model="hero.stats.{{ $i }}.number" class="input text-xs py-1">
                            </div>
                            <div>
                                <label class="text-[11px] font-semibold text-text-secondary">Label Utama</label>
                                <input type="text" wire:model="hero.stats.{{ $i }}.label" class="input text-xs py-1">
                            </div>
                            <div>
                                <label class="text-[11px] font-semibold text-text-secondary">Sub Label</label>
                                <input type="text" wire:model="hero.stats.{{ $i }}.sub" class="input text-xs py-1">
                            </div>
                        </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- ── TAB 3: SHOWCASE 3 PILAR & SLIDE ── --}}
        @if ($activeTab === 'slides')
        <div class="card space-y-5">
            <div class="border-b border-border pb-3">
                <h2 class="text-base font-bold text-text-main">Slide Pameran Interaktif (Interactive Showcase Studio)</h2>
                <p class="text-xs text-text-secondary">Kelola 4 tab pameran di sisi kanan hero: SPMB, Teknologi, Bisnis, dan Hospitaliti.</p>
            </div>

            <div class="space-y-6">
                @for ($s = 0; $s < 4; $s++)
                <div class="p-4 rounded-2xl bg-surface-hover border border-border space-y-4">
                    <div class="flex items-center justify-between border-b border-border/60 pb-2">
                        <span class="text-xs font-bold font-mono text-primary uppercase">
                            SLIDE #{{ $s + 1 }}: {{ $hero['slides'][$s]['tab_name'] ?? 'Tab' }}
                        </span>
                        <span class="text-[10px] text-text-secondary font-mono">ID: {{ $hero['slides'][$s]['id'] ?? '' }}</span>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="label">Nama Tab Tombol</label>
                            <input type="text" wire:model="hero.slides.{{ $s }}.tab_name" class="input text-xs">
                        </div>
                        <div>
                            <label class="label">Badge Tag Foto</label>
                            <input type="text" wire:model="hero.slides.{{ $s }}.badge" class="input text-xs">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="label">Judul Slide</label>
                            <input type="text" wire:model="hero.slides.{{ $s }}.title" class="input text-xs">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="label">Deskripsi Singkat</label>
                            <textarea wire:model="hero.slides.{{ $s }}.desc" rows="2" class="input text-xs"></textarea>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="label">Teks Tombol Aksi</label>
                            <input type="text" wire:model="hero.slides.{{ $s }}.cta_text" class="input text-xs">
                        </div>

                        {{-- ── Inline Media Upload & Preview (Gambar / Video) ── --}}
                        <div class="sm:col-span-2 p-3.5 rounded-xl bg-surface border border-border space-y-3">
                            @php
                                $slideItem = $hero['slides'][$s] ?? [];
                                $isSlideVideo = in_array($slideItem['media_type'] ?? 'image', ['video_upload', 'video_url']) && !empty($slideItem['video_url']);
                                $slideMediaSrc = $isSlideVideo 
                                    ? \App\Modules\AdministrasiWeb\Models\WebSetting::resolveImageUrl($slideItem['video_url']) 
                                    : \App\Modules\AdministrasiWeb\Models\WebSetting::resolveImageUrl($slideItem['image'] ?? null, 'images/web/campus-environment.jpg');
                            @endphp

                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-border/50 pb-2">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-text-main">Media Slide (Gambar atau Video)</span>
                                    @if ($isSlideVideo)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300 uppercase flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                            <span>Video Aktif</span>
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 uppercase flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span>Gambar Aktif</span>
                                        </span>
                                    @endif
                                </div>

                                <button type="button" 
                                        wire:click="resetSlideMedia({{ $s }})" 
                                        wire:confirm="Kembalikan media slide #{{ $s + 1 }} ke gambar bawaan?" 
                                        class="text-[11px] text-danger hover:underline font-semibold flex items-center gap-1 self-start sm:self-auto">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    <span>Reset ke Bawaan</span>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                                {{-- Preview Box --}}
                                <div class="md:col-span-5">
                                    <div class="relative rounded-xl overflow-hidden aspect-[16/9] bg-slate-950 border border-border shadow-inner flex items-center justify-center">
                                        @if ($isSlideVideo)
                                            <video src="{{ $slideMediaSrc }}" 
                                                   class="w-full h-full object-cover" 
                                                   controls 
                                                   muted 
                                                   preload="metadata">
                                            </video>
                                        @else
                                            <img src="{{ $slideMediaSrc }}" 
                                                 alt="Pratinjau Slide #{{ $s + 1 }}" 
                                                 class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                </div>

                                {{-- Upload Input --}}
                                <div class="md:col-span-7 space-y-2">
                                    <div>
                                        <label class="text-[11px] font-bold text-text-secondary block mb-1">
                                            Pilih File Media Baru (Maks. 25 MB)
                                        </label>
                                        <input type="file" 
                                               wire:model="slideUploadFiles.{{ $s }}" 
                                               accept="image/png,image/jpeg,image/webp,image/jpg,video/mp4,video/webm" 
                                               class="file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-hover text-xs text-text-secondary cursor-pointer border border-border rounded-xl p-1.5 w-full bg-surface">
                                        
                                        <div wire:loading wire:target="slideUploadFiles.{{ $s }}" class="text-xs text-primary font-semibold mt-1 flex items-center gap-1.5">
                                            <svg class="animate-spin h-3.5 w-3.5 text-primary" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                            </svg>
                                            <span>Mengunggah media slide #{{ $s + 1 }}...</span>
                                        </div>
                                        @error('slideUploadFiles.' . $s) 
                                            <span class="text-xs text-danger mt-1 block">{{ $message }}</span> 
                                        @enderror
                                    </div>
                                    
                                    <div class="p-2 rounded-lg bg-surface-hover/80 border border-border/60 text-[11px] text-text-muted leading-relaxed space-y-1">
                                        <div><strong class="text-text-secondary">&bull; Gambar:</strong> JPG, JPEG, PNG, WEBP.</div>
                                        <div><strong class="text-text-secondary">&bull; Video:</strong> WebM (.webm) atau MP4 (.mp4).</div>
                                        <div class="text-[10px] text-primary font-semibold">&bull; Rekomendasi: Gunakan video <strong>.webm</strong> untuk ukuran sangat ringan. Di landing page, durasi slide otomatis mengikuti panjang video ini sampai selesai.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endfor
            </div>
        </div>
        @endif

        {{-- ── TAB 4: FILOSOFI & MANIFESTO ── --}}
        @if ($activeTab === 'welcome')
        <div class="card space-y-5">
            <div class="border-b border-border pb-3">
                <h2 class="text-base font-bold text-text-main">Filosofi Pendidikan &amp; Nilai Karakter</h2>
                <p class="text-xs text-text-secondary">Kutipan manifesto pendidikan Wikrama dan 4 pilar pertumbuhan holistik.</p>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="label">Tag / Lencana Filosofi</label>
                    <input type="text" wire:model="welcome.tag" class="input" placeholder="Filosofi Pendidikan Wikrama">
                </div>

                <div>
                    <label class="label">Teks Kutipan Manifesto Pendidikan</label>
                    <textarea wire:model="welcome.quote" rows="4" class="input"></textarea>
                </div>

                <div>
                    <label class="label">Penulis / Penandatangan Manifesto</label>
                    <input type="text" wire:model="welcome.author" class="input" placeholder="Dewan Pembina &amp; Manajemen Pendidikan Wikrama Indonesia">
                </div>

                <!-- 4 Pilar Pertumbuhan -->
                <div class="pt-4 border-t border-border">
                    <h3 class="text-sm font-bold text-text-main mb-3">4 Pilar Karakter Holistik</h3>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        @for ($p = 0; $p < 4; $p++)
                        <div class="p-3.5 rounded-xl bg-surface-hover border border-border space-y-2">
                            <div class="flex items-center gap-2">
                                <input type="text" wire:model="welcome.pillars.{{ $p }}.icon" class="input text-center text-lg w-12 py-1" placeholder="🕌">
                                <input type="text" wire:model="welcome.pillars.{{ $p }}.name" class="input text-xs font-bold py-1" placeholder="Nama Pilar">
                            </div>
                            <div>
                                <textarea wire:model="welcome.pillars.{{ $p }}.desc" rows="3" class="input text-xs py-1" placeholder="Deskripsi pilar..."></textarea>
                            </div>
                        </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- ── TAB 5: DINAMIKA SISWA & CAROUSEL CARDS ── --}}
        @if ($activeTab === 'student_moments')
        <div class="space-y-6">
            <!-- Header Settings Card -->
            <div class="card space-y-4">
                <div class="border-b border-border pb-3">
                    <h2 class="text-base font-bold text-text-main">Judul &amp; Informasi Section Dinamika Siswa</h2>
                    <p class="text-xs text-text-secondary">Teks pembuka yang tampil di atas baris card carousel pada landing page.</p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="label">Badge Tag Kecil</label>
                        <input type="text" wire:model="student_moments.tag" class="input" placeholder="Dinamika Kehidupan Siswa">
                    </div>
                    <div>
                        <label class="label">Judul Utama Section</label>
                        <input type="text" wire:model="student_moments.title" class="input" placeholder="Tumbuh, Berkarya, &amp; Berprestasi Bersama Wikrama">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="label">Subjudul / Deskripsi Pengantar</label>
                        <textarea wire:model="student_moments.subtitle" rows="2" class="input" placeholder="Deskripsi pengantar..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Cards Management Card -->
            <div class="card space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-border pb-3">
                    <div>
                        <h2 class="text-base font-bold text-text-main">Daftar Card Dinamika Siswa (Format 1 Baris Carousel)</h2>
                        <p class="text-xs text-text-secondary">
                            Foto penuh vertikal bergaya JIS. Saat disorot (hover), tirai gelap naik menampilkan deskripsi dan tombol tautan.
                        </p>
                    </div>
                    <button type="button" 
                            wire:click="openCreateCardModal" 
                            class="btn btn-sm btn-primary inline-flex items-center gap-1.5 self-start sm:self-auto cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>+ Tambah Card Baru</span>
                    </button>
                </div>

                <!-- Cards Table / Visual List -->
                @if (empty($student_moments['items']))
                <div class="text-center py-8 text-text-muted">
                    <p class="text-sm">Belum ada card dinamika siswa. Klik tombol di atas untuk menambahkan card pertama.</p>
                </div>
                @else
                <div class="space-y-3">
                    @foreach ($student_moments['items'] as $cIdx => $card)
                    <div class="p-3.5 sm:p-4 rounded-2xl border border-border bg-surface hover:border-primary/40 transition-all flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-2xs {{ !($card['is_active'] ?? true) ? 'opacity-60 bg-surface-raised/40' : '' }}">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <!-- Drag/Order badge -->
                            <div class="flex flex-col items-center gap-1 shrink-0">
                                <button type="button" 
                                        wire:click="moveCardUp({{ $cIdx }})" 
                                        @if($cIdx === 0) disabled @endif
                                        class="p-1 rounded hover:bg-surface-raised text-text-muted hover:text-text-main disabled:opacity-30 disabled:cursor-not-allowed"
                                        title="Pindah ke Atas">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                </button>
                                <span class="font-mono-code text-[11px] font-bold text-text-muted bg-surface-raised px-1.5 py-0.5 rounded">
                                    #{{ $cIdx + 1 }}
                                </span>
                                <button type="button" 
                                        wire:click="moveCardDown({{ $cIdx }})" 
                                        @if($cIdx === count($student_moments['items']) - 1) disabled @endif
                                        class="p-1 rounded hover:bg-surface-raised text-text-muted hover:text-text-main disabled:opacity-30 disabled:cursor-not-allowed"
                                        title="Pindah ke Bawah">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                            </div>

                            <!-- Thumbnail Image -->
                            <div class="w-16 h-20 sm:w-20 sm:h-24 rounded-xl overflow-hidden bg-slate-900 shrink-0 border border-border relative">
                                <img src="{{ \App\Modules\AdministrasiWeb\Models\WebSetting::resolveImageUrl($card['image'] ?? null, 'images/web/campus-environment.jpg') }}" 
                                     alt="{{ $card['title'] ?? 'Card' }}" 
                                     class="w-full h-full object-cover">
                                @if(!empty($card['badge']))
                                <span class="absolute bottom-1 left-1 right-1 truncate text-[9px] font-bold px-1 py-0.5 rounded bg-black/70 text-white text-center">
                                    {{ $card['badge'] }}
                                </span>
                                @endif
                            </div>

                            <!-- Text Details -->
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="text-sm font-bold text-text-main truncate">
                                        {{ $card['title'] ?? 'Tanpa Judul' }}
                                    </h3>
                                    @if(!($card['is_active'] ?? true))
                                    <span class="badge badge-neutral text-[10px]">Nonaktif</span>
                                    @else
                                    <span class="badge badge-success text-[10px]">Aktif</span>
                                    @endif
                                </div>
                                <p class="text-xs text-text-secondary line-clamp-2 mt-1 leading-relaxed">
                                    {{ $card['desc'] ?? '-' }}
                                </p>
                                <div class="flex items-center gap-3 mt-2 text-[11px] text-text-muted flex-wrap">
                                    <span class="font-mono-code text-primary truncate max-w-xs">
                                        🔗 {{ $card['url'] ?? '#' }} ({{ $card['target'] ?? '_self' }})
                                    </span>
                                    <span>&bull;</span>
                                    <span class="font-medium text-text-main">
                                        Tombol: {{ $card['cta_text'] ?? 'Lebih Lanjut' }} &rarr;
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-2 shrink-0 self-end md:self-center pt-2 md:pt-0 border-t md:border-t-0 border-border w-full md:w-auto justify-end">
                            <button type="button" 
                                    wire:click="toggleCardActive({{ $cIdx }})" 
                                    class="btn btn-xs {{ ($card['is_active'] ?? true) ? 'btn-outline' : 'btn-primary' }}"
                                    title="Ubah Status Aktif">
                                {{ ($card['is_active'] ?? true) ? 'Sembunyikan' : 'Aktifkan' }}
                            </button>
                            <button type="button" 
                                    wire:click="openEditCardModal({{ $cIdx }})" 
                                    class="btn btn-xs btn-outline inline-flex items-center gap-1"
                                    title="Edit Card">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                <span>Edit</span>
                            </button>
                            <button type="button" 
                                    wire:click="deleteCard({{ $cIdx }})" 
                                    wire:confirm="Yakin ingin menghapus card '{{ $card['title'] }}'?" 
                                    class="btn btn-xs btn-danger inline-flex items-center gap-1"
                                    title="Hapus Card">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                <span>Hapus</span>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- ── TAB 5: MITRA LINK & MATCH (BMW) ── --}}
        @if ($activeTab === 'mitra')
        <div class="space-y-6">
            <!-- Header Section Settings -->
            <div class="card space-y-4">
                <div class="border-b border-border pb-3">
                    <h2 class="text-base font-bold text-text-main">Pengaturan Judul Section Link &amp; Match BMW</h2>
                    <p class="text-xs text-text-secondary">
                        Section logo kemitraan industri &amp; perguruan tinggi (Bekerja, Melanjutkan, Wirausaha).
                    </p>
                </div>
                <div>
                    <label class="label">Badge Tag / Judul Section</label>
                    <input type="text" wire:model="mitra.tag" class="input" placeholder="Link &amp; Match Industri &bull; BMW (Bekerja, Melanjutkan, Wirausaha)">
                </div>
            </div>

            <!-- Mitra List & Management -->
            <div class="card space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-border pb-3">
                    <div>
                        <h2 class="text-base font-bold text-text-main">Daftar Mitra &amp; Logo Industri (Running Marquee)</h2>
                        <p class="text-xs text-text-secondary">
                            Logo dan teks pendamping di sebelah kiri (dapat dibuat 2 baris) yang berjalan otomatis pada halaman depan.
                        </p>
                    </div>
                    <button type="button" 
                            wire:click="openCreateMitraModal" 
                            class="btn btn-sm btn-primary inline-flex items-center gap-1.5 self-start sm:self-auto cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>+ Tambah Mitra Baru</span>
                    </button>
                </div>

                @if (empty($mitra['items']))
                <div class="text-center py-8 text-text-muted">
                    <p class="text-sm">Belum ada data mitra. Klik tombol di atas untuk menambahkan mitra pertama.</p>
                </div>
                @else
                <div class="space-y-3">
                    @foreach ($mitra['items'] as $mIdx => $item)
                    <div class="p-3.5 sm:p-4 rounded-2xl border border-border bg-surface hover:border-primary/40 transition-all flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-2xs {{ !($item['is_active'] ?? true) ? 'opacity-60 bg-surface-raised/40' : '' }}">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <!-- Order Buttons -->
                            <div class="flex flex-col items-center gap-1 shrink-0">
                                <button type="button" 
                                        wire:click="moveMitraUp({{ $mIdx }})" 
                                        @if($mIdx === 0) disabled @endif
                                        class="p-1 rounded hover:bg-surface-raised text-text-muted hover:text-text-main disabled:opacity-30 disabled:cursor-not-allowed"
                                        title="Pindah ke Atas">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                </button>
                                <span class="font-mono-code text-[11px] font-bold text-text-muted bg-surface-raised px-1.5 py-0.5 rounded">
                                    #{{ $mIdx + 1 }}
                                </span>
                                <button type="button" 
                                        wire:click="moveMitraDown({{ $mIdx }})" 
                                        @if($mIdx === count($mitra['items']) - 1) disabled @endif
                                        class="p-1 rounded hover:bg-surface-raised text-text-muted hover:text-text-main disabled:opacity-30 disabled:cursor-not-allowed"
                                        title="Pindah ke Bawah">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                            </div>

                            <!-- Live Visual Preview of [Logo] [Text] -->
                            <div class="flex items-center gap-3 p-2.5 rounded-xl border border-slate-200 bg-white shadow-xs shrink-0 max-w-xs sm:max-w-sm">
                                <div class="w-20 h-10 flex items-center justify-center p-1 bg-slate-50 rounded-lg border border-slate-100 shrink-0">
                                    <img src="{{ \App\Modules\AdministrasiWeb\Models\WebSetting::resolveImageUrl($item['image'] ?? null, 'images/landing/mitra-huawei.png') }}" 
                                         alt="{{ $item['name'] ?? 'Logo' }}" 
                                         class="max-h-8 max-w-full object-contain">
                                </div>
                                <div class="text-left text-xs font-semibold text-slate-800 leading-tight whitespace-pre-line truncate max-w-[140px]">
                                    {{ !empty($item['text']) ? $item['text'] : ($item['name'] ?: 'Tanpa Teks') }}
                                </div>
                            </div>

                            <!-- Text Details -->
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="text-sm font-bold text-text-main truncate">
                                        {{ $item['name'] ?: ($item['text'] ?? 'Mitra Tanpa Nama') }}
                                    </h3>
                                    @if(!($item['is_active'] ?? true))
                                    <span class="badge badge-neutral text-[10px]">Nonaktif</span>
                                    @else
                                    <span class="badge badge-success text-[10px]">Aktif</span>
                                    @endif
                                </div>
                                @if(!empty($item['url']))
                                <p class="text-xs font-mono-code text-primary truncate mt-1">
                                    🔗 {{ $item['url'] }}
                                </p>
                                @endif
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-2 shrink-0 self-end md:self-center">
                            <button type="button" 
                                    wire:click="toggleMitraActive({{ $mIdx }})" 
                                    class="btn btn-xs {{ !($item['is_active'] ?? true) ? 'btn-success' : 'btn-outline' }}">
                                {{ !($item['is_active'] ?? true) ? 'Aktifkan' : 'Nonaktifkan' }}
                            </button>
                            <button type="button" 
                                    wire:click="openEditMitraModal({{ $mIdx }})" 
                                    class="btn btn-xs btn-outline inline-flex items-center gap-1"
                                    title="Edit Mitra">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                <span>Edit</span>
                            </button>
                            <button type="button" 
                                    wire:click="deleteMitra({{ $mIdx }})" 
                                    wire:confirm="Yakin ingin menghapus mitra ini?" 
                                    class="btn btn-xs btn-danger inline-flex items-center gap-1"
                                    title="Hapus Mitra">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                <span>Hapus</span>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Floating/Sticky Save Button Bar -->
        <div class="sticky bottom-4 z-20 flex items-center justify-between p-4 rounded-2xl bg-surface/90 backdrop-blur-md border border-border shadow-xl">
            <span class="text-xs text-text-secondary hidden sm:inline">
                Perubahan yang disimpan akan langsung tampak secara instan di <strong class="text-primary">127.0.0.1:8000</strong>.
            </span>
            <div class="flex items-center gap-3 ml-auto">
                <button type="submit" class="btn btn-primary flex items-center gap-2 text-sm px-6 py-2.5">
                    <svg wire:loading.remove wire:target="save" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <svg wire:loading wire:target="save" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span wire:loading.remove wire:target="save">Simpan Perubahan</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </button>
            </div>
        </div>
    </form>

    {{-- ── MODAL FORM TAMBAH / EDIT CARD DINAMIKA SISWA ── --}}
    @if ($showCardModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs overflow-y-auto">
        <div class="bg-surface border border-border rounded-3xl p-6 sm:p-8 max-w-xl w-full shadow-2xl space-y-5 my-8">
            <div class="flex items-center justify-between border-b border-border pb-3">
                <h3 class="text-lg font-bold text-text-main">
                    {{ $cardIndexEditing !== null ? 'Edit Card Dinamika Siswa' : 'Tambah Card Dinamika Siswa' }}
                </h3>
                <button type="button" wire:click="closeCardModal" class="p-1 rounded-lg text-text-muted hover:text-text-main hover:bg-surface-raised cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="space-y-4">
                <!-- Foto Card Preview & Upload -->
                <div>
                    <label class="label">Foto Card (Portrait Vertikal Penuh)</label>
                    <div class="flex items-center gap-4">
                        <div class="w-20 h-28 rounded-xl overflow-hidden bg-slate-900 border border-border shrink-0 shadow-inner">
                            <img src="{{ $this->cardImagePreview }}" alt="Preview" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 space-y-1.5">
                            <input type="file" wire:model="cardImageFile" accept="image/*" class="input text-xs py-1.5">
                            <p class="text-[11px] text-text-muted">
                                Format JPG/PNG/WebP, disarankan rasio 3:4 atau 9:16 (tinggi ~800px). Maks 5MB.
                            </p>
                            @error('cardImageFile') <span class="error">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Judul & Badge -->
                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="label">Judul Card <span class="text-danger">*</span></label>
                        <input type="text" wire:model="cardForm.title" class="input" placeholder="Contoh: Prestasi Juara 1 Taekwondo Nasional">
                        @error('cardForm.title') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="label">Badge Tag (Opsional)</label>
                        <input type="text" wire:model="cardForm.badge" class="input" placeholder="Contoh: Juara 1 Nasional">
                        @error('cardForm.badge') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="label">Target Link</label>
                        <select wire:model="cardForm.target" class="input">
                            <option value="_self">Tab Saat Ini (_self)</option>
                            <option value="_blank">Tab Baru (_blank)</option>
                        </select>
                        @error('cardForm.target') <span class="error">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Deskripsi Hover -->
                <div>
                    <label class="label">Deskripsi (Muncul Saat Card di-Hover) <span class="text-danger">*</span></label>
                    <textarea wire:model="cardForm.desc" rows="3" class="input text-xs" placeholder="Tuliskan cerita singkat atau deskripsi kegiatan yang menginspirasi..."></textarea>
                    @error('cardForm.desc') <span class="error">{{ $message }}</span> @enderror
                </div>

                <!-- URL & CTA Text -->
                <div class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <label class="label">URL Link Tujuan <span class="text-danger">*</span></label>
                        <input type="text" wire:model="cardForm.url" class="input" placeholder="https://... atau #agenda atau /berita/...">
                        @error('cardForm.url') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="label">Teks Tombol Aksi <span class="text-danger">*</span></label>
                        <input type="text" wire:model="cardForm.cta_text" class="input" placeholder="Lebih Lanjut / Pelajari Selengkapnya">
                        @error('cardForm.cta_text') <span class="error">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Checkbox Aktif -->
                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" id="card_is_active" wire:model="cardForm.is_active" class="rounded border-border text-primary focus:ring-primary w-4 h-4">
                    <label for="card_is_active" class="text-sm font-semibold text-text-main cursor-pointer">
                        Aktifkan &amp; tampilkan card ini pada carousel beranda
                    </label>
                </div>
            </div>

            <!-- Modal Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-border">
                <button type="button" wire:click="closeCardModal" class="btn btn-outline text-xs cursor-pointer">
                    Batal
                </button>
                <button type="button" wire:click="saveCard" class="btn btn-primary text-xs px-5 cursor-pointer">
                    Simpan Card
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ── MODAL FORM TAMBAH / EDIT MITRA LINK & MATCH (BMW) ── --}}
    @if ($showMitraModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs overflow-y-auto">
        <div class="bg-surface border border-border rounded-3xl p-6 sm:p-8 max-w-xl w-full shadow-2xl space-y-5 my-8">
            <div class="flex items-center justify-between border-b border-border pb-3">
                <div>
                    <h3 class="text-lg font-bold text-text-main">
                        {{ $mitraIndexEditing !== null ? 'Edit Mitra Link & Match' : 'Tambah Mitra Baru' }}
                    </h3>
                    <p class="text-xs text-text-secondary">Atur teks pendamping (2 baris rata kiri) dan upload logo mitra.</p>
                </div>
                <button type="button" wire:click="closeMitraModal" class="p-1 rounded-lg text-text-muted hover:text-text-main hover:bg-surface-raised cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="space-y-4">
                <!-- Teks Samping Kanan Logo (Bisa 2 Baris) -->
                <div>
                    <div class="flex items-center justify-between">
                        <label class="label">Teks Samping Kanan Logo</label>
                        <span class="text-[11px] text-text-muted font-normal">Tekan Enter untuk baris ke-2</span>
                    </div>
                    <textarea wire:model="mitraForm.text" rows="2" class="input text-xs" placeholder="Contoh:&#10;Mitra Industri&#10;PT Telkom Indonesia"></textarea>
                    <p class="text-[11px] text-text-muted mt-1">
                        Teks ini akan ditampilkan di sebelah kanan logo dengan format rata kiri. Bisa 1 atau 2 baris.
                    </p>
                    @error('mitraForm.text') <span class="error">{{ $message }}</span> @enderror
                </div>

                <!-- Logo Mitra Upload & Preview -->
                <div>
                    <label class="label">Upload Logo Mitra</label>
                    <div class="flex items-center gap-4">
                        <div class="w-28 h-16 rounded-xl overflow-hidden bg-slate-50 border border-border shrink-0 flex items-center justify-center p-2 shadow-inner">
                            <img src="{{ $this->mitraImagePreview }}" alt="Preview" class="max-h-12 max-w-full object-contain">
                        </div>
                        <div class="flex-1 space-y-1.5">
                            <input type="file" wire:model="mitraImageFile" accept="image/*" class="input text-xs py-1.5">
                            <p class="text-[11px] text-text-muted">
                                Format PNG transparan, SVG, atau WebP disarankan. Maksimal 5MB.
                            </p>
                            @error('mitraImageFile') <span class="error">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Live Preview of Result -->
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-1.5">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Pratinjau Tampilan di Halaman Depan:</span>
                    <div class="inline-flex items-center gap-3 p-2 px-3 rounded-xl bg-white border border-slate-200 shadow-2xs">
                        <div class="w-20 h-10 flex items-center justify-center p-1 bg-slate-50 rounded-lg border border-slate-100 shrink-0">
                            <img src="{{ $this->mitraImagePreview }}" alt="Preview" class="max-h-8 max-w-full object-contain">
                        </div>
                        <div class="text-left text-xs font-semibold text-slate-800 leading-tight whitespace-pre-line max-w-[150px]">
                            {{ !empty($mitraForm['text']) ? $mitraForm['text'] : ($mitraForm['name'] ?: 'Teks Kemitraan (Baris 1&#10;Baris 2)') }}
                        </div>
                    </div>
                </div>

                <!-- Nama Instansi / Perusahaan & URL -->
                <div class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <label class="label">Nama Instansi / Mitra (Alt/Title)</label>
                        <input type="text" wire:model="mitraForm.name" class="input text-xs" placeholder="Contoh: Huawei ICT Academy">
                        @error('mitraForm.name') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="label">URL Website Mitra (Opsional)</label>
                        <input type="text" wire:model="mitraForm.url" class="input text-xs" placeholder="https://...">
                        @error('mitraForm.url') <span class="error">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Checkbox Aktif -->
                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" id="mitra_is_active" wire:model="mitraForm.is_active" class="rounded border-border text-primary focus:ring-primary w-4 h-4">
                    <label for="mitra_is_active" class="text-sm font-semibold text-text-main cursor-pointer">
                        Aktifkan &amp; tampilkan logo mitra ini pada marquee landing page
                    </label>
                </div>
            </div>

            <!-- Modal Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-border">
                <button type="button" wire:click="closeMitraModal" class="btn btn-outline text-xs cursor-pointer">
                    Batal
                </button>
                <button type="button" wire:click="saveMitra" class="btn btn-primary text-xs px-5 cursor-pointer">
                    Simpan Mitra
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
