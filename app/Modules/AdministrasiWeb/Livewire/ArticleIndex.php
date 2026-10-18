<?php

namespace App\Modules\AdministrasiWeb\Livewire;

use App\Modules\AdministrasiWeb\Models\WebArticle;
use App\Modules\AdministrasiWeb\Models\WebCategory;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ArticleIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public ?string $filterKategori = null;

    #[Url]
    public ?string $filterStatus = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterKategori(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function toggleStatus(int $articleId): void
    {
        abort_unless(auth()->user()->can('web.artikel.publikasi'), 403, 'Anda tidak memiliki hak akses untuk mempublikasikan artikel.');

        $article = WebArticle::findOrFail($articleId);
        $newStatus = $article->status === 'published' ? 'draft' : 'published';
        $article->update([
            'status' => $newStatus,
            'published_at' => $newStatus === 'published' && !$article->published_at ? now() : $article->published_at,
        ]);

        session()->flash('message', "Status artikel '{$article->judul}' berhasil diubah ke {$newStatus}!");
    }

    public function deleteArticle(int $articleId): void
    {
        abort_unless(auth()->user()->can('web.artikel.hapus'), 403, 'Anda tidak memiliki hak akses untuk menghapus artikel.');

        $article = WebArticle::findOrFail($articleId);
        $judul = $article->judul;
        $article->delete();

        session()->flash('message', "Artikel '{$judul}' berhasil dihapus.");
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        $categories = WebCategory::orderBy('urutan')->get();

        $articles = WebArticle::with('category')
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('judul', 'like', "%{$this->search}%")
                        ->orWhere('ringkasan', 'like', "%{$this->search}%")
                        ->orWhere('penulis_nama', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterKategori, fn ($q) => $q->where('kategori_id', $this->filterKategori))
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->latest('id')
            ->paginate(10);

        return view('administrasiweb::livewire.article-index', [
            'articles' => $articles,
            'categories' => $categories,
        ]);
    }
}
