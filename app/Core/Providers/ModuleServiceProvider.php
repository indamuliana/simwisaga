<?php

namespace App\Core\Providers;

use App\Core\Modules\ModuleRegistry;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ModuleRegistry::class, fn () => new ModuleRegistry(app_path('Modules')));
    }

    public function boot(): void
    {
        $registry = $this->app->make(ModuleRegistry::class);

        foreach ($registry->all() as $module) {
            $path = $module['path'];
            $slug = strtolower($module['name']);

            if (is_dir($path.'/Database/Migrations')) {
                $this->loadMigrationsFrom($path.'/Database/Migrations');
            }

            if (is_dir($path.'/Resources/views')) {
                $this->loadViewsFrom($path.'/Resources/views', $slug);
            }

            if (is_file($path.'/routes.php')) {
                Route::middleware(['web', 'auth', 'active', 'password.fresh'])
                    ->group($path.'/routes.php');
            }
        }

        // Registrasi Livewire resolver agar komponen di Core dan Modules
        // dapat di-resolve saat POST /livewire/update tanpa error 419.
        if (class_exists(\Livewire\Mechanisms\ComponentRegistry::class)) {
            $registryMechanisms = app(\Livewire\Mechanisms\ComponentRegistry::class);
            $registryMechanisms->resolveMissingComponent(function ($name) {
                if (str_starts_with($name, 'app.')) {
                    $class = collect(explode('.', $name))
                        ->map(fn ($segment) => \Illuminate\Support\Str::studly($segment))
                        ->join('\\');

                    if (class_exists($class) && is_subclass_of($class, \Livewire\Component::class)) {
                        return $class;
                    }
                }

                return null;
            });
        }

        // Super Admin melewati semua pengecekan hak akses.
        Gate::before(fn (?User $user) => $user?->isSuperAdmin() ? true : null);

        // Menu sidebar tersedia di semua view.
        View::composer('components.layouts.app', function ($view) use ($registry) {
            $view->with('sidebarMenu', $registry->menuFor(auth()->user()));
        });
    }
}
