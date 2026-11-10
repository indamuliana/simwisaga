<?php

namespace Tests\Feature\Kepegawaian;

use App\Models\User;
use App\Modules\Akademik\Models\Mapel;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Kepegawaian\Livewire\PegawaiCreate;
use App\Modules\Kepegawaian\Livewire\PegawaiDetail;
use App\Modules\Kepegawaian\Livewire\PegawaiEdit;
use App\Modules\Kepegawaian\Livewire\PegawaiIndex;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kepegawaian\Models\PegawaiFoto;
use App\Modules\Kepegawaian\Models\PenugasanMengajar;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\DocumentTemplateSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class PegawaiCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(DocumentTemplateSeeder::class);
        $this->seed(AkademikInitialSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'superadmin_kepegawaian',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');
    }

    public function test_halaman_pegawai_create_show_edit_dapat_diakses(): void
    {
        $pegawai = Pegawai::create([
            'nama_lengkap' => 'Budi Santoso',
            'jenis_gtk' => 'Guru',
            'status_kepegawaian' => 'GTY',
            'jenis_kelamin' => 'L',
        ]);

        $this->actingAs($this->admin)
            ->get(route('kepegawaian.pegawai.create'))
            ->assertOk()
            ->assertSee('Tambah Pegawai (GTK) Baru');

        $this->actingAs($this->admin)
            ->get(route('kepegawaian.pegawai.show', $pegawai->id))
            ->assertOk()
            ->assertSee('Budi Santoso');

        $this->actingAs($this->admin)
            ->get(route('kepegawaian.pegawai.edit', $pegawai->id))
            ->assertOk()
            ->assertSee('Ubah Data Pegawai');
    }

    public function test_pegawai_create_menyimpan_data_lengkap_dan_berkas_foto(): void
    {
        Storage::fake('public');

        $file1 = UploadedFile::fake()->image('profil.jpg');
        $file2 = UploadedFile::fake()->image('tambahan1.jpg');

        Livewire::actingAs($this->admin)
            ->test(PegawaiCreate::class)
            ->set('nama_lengkap', 'Dr. Irfan Maulana')
            ->set('gelar_depan', 'Dr.')
            ->set('gelar_belakang', 'M.T.')
            ->set('jenis_kelamin', 'L')
            ->set('jenis_gtk', 'Guru')
            ->set('status_kepegawaian', 'GTY')
            ->set('jabatan_struktural', 'Kepala Bengkel')
            ->set('nip', '198702152011011002')
            ->set('nuptk', '1234567890123456')
            ->set('nik', '3201012345670001')
            ->set('tempat_lahir', 'Garut')
            ->set('tanggal_lahir', '1987-02-15')
            ->set('golongan_darah', 'O')
            ->set('no_hp', '081234567890')
            ->set('email', 'irfan@wikrama.sch.id')
            ->set('tgl_awal_masuk_wikrama', '2015-07-01')
            ->set('tmt_awal_guru', '2012-01-01')
            ->set('no_sk_mulai_guru', 'SK/GURU/001/2012')
            ->set('nomor_peserta_ukg', '201501234567')
            ->set('nomor_registrasi_guru', 'NRG-987654')
            ->set('npwp', '09.123.456.7-441.000')
            ->set('nama_wajib_pajak', 'Irfan Maulana')
            ->set('no_kartu_bpjs_kesehatan', '0001234567890')
            ->set('no_kartu_bpjs_ketenagakerjaan', '09876543210')
            ->set('pendidikan_terakhir', 'S2')
            ->set('jurusan_dan_instansi', 'Teknik Informatika - ITB')
            ->set('tahun_lulus_pendidikan_terakhir', '2014')
            ->set('status_pernikahan', 'Menikah')
            ->set('nama_pasangan', 'Siti Rahma')
            ->set('data_anak.0.nama', 'Farhan Maulana')
            ->set('data_anak.0.tgl_lahir', '2017-05-10')
            ->set('pengalaman_kerja.0.lembaga', 'SMK IT Garuda')
            ->set('pengalaman_kerja.0.posisi', 'Guru Produktif')
            ->set('pengalaman_kerja.0.periode', '2012 - 2015')
            ->set('foto_1', $file1)
            ->set('keterangan_1', 'Foto Jas Resmi')
            ->set('foto_2', $file2)
            ->set('keterangan_2', 'Foto Penugasan')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect();

        $pegawai = Pegawai::where('nip', '198702152011011002')->first();
        $this->assertNotNull($pegawai);
        $this->assertEquals('Dr. Irfan Maulana', $pegawai->nama_lengkap);
        $this->assertEquals('Kepala Bengkel', $pegawai->jabatan_struktural);
        $this->assertEquals('Teknik Informatika - ITB', $pegawai->jurusan_dan_instansi);
        $this->assertEquals(1, $pegawai->jumlah_anak);
        $this->assertNotNull($pegawai->foto_path);

        // Verifikasi 2 foto tersimpan di tabel pegawai_fotos
        $fotos = PegawaiFoto::where('pegawai_id', $pegawai->id)->orderBy('slot')->get();
        $this->assertCount(2, $fotos);
        $this->assertEquals(1, $fotos[0]->slot);
        $this->assertEquals('Foto Jas Resmi', $fotos[0]->keterangan);
        $this->assertEquals(2, $fotos[1]->slot);
        $this->assertEquals('Foto Penugasan', $fotos[1]->keterangan);
    }

    public function test_pegawai_detail_menampilkan_umur_lama_bekerja_dan_penugasan_mengajar(): void
    {
        $pegawai = Pegawai::create([
            'nama_lengkap' => 'Dewi Sartika',
            'gelar_belakang' => 'M.Kom.',
            'jenis_gtk' => 'Guru',
            'status_kepegawaian' => 'GTY',
            'jenis_kelamin' => 'P',
            'tanggal_lahir' => '1990-04-20',
            'tgl_awal_masuk_wikrama' => '2018-07-01',
        ]);

        $sem = Semester::where('is_active', true)->first();
        $rombel = Rombel::first();
        $mapel = Mapel::first();

        PenugasanMengajar::create([
            'semester_id' => $sem->id,
            'pegawai_id' => $pegawai->id,
            'rombel_id' => $rombel->id,
            'mapel_id' => $mapel->id,
            'jp' => 4,
            'keterangan' => 'Pengampu Utama',
        ]);

        Livewire::actingAs($this->admin)
            ->test(PegawaiDetail::class, ['id' => $pegawai->id])
            ->assertSee('Dewi Sartika')
            ->assertSee($pegawai->umur_label)
            ->assertSee($pegawai->lama_bekerja_label)
            ->call('setTab', 'mapel')
            ->assertSee($mapel->nama)
            ->assertSee('4 JP');
    }

    public function test_pegawai_edit_dapat_memperbarui_data(): void
    {
        $pegawai = Pegawai::create([
            'nama_lengkap' => 'Agus Salim',
            'jenis_gtk' => 'Guru',
            'status_kepegawaian' => 'GTT',
            'jenis_kelamin' => 'L',
            'nip' => '199201012015011003',
        ]);

        Livewire::actingAs($this->admin)
            ->test(PegawaiEdit::class, ['id' => $pegawai->id])
            ->assertSet('nama_lengkap', 'Agus Salim')
            ->set('nama_lengkap', 'H. Agus Salim')
            ->set('status_kepegawaian', 'GTY')
            ->set('jabatan_struktural', 'Waka Kesiswaan')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('kepegawaian.pegawai.show', $pegawai->id));

        $pegawai->refresh();
        $this->assertEquals('H. Agus Salim', $pegawai->nama_lengkap);
        $this->assertEquals('GTY', $pegawai->status_kepegawaian);
        $this->assertEquals('Waka Kesiswaan', $pegawai->jabatan_struktural);
    }

    public function test_pegawai_delete_menghapus_pegawai_dan_akun_user(): void
    {
        $user = User::factory()->create([
            'username' => 'guru_agus',
        ]);

        $pegawai = Pegawai::create([
            'user_id' => $user->id,
            'nama_lengkap' => 'Agus Salim',
            'jenis_gtk' => 'Guru',
            'status_kepegawaian' => 'GTY',
            'jenis_kelamin' => 'L',
        ]);

        Livewire::actingAs($this->admin)
            ->test(PegawaiIndex::class)
            ->call('delete', $pegawai->id);

        $this->assertDatabaseMissing('pegawais', ['id' => $pegawai->id]);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
