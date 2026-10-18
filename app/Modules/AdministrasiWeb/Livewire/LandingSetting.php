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
    public string $typewriterInput = '';

    // File upload for hero background image
    public $hero_bg_file = null;

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

        session()->flash('message', 'Pengaturan landing page berhasil disimpan dan langsung aktif di halaman depan!');
    }

    public function resetToDefault(): void
    {
        abort_unless(auth()->user()->can('web.landing.edit'), 403, 'Anda tidak memiliki hak akses untuk mereset pengaturan landing page.');

        WebSetting::whereIn('group', ['announcement', 'branding', 'hero', 'welcome'])->delete();
        $this->loadData();
        session()->flash('message', 'Pengaturan berhasil direset ke konfigurasi awal file landing.php!');
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('administrasiweb::livewire.landing-setting');
    }
}
