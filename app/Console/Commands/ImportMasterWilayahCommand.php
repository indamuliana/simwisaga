<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportMasterWilayahCommand extends Command
{
    protected $signature = 'wilayah:import';

    protected $description = 'Impor Master Data Wilayah Nasional resmi Kemendagri ke database';

    public function handle(): int
    {
        $this->info('Memulai impor Master Wilayah Kemendagri...');
        $this->call('db:seed', ['--class' => 'Database\\Seeders\\MasterWilayahSeeder']);
        $this->info('Selesai!');

        return self::SUCCESS;
    }
}
