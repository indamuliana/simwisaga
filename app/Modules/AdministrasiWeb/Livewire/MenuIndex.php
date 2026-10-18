<?php

namespace App\Modules\AdministrasiWeb\Livewire;

use App\Modules\AdministrasiWeb\Models\WebMenu;
use Livewire\Attributes\Layout;
use Livewire\Component;

class MenuIndex extends Component
{
    public string $activeTab = 'header'; // header or footer

    public bool $showModal = false;
    public ?int $editingId = null;

    public string $label = '';
    public string $url = '';
    public string $target = '_self';
    public int $urutan = 0;
    public bool $is_active = true;

    protected function rules(): array
    {
        return [
            'label' => 'required|string|max:100',
            'url' => 'required|string|max:255',
            'target' => 'required|in:_self,_blank',
            'urutan' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ];
    }

    public function openCreateModal(): void
    {
        $this->resetValidation();
        $this->editingId = null;
        $this->label = '';
        $this->url = '';
        $this->target = '_self';
        $maxUrutan = WebMenu::where('lokasi', $this->activeTab)->max('urutan') ?? 0;
        $this->urutan = $maxUrutan + 1;
        $this->is_active = true;
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $this->resetValidation();
        $menu = WebMenu::findOrFail($id);
        $this->editingId = $menu->id;
        $this->label = $menu->label;
        $this->url = $menu->url;
        $this->target = $menu->target;
        $this->urutan = $menu->urutan;
        $this->is_active = $menu->is_active;
        $this->showModal = true;
    }

    public function save(): void
    {
        abort_unless(auth()->user()->can('web.menu.edit'), 403, 'Anda tidak memiliki hak akses untuk mengelola menu navigasi.');

        $validated = $this->validate();
        $validated['lokasi'] = $this->activeTab;

        if ($this->editingId) {
            $menu = WebMenu::findOrFail($this->editingId);
            $menu->update($validated);
            session()->flash('message', 'Menu navigasi berhasil diperbarui.');
        } else {
            WebMenu::create($validated);
            session()->flash('message', 'Menu baru berhasil ditambahkan.');
        }

        $this->showModal = false;
    }

    public function toggleActive(int $id): void
    {
        abort_unless(auth()->user()->can('web.menu.edit'), 403, 'Anda tidak memiliki hak akses untuk mengelola menu navigasi.');

        $menu = WebMenu::findOrFail($id);
        $menu->update(['is_active' => ! $menu->is_active]);
    }

    public function moveUp(int $id): void
    {
        abort_unless(auth()->user()->can('web.menu.edit'), 403, 'Anda tidak memiliki hak akses untuk mengelola menu navigasi.');

        $menu = WebMenu::findOrFail($id);
        $previous = WebMenu::where('lokasi', $this->activeTab)
            ->where('urutan', '<', $menu->urutan)
            ->orderBy('urutan', 'desc')
            ->first();

        if ($previous) {
            $prevUrutan = $previous->urutan;
            $previous->update(['urutan' => $menu->urutan]);
            $menu->update(['urutan' => $prevUrutan]);
        }
    }

    public function moveDown(int $id): void
    {
        abort_unless(auth()->user()->can('web.menu.edit'), 403, 'Anda tidak memiliki hak akses untuk mengelola menu navigasi.');

        $menu = WebMenu::findOrFail($id);
        $next = WebMenu::where('lokasi', $this->activeTab)
            ->where('urutan', '>', $menu->urutan)
            ->orderBy('urutan', 'asc')
            ->first();

        if ($next) {
            $nextUrutan = $next->urutan;
            $next->update(['urutan' => $menu->urutan]);
            $menu->update(['urutan' => $nextUrutan]);
        }
    }

    public function delete(int $id): void
    {
        abort_unless(auth()->user()->can('web.menu.edit'), 403, 'Anda tidak memiliki hak akses untuk menghapus menu navigasi.');

        $menu = WebMenu::findOrFail($id);
        $menu->delete();
        session()->flash('message', 'Menu berhasil dihapus.');
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        $menus = WebMenu::where('lokasi', $this->activeTab)
            ->orderBy('urutan')
            ->get();

        return view('administrasiweb::livewire.menu-index', [
            'menus' => $menus,
        ]);
    }
}
