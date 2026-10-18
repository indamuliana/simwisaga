<?php

namespace App\Modules\AdministrasiWeb\Livewire;

use App\Modules\AdministrasiWeb\Models\WebFacility;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

class FacilityIndex extends Component
{
    use WithFileUploads;

    public string $filterCategory = 'all';

    public bool $showModal = false;
    public ?int $editingId = null;

    public string $category = 'lab';
    public string $cat_label = 'Lab Komputer & Coding';
    public string $badge = '';
    public string $title = '';
    public string $desc = '';
    public string $highlight = '';
    public ?string $existing_image = null;
    public $image_upload = null;
    public int $urutan = 0;
    public bool $is_active = true;

    protected function rules(): array
    {
        return [
            'category' => 'required|string|max:50',
            'cat_label' => 'nullable|string|max:100',
            'badge' => 'nullable|string|max:100',
            'title' => 'required|string|max:150',
            'desc' => 'nullable|string|max:1000',
            'highlight' => 'nullable|string|max:100',
            'image_upload' => 'nullable|image|max:2048',
            'urutan' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ];
    }

    public function openCreateModal(): void
    {
        $this->resetValidation();
        $this->editingId = null;
        $this->category = 'lab';
        $this->cat_label = 'Lab Komputer & Coding';
        $this->badge = '';
        $this->title = '';
        $this->desc = '';
        $this->highlight = '';
        $this->existing_image = null;
        $this->image_upload = null;
        $maxUrutan = WebFacility::max('urutan') ?? 0;
        $this->urutan = $maxUrutan + 1;
        $this->is_active = true;
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $this->resetValidation();
        $facility = WebFacility::findOrFail($id);
        $this->editingId = $facility->id;
        $this->category = $facility->category;
        $this->cat_label = $facility->cat_label ?? '';
        $this->badge = $facility->badge ?? '';
        $this->title = $facility->title;
        $this->desc = $facility->desc ?? '';
        $this->highlight = $facility->highlight ?? '';
        $this->existing_image = $facility->image;
        $this->image_upload = null;
        $this->urutan = $facility->urutan;
        $this->is_active = $facility->is_active;
        $this->showModal = true;
    }

    public function updatedCategory(string $val): void
    {
        $map = [
            'lab' => 'Lab Komputer & Coding',
            'kelas' => 'Kelas Digital & TeFa',
            'gedung' => 'Gedung & Adiwiyata',
            'aktivitas' => 'Ekosistem Kampus',
            'umum' => 'Fasilitas Umum',
        ];
        if (isset($map[$val])) {
            $this->cat_label = $map[$val];
        }
    }

    public function save(): void
    {
        if ($this->editingId) {
            abort_unless(auth()->user()->can('web.galeri.edit'), 403, 'Anda tidak memiliki hak akses untuk mengedit fasilitas.');
        } else {
            abort_unless(auth()->user()->can('web.galeri.tambah'), 403, 'Anda tidak memiliki hak akses untuk menambah fasilitas.');
        }

        $validated = $this->validate();

        $data = [
            'category' => $this->category,
            'cat_label' => strip_tags($this->cat_label),
            'badge' => strip_tags($this->badge),
            'title' => strip_tags($this->title),
            'desc' => strip_tags($this->desc),
            'highlight' => strip_tags($this->highlight),
            'urutan' => $this->urutan,
            'is_active' => $this->is_active,
        ];

        if ($this->image_upload) {
            $path = $this->image_upload->store('web/facilities', 'public');
            $data['image'] = $path;
        }

        if ($this->editingId) {
            $facility = WebFacility::findOrFail($this->editingId);
            $facility->update($data);
            session()->flash('message', 'Fasilitas berhasil diperbarui.');
        } else {
            if (! isset($data['image'])) {
                $data['image'] = 'images/landing/lab-komputer-pplg.jpg';
            }
            WebFacility::create($data);
            session()->flash('message', 'Fasilitas baru berhasil ditambahkan.');
        }

        $this->showModal = false;
    }

    public function toggleActive(int $id): void
    {
        abort_unless(auth()->user()->can('web.galeri.edit'), 403, 'Anda tidak memiliki hak akses untuk mengubah status fasilitas.');

        $facility = WebFacility::findOrFail($id);
        $facility->update(['is_active' => ! $facility->is_active]);
    }

    public function moveUp(int $id): void
    {
        abort_unless(auth()->user()->can('web.galeri.edit'), 403, 'Anda tidak memiliki hak akses untuk mengubah urutan fasilitas.');

        $facility = WebFacility::findOrFail($id);
        $previous = WebFacility::where('urutan', '<', $facility->urutan)
            ->orderBy('urutan', 'desc')
            ->first();

        if ($previous) {
            $prevUrutan = $previous->urutan;
            $previous->update(['urutan' => $facility->urutan]);
            $facility->update(['urutan' => $prevUrutan]);
        }
    }

    public function moveDown(int $id): void
    {
        abort_unless(auth()->user()->can('web.galeri.edit'), 403, 'Anda tidak memiliki hak akses untuk mengubah urutan fasilitas.');

        $facility = WebFacility::findOrFail($id);
        $next = WebFacility::where('urutan', '>', $facility->urutan)
            ->orderBy('urutan', 'asc')
            ->first();

        if ($next) {
            $nextUrutan = $next->urutan;
            $next->update(['urutan' => $facility->urutan]);
            $facility->update(['urutan' => $nextUrutan]);
        }
    }

    public function delete(int $id): void
    {
        abort_unless(auth()->user()->can('web.galeri.hapus'), 403, 'Anda tidak memiliki hak akses untuk menghapus fasilitas.');

        $facility = WebFacility::findOrFail($id);
        $facility->delete();
        session()->flash('message', 'Fasilitas berhasil dihapus.');
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        $query = WebFacility::query()->orderBy('urutan');

        if ($this->filterCategory !== 'all') {
            $query->where('category', $this->filterCategory);
        }

        $facilities = $query->get();

        return view('administrasiweb::livewire.facility-index', [
            'facilities' => $facilities,
        ]);
    }
}
