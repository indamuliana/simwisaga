<?php

namespace App\Core\Livewire\Traits;

trait WithSorting
{
    public string $sortField = 'id';
    public string $sortDirection = 'asc';

    public function sortBy(string $field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
            $this->sortField = $field;
        }

        if (method_exists($this, 'resetPage')) {
            $this->resetPage();
        }
    }
}
