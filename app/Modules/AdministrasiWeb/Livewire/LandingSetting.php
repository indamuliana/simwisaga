<?php

namespace App\Modules\AdministrasiWeb\Livewire;

use App\Modules\AdministrasiWeb\Models\WebSetting;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

class LandingSetting extends Component
{
    use WithFileUploads;

    public string $activeTab = 'announcement';

    // Form data groups
    public array $announcement = [];
    public array $branding = [];
    public array $hero = [];
    public array $welcome = [];
    public array $fast_facts = [];
    public array $principal = [];
    public array $student_moments = [];
    public array $mitra = [];
    public string $typewriterInput = '';

    // File upload for hero background image
    public $hero_bg_file = null;

    // File upload for principal image & fast facts images
    public $principalImageFile = null;
    public array $fastFactsImageFiles = [];

    // Inline file uploads for slides (images / videos)
    public array $slideUploadFiles = [];

    // Hero Slide modal management
    public bool $showHeroSlideModal = false;
    public ?int $heroSlideIndexEditing = null;
    public array $heroSlideForm = [
        'id' => '',
        'tab_name' => '',
        'badge' => '',
        'title' => '',
        'highlight_input' => '',
        'desc' => '',
        'media_type' => 'image', // 'image' | 'video_upload' | 'video_url'
        'image' => '',
        'video_url' => '',
        'poster' => '',
        'overlay_opacity' => 70,
        'dimming' => 40,
        'cta_text' => 'Daftar SPMB Online',
        'cta_url' => '#',
        'cta_target' => '_self',
        'is_active' => true,
    ];
    public $heroSlideMediaFile = null;
    public $heroSlidePosterFile = null;

    // Card modal management for student moments
    public bool $showCardModal = false;
    public ?int $cardIndexEditing = null;
    public array $cardForm = [
        'title' => '',
        'desc' => '',
        'image' => '',
        'url' => '#',
        'cta_text' => 'Lebih Lanjut',
        'target' => '_self',
        'badge' => '',
        'is_active' => true,
    ];
    public $cardImageFile = null;

    // Mitra Link & Match modal management
    public bool $showMitraModal = false;
    public ?int $mitraIndexEditing = null;
    public array $mitraForm = [
        'name' => '',
        'text' => '', // Teks bebas samping kiri logo, bisa dibuat 2 baris (Enter)
        'image' => '',
        'url' => '',
        'is_active' => true,
    ];
    public $mitraImageFile = null;

    public function mount(): void
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        $this->announcement = WebSetting::getGroup('announcement');
        $this->branding = WebSetting::getGroup('branding');
        $this->hero = WebSetting::getGroup('hero');
        $this->welcome = WebSetting::getGroup('welcome');
        $this->fast_facts = WebSetting::getGroup('fast_facts');
        $this->principal = WebSetting::getGroup('principal');
        $this->student_moments = WebSetting::getGroup('student_moments');
        $this->mitra = WebSetting::getGroup('mitra');

        if (empty($this->fast_facts)) {
            $this->fast_facts = config('landing.fast_facts', []);
        }
        while (count($this->fast_facts) < 4) {
            $this->fast_facts[] = [
                'number' => '100%',
                'label' => 'Metrik Unggulan',
                'desc' => 'Deskripsi pencapaian mutu...',
                'image' => 'images/web/campus-environment.jpg',
                'tag' => 'Standar Mutu',
            ];
        }

        $defaultWelcome = config('landing.welcome', []);
        $this->welcome = array_merge([
            'tag' => 'Manifesto Pendidikan Wikrama',
            'quote' => 'Di SMK Wikrama 1 Garut, kami mempersiapkan anak bangsa dari berbagai daerah untuk menjadi yang terbaik dalam keahlian yang mereka tekuni, sekaligus menjadi pribadi yang membawa kemaslahatan terbaik bagi keluarga, masyarakat, dan masa depan peradaban.',
            'author' => 'Kunedi, S.Si. — Kepala SMK Wikrama 1 Garut',
        ], $defaultWelcome, $this->welcome ?: []);

        $defaultPrincipal = config('landing.principal', []);
        $this->principal = array_merge([
            'name' => 'Kunedi, S.Si.',
            'role' => 'Kepala SMK Wikrama 1 Garut',
            'image' => 'images/web/principal-kunedi.jpg',
            'quote' => 'Mendidik anak bangsa dengan hati dan teknologi sehingga memenuhi kebutuhan mutu dunia kerja, serta melahirkan generasi pemimpin berkarakter mulia yang membawa kemaslahatan umat.',
            'vision_title' => 'Sekolah Kejuruan Teladan Nasional',
            'vision_desc' => 'Menjadi sekolah kejuruan teladan nasional yang berbudaya lingkungan berkelanjutan, berbasis teknologi informasi, berkomitmen mencetak lulusan berkarakter mulia, sehat jasmani rohani, berdaya saing global, serta mampu beradaptasi dengan perkembangan industri 4.0 dan 5.0 dengan berpedoman pada Al Qur\'an dan Hadis.',
        ], $defaultPrincipal, $this->principal ?: []);

