<?php

namespace App\Modules\AdministrasiWeb\Livewire;

use App\Modules\AdministrasiWeb\Models\WebPage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class PageIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function toggleActive(int $id): void
    {
        abort_unless(auth()->user()->can('web.halaman.edit'), 403, 'Anda tidak memiliki hak akses untuk mengubah status halaman.');

        $page = WebPage::findOrFail($id);
        $page->update(['is_active' => ! $page->is_active]);
    }

    public function delete(int $id): void
    {
        abort_unless(auth()->user()->can('web.halaman.hapus'), 403, 'Anda tidak memiliki hak akses untuk menghapus halaman.');

        $page = WebPage::findOrFail($id);
        $page->delete();
        session()->flash('message', 'Halaman kustom berhasil dihapus.');
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        $query = WebPage::query()->latest();

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('judul', 'like', '%' . $this->search . '%')
                    ->orWhere('slug', 'like', '%' . $this->search . '%');
            });
        }

        $pages = $query->paginate(10);

        return view('administrasiweb::livewire.page-index', [
            'pages' => $pages,
        ]);
    }
}
