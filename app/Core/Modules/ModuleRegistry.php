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

        if (!app()->isLocal() && !app()->runningUnitTests()) {
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

        if (!app()->isLocal() && !app()->runningUnitTests()) {
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

    /** Pemetaan kelompok utama untuk struktur menu 3-tingkat (Kelompok -> Modul -> Submodul). */
    public const DEFAULT_GROUPS = [
        'ANALISIS' => [
            'label' => 'ANALISIS',
            'icon'  => 'chart-bar',
            'order' => 10,
        ],
        'KURIKULUM' => [
            'label' => 'KURIKULUM',
            'icon'  => 'academic-cap',
            'order' => 20,
        ],
        'KESISWAAN' => [
            'label' => 'KESISWAAN',
            'icon'  => 'user-group',
            'order' => 30,
        ],
        'KEPESANTRENAN' => [
            'label' => 'KEPESANTRENAN',
            'icon'  => 'moon',
            'order' => 40,
        ],
        'KEPEGAWAIAN & SDM' => [
            'label' => 'KEPEGAWAIAN & SDM',
            'icon'  => 'badge-check',
            'order' => 50,
        ],
        'SARANA & PRASARANA' => [
            'label' => 'SARANA & PRASARANA',
            'icon'  => 'office-building',
            'order' => 60,
        ],
        'PENGATURAN' => [
            'label' => 'PENGATURAN',
            'icon'  => 'cog',
            'order' => 70,
        ],
        'Portal Siswa' => [
            'label' => 'Portal Siswa',
            'icon'  => 'user',
            'order' => 80,
        ],
    ];

    /** Default mapping modul ke kelompok */
    public const MODULE_GROUP_MAP = [
        'AnalitikSpmb'       => 'ANALISIS',
        'DashboardData'      => 'ANALISIS',
        'AiAssistant'        => 'ANALISIS',

        'KalenderPendidikan' => 'KURIKULUM',
        'Akademik'           => 'KURIKULUM',
        'Penilaian'          => 'KURIKULUM',
        'SeniBudaya'         => 'KURIKULUM',
        'Ekstrakurikuler'    => 'KURIKULUM',
        'Rapor'              => 'KURIKULUM',

        'Kesiswaan'          => 'KESISWAAN',
        'Alumni'             => 'KESISWAAN',
        'Absensi'            => 'KESISWAAN',
        'Prestasi'           => 'KESISWAAN',

        'Kepesantrenan'      => 'KEPESANTRENAN',
        'BeasiswaOta'        => 'KEPESANTRENAN',

        'Kepegawaian'        => 'KEPEGAWAIAN & SDM',

        'Sarpras'            => 'SARANA & PRASARANA',

        'AdministrasiWeb'    => 'PENGATURAN',
        'Sistem'             => 'PENGATURAN',
        'Panduan'            => 'PENGATURAN',

        'PortalSiswa'        => 'Portal Siswa',
    ];

    /** Menu sidebar 3-tingkat (Kelompok -> Modul -> Submodul) yang difilter oleh permission. */
    public function menuFor($user): array
    {
        $groupedModules = [];

        foreach ($this->all() as $module) {
            $items = collect($module['menu'])
                ->filter(fn ($item) => empty($item['permission']) || ($user && $user->can($item['permission'])))
                ->values()
                ->all();

            if (!empty($items)) {
                $moduleName = $module['name'];
                $groupName = $module['group'] ?? self::MODULE_GROUP_MAP[$moduleName] ?? ($module['label'] ?? $moduleName);

                $groupedModules[$groupName][] = [
                    'name'  => $moduleName,
                    'label' => $module['label'] ?? $moduleName,
                    'icon'  => $module['icon'] ?? null,
                    'order' => $module['order'] ?? 100,
                    'items' => $items,
                ];
            }
        }

        $result = [];
        foreach ($groupedModules as $groupName => $modules) {
            // Urutkan modul dalam kelompok berdasarkan 'order'
            usort($modules, fn ($a, $b) => ($a['order'] ?? 100) <=> ($b['order'] ?? 100));

            $def = self::DEFAULT_GROUPS[$groupName] ?? [
                'label' => $groupName,
                'icon'  => null,
                'order' => 999,
            ];

            // Flatten all submodules for backwards-compatibility with tests expecting $group['items']
            $allItems = collect($modules)->flatMap(fn ($m) => $m['items'])->all();

            $result[] = [
                'label'   => $def['label'] ?? $groupName,
                'icon'    => $def['icon'] ?? null,
                'order'   => $def['order'] ?? 999,
                'modules' => $modules,
                'items'   => $allItems,
            ];
        }

        // Urutkan kelompok berdasarkan 'order' kelompok
        usort($result, fn ($a, $b) => ($a['order'] ?? 999) <=> ($b['order'] ?? 999));

        return $result;
    }

    /**
     * Daftar permission untuk matriks hak akses.
     * Hasil: [ 'akademik.jurusan' => ['module'=>'Akademik','label'=>'Jurusan','actions'=>[...]] ]
     */
    public function permissionMatrix(): array
    {
        $matrix = [];
        foreach ($this->all() as $module) {
            $moduleName = $module['name'];
            $groupName = $module['group'] ?? self::MODULE_GROUP_MAP[$moduleName] ?? ($module['label'] ?? $moduleName);
            $groupDef = self::DEFAULT_GROUPS[$groupName] ?? ['label' => $groupName, 'icon' => 'folder', 'order' => 999];

            foreach ($module['permissions'] as $resource => $def) {
                $matrix[$resource] = [
                    'group' => $groupName,
                    'group_icon' => $groupDef['icon'] ?? 'folder',
                    'group_order' => $groupDef['order'] ?? 999,
                    'module' => $module['label'] ?? $moduleName,
                    'module_name' => $moduleName,
                    'module_order' => $module['order'] ?? 100,
                    'label' => $def['label'] ?? $resource,
                    'actions' => $def['actions'] ?? ['lihat', 'tambah', 'edit', 'hapus', 'cetak'],
                ];
            }
        }

        // Urutkan berdasarkan group_order, lalu module_order
        uasort($matrix, function ($a, $b) {
            if ($a['group_order'] !== $b['group_order']) {
                return $a['group_order'] <=> $b['group_order'];
            }
            if ($a['module_order'] !== $b['module_order']) {
                return $a['module_order'] <=> $b['module_order'];
            }
            return 0;
        });

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