        if (empty($this->principal['pillars'])) {
            $this->principal['pillars'] = config('landing.principal.pillars', []);
        }
        while (count($this->principal['pillars'] ?? []) < 4) {
            $pIdx = count($this->principal['pillars'] ?? []) + 1;
            $this->principal['pillars'][] = [
                'num' => sprintf('%02d', $pIdx),
                'title' => 'Pilar Keunggulan ' . $pIdx,
                'desc' => 'Uraian pilar keunggulan kelembagaan...',
            ];
        }

        if (empty($this->mitra['items'])) {
            $defaultMitra = config('landing.mitra', []);
            $mitraItems = [];
            foreach ($defaultMitra as $m) {
                $mitraItems[] = [
                    'name' => $m['name'] ?? '',
                    'text' => $m['text'] ?? ($m['name'] ?? ''),
                    'image' => $m['image'] ?? '',
                    'url' => $m['url'] ?? '',
                    'is_active' => true,
                ];
            }
            $this->mitra = [
                'tag' => 'Link & Match Industri • BMW (Bekerja, Melanjutkan, Wirausaha)',
                'items' => $mitraItems,
            ];
        }

        if (empty($this->student_moments['items'])) {
            $defaultMoments = config('landing.student_moments', []);
            $items = [];
            foreach ($defaultMoments['items'] ?? [] as $m) {
                $items[] = [
                    'title' => $m['title'] ?? '',
                    'desc' => $m['desc'] ?? '',
                    'image' => $m['image'] ?? 'images/web/campus-environment.jpg',
                    'url' => $m['url'] ?? '#',
                    'cta_text' => $m['cta_text'] ?? 'Lebih Lanjut',
                    'target' => $m['target'] ?? '_self',
                    'badge' => $m['badge'] ?? ($m['cat_label'] ?? 'Momen Siswa'),
                    'is_active' => true,
                ];
            }
            $this->student_moments = [
                'tag' => $defaultMoments['tag'] ?? 'Dinamika Kehidupan Siswa',
                'title' => $defaultMoments['title'] ?? 'Tumbuh, Berkarya, & Berprestasi Bersama Wikrama',
                'subtitle' => $defaultMoments['subtitle'] ?? 'Setiap hari di SMK Wikrama 1 Garut diisi dengan antusiasme belajar nyata, pembiasaan karakter luhur, dan torehan prestasi nasional.',
                'items' => $items,
            ];
        }

        if (!isset($this->hero['overlay_opacity'])) {
            $this->hero['overlay_opacity'] = 70;
        }
        if (!isset($this->hero['image_brightness'])) {
            $this->hero['image_brightness'] = 40;
        }
        if (!isset($this->hero['slider_delay'])) {
            $this->hero['slider_delay'] = 6;
        }
        if (!isset($this->hero['slider_autoplay'])) {
            $this->hero['slider_autoplay'] = true;
        }

        // Normalize hero slides list
        $defaultHeroSlides = config('landing.hero.slides', []);
        $rawSlides = $this->hero['slides'] ?? $defaultHeroSlides;
        $normalizedSlides = [];
        foreach ($rawSlides as $i => $s) {
            $normalizedSlides[] = [
                'id' => $s['id'] ?? ('slide_' . ($i + 1)),
                'tab_name' => $s['tab_name'] ?? ($s['badge'] ?? 'Slide ' . ($i + 1)),
                'badge' => $s['badge'] ?? '',
                'title' => $s['title'] ?? '',
                'highlight_words' => $s['highlight_words'] ?? [$s['title'] ?? 'Wikrama'],
                'desc' => $s['desc'] ?? '',
                'media_type' => $s['media_type'] ?? 'image',
                'image' => $s['image'] ?? 'images/web/campus-environment.jpg',
                'video_url' => $s['video_url'] ?? '',
                'poster' => $s['poster'] ?? ($s['image'] ?? 'images/web/campus-environment.jpg'),
                'overlay_opacity' => isset($s['overlay_opacity']) ? (int)$s['overlay_opacity'] : 70,
                'dimming' => isset($s['dimming']) ? (int)$s['dimming'] : 40,
                'cta_text' => $s['cta_text'] ?? 'Daftar SPMB Online',
                'cta_url' => $s['cta_url'] ?? '#',
                'cta_target' => $s['cta_target'] ?? '_self',
                'is_active' => isset($s['is_active']) ? (bool)$s['is_active'] : true,
            ];
        }
        $this->hero['slides'] = $normalizedSlides;

