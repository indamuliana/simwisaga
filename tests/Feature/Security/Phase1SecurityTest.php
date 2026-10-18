<?php

namespace Tests\Feature\Security;

use App\Models\User;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kesiswaan\Models\Siswa;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\ProgramInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class Phase1SecurityTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $guruPolos;
    protected Siswa $siswa1;
    protected Siswa $siswa2;
    protected Rombel $rombel;
    protected TahunAjaran $ta;
    protected Semester $sem;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AkademikInitialSeeder::class);
        $this->seed(ProgramInitialSeeder::class);

        // Buat permissions yang diperlukan
        Permission::findOrCreate('beasiswa_ota.lihat', 'web');
        Permission::findOrCreate('beasiswa_ota.kelola', 'web');
        Permission::findOrCreate('beasiswa_ota.setting', 'web');
        Permission::findOrCreate('seni_budaya.kelola', 'web');
        Permission::findOrCreate('ekskul.kelola', 'web');
        Permission::findOrCreate('ekskul.setting', 'web');
        Permission::findOrCreate('rapor.cetak', 'web');
        Permission::findOrCreate('absensi.rekap.cetak', 'web');

        $this->admin = User::factory()->create([
            'username' => 'superadmin_sec',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');

        $this->guruPolos = User::factory()->create([
            'username' => 'guru_polos',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->guruPolos->assignRole('Guru');

        $this->ta = TahunAjaran::where('is_active', true)->firstOrFail();
        $this->sem = Semester::where('is_active', true)->firstOrFail();
        $this->rombel = Rombel::firstOrFail();

        $this->siswa1 = Siswa::create([
            'nama' => 'Siswa Satu',
            'nisn' => '1111222233',
            'nipd' => '111122',
            'jk' => 'L',
            'rombel_id' => $this->rombel->id,
            'is_active' => true,
            'status' => 'aktif',
        ]);

        $this->siswa2 = Siswa::create([
            'nama' => 'Siswa Dua',
            'nisn' => '4444555566',
            'nipd' => '444455',
            'jk' => 'P',
            'rombel_id' => $this->rombel->id,
            'is_active' => true,
            'status' => 'aktif',
        ]);
    }

    public function test_modul_beasiswa_ota_menolak_user_tanpa_hak_akses(): void
    {
        $this->actingAs($this->guruPolos);

        $this->get(route('beasiswa-ota.index'))->assertForbidden();
        $this->get(route('beasiswa-ota.setting'))->assertForbidden();
        $this->get(route('beasiswa-ota.donatur-kelompok'))->assertForbidden();
    }

    public function test_modul_seni_budaya_dan_ekskul_menolak_user_tanpa_hak_akses(): void
    {
        $this->actingAs($this->guruPolos);

        $this->get(route('seni-budaya.index'))->assertForbidden();
        $this->get(route('ekskul.index'))->assertForbidden();
        $this->get(route('ekskul.setting'))->assertForbidden();
    }

    public function test_cetak_rapor_individual_menolak_user_yang_bukan_walikelas_atau_pemilik(): void
    {
        $this->actingAs($this->guruPolos);

        // Guru polos bukan walikelas rombel ini
        $this->get(route('rapor.cetak.wikrama', ['siswa_id' => $this->siswa1->id]))->assertForbidden();
        $this->get(route('penilaian.transkrip.pdf', ['siswa_id' => $this->siswa1->id]))->assertForbidden();
    }

    public function test_walikelas_dapat_melihat_rapor_siswanya_sendiri(): void
    {
        $pegawai = Pegawai::create([
            'user_id' => $this->guruPolos->id,
            'nama_lengkap' => 'Guru Walikelas',
            'is_active' => true,
        ]);
        $this->rombel->update(['wali_kelas_id' => $pegawai->id]);

        $this->actingAs($this->guruPolos);

        $this->assertTrue($this->guruPolos->canViewStudent($this->siswa1));
        $this->get(route('rapor.cetak.wikrama', ['siswa_id' => $this->siswa1->id]))->assertOk();
    }

    public function test_siswa_dapat_melihat_rapor_sendiri_tetapi_ditolak_melihat_rapor_siswa_lain(): void
    {
        $userSiswa1 = User::factory()->create([
            'username' => '111122', // NIPD siswa 1
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $userSiswa1->assignRole('Siswa');

        $this->actingAs($userSiswa1);

        // Boleh lihat rapor dirinya sendiri
        $this->assertTrue($userSiswa1->canViewStudent($this->siswa1));
        $this->get(route('rapor.cetak.wikrama', ['siswa_id' => $this->siswa1->id]))->assertOk();

        // Ditolak melihat rapor siswa 2
        $this->assertFalse($userSiswa1->canViewStudent($this->siswa2));
        $this->get(route('rapor.cetak.wikrama', ['siswa_id' => $this->siswa2->id]))->assertForbidden();
    }
}
