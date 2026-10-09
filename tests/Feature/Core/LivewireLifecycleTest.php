<?php

namespace Tests\Feature\Core;

use App\Core\Livewire\Auth\Login;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LivewireLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_livewire_update_berhasil_resolve_komponen_tanpa_error_419(): void
    {
        $user = User::factory()->create([
            'username' => 'admin',
            'password' => 'UXF7GxgPVGlzQU',
        ]);

        // Simulasikan Livewire submit component
        $component = Livewire::test(Login::class)
            ->set('username', 'admin')
            ->set('password', 'UXF7GxgPVGlzQU')
            ->call('login');

        $component->assertHasNoErrors();
        $this->assertAuthenticatedAs($user);
    }

    public function test_component_registry_bisa_resolve_komponen_di_app_core_dan_modules(): void
    {
        $registry = app(\Livewire\Mechanisms\ComponentRegistry::class);

        $class = $registry->getClass('app.core.livewire.auth.login');
        $this->assertSame(Login::class, $class);

        $identitasClass = $registry->getClass('app.modules.sistem.livewire.identitas-sekolah');
        $this->assertSame(\App\Modules\Sistem\Livewire\IdentitasSekolah::class, $identitasClass);
    }
}