        $words = $this->hero['typewriter_words'] ?? [];
        $this->typewriterInput = is_array($words) ? implode("\n", $words) : (string) $words;
    }

    public function getHeroBgPreviewProperty(): string
    {
        if ($this->hero_bg_file) {
            try {
                return $this->hero_bg_file->temporaryUrl();
            } catch (\Throwable $e) {
                // Ignore temporaryUrl errors
            }
        }

        return WebSetting::resolveImageUrl(
            $this->hero['background_image'] ?? null,
            'images/web/campus-environment.jpg'
        );
    }

    public function setHeroPreset(int $overlay, int $brightness): void
    {
        $this->hero['overlay_opacity'] = $overlay;
        $this->hero['image_brightness'] = $brightness;
    }

    public function resetHeroBackground(): void
    {
        abort_unless(auth()->user()->can('web.landing.edit'), 403, 'Anda tidak memiliki hak akses.');

        $this->hero_bg_file = null;
        $this->hero['background_image'] = 'images/web/campus-environment.jpg';
        $this->hero['overlay_opacity'] = 70;
        $this->hero['image_brightness'] = 40;
        WebSetting::setGroup('hero', $this->hero);

        session()->flash('message', 'Foto latar belakang Hero berhasil direset ke foto bawaan kampus!');
    }

    /**
     * Upload inline media (gambar atau video) untuk slide showcase.
     */
    public function updatedSlideUploadFiles($value, $key): void
    {
        abort_unless(auth()->user()->can('web.landing.edit'), 403, 'Anda tidak memiliki hak akses.');

        $index = (int) $key;
        if (!isset($this->slideUploadFiles[$index]) || !$this->slideUploadFiles[$index]) {
            return;
        }

        $this->validate([
            "slideUploadFiles.{$index}" => 'required|file|mimes:jpg,jpeg,png,webp,mp4,webm|max:25600',
        ], [
            "slideUploadFiles.{$index}.mimes" => 'File harus berupa gambar (JPG, PNG, WEBP) atau video (MP4, WebM).',
            "slideUploadFiles.{$index}.max" => 'Ukuran file maksimal adalah 25 MB.',
        ]);

        $file = $this->slideUploadFiles[$index];
        $mime = $file->getMimeType() ?? '';
        $path = $file->store('web/hero', 'public');

        if (str_starts_with($mime, 'video/')) {
            $this->hero['slides'][$index]['media_type'] = 'video_upload';
            $this->hero['slides'][$index]['video_url'] = $path;
            if (empty($this->hero['slides'][$index]['poster'])) {
                $this->hero['slides'][$index]['poster'] = $this->hero['slides'][$index]['image'] ?? 'images/web/campus-environment.jpg';
            }
        } else {
            $this->hero['slides'][$index]['media_type'] = 'image';
            $this->hero['slides'][$index]['image'] = $path;
            $this->hero['slides'][$index]['poster'] = $path;
            $this->hero['slides'][$index]['video_url'] = '';
        }

        // Auto save group hero
        WebSetting::setGroup('hero', $this->hero);

        $this->slideUploadFiles[$index] = null;
        session()->flash('message', 'Media Slide #' . ($index + 1) . ' berhasil diunggah dan disimpan!');
    }

    /**
     * Kembalikan media slide ke pengaturan bawaan konfigurasi.
     */
    public function resetSlideMedia(int $index): void
    {
        abort_unless(auth()->user()->can('web.landing.edit'), 403, 'Anda tidak memiliki hak akses.');

        $defaultSlides = config('landing.hero.slides', []);
        $default = $defaultSlides[$index] ?? [
            'image' => 'images/web/campus-environment.jpg',
            'poster' => 'images/web/campus-environment.jpg',
            'video_url' => '',
            'media_type' => 'image',
        ];

        if (isset($this->hero['slides'][$index])) {
            $this->hero['slides'][$index]['media_type'] = $default['media_type'] ?? 'image';
            $this->hero['slides'][$index]['image'] = $default['image'] ?? 'images/web/campus-environment.jpg';
            $this->hero['slides'][$index]['poster'] = $default['poster'] ?? 'images/web/campus-environment.jpg';
            $this->hero['slides'][$index]['video_url'] = $default['video_url'] ?? '';

            WebSetting::setGroup('hero', $this->hero);
            $this->slideUploadFiles[$index] = null;
            session()->flash('message', 'Media Slide #' . ($index + 1) . ' berhasil dikembalikan ke bawaan!');
        }
    }

    public function getPrincipalPhotoPreviewProperty(): string
    {
        if ($this->principalImageFile) {
            try {
                return $this->principalImageFile->temporaryUrl();
            } catch (\Throwable $e) {
                // Ignore temporaryUrl errors
            }
        }

        return WebSetting::resolveImageUrl(
            $this->principal['image'] ?? null,
            'images/web/principal-kunedi.jpg'
        );
    }

    public function getFactImagePreview(int $index): string
    {
        if (isset($this->fastFactsImageFiles[$index]) && $this->fastFactsImageFiles[$index]) {
            try {
                return $this->fastFactsImageFiles[$index]->temporaryUrl();
            } catch (\Throwable $e) {
                // Ignore temporaryUrl errors
            }
        }

        return WebSetting::resolveImageUrl(
            $this->fast_facts[$index]['image'] ?? null,
            'images/web/campus-environment.jpg'
        );
    }

    public function updatedFastFactsImageFiles($value, $key): void
    {
        abort_unless(auth()->user()->can('web.landing.edit'), 403, 'Anda tidak memiliki hak akses.');

        $index = (int) $key;
        if (!isset($this->fastFactsImageFiles[$index]) || !$this->fastFactsImageFiles[$index]) {
            return;
        }

        $this->validate([
            "fastFactsImageFiles.{$index}" => 'required|image|max:5120',
        ]);

        $path = $this->fastFactsImageFiles[$index]->store('web/facts', 'public');
        $this->fast_facts[$index]['image'] = $path;
        WebSetting::setGroup('fast_facts', $this->fast_facts);
        $this->fastFactsImageFiles[$index] = null;
        session()->flash('message', 'Foto Kartu Metrik #' . ($index + 1) . ' berhasil diunggah!');
    }

    public function updatedPrincipalImageFile(): void
    {
        abort_unless(auth()->user()->can('web.landing.edit'), 403, 'Anda tidak memiliki hak akses.');
        if (!$this->principalImageFile) {
            return;
        }

        $this->validate([
            'principalImageFile' => 'required|image|max:5120',
        ]);

        $path = $this->principalImageFile->store('web/principal', 'public');
        $this->principal['image'] = $path;
        WebSetting::setGroup('principal', $this->principal);
        $this->principalImageFile = null;
        session()->flash('message', 'Foto Kepala Sekolah berhasil diunggah dan disimpan!');
    }

    public function resetFactImage(int $index): void
    {
        abort_unless(auth()->user()->can('web.landing.edit'), 403, 'Anda tidak memiliki hak akses.');

        $defaultFacts = config('landing.fast_facts', []);
        $this->fast_facts[$index]['image'] = $defaultFacts[$index]['image'] ?? 'images/web/campus-environment.jpg';
        WebSetting::setGroup('fast_facts', $this->fast_facts);
        session()->flash('message', 'Foto Kartu Metrik #' . ($index + 1) . ' berhasil direset ke foto bawaan!');
    }

    public function resetPrincipalPhoto(): void
    {
        abort_unless(auth()->user()->can('web.landing.edit'), 403, 'Anda tidak memiliki hak akses.');

        $this->principal['image'] = 'images/web/principal-kunedi.jpg';
        WebSetting::setGroup('principal', $this->principal);
        session()->flash('message', 'Foto Kepala Sekolah berhasil direset ke foto bawaan!');
    }

    public function save(): void
    {
        abort_unless(auth()->user()->can('web.landing.edit'), 403, 'Anda tidak memiliki hak akses untuk mengubah pengaturan landing page.');

        if ($this->hero_bg_file) {
            $this->validate([
                'hero_bg_file' => 'nullable|image|max:5120',
            ]);
            $path = $this->hero_bg_file->store('web/hero', 'public');
            $this->hero['background_image'] = $path;
            $this->hero_bg_file = null;
        }

        if (isset($this->hero['overlay_opacity'])) {
            $this->hero['overlay_opacity'] = max(0, min(100, (int)$this->hero['overlay_opacity']));
        }
        if (isset($this->hero['image_brightness'])) {
            $this->hero['image_brightness'] = max(0, min(100, (int)$this->hero['image_brightness']));
        }
        if (isset($this->hero['slider_delay'])) {
            $this->hero['slider_delay'] = max(3, min(30, (int)$this->hero['slider_delay']));
        }
        $this->hero['slider_autoplay'] = (bool)($this->hero['slider_autoplay'] ?? true);

        // Parse typewriter words from lines
        $words = array_values(array_filter(array_map('trim', explode("\n", $this->typewriterInput))));
        if (!empty($words)) {
            $this->hero['typewriter_words'] = $words;
        }

        // Pastikan welcome.pillars tersinkronisasi dengan principal.pillars
        $this->welcome['pillars'] = $this->principal['pillars'] ?? [];

        WebSetting::setGroup('announcement', $this->announcement);
        WebSetting::setGroup('branding', $this->branding);
        WebSetting::setGroup('hero', $this->hero);
        WebSetting::setGroup('welcome', $this->welcome);
        WebSetting::setGroup('fast_facts', $this->fast_facts);
        WebSetting::setGroup('principal', $this->principal);
        WebSetting::setGroup('student_moments', $this->student_moments);
        WebSetting::setGroup('mitra', $this->mitra);

        session()->flash('message', 'Pengaturan landing page berhasil disimpan dan langsung aktif di halaman depan!');
    }

    public function getHeroSlideMediaPreviewProperty(): string
    {
        if ($this->heroSlideMediaFile) {
            try {
                return $this->heroSlideMediaFile->temporaryUrl();
            } catch (\Throwable $e) {
                // Ignore temporaryUrl errors
            }
        }

        $media = $this->heroSlideForm['image'] ?? ($this->heroSlideForm['poster'] ?? null);
        return WebSetting::resolveImageUrl($media, 'images/web/campus-environment.jpg');
    }

    public function getHeroSlidePosterPreviewProperty(): string
    {
        if ($this->heroSlidePosterFile) {
            try {
                return $this->heroSlidePosterFile->temporaryUrl();
            } catch (\Throwable $e) {
                // Ignore temporaryUrl errors
            }
        }

        return WebSetting::resolveImageUrl(
            $this->heroSlideForm['poster'] ?? null,
            'images/web/campus-environment.jpg'
        );
    }

    public function openCreateHeroSlideModal(): void
    {
        $this->resetErrorBag();
        $this->heroSlideIndexEditing = null;
        $this->heroSlideMediaFile = null;
        $this->heroSlidePosterFile = null;
        $this->heroSlideForm = [
            'id' => 'slide_' . time(),
            'tab_name' => '',
            'badge' => 'SPMB 2026/2027',
            'title' => '',
            'highlight_input' => 'Teknologi & AI, Karakter Unggul, Prestasi Nasional',
            'desc' => '',
            'media_type' => 'image',
            'image' => 'images/web/campus-environment.jpg',
            'video_url' => '',
            'poster' => 'images/web/campus-environment.jpg',
            'overlay_opacity' => 70,
            'dimming' => 40,
            'cta_text' => 'Daftar SPMB Online',
            'cta_url' => 'https://spmb.smkwikrama1garut.sch.id/',
            'cta_target' => '_blank',
            'is_active' => true,
        ];
        $this->showHeroSlideModal = true;
    }

    public function openEditHeroSlideModal(int $index): void
    {
        $this->resetErrorBag();
        $this->heroSlideIndexEditing = $index;
        $this->heroSlideMediaFile = null;
        $this->heroSlidePosterFile = null;

        $slide = $this->hero['slides'][$index] ?? [];
        $hw = $slide['highlight_words'] ?? [];
        $highlightInput = is_array($hw) ? implode(', ', $hw) : (string) $hw;

        $this->heroSlideForm = array_merge([
            'id' => 'slide_' . ($index + 1),
            'tab_name' => '',
            'badge' => '',
            'title' => '',
            'highlight_input' => $highlightInput,
            'desc' => '',
            'media_type' => 'image',
            'image' => 'images/web/campus-environment.jpg',
            'video_url' => '',
            'poster' => '',
            'overlay_opacity' => 70,
            'dimming' => 40,
            'cta_text' => 'Pelajari Selengkapnya',
            'cta_url' => '#',
            'cta_target' => '_self',
            'is_active' => true,
        ], $slide);

        $this->heroSlideForm['highlight_input'] = $highlightInput;
        $this->showHeroSlideModal = true;
    }

    public function closeHeroSlideModal(): void
    {
        $this->showHeroSlideModal = false;
        $this->heroSlideIndexEditing = null;
        $this->heroSlideMediaFile = null;
        $this->heroSlidePosterFile = null;
    }

    public function saveHeroSlide(): void
    {
        abort_unless(auth()->user()->can('web.landing.edit'), 403, 'Anda tidak memiliki hak akses.');

        $this->validate([
            'heroSlideForm.title' => 'required|string|max:255',
            'heroSlideForm.desc' => 'required|string|max:1000',
            'heroSlideForm.cta_text' => 'required|string|max:100',
            'heroSlideForm.cta_url' => 'required|string|max:500',
            'heroSlideForm.cta_target' => 'required|in:_self,_blank',
            'heroSlideForm.media_type' => 'required|in:image,video_upload,video_url',
            'heroSlideForm.overlay_opacity' => 'required|numeric|min:0|max:100',
            'heroSlideForm.dimming' => 'required|numeric|min:0|max:100',
            'heroSlideMediaFile' => 'nullable|file|mimes:mp4,webm,ogg,jpg,jpeg,png,webp|max:25600', // max 25MB
            'heroSlidePosterFile' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        if ($this->heroSlideMediaFile) {
            $mime = $this->heroSlideMediaFile->getMimeType() ?? '';
            $path = $this->heroSlideMediaFile->store('web/hero', 'public');
            if (str_starts_with($mime, 'video/')) {
                $this->heroSlideForm['video_url'] = $path;
                $this->heroSlideForm['media_type'] = 'video_upload';
            } else {
                $this->heroSlideForm['image'] = $path;
                $this->heroSlideForm['media_type'] = 'image';
            }
            $this->heroSlideMediaFile = null;
        }

        if ($this->heroSlidePosterFile) {
            $path = $this->heroSlidePosterFile->store('web/hero', 'public');
            $this->heroSlideForm['poster'] = $path;
            $this->heroSlidePosterFile = null;
        }

        // If poster is empty and image is available, set poster to image
        if (empty($this->heroSlideForm['poster']) && !empty($this->heroSlideForm['image'])) {
            $this->heroSlideForm['poster'] = $this->heroSlideForm['image'];
        }

        // Parse highlight words
        $rawHighlights = $this->heroSlideForm['highlight_input'] ?? '';
        $words = array_values(array_filter(array_map('trim', preg_split('/[\r\n,]+/', $rawHighlights))));
        $this->heroSlideForm['highlight_words'] = !empty($words) ? $words : [$this->heroSlideForm['title']];

        // Generate id if empty
        if (empty($this->heroSlideForm['id'])) {
            $this->heroSlideForm['id'] = 'slide_' . time() . '_' . rand(100, 999);
        }
        if (empty($this->heroSlideForm['tab_name'])) {
            $this->heroSlideForm['tab_name'] = \Illuminate\Support\Str::limit($this->heroSlideForm['title'], 18);
        }

        // Format numbers
        $this->heroSlideForm['overlay_opacity'] = (int) $this->heroSlideForm['overlay_opacity'];
        $this->heroSlideForm['dimming'] = (int) $this->heroSlideForm['dimming'];

        // Clean internal input field before storing
        $slideData = $this->heroSlideForm;
        unset($slideData['highlight_input']);

        if ($this->heroSlideIndexEditing !== null && isset($this->hero['slides'][$this->heroSlideIndexEditing])) {
            $this->hero['slides'][$this->heroSlideIndexEditing] = $slideData;
        } else {
            $this->hero['slides'][] = $slideData;
        }

        // Auto save to database
        WebSetting::setGroup('hero', $this->hero);

        $this->closeHeroSlideModal();
        session()->flash('message', 'Slide Hero berhasil disimpan dan langsung aktif di halaman depan!');
    }

    public function deleteHeroSlide(int $index): void
    {
        abort_unless(auth()->user()->can('web.landing.edit'), 403, 'Anda tidak memiliki hak akses.');

        if (isset($this->hero['slides'][$index])) {
            array_splice($this->hero['slides'], $index, 1);
            WebSetting::setGroup('hero', $this->hero);
            session()->flash('message', 'Slide Hero berhasil dihapus.');
        }
    }

    public function moveHeroSlideUp(int $index): void
    {
        abort_unless(auth()->user()->can('web.landing.edit'), 403, 'Anda tidak memiliki hak akses.');

        if ($index > 0 && isset($this->hero['slides'][$index])) {
            $temp = $this->hero['slides'][$index - 1];
            $this->hero['slides'][$index - 1] = $this->hero['slides'][$index];
            $this->hero['slides'][$index] = $temp;
            WebSetting::setGroup('hero', $this->hero);
        }
    }

    public function moveHeroSlideDown(int $index): void
    {
        abort_unless(auth()->user()->can('web.landing.edit'), 403, 'Anda tidak memiliki hak akses.');

        $count = count($this->hero['slides'] ?? []);
        if ($index < $count - 1 && isset($this->hero['slides'][$index])) {
            $temp = $this->hero['slides'][$index + 1];
            $this->hero['slides'][$index + 1] = $this->hero['slides'][$index];
            $this->hero['slides'][$index] = $temp;
            WebSetting::setGroup('hero', $this->hero);
        }
    }

    public function toggleHeroSlideActive(int $index): void
    {
        abort_unless(auth()->user()->can('web.landing.edit'), 403, 'Anda tidak memiliki hak akses.');

        if (isset($this->hero['slides'][$index])) {
            $this->hero['slides'][$index]['is_active'] = !($this->hero['slides'][$index]['is_active'] ?? true);
            WebSetting::setGroup('hero', $this->hero);
        }
    }

    public function getCardImagePreviewProperty(): string
    {
        if ($this->cardImageFile) {
            try {
                return $this->cardImageFile->temporaryUrl();
            } catch (\Throwable $e) {
                // Ignore temporaryUrl errors
            }
        }

        return WebSetting::resolveImageUrl(
            $this->cardForm['image'] ?? null,
            'images/web/campus-environment.jpg'
        );
    }

    public function openCreateCardModal(): void
    {
        $this->resetErrorBag();
        $this->cardIndexEditing = null;
        $this->cardImageFile = null;
        $this->cardForm = [
            'title' => '',
            'desc' => '',
            'image' => 'images/web/campus-environment.jpg',
            'url' => '#',
            'cta_text' => 'Lebih Lanjut',
            'target' => '_self',
            'badge' => '',
            'is_active' => true,
        ];
        $this->showCardModal = true;
    }

    public function openEditCardModal(int $index): void
    {
        $this->resetErrorBag();
        $this->cardIndexEditing = $index;
        $this->cardImageFile = null;
        $this->cardForm = array_merge([
            'title' => '',
            'desc' => '',
            'image' => 'images/web/campus-environment.jpg',
            'url' => '#',
            'cta_text' => 'Lebih Lanjut',
            'target' => '_self',
            'badge' => '',
            'is_active' => true,
        ], $this->student_moments['items'][$index] ?? []);
        $this->showCardModal = true;
    }

    public function closeCardModal(): void
    {
        $this->showCardModal = false;
        $this->cardIndexEditing = null;
        $this->cardImageFile = null;
    }

    public function saveCard(): void
    {
        abort_unless(auth()->user()->can('web.landing.edit'), 403, 'Anda tidak memiliki hak akses.');

        $this->validate([
            'cardForm.title' => 'required|string|max:255',
            'cardForm.desc' => 'required|string|max:1000',
            'cardForm.url' => 'required|string|max:500',
            'cardForm.cta_text' => 'required|string|max:100',
            'cardForm.target' => 'required|in:_self,_blank',
            'cardForm.badge' => 'nullable|string|max:100',
            'cardImageFile' => 'nullable|image|max:5120',
        ]);

        if ($this->cardImageFile) {
            $path = $this->cardImageFile->store('web/student-moments', 'public');
            $this->cardForm['image'] = $path;
            $this->cardImageFile = null;
        }

        if ($this->cardIndexEditing !== null && isset($this->student_moments['items'][$this->cardIndexEditing])) {
            $this->student_moments['items'][$this->cardIndexEditing] = $this->cardForm;
        } else {
            $this->student_moments['items'][] = $this->cardForm;
        }

        // Auto save to database
        WebSetting::setGroup('student_moments', $this->student_moments);

        $this->closeCardModal();
        session()->flash('message', 'Card Dinamika Siswa berhasil disimpan!');
    }

    public function deleteCard(int $index): void
    {
        abort_unless(auth()->user()->can('web.landing.edit'), 403, 'Anda tidak memiliki hak akses.');

        if (isset($this->student_moments['items'][$index])) {
            array_splice($this->student_moments['items'], $index, 1);
            WebSetting::setGroup('student_moments', $this->student_moments);
            session()->flash('message', 'Card Dinamika Siswa berhasil dihapus.');
        }
    }

    public function moveCardUp(int $index): void
    {
        abort_unless(auth()->user()->can('web.landing.edit'), 403, 'Anda tidak memiliki hak akses.');

        if ($index > 0 && isset($this->student_moments['items'][$index])) {
            $temp = $this->student_moments['items'][$index - 1];
            $this->student_moments['items'][$index - 1] = $this->student_moments['items'][$index];
            $this->student_moments['items'][$index] = $temp;
            WebSetting::setGroup('student_moments', $this->student_moments);
        }
    }

    public function moveCardDown(int $index): void
    {
        abort_unless(auth()->user()->can('web.landing.edit'), 403, 'Anda tidak memiliki hak akses.');

        $count = count($this->student_moments['items'] ?? []);
        if ($index < $count - 1 && isset($this->student_moments['items'][$index])) {
            $temp = $this->student_moments['items'][$index + 1];
            $this->student_moments['items'][$index + 1] = $this->student_moments['items'][$index];
            $this->student_moments['items'][$index] = $temp;
            WebSetting::setGroup('student_moments', $this->student_moments);
        }
    }

    public function toggleCardActive(int $index): void
    {
        abort_unless(auth()->user()->can('web.landing.edit'), 403, 'Anda tidak memiliki hak akses.');

        if (isset($this->student_moments['items'][$index])) {
            $this->student_moments['items'][$index]['is_active'] = !($this->student_moments['items'][$index]['is_active'] ?? true);
            WebSetting::setGroup('student_moments', $this->student_moments);
        }
    }

    public function getMitraImagePreviewProperty(): string
    {
        if ($this->mitraImageFile) {
            try {
                return $this->mitraImageFile->temporaryUrl();
            } catch (\Throwable $e) {
                // Ignore temporaryUrl errors
            }
        }

        return WebSetting::resolveImageUrl(
            $this->mitraForm['image'] ?? null,
            'images/landing/mitra-huawei.png'
        );
    }

    public function openCreateMitraModal(): void
    {
        $this->resetErrorBag();
        $this->mitraIndexEditing = null;
        $this->mitraImageFile = null;
        $this->mitraForm = [
            'name' => '',
            'text' => '',
            'image' => '',
            'url' => '',
            'is_active' => true,
        ];
        $this->showMitraModal = true;
    }

    public function openEditMitraModal(int $index): void
    {
        $this->resetErrorBag();
        $this->mitraIndexEditing = $index;
        $this->mitraImageFile = null;

        if (isset($this->mitra['items'][$index])) {
            $item = $this->mitra['items'][$index];
            $this->mitraForm = [
                'name' => $item['name'] ?? '',
                'text' => $item['text'] ?? ($item['name'] ?? ''),
                'image' => $item['image'] ?? '',
                'url' => $item['url'] ?? '',
                'is_active' => isset($item['is_active']) ? (bool)$item['is_active'] : true,
            ];
            $this->showMitraModal = true;
        }
    }

    public function closeMitraModal(): void
    {
        $this->showMitraModal = false;
        $this->mitraIndexEditing = null;
        $this->mitraImageFile = null;
    }

    public function saveMitra(): void
    {
        abort_unless(auth()->user()->can('web.landing.edit'), 403, 'Anda tidak memiliki hak akses.');

        $this->validate([
            'mitraForm.text' => 'nullable|string|max:500',
            'mitraForm.name' => 'nullable|string|max:255',
            'mitraForm.url' => 'nullable|string|max:500',
            'mitraImageFile' => 'nullable|image|max:5120',
        ]);

        if (empty($this->mitraForm['name']) && !empty($this->mitraForm['text'])) {
            $this->mitraForm['name'] = strtok($this->mitraForm['text'], "\n");
        }

        if ($this->mitraImageFile) {
            $path = $this->mitraImageFile->store('web/mitra', 'public');
            $this->mitraForm['image'] = $path;
            $this->mitraImageFile = null;
        }

        if ($this->mitraIndexEditing !== null && isset($this->mitra['items'][$this->mitraIndexEditing])) {
            $this->mitra['items'][$this->mitraIndexEditing] = $this->mitraForm;
        } else {
            $this->mitra['items'][] = $this->mitraForm;
        }

        // Auto save to database
        WebSetting::setGroup('mitra', $this->mitra);

        $this->closeMitraModal();
        session()->flash('message', 'Mitra Link & Match industri berhasil disimpan!');
    }

    public function deleteMitra(int $index): void
    {
        abort_unless(auth()->user()->can('web.landing.edit'), 403, 'Anda tidak memiliki hak akses.');

        if (isset($this->mitra['items'][$index])) {
            array_splice($this->mitra['items'], $index, 1);
            WebSetting::setGroup('mitra', $this->mitra);
            session()->flash('message', 'Mitra industri berhasil dihapus.');
        }
    }

    public function moveMitraUp(int $index): void
    {
        abort_unless(auth()->user()->can('web.landing.edit'), 403, 'Anda tidak memiliki hak akses.');

        if ($index > 0 && isset($this->mitra['items'][$index])) {
            $temp = $this->mitra['items'][$index - 1];
            $this->mitra['items'][$index - 1] = $this->mitra['items'][$index];
            $this->mitra['items'][$index] = $temp;
            WebSetting::setGroup('mitra', $this->mitra);
        }
    }

    public function moveMitraDown(int $index): void
    {
        abort_unless(auth()->user()->can('web.landing.edit'), 403, 'Anda tidak memiliki hak akses.');

        $count = count($this->mitra['items'] ?? []);
        if ($index < $count - 1 && isset($this->mitra['items'][$index])) {
            $temp = $this->mitra['items'][$index + 1];
            $this->mitra['items'][$index + 1] = $this->mitra['items'][$index];
            $this->mitra['items'][$index] = $temp;
            WebSetting::setGroup('mitra', $this->mitra);
        }
    }

    public function toggleMitraActive(int $index): void
    {
        abort_unless(auth()->user()->can('web.landing.edit'), 403, 'Anda tidak memiliki hak akses.');

        if (isset($this->mitra['items'][$index])) {
            $this->mitra['items'][$index]['is_active'] = !($this->mitra['items'][$index]['is_active'] ?? true);
            WebSetting::setGroup('mitra', $this->mitra);
        }
    }

    public function resetToDefault(): void
    {
        abort_unless(auth()->user()->can('web.landing.edit'), 403, 'Anda tidak memiliki hak akses untuk mereset pengaturan landing page.');

        WebSetting::whereIn('group', ['announcement', 'branding', 'hero', 'welcome', 'student_moments', 'mitra'])->delete();
        $this->loadData();
        session()->flash('message', 'Pengaturan berhasil direset ke konfigurasi awal file landing.php!');
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('administrasiweb::livewire.landing-setting');
    }
}
