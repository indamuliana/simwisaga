<?php

namespace Tests\Feature\Dummy;

use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Kepegawaian\Models\PenugasanMengajar;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Penilaian\Models\NilaiAkhir;
use App\Modules\Absensi\Models\AbsensiMapel;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\KepegawaianInitialSeeder;
use Database\Seeders\PopulateXHTL1DummyDataSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PopulateXHTL1DummyDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_berhasil_mengisi_nilai_dan_absen_x_htl_1(): void
    {
        $this->seed(RolesSeeder::class);
        $this->seed(AkademikInitialSeeder::class);
        $this->seed(KepegawaianInitialSeeder::class);

        $ta = TahunAjaran::create([
            'nama' => '2025/2026',
            'tgl_mulai' => '2025-06-01',
            'tgl_selesai' => '2026-06-01',
            'is_active' => true,
        ]);

        $semGanjil = Semester::create([
            'tahun_ajaran_id' => $ta->id,
            'semester' => 'ganjil',
            'is_active' => true,
        ]);

        $semGenap = Semester::create([
            'tahun_ajaran_id' => $ta->id,
            'semester' => 'genap',
            'is_active' => false,
        ]);

        $rombel = Rombel::create([
            'tahun_ajaran_id' => $ta->id,
            'jurusan_id' => 4,
            'tingkat' => 'X',
            'nomor_urut' => 1,
            'nama' => 'X HTL 1',
        ]);

        // Buat 5 siswa dummy di rombel
        for ($i = 1; $i <= 5; $i++) {
            Siswa::create([
                'nama' => "Siswa HTL {$i}",
                'rombel_id' => $rombel->id,
                'nisn' => "007000000{$i}",
                'jk' => $i % 2 === 0 ? 'P' : 'L',
                'is_active' => true,
            ]);
        }

        $this->seed(PopulateXHTL1DummyDataSeeder::class);

        // Verifikasi Penugasan Mengajar dibuat untuk Ganjil & Genap
        $pmGanjil = PenugasanMengajar::where('rombel_id', $rombel->id)->where('semester_id', $semGanjil->id)->count();
        $pmGenap = PenugasanMengajar::where('rombel_id', $rombel->id)->where('semester_id', $semGenap->id)->count();

        $this->assertGreaterThanOrEqual(10, $pmGanjil);
        $this->assertGreaterThanOrEqual(10, $pmGenap);

        // Verifikasi Nilai Akhir tersimpan
        $naCount = NilaiAkhir::whereHas('siswa', fn($q) => $q->where('rombel_id', $rombel->id))->count();
        $this->assertGreaterThan(0, $naCount);

        // Verifikasi Absensi Mapel tersimpan
        $absenCount = AbsensiMapel::whereHas('siswa', fn($q) => $q->where('rombel_id', $rombel->id))->count();
        $this->assertGreaterThan(0, $absenCount);
    }
}
