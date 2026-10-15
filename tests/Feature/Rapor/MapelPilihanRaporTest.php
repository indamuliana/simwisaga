<?php

namespace Tests\Feature\Rapor;

use App\Models\User;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\KelompokMapel;
use App\Modules\Akademik\Models\Mapel;
use App\Modules\Akademik\Models\MapelPilihan;
use App\Modules\Akademik\Models\MapelPilihanAnggota;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kepegawaian\Models\PenugasanMengajar;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Penilaian\Models\MapelPilihanTp;
use App\Modules\Penilaian\Models\NilaiMapelPilihanAkhir;
use App\Modules\Penilaian\Models\NilaiMapelPilihanFormatif;
use App\Modules\Penilaian\Models\NilaiMapelPilihanSumatif;
use App\Modules\Penilaian\Services\MapelPilihanScoreService;
use App\Modules\Rapor\Livewire\RaporWikramaIndex;
use App\Modules\Rapor\Services\RaporScoreService;
use App\Modules\Sistem\Models\PeriodeRapor;
use App\Modules\Sistem\Models\School;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MapelPilihanRaporTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected TahunAjaran $ta;
    protected Semester $semGanjil;
    protected PeriodeRapor $periode9M1;
    protected Rombel $rombel;
    protected Rayon $rayon;
    protected Siswa $siswaWithMapil;
    protected Siswa $siswaWithoutMapil;
    protected MapelPilihan $mapil;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);

        School::create(School::defaults());

        $this->admin = User::factory()->create([
            'username' => 'admin_rapor_mapil',
            'must_change_password' => false,
        ]);
        $this->admin->assignRole(User::SUPER_ADMIN_ROLE);

        $this->ta = TahunAjaran::create([
            'tahun' => '2026/2027',
            'nama' => '2026/2027',
            'is_active' => true,
        ]);

        $this->semGanjil = Semester::create([
            'tahun_ajaran_id' => $this->ta->id,
            'semester' => 'ganjil',
            'nama' => 'Ganjil',
            'is_active' => true,
        ]);

        $this->periode9M1 = PeriodeRapor::create([
            'tahun_ajaran_id' => $this->ta->id,
            'semester' => 'ganjil',
            'kode' => '9M1',
            'nama' => '9 Minggu ke-1 (STS 1)',
            'sebutan' => '9 Minggu ke-1',
            'tanggal_bagi' => '2026-10-15',
            'is_active' => true,
        ]);

        $jurusan = Jurusan::create([
            'kode' => 'PPLG',
            'nama' => 'Pengembangan Perangkat Lunak dan Gim',
        ]);

        $this->rombel = Rombel::create([
            'tahun_ajaran_id' => $this->ta->id,
            'jurusan_id' => $jurusan->id,
            'tingkat' => 'XI',
            'nama' => 'PPLG XI-2',
            'is_active' => true,
        ]);

        $pembimbing = Pegawai::create([
            'nama_lengkap' => 'Pembimbing Rayon Wikrama',
            'nik' => '9876543210123456',
            'jenis_kelamin' => 'L',
            'status_kepegawaian' => 'gty',
            'is_active' => true,
        ]);

        $this->rayon = Rayon::create([
            'nama' => 'Ciawi 1',
            'pembimbing_id' => $pembimbing->id,
            'is_active' => true,
        ]);

        $this->siswaWithMapil = Siswa::create([
            'rombel_id' => $this->rombel->id,
            'rayon_id' => $this->rayon->id,
            'nisn' => '0099887766',
            'nama' => 'Galih Pratama',
            'status' => 'aktif',
            'is_active' => true,
        ]);

        $this->siswaWithoutMapil = Siswa::create([
            'rombel_id' => $this->rombel->id,
            'rayon_id' => $this->rayon->id,
            'nisn' => '0099887767',
            'nama' => 'Hendra Kusuma',
            'status' => 'aktif',
            'is_active' => true,
        ]);

        // Mapel Reguler (Kelompok A)
        $kelA = KelompokMapel::firstOrCreate(['kode' => 'A'], [
            'nama' => 'Muatan Umum',
            'urutan' => 1,
        ]);

        $mapelReguler = Mapel::create([
            'kelompok_mapel_id' => $kelA->id,
            'kelompok' => 'A',
            'kode' => 'BINDO',
            'nama' => 'Bahasa Indonesia',
            'kkm_default' => 75,
            'urutan' => 1,
        ]);

        PenugasanMengajar::create([
            'semester_id' => $this->semGanjil->id,
            'rombel_id' => $this->rombel->id,
            'mapel_id' => $mapelReguler->id,
            'pegawai_id' => $pembimbing->id,
            'jp' => 3,
        ]);

        // Mapel Pilihan (Mapil)
        $this->mapil = MapelPilihan::create([
            'tahun_ajaran_id' => $this->ta->id,
            'kode' => 'MP-CLOUD',
            'nama' => 'Komputasi Awan (Cloud Computing)',
            'tingkats' => ['XI'],
            'kkm_default' => 78,
            'jp' => 4,
            'guru_id' => $pembimbing->id,
            'is_active' => true,
        ]);

        MapelPilihanAnggota::create([
            'mapel_pilihan_id' => $this->mapil->id,
            'siswa_id' => $this->siswaWithMapil->id,
            'tahun_ajaran_id' => $this->ta->id,
            'status' => 'aktif',
        ]);

        // TP Mapil
        MapelPilihanTp::create([
            'mapel_pilihan_id' => $this->mapil->id,
            'semester_id' => $this->semGanjil->id,
            'kode_tp' => 'TP 1',
            'deskripsi_tp' => 'mengonfigurasi virtual machine cloud',
            'kode_ah' => 'AH 1',
            'target_periode' => '9M1',
            'urutan' => 1,
        ]);

        MapelPilihanTp::create([
            'mapel_pilihan_id' => $this->mapil->id,
            'semester_id' => $this->semGanjil->id,
            'kode_tp' => 'TP 2',
            'deskripsi_tp' => 'menerapkan deployment serverless',
            'kode_ah' => 'AH 2',
            'target_periode' => '9M1',
            'urutan' => 2,
        ]);

        // Nilai Formatif & Sumatif Mapil
        NilaiMapelPilihanFormatif::create([
            'mapel_pilihan_id' => $this->mapil->id,
            'siswa_id' => $this->siswaWithMapil->id,
            'semester_id' => $this->semGanjil->id,
            'kode_ah' => 'AH 1',
            'nilai' => 88,
        ]);

        NilaiMapelPilihanFormatif::create([
            'mapel_pilihan_id' => $this->mapil->id,
            'siswa_id' => $this->siswaWithMapil->id,
            'semester_id' => $this->semGanjil->id,
            'kode_ah' => 'AH 2',
            'nilai' => 82,
        ]);

        NilaiMapelPilihanSumatif::create([
            'mapel_pilihan_id' => $this->mapil->id,
            'siswa_id' => $this->siswaWithMapil->id,
            'semester_id' => $this->semGanjil->id,
            'sts_1' => 85,
            'sas' => 88,
        ]);

        // Hitung & simpan rekap
        app(MapelPilihanScoreService::class)->calculateAndSaveRekap($this->mapil, $this->semGanjil);
    }

    public function test_student_with_mapil_has_kelompok_c_in_wikrama_academic_records(): void
    {
        $service = app(RaporScoreService::class);
        $records = $service->getWikramaAcademicRecords($this->siswaWithMapil, $this->semGanjil, '9M1');

        $mapilRecord = $records->firstWhere('is_mapil', true);
        $this->assertNotNull($mapilRecord);
        $this->assertEquals('Komputasi Awan (Cloud Computing)', $mapilRecord->mapel_nama);
        $this->assertEquals('C', $mapilRecord->kelompok_kode);
        $this->assertEquals('Mata Pelajaran Pilihan', $mapilRecord->kelompok_nama);
        $this->assertEquals('kompeten', $mapilRecord->status);
        $this->assertEquals(85.00, $mapilRecord->nilai_akhir);

        $grouped = $service->groupAcademicRecords($records);
        $this->assertTrue($grouped->has('C. Mata Pelajaran Pilihan'));
    }

    public function test_student_with_mapil_has_kelompok_c_in_pemerintah_academic_records(): void
    {
        $service = app(RaporScoreService::class);
        $records = $service->getPemerintahAcademicRecords($this->siswaWithMapil, $this->semGanjil);

        $mapilRecord = $records->firstWhere('is_mapil', true);
        $this->assertNotNull($mapilRecord);
        $this->assertEquals('C', $mapilRecord->kelompok_kode);
        $this->assertEquals('Mata Pelajaran Pilihan', $mapilRecord->kelompok_nama);

        $grouped = $service->groupAcademicRecords($records);
        $this->assertTrue($grouped->has('C. Mata Pelajaran Pilihan'));
    }

    public function test_student_without_mapil_does_not_have_kelompok_c(): void
    {
        $service = app(RaporScoreService::class);
        $records = $service->getWikramaAcademicRecords($this->siswaWithoutMapil, $this->semGanjil, '9M1');

        $mapilRecord = $records->firstWhere('is_mapil', true);
        $this->assertNull($mapilRecord);

        $grouped = $service->groupAcademicRecords($records);
        $this->assertFalse($grouped->has('C. Mata Pelajaran Pilihan'));
    }

    public function test_wikrama_rapor_preview_modal_shows_mapil(): void
    {
        Livewire::actingAs($this->admin)
            ->test(RaporWikramaIndex::class)
            ->set('filterScope', 'rombel')
            ->set('selectedRombelId', $this->rombel->id)
            ->set('selectedPeriodeId', $this->periode9M1->id)
            ->call('openPreview', $this->siswaWithMapil->id)
            ->assertSee('Komputasi Awan (Cloud Computing)')
            ->assertSee('C. Mata Pelajaran Pilihan');
    }
}
