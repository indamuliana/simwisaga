<?php

namespace App\Core\Concerns;

use Illuminate\Support\Facades\Gate;

/**
 * Pengaman di dalam komponen Livewire. Route sudah dijaga middleware `can:`,
 * tetapi aksi Livewire (simpan, hapus, dll.) dikirim lewat endpoint terpisah,
 * sehingga HARUS dicek ulang di setiap method yang mengubah data.
 */
trait GuardsPermission
{
    protected function guard(string $permission): void
    {
        abort_unless(Gate::allows($permission), 403, 'Anda tidak memiliki hak akses untuk aksi ini.');
    }
}
