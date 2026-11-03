<?php

namespace Tests\Feature\PortalSiswa;

use App\Core\Modules\ModuleRegistry;
use App\Core\Support\PermissionSync;
use App\Models\User;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Kesiswaan\Livewire\VerifikasiPerbaikanData;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\BeasiswaOta\Livewire\VerifikasiPengajuanBeasiswa;
use App\Modules\PortalSiswa\Livewire\BeasiswaSiswa;
use App\Modules\PortalSiswa\Livewire\BerandaSiswa;
use App\Modules\PortalSiswa\Livewire\KehadiranSiswa;
use App\Modules\PortalSiswa\Livewire\NilaiRaporSiswa;
use App\Modules\PortalSiswa\Livewire\ProfilSiswa;
use App\Modules\PortalSiswa\Livewire\UpdSeniSiswa;
use App\Modules\PortalSiswa\Models\PengajuanBeasiswa;
use App\Modules\PortalSiswa\Models\PengajuanPerbaikanDataSiswa;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PortalSiswaTest extends TestCase
{
    use RefreshDatabase;

    protected User $studentUser;
    protected Siswa $siswa;
    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesSeeder::class);
        PermissionSync::run();

        // Siapkan Tahun Ajaran & Semester
        $ta = TahunAjaran::create([
            'nama' => '2026/2027',
            'is_active' => true,
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai' => '2027-06-30',
        ]);
        Semester::create([
            'tahun_ajaran_id' => $ta->id,
            'semester' => 'ganjil',
            'is_active' => true,
        ]);

        // Berikan izin portal_siswa ke role Siswa
        $siswaRole = Role::findByName('Siswa', 'web');
        $portalPerms = [
            'portal_siswa.agenda.lihat',
            'portal_siswa.profil.lihat',
            'portal_siswa.profil.edit',
            'portal_siswa.nilai.lihat',
            'portal_siswa.nilai.cetak',
            'portal_siswa.kehadiran.lihat',
            'portal_siswa.upd_seni.lihat',
            'portal_siswa.prestasi.lihat',
            'portal_siswa.prestasi.tambah',
            'portal_siswa.beasiswa.lihat',
            'portal_siswa.beasiswa.tambah',
        ];
        $siswaRole->syncPermissions($portalPerms);

        // Buat akun siswa
        $this->studentUser = User::factory()->create([
            'username' => 'siswa123',
            'name' => 'Budi Santoso',
            'email' => 'budi@test.com',
        ]);
        $this->studentUser->assignRole('Siswa');

        $this->siswa = Siswa::create([
            'user_id' => $this->studentUser->id,
            'nama' => 'Budi Santoso',
            'nisn' => '0012345678',
            'nipd' => '12345',
            'nik' => '3205010101010001',
            'jk' => 'L',
            'tempat_lahir' => 'Garut',
            'tanggal_lahir' => '2008-05-15',
            'agama' => 'Islam',
            'alamat' => 'Jl. Merdeka No. 10 Garut',
            'hp' => '081234567890',
            'is_active' => true,
        ]);

        // Buat akun Super Admin
        $this->adminUser = User::factory()->create(['name' => 'Admin Utama']);
        $this->adminUser->assignRole(User::SUPER_ADMIN_ROLE);
    }

    public function test_menu_portal_siswa_muncul_untuk_role_siswa(): void
    {
        $registry = app(ModuleRegistry::class);
        $menu = $registry->menuFor($this->studentUser);

        $portalGroup = collect($menu)->firstWhere('label', 'Portal Siswa');
        $this->assertNotNull($portalGroup);

        $routes = collect($portalGroup['items'])->pluck('route')->toArray();
        $this->assertContains('portal-siswa.beranda', $routes);
        $this->assertContains('portal-siswa.profil', $routes);
        $this->assertContains('portal-siswa.nilai-rapor', $routes);
        $this->assertContains('portal-siswa.kehadiran', $routes);
        $this->assertContains('portal-siswa.upd-seni', $routes);
        $this->assertContains('portal-siswa.prestasi', $routes);
        $this->assertContains('portal-siswa.beasiswa', $routes);
    }

    public function test_siswa_bisa_mengakses_semua_halaman_portal(): void
    {
        $this->actingAs($this->studentUser);

        $this->get(route('portal-siswa.beranda'))->assertOk()->assertSee('Budi Santoso');
        $this->get(route('portal-siswa.profil'))->assertOk()->assertSee('Identitas Diri');
        $this->get(route('portal-siswa.nilai-rapor'))->assertOk()->assertSee('Nilai Akademik');
        $this->get(route('portal-siswa.kehadiran'))->assertOk()->assertSee('Rekap Kehadiran Siswa');
        $this->get(route('portal-siswa.upd-seni'))->assertOk()->assertSee('UPD & Seni Budaya', false);
        $this->get(route('portal-siswa.prestasi'))->assertOk();
        $this->get(route('portal-siswa.beasiswa'))->assertOk()->assertSee('Beasiswa & Bantuan Pendidikan', false);
    }

    public function test_siswa_dapat_mengajukan_perbaikan_data(): void
    {
        $this->actingAs($this->studentUser);

        Livewire::test(ProfilSiswa::class)
            ->set('kategori', 'biodata')
            ->set('namaField', 'nama')
            ->set('nilaiBaru', 'Budi Santoso Sudirman')
            ->set('alasan', 'Sesuai akta kelahiran terbaru')
            ->call('submitPengajuan')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('pengajuan_perbaikan_data_siswas', [
            'siswa_id' => $this->siswa->id,
            'kategori' => 'biodata',
            'alasan' => 'Sesuai akta kelahiran terbaru',
            'status' => 'menunggu',
        ]);
    }

    public function test_admin_dapat_menyetujui_dan_auto_update_data_siswa(): void
    {
        // Buat pengajuan perbaikan nama
        $pengajuan = PengajuanPerbaikanDataSiswa::create([
            'siswa_id' => $this->siswa->id,
            'kategori' => 'biodata',
            'rincian_perubahan' => [
                'nama' => [
                    'field' => 'nama',
                    'lama' => 'Budi Santoso',
                    'baru' => 'Budi Santoso Putra',
                ],
            ],
            'alasan' => 'Revisi Akta',
            'status' => PengajuanPerbaikanDataSiswa::STATUS_MENUNGGU,
        ]);

        $this->actingAs($this->adminUser);

        Livewire::test(VerifikasiPerbaikanData::class)
            ->call('approve', $pengajuan->id)
            ->assertHasNoErrors();

        // Pastikan status pengajuan disetujui
        $pengajuan->refresh();
        $this->assertEquals(PengajuanPerbaikanDataSiswa::STATUS_DISETUJUI, $pengajuan->status);
        $this->assertEquals($this->adminUser->id, $pengajuan->diproses_oleh);

        // Pastikan tabel siswas ter-update secara otomatis!
        $this->siswa->refresh();
        $this->assertEquals('Budi Santoso Putra', $this->siswa->nama);
    }

    public function test_admin_dapat_menolak_pengajuan_perbaikan_data(): void
    {
        $pengajuan = PengajuanPerbaikanDataSiswa::create([
            'siswa_id' => $this->siswa->id,
            'kategori' => 'kontak',
            'rincian_perubahan' => [
                'hp' => [
                    'field' => 'hp',
                    'lama' => '081234567890',
                    'baru' => '089999999999',
                ],
            ],
            'alasan' => 'Ganti nomor',
            'status' => PengajuanPerbaikanDataSiswa::STATUS_MENUNGGU,
        ]);

        $this->actingAs($this->adminUser);

        Livewire::test(VerifikasiPerbaikanData::class)
            ->set('selectedPengajuanId', $pengajuan->id)
            ->set('catatanTolak', 'Format nomor tidak valid')
            ->call('reject')
            ->assertHasNoErrors();

        $pengajuan->refresh();
        $this->assertEquals(PengajuanPerbaikanDataSiswa::STATUS_DITOLAK, $pengajuan->status);
        $this->assertEquals('Format nomor tidak valid', $pengajuan->catatan_admin);

        // Pastikan nomor lama tidak berubah
        $this->siswa->refresh();
        $this->assertEquals('081234567890', $this->siswa->hp);
    }

    public function test_siswa_dapat_mengajukan_beasiswa(): void
    {
        $this->actingAs($this->studentUser);

        Livewire::test(BeasiswaSiswa::class)
            ->set('jenisBeasiswa', 'ota')
            ->set('penghasilanOrangTua', 1200000)
            ->set('jumlahTanggungan', 4)
            ->set('nominalPengajuan', 500000)
            ->set('alasan', 'Orang tua mengalami penurunan penghasilan')
            ->call('submitPengajuan')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('pengajuan_beasiswas', [
            'siswa_id' => $this->siswa->id,
            'jenis_beasiswa' => 'ota',
            'status' => 'menunggu',
            'jumlah_tanggungan' => 4,
        ]);
    }

    public function test_admin_dapat_menyetujui_pengajuan_beasiswa(): void
    {
        $pengajuan = PengajuanBeasiswa::create([
            'siswa_id' => $this->siswa->id,
            'jenis_beasiswa' => 'ota',
            'alasan' => 'Kebutuhan biaya seragam',
            'status' => PengajuanBeasiswa::STATUS_MENUNGGU,
        ]);

        $this->actingAs($this->adminUser);

        Livewire::test(VerifikasiPengajuanBeasiswa::class)
            ->call('approve', $pengajuan->id)
            ->assertHasNoErrors();

        $pengajuan->refresh();
        $this->assertEquals(PengajuanBeasiswa::STATUS_DISETUJUI, $pengajuan->status);
        $this->assertEquals($this->adminUser->id, $pengajuan->diproses_oleh);
    }
}
