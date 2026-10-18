<?php

namespace App\Modules\AdministrasiWeb\Livewire;

use App\Modules\AdministrasiWeb\Models\WebTestimonial;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

class TestimonialIndex extends Component
{
    use WithFileUploads;

    public string $filterCategory = 'all';

    public bool $showModal = false;
    public ?int $editingId = null;

    public string $category = 'Alumni';
    public string $name = '';
    public string $role = '';
    public string $question = '';
    public string $quote = '';
    public int $rating = 5;
    public ?string $existing_image = null;
    public $avatar_upload = null;
    public int $urutan = 0;
    public bool $is_active = true;

    protected function rules(): array
    {
        return [
            'category' => 'required|string|max:50',
            'name' => 'required|string|max:150',
            'role' => 'nullable|string|max:150',
            'question' => 'nullable|string|max:200',
            'quote' => 'required|string|max:1000',
            'rating' => 'required|integer|min:1|max:5',
            'avatar_upload' => 'nullable|image|max:2048',
            'urutan' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ];
    }

    public function openCreateModal(): void
    {
        $this->resetValidation();
        $this->editingId = null;
        $this->category = 'Alumni';
        $this->name = '';
        $this->role = '';
        $this->question = 'Apa yang membuat Anda bangga menjadi bagian dari Wikrama?';
        $this->quote = '';
        $this->rating = 5;
        $this->existing_image = null;
        $this->avatar_upload = null;
        $maxUrutan = WebTestimonial::max('urutan') ?? 0;
        $this->urutan = $maxUrutan + 1;
        $this->is_active = true;
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $this->resetValidation();
        $item = WebTestimonial::findOrFail($id);
        $this->editingId = $item->id;
        $this->category = $item->category;
        $this->name = $item->name;
        $this->role = $item->role ?? '';
        $this->question = $item->question ?? '';
        $this->quote = $item->quote;
        $this->rating = $item->rating;
        $this->existing_image = $item->image;
        $this->avatar_upload = null;
        $this->urutan = $item->urutan;
        $this->is_active = $item->is_active;
        $this->showModal = true;
    }

    public function save(): void
    {
        if ($this->editingId) {
            abort_unless(auth()->user()->can('web.galeri.edit'), 403, 'Anda tidak memiliki hak akses untuk mengedit testimoni.');
        } else {
            abort_unless(auth()->user()->can('web.galeri.tambah'), 403, 'Anda tidak memiliki hak akses untuk menambah testimoni.');
        }

        $validated = $this->validate();

        $data = [
            'category' => $this->category,
            'name' => strip_tags($this->name),
            'role' => strip_tags($this->role),
            'question' => strip_tags($this->question),
            'quote' => strip_tags($this->quote),
            'rating' => $this->rating,
            'urutan' => $this->urutan,
            'is_active' => $this->is_active,
        ];

        if ($this->avatar_upload) {
            $path = $this->avatar_upload->store('web/testimonials', 'public');
            $data['image'] = $path;
        }

        if ($this->editingId) {
            $item = WebTestimonial::findOrFail($this->editingId);
            $item->update($data);
            session()->flash('message', 'Testimoni berhasil diperbarui.');
        } else {
            if (! isset($data['image'])) {
                $data['image'] = 'images/landing/suasana-belajar-aktif.jpg';
            }
            WebTestimonial::create($data);
            session()->flash('message', 'Testimoni baru berhasil ditambahkan.');
        }

        $this->showModal = false;
    }

    public function toggleActive(int $id): void
    {
        abort_unless(auth()->user()->can('web.galeri.edit'), 403, 'Anda tidak memiliki hak akses untuk mengubah status testimoni.');

        $item = WebTestimonial::findOrFail($id);
        $item->update(['is_active' => ! $item->is_active]);
    }

    public function moveUp(int $id): void
    {
        abort_unless(auth()->user()->can('web.galeri.edit'), 403, 'Anda tidak memiliki hak akses untuk mengubah urutan testimoni.');

        $item = WebTestimonial::findOrFail($id);
        $previous = WebTestimonial::where('urutan', '<', $item->urutan)
            ->orderBy('urutan', 'desc')
            ->first();

        if ($previous) {
            $prevUrutan = $previous->urutan;
            $previous->update(['urutan' => $item->urutan]);
            $item->update(['urutan' => $prevUrutan]);
        }
    }

    public function moveDown(int $id): void
    {
        abort_unless(auth()->user()->can('web.galeri.edit'), 403, 'Anda tidak memiliki hak akses untuk mengubah urutan testimoni.');

        $item = WebTestimonial::findOrFail($id);
        $next = WebTestimonial::where('urutan', '>', $item->urutan)
            ->orderBy('urutan', 'asc')
            ->first();

        if ($next) {
            $nextUrutan = $next->urutan;
            $next->update(['urutan' => $item->urutan]);
            $item->update(['urutan' => $nextUrutan]);
        }
    }

    public function delete(int $id): void
    {
        abort_unless(auth()->user()->can('web.galeri.hapus'), 403, 'Anda tidak memiliki hak akses untuk menghapus testimoni.');

        $item = WebTestimonial::findOrFail($id);
        $item->delete();
        session()->flash('message', 'Testimoni berhasil dihapus.');
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        $query = WebTestimonial::query()->orderBy('urutan');

        if ($this->filterCategory !== 'all') {
            $query->where('category', $this->filterCategory);
        }

        $testimonials = $query->get();

        return view('administrasiweb::livewire.testimonial-index', [
            'testimonials' => $testimonials,
        ]);
    }
}
