<?php

namespace Tests\Feature\Kesiswaan;

use App\Models\User;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Kesiswaan\Models\Program;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Kesiswaan\Services\DapodikImporter;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\DocumentTemplateSeeder;
use Database\Seeders\ProgramInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SiswaDapodikTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected TahunAjaran $ta;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(DocumentTemplateSeeder::class);
        $this->seed(AkademikInitialSeeder::class);
        $this->seed(ProgramInitialSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'admin_kesiswaan',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');

        $this->ta = TahunAjaran::where('is_active', true)->first();
    }

    public function test_resolve_rombel_memetakan_string_dapodik_dan_alias_dengan_benar(): void
    {
        $importer = new DapodikImporter($this->ta);

        // Uji PPLG X -> X PPLG 1
        $r1 = $importer->resolveRombel('PPLG X');
        $this->assertNotNull($r1);
        $this->assertEquals('X PPLG 1', $r1->nama);

        // Uji TJKT XI -> XI TJKT 1
        $r2 = $importer->resolveRombel('TJKT XI');
        $this->assertNotNull($r2);
        $this->assertEquals('XI TJKT 1', $r2->nama);

        // Uji Alias Lama: TKJ XII -> XII TJKT 1
        $r3 = $importer->resolveRombel('TKJ XII');
        $this->assertNotNull($r3);
        $this->assertEquals('XII TJKT 1', $r3->nama);

        // Uji Alias Lama: RPL XII -> XII PPLG 1
        $r4 = $importer->resolveRombel('RPL XII');
        $this->assertNotNull($r4);
        $this->assertEquals('XII PPLG 1', $r4->nama);

        // Uji Alias Lama: BDP XII -> XII PMN 1
        $r5 = $importer->resolveRombel('BDP XII');
        $this->assertNotNull($r5);
        $this->assertEquals('XII PMN 1', $r5->nama);
    }

    public function test_import_sample_dapodik_berhasil_membuat_19_siswa_dan_akun_login_nis_ddmmyyyy(): void
    {
        $samplePath = file_exists(database_path('seeders/data/sample_dapodik.xlsx'))
            ? database_path('seeders/data/sample_dapodik.xlsx')
            : base_path('DATABASE_SISWA_SAMPLE.xlsx');
        $this->assertFileExists($samplePath);

        $importer = new DapodikImporter($this->ta);
        $res = $importer->import($samplePath);

        $this->assertEquals(19, $res['imported']);
        $this->assertCount(19, Siswa::all());

        // Verifikasi salah satu siswa sampel: ABAN GYMNASTIAR (NIS: 12200760, Tgl Lahir: 2006-07-08)
        $aban = Siswa::where('nisn', '0063473065')->first();
        $this->assertNotNull($aban);
        $this->assertEquals('ABAN GYMNASTIAR', $aban->nama);
        $this->assertEquals('L', $aban->jk);
        $this->assertEquals('12200760', $aban->nipd);
        $this->assertEquals('GARUT', $aban->tempat_lahir);
        $this->assertEquals('Kec. Karangpawitan', $aban->kecamatan);

        // Verifikasi Program default adalah Reguler
        $this->assertNotNull($aban->program);
        $this->assertEquals('Reguler', $aban->program->nama);

        // Verifikasi Rombel otomatis masuk ke XI TJKT 1
        $this->assertNotNull($aban->rombel);
        $this->assertEquals('XI TJKT 1', $aban->rombel->nama);

        // Verifikasi data orang tua tersimpan
        $this->assertNotNull($aban->ayah);
        $this->assertEquals('H. DINDIN SABARUDIN S', $aban->ayah->nama);
        $this->assertNotNull($aban->ibu);
        $this->assertEquals('IDA ALIDA ROHMAH', $aban->ibu->nama);

        // Verifikasi Akun Siswa menggunakan Username NIS (12200760) & password default DDMMYYYY (08072006)
        $userSiswa = User::where('username', '12200760')->first();
        $this->assertNotNull($userSiswa);
        $this->assertTrue($userSiswa->hasRole('Siswa'));
        $this->assertTrue(Hash::check('08072006', $userSiswa->password));

        // Verifikasi Akun Orang Tua menggunakan Username ortu_[NIS] (ortu_12200760) & password DDMMYYYY
        $userOrtu = User::where('username', 'ortu_12200760')->first();
        $this->assertNotNull($userOrtu);
        $this->assertTrue($userOrtu->hasRole('Orang Tua'));
        $this->assertTrue(Hash::check('08072006', $userOrtu->password));
    }

    public function test_halaman_data_siswa_tampil_dengan_header_resmi(): void
    {
        $this->actingAs($this->admin)
            ->get(route('kesiswaan.siswa'))
            ->assertOk()
            ->assertSee('Data Induk Siswa')
            ->assertSee('NIS')
            ->assertSee('Program');
    }

    public function test_halaman_edit_siswa_tampil_dan_dapat_diakses(): void
    {
        $siswa = Siswa::create([
            'nama' => 'Uji Siswa',
            'nipd' => '12209999',
            'nisn' => '0099999999',
            'jk' => 'L',
            'tanggal_lahir' => '2007-05-15',
        ]);

        $this->actingAs($this->admin)
            ->get(route('kesiswaan.siswa.edit', $siswa->id))
            ->assertOk()
            ->assertSee('Uji Siswa')
            ->assertSee('12209999')
            ->assertSee('Identitas & Foto', false);
    }

    public function test_halaman_plotting_program_tampil(): void
    {
        $this->actingAs($this->admin)
            ->get(route('kesiswaan.plotting-program'))
            ->assertOk()
            ->assertSee('Plotting Program Siswa');
    }

    public function test_halaman_import_dapodik_tampil(): void
    {
        $this->actingAs($this->admin)
            ->get(route('kesiswaan.import'))
            ->assertOk()
            ->assertSee('Import Data Siswa DAPODIK');
    }

    public function test_halaman_plotting_rayon_tampil(): void
    {
        $this->actingAs($this->admin)
            ->get(route('kesiswaan.plotting-rayon'))
            ->assertOk()
            ->assertSee('Plotting Rayon Massal');
    }
}
