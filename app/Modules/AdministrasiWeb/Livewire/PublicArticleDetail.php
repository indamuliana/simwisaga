<?php

namespace App\Modules\AdministrasiWeb\Livewire;

use App\Modules\AdministrasiWeb\Models\WebArticle;
use Livewire\Attributes\Layout;
use Livewire\Component;

class PublicArticleDetail extends Component
{
    public string $slug;

    public function mount(string $slug): void
    {
        $this->slug = $slug;
    }

    #[Layout('components.layouts.blank')]
    public function render()
    {
        $article = WebArticle::with('category')->where('slug', $this->slug)->firstOrFail();
        
        // Increment view count
        $article->increment('view_count');

        $relatedArticles = WebArticle::with('category')
            ->published()
            ->where('id', '!=', $article->id)
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('administrasiweb::livewire.public-article-detail', [
            'article' => $article,
            'relatedArticles' => $relatedArticles,
        ]);
    }
}
