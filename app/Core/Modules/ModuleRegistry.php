<?php

namespace App\Core\Modules;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

/**
 * Membaca manifest `app/Modules/<Nama>/module.php`.
 *
 * Format manifest:
 * [
 *   'name'        => 'Akademik',
 *   'label'       => 'Akademik',
 *   'icon'        => 'academic-cap',
 *   'order'       => 20,
 *   'menu'        => [ ['label'=>'Jurusan','route'=>'akademik.jurusan.index','permission'=>'akademik.jurusan.lihat'] ],
 *   'permissions' => [ 'akademik.jurusan' => ['label'=>'Jurusan','actions'=>['lihat','tambah','edit','hapus','cetak']] ],
 * ]
 */
class ModuleRegistry
{
    /** @var Collection<string, array>|null */
    protected ?Collection $modules = null;

    public function __construct(protected string $modulesPath) {}

    /** @return Collection<string, array> */
    public function all(): Collection
    {
        if ($this->modules !== null) {
            return $this->modules;
        }

        if (!app()->isLocal()) {
            $cached = Cache::get('siraga_modules_manifest');
            if (is_array($cached)) {
                return $this->modules = collect($cached);
            }
        }

        $modules = collect();

        if (File::isDirectory($this->modulesPath)) {
            foreach (File::directories($this->modulesPath) as $dir) {
                $manifest = $dir.DIRECTORY_SEPARATOR.'module.php';
                if (! File::exists($manifest)) {
                    continue;
                }
                $data = require $manifest;
                $data['path'] = $dir;
                $data['name'] ??= basename($dir);
                $data['order'] ??= 100;
                $data['menu'] ??= [];
                $data['permissions'] ??= [];
                $modules->put($data['name'], $data);
            }
        }

        $sorted = $modules->sortBy('order');

        if (!app()->isLocal()) {
            Cache::forever('siraga_modules_manifest', $sorted->all());
        }

        return $this->modules = $sorted;
    }

    /** Kelas komponen Livewire yang ditampilkan di topbar (dari manifest `topbar`). */
    public function topbarWidgets(): array
    {
        return $this->all()->pluck('topbar')->filter()->flatten()->values()->all();
    }

    /**
     * Blok data dinamis landing page. Manifest `landing` berisi closure yang
     * mengembalikan ['label' => ..., 'value' => ...]. Closure yang gagal diabaikan
     * agar landing page tidak pernah error karena satu modul.
     */
    public function landingBlocks(): array
    {
        $blocks = [];
        foreach ($this->all() as $module) {
            foreach ($module['landing'] ?? [] as $provider) {
                try {
                    if ($block = $provider()) {
                        $blocks[] = $block;
                    }
                } catch (\Throwable $e) {
                    report($e);
                }
            }
        }

        return $blocks;
    }

    /** Menu sidebar yang boleh dilihat user (difilter oleh permission). */
    public function menuFor($user): array
    {
        $groups = [];

        foreach ($this->all() as $module) {
            $items = collect($module['menu'])
                ->filter(fn ($item) => empty($item['permission']) || ($user && $user->can($item['permission'])))
                ->values()
                ->all();

            if ($items) {
                $groups[] = [
                    'label' => $module['label'] ?? $module['name'],
                    'icon' => $module['icon'] ?? null,
                    'items' => $items,
                ];
            }
        }

        return $groups;
    }

    /**
     * Daftar permission untuk matriks hak akses.
     * Hasil: [ 'akademik.jurusan' => ['module'=>'Akademik','label'=>'Jurusan','actions'=>[...]] ]
     */
    public function permissionMatrix(): array
    {
        $matrix = [];
        foreach ($this->all() as $module) {
            foreach ($module['permissions'] as $resource => $def) {
                $matrix[$resource] = [
                    'module' => $module['label'] ?? $module['name'],
                    'label' => $def['label'] ?? $resource,
                    'actions' => $def['actions'] ?? ['lihat', 'tambah', 'edit', 'hapus', 'cetak'],
                ];
            }
        }

        return $matrix;
    }

    /** Semua nama permission datar, mis. `akademik.jurusan.lihat`. */
    public function allPermissionNames(): array
    {
        $names = [];
        foreach ($this->permissionMatrix() as $resource => $def) {
            foreach ($def['actions'] as $action) {
                $names[] = "{$resource}.{$action}";
            }
        }

        return $names;
    }
}
