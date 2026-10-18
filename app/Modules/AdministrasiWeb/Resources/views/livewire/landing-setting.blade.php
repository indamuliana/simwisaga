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
                        <div>
                            <label class="label">Path Gambar (dari folder public)</label>
                            <input type="text" wire:model="hero.slides.{{ $s }}.image" class="input text-xs font-mono">
                        </div>
                        <div>
                            <label class="label">Teks Tombol Aksi</label>
                            <input type="text" wire:model="hero.slides.{{ $s }}.cta_text" class="input text-xs">
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
</div>
