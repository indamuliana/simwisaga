<?php

namespace Tests\Feature\Core;

use App\Core\Modules\ModuleRegistry;
use App\Models\User;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ModuleRegistryTest extends TestCase
{
    use RefreshDatabase;

    private string $dir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dir = storage_path('framework/testing/modules_'.uniqid());
        File::ensureDirectoryExists($this->dir.'/Demo');
        File::put($this->dir.'/Demo/module.php', <<<'PHP'
<?php
return [
    'name' => 'Demo', 'label' => 'Demo', 'order' => 5,
    'menu' => [
        ['label' => 'Terbuka', 'route' => 'dashboard'],
        ['label' => 'Tertutup', 'route' => 'dashboard', 'permission' => 'demo.item.lihat'],
    ],
    'permissions' => ['demo.item' => ['label' => 'Item', 'actions' => ['lihat', 'edit']]],
];
PHP);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->dir);
        parent::tearDown();
    }

    public function test_manifest_terbaca_dan_permission_terbentuk(): void
    {
        $registry = new ModuleRegistry($this->dir);

        $this->assertSame(['demo.item.lihat', 'demo.item.edit'], $registry->allPermissionNames());
        $this->assertSame('Demo', $registry->permissionMatrix()['demo.item']['module']);
    }

    public function test_menu_difilter_berdasarkan_permission(): void
    {
        $this->seed(RolesSeeder::class);
        $registry = new ModuleRegistry($this->dir);
        $user = User::factory()->create();

        $labels = collect($registry->menuFor($user)[0]['items'])->pluck('label')->all();
        $this->assertSame(['Terbuka'], $labels);

        \Spatie\Permission\Models\Permission::findOrCreate('demo.item.lihat');
        $user->givePermissionTo('demo.item.lihat');
        $labels = collect($registry->menuFor($user->fresh())[0]['items'])->pluck('label')->all();
        $this->assertSame(['Terbuka', 'Tertutup'], $labels);
    }

    public function test_folder_modul_kosong_tidak_error(): void
    {
        $registry = new ModuleRegistry($this->dir.'/tidak-ada');
        $this->assertTrue($registry->all()->isEmpty());
        $this->assertSame([], $registry->menuFor(null));
    }
}
