<?php

namespace App\Modules\AdministrasiWeb\Livewire;

use App\Modules\AdministrasiWeb\Models\WebArticle;
use App\Modules\AdministrasiWeb\Models\WebCategory;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class PublicArticleList extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $kategori = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingKategori(): void
    {
        $this->resetPage();
    }

    public function selectKategori(string $slug): void
    {
        $this->kategori = ($this->kategori === $slug) ? '' : $slug;
        $this->resetPage();
    }

    #[Layout('components.layouts.blank')]
    public function render()
    {
        $query = WebArticle::with('category')
            ->published()
            ->latest('published_at');

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('judul', 'like', '%' . $this->search . '%')
                    ->orWhere('ringkasan', 'like', '%' . $this->search . '%')
                    ->orWhere('konten', 'like', '%' . $this->search . '%');
            });
        }

        if (! empty($this->kategori)) {
            $query->whereHas('category', function ($q) {
                $q->where('slug', $this->kategori);
            });
        }

        $articles = $query->paginate(9);
        $categories = WebCategory::whereHas('articles', fn ($q) => $q->published())->get();

        return view('administrasiweb::livewire.public-article-list', [
            'articles' => $articles,
            'categories' => $categories,
        ]);
    }
}
