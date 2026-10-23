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
    public array $student_moments = [];
    public string $typewriterInput = '';

    // File upload for hero background image
    public $hero_bg_file = null;

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
        $this->student_moments = WebSetting::getGroup('student_moments');

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

        // Parse typewriter words from lines
        $words = array_values(array_filter(array_map('trim', explode("\n", $this->typewriterInput))));
        if (!empty($words)) {
            $this->hero['typewriter_words'] = $words;
        }

        WebSetting::setGroup('announcement', $this->announcement);
        WebSetting::setGroup('branding', $this->branding);
        WebSetting::setGroup('hero', $this->hero);
        WebSetting::setGroup('welcome', $this->welcome);
        WebSetting::setGroup('student_moments', $this->student_moments);

        session()->flash('message', 'Pengaturan landing page berhasil disimpan dan langsung aktif di halaman depan!');
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

    public function resetToDefault(): void
    {
        abort_unless(auth()->user()->can('web.landing.edit'), 403, 'Anda tidak memiliki hak akses untuk mereset pengaturan landing page.');

        WebSetting::whereIn('group', ['announcement', 'branding', 'hero', 'welcome', 'student_moments'])->delete();
        $this->loadData();
        session()->flash('message', 'Pengaturan berhasil direset ke konfigurasi awal file landing.php!');
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('administrasiweb::livewire.landing-setting');
    }
}
