<?php

namespace App\Modules\AdministrasiWeb\Livewire;

use App\Modules\AdministrasiWeb\Models\WebArticle;
use App\Modules\AdministrasiWeb\Models\WebCategory;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

class ArticleForm extends Component
{
    use WithFileUploads;

    public ?int $articleId = null;

    public string $judul = '';
    public string $slug = '';
    public ?int $kategori_id = null;
    public string $penulis_nama = '';
    public string $ringkasan = '';
    public string $konten = '';
    public string $status = 'published';
    public ?string $published_at = null;

    public $gambar_sampul_file = null;
    public ?string $existing_gambar_sampul = null;

    public function mount(?int $id = null): void
    {
        WebCategory::seedDefaultsIfEmpty();

        if ($id) {
            $article = WebArticle::findOrFail($id);
            $this->articleId = $article->id;
            $this->judul = $article->judul;
            $this->slug = $article->slug;
            $this->kategori_id = $article->kategori_id;
            $this->penulis_nama = $article->penulis_nama ?: '';
            $this->ringkasan = $article->ringkasan ?: '';
            $this->konten = $article->konten;
            $this->status = $article->status;
            $this->published_at = $article->published_at ? $article->published_at->format('Y-m-d\TH:i') : null;
            $this->existing_gambar_sampul = $article->gambar_sampul;
        } else {
            $this->penulis_nama = auth()->user()?->name ?: 'Humas Wikrama';
            $this->published_at = now()->format('Y-m-d\TH:i');
            $this->kategori_id = WebCategory::first()?->id;
        }
    }

    public function updatedJudul(string $val): void
    {
        if (!$this->articleId || empty($this->slug)) {
            $this->slug = Str::slug($val);
        }
    }

    public function save()
    {
        if ($this->articleId) {
            abort_unless(auth()->user()->can('web.artikel.edit'), 403, 'Anda tidak memiliki hak akses untuk mengedit artikel.');
        } else {
            abort_unless(auth()->user()->can('web.artikel.tambah'), 403, 'Anda tidak memiliki hak akses untuk menambah artikel.');
        }

        $this->validate([
            'judul' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:web_articles,slug,' . $this->articleId,
            'kategori_id' => 'nullable|exists:web_categories,id',
            'penulis_nama' => 'nullable|string|max:100',
            'ringkasan' => 'nullable|string|max:500',
            'konten' => 'required|string',
            'status' => 'required|in:draft,published',
            'gambar_sampul_file' => 'nullable|image|max:3072',
        ]);

        $sampulPath = $this->existing_gambar_sampul;
        if ($this->gambar_sampul_file) {
            $sampulPath = $this->gambar_sampul_file->store('web/articles/sampul', 'public');
        }

        $cleanKonten = \App\Modules\AdministrasiWeb\Support\WebSecurity::cleanHtml($this->konten);

        $data = [
            'kategori_id' => $this->kategori_id,
            'penulis_id' => auth()->id(),
            'penulis_nama' => $this->penulis_nama,
            'judul' => $this->judul,
            'slug' => Str::slug($this->slug),
            'ringkasan' => $this->ringkasan ?: Str::limit(strip_tags($cleanKonten), 160),
            'konten' => $cleanKonten,
            'gambar_sampul' => $sampulPath,
            'status' => $this->status,
            'published_at' => $this->status === 'published' ? ($this->published_at ? date('Y-m-d H:i:s', strtotime($this->published_at)) : now()) : null,
        ];

        if ($this->articleId) {
            $article = WebArticle::findOrFail($this->articleId);
            $article->update($data);
            $pesan = "Artikel '{$article->judul}' berhasil diperbarui!";
        } else {
            $article = WebArticle::create($data);
            $pesan = "Artikel baru berhasil diterbitkan!";
        }

        session()->flash('message', $pesan);
        return redirect()->route('web-admin.articles.index');
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        $categories = WebCategory::orderBy('urutan')->get();

        return view('administrasiweb::livewire.article-form', [
            'categories' => $categories,
        ]);
    }
}
