<?php

namespace Database\Seeders;

use App\Modules\Kepesantrenan\Database\Seeders\QuranSurahSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        require_once __DIR__.'/SeniBudayaPermissionSeeder.php';
        require_once __DIR__.'/PrestasiPermissionSeeder.php';
        require_once __DIR__.'/AnalitikSpmbPermissionSeeder.php';

        $this->call([
            RolesSeeder::class,
            SuperAdminSeeder::class,
            SeniBudayaPermissionSeeder::class,
            PrestasiPermissionSeeder::class,
            AnalitikSpmbPermissionSeeder::class,
            QuranSurahSeeder::class,
        ]);
    }
}
