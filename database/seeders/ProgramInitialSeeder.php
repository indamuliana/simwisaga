<?php

namespace Database\Seeders;

use App\Modules\Kesiswaan\Models\Program;
use Illuminate\Database\Seeder;

class ProgramInitialSeeder extends Seeder
{
    public function run(): void
    {
        $programs = [
            [
                'kode' => 'REG',
                'nama' => 'Reguler',
                'deskripsi' => 'Program Reguler SMK Wikrama 1 Garut',
                'is_active' => true,
            ],
            [
                'kode' => 'UNG',
                'nama' => 'Unggulan',
                'deskripsi' => 'Program Kelas Unggulan SMK Wikrama 1 Garut',
                'is_active' => true,
            ],
        ];

        foreach ($programs as $p) {
            Program::updateOrCreate(['kode' => $p['kode']], $p);
        }
    }
}
