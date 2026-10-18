<?php

namespace App\Modules\AdministrasiWeb\Livewire;

use App\Modules\AdministrasiWeb\Models\WebPage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

class PageForm extends Component
{
    use WithFileUploads;

    public ?int $pageId = null;

    public string $judul = '';
    public string $slug = '';
    public string $ringkasan = '';
    public string $konten = '';
    public string $meta_deskripsi = '';
    public ?string $gambar_banner = null;
    public $banner_upload = null;
    public bool $is_active = true;

    public function mount(?int $id = null): void
    {
        if ($id) {
            $page = WebPage::findOrFail($id);
            $this->pageId = $page->id;
            $this->judul = $page->judul;
            $this->slug = $page->slug;
            $this->ringkasan = $page->ringkasan ?? '';
            $this->konten = $page->konten ?? '';
            $this->meta_deskripsi = $page->meta_deskripsi ?? '';
            $this->gambar_banner = $page->gambar_banner;
            $this->is_active = $page->is_active;
        }
    }

    public function updatedJudul(string $val): void
    {
        if (! $this->pageId || empty($this->slug)) {
            $this->slug = Str::slug($val);
        }
    }

    protected function rules(): array
    {
        return [
            'judul' => 'required|string|max:200',
            'slug' => 'required|string|max:200|unique:web_pages,slug,' . ($this->pageId ?? 'NULL') . ',id',
            'ringkasan' => 'nullable|string|max:500',
            'konten' => 'nullable|string',
            'meta_deskripsi' => 'nullable|string|max:255',
            'banner_upload' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ];
    }

    public function save()
    {
        if ($this->pageId) {
            abort_unless(auth()->user()->can('web.halaman.edit'), 403, 'Anda tidak memiliki hak akses untuk mengedit halaman.');
        } else {
            abort_unless(auth()->user()->can('web.halaman.tambah'), 403, 'Anda tidak memiliki hak akses untuk menambah halaman.');
        }

        $validated = $this->validate();

        $cleanKonten = \App\Modules\AdministrasiWeb\Support\WebSecurity::cleanHtml($this->konten);

        $data = [
            'judul' => $this->judul,
            'slug' => Str::slug($this->slug),
            'ringkasan' => \App\Modules\AdministrasiWeb\Support\WebSecurity::cleanHtml($this->ringkasan),
            'konten' => $cleanKonten,
            'meta_deskripsi' => $this->meta_deskripsi,
            'is_active' => $this->is_active,
        ];

        if ($this->banner_upload) {
            $path = $this->banner_upload->store('web/pages', 'public');
            $data['gambar_banner'] = $path;
        }

        if ($this->pageId) {
            $page = WebPage::findOrFail($this->pageId);
            $page->update($data);
            session()->flash('message', 'Halaman kustom berhasil diperbarui.');
        } else {
            WebPage::create($data);
            session()->flash('message', 'Halaman kustom baru berhasil dibuat.');
        }

        return redirect()->route('web-admin.pages.index');
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('administrasiweb::livewire.page-form');
    }
}
