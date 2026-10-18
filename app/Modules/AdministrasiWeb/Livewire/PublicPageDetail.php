<?php

namespace App\Modules\AdministrasiWeb\Livewire;

use App\Modules\AdministrasiWeb\Models\WebPage;
use Livewire\Attributes\Layout;
use Livewire\Component;

class PublicPageDetail extends Component
{
    public string $slug;

    public function mount(string $slug): void
    {
        $this->slug = $slug;
    }

    #[Layout('components.layouts.blank')]
    public function render()
    {
        $page = WebPage::where('slug', $this->slug)
            ->where('is_active', true)
            ->firstOrFail();

        return view('administrasiweb::livewire.public-page-detail', [
            'page' => $page,
        ]);
    }
}
