<?php

namespace Tests\Feature\Rapor;

use App\Models\User;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Mapel;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kepegawaian\Models\PenugasanMengajar;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Penilaian\Models\NilaiAkhir;
use App\Modules\Penilaian\Models\NilaiAsesmenHarian;
use App\Modules\Penilaian\Models\NilaiSumatif;
use App\Modules\Rapor\Livewire\CatatanWaliKelasIndex;
use App\Modules\Rapor\Livewire\RaporPemerintahIndex;
use App\Modules\Rapor\Livewire\RaporWikramaIndex;
use App\Modules\Rapor\Models\CatatanWaliKelas;
use App\Modules\SeniBudaya\Models\SeniBudaya;
use App\Modules\SeniBudaya\Models\SeniBudayaAnggota;
use App\Modules\Sistem\Models\PeriodeRapor;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\ProgramInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RaporSubmodulTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected TahunAjaran $ta;
    protected Semester $semGanjil;
    protected Rombel $rombel;
    protected Rayon $rayon;
    protected Siswa $siswa1;
    protected Siswa $siswa2;
    protected PeriodeRapor $periode9M1;
    protected PeriodeRapor $periode9M2;
    protected Mapel $mapel;
    protected PenugasanMengajar $pm;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AkademikInitialSeeder::class);
        $this->seed(ProgramInitialSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'superadmin_rapor',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');

        $this->ta = TahunAjaran::where('is_active', true)->firstOrFail();
        $this->semGanjil = Semester::where('tahun_ajaran_id', $this->ta->id)->where('semester', 'ganjil')->firstOrFail();

        $this->rombel = Rombel::where('tahun_ajaran_id', $this->ta->id)
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->firstOrFail();
        $this->rayon = Rayon::create([
            'nama' => 'Ciawi 1',
            'wilayah_cakupan' => 'Bogor Timur',
            'pembimbing_id' => $this->admin->id,
        ]);

        $this->siswa1 = Siswa::create([
            'nama' => 'Ahmad Dahlan',
            'nipd' => '2026101',
            'nisn' => '0012345671',
            'nik' => '3201010101010001',
            'jenis_kelamin' => 'L',
            'rombel_id' => $this->rombel->id,
            'rayon_id' => $this->rayon->id,
            'tahun_ajaran_id' => $this->ta->id,
            'is_active' => true,
        ]);

        $this->siswa2 = Siswa::create([
            'nama' => 'Budi Sudarsono',
            'nipd' => '2026102',
            'nisn' => '0012345672',
            'nik' => '3201010101010002',
            'jenis_kelamin' => 'L',
            'rombel_id' => $this->rombel->id,
            'rayon_id' => $this->rayon->id,
            'tahun_ajaran_id' => $this->ta->id,
            'is_active' => true,
        ]);

        $this->periode9M1 = PeriodeRapor::create([
            'tahun_ajaran_id' => $this->ta->id,
            'semester' => 'ganjil',
            'kode' => 'GANJIL-PTS',
            'nama' => 'Penilaian Tengah Semester Ganjil',
            'sebutan' => '9 Minggu ke-1',
            'tanggal_bagi' => now()->toDateString(),
            'is_active' => true,
            'urutan' => 1,
        ]);

        $this->periode9M2 = PeriodeRapor::create([
            'tahun_ajaran_id' => $this->ta->id,
            'semester' => 'ganjil',
            'kode' => 'GANJIL-PAS',
            'nama' => 'Penilaian Akhir Semester Ganjil',
            'sebutan' => '9 Minggu ke-2',
            'tanggal_bagi' => now()->addMonths(3)->toDateString(),
            'is_active' => false,
            'urutan' => 2,
        ]);

        $pegawai = Pegawai::create([
            'user_id' => $this->admin->id,
            'nama_lengkap' => 'Guru Penilai',
            'nip' => '199001012020011001',
            'jenis_kelamin' => 'L',
            'status_kepegawaian' => 'gty',
            'is_active' => true,
        ]);

        $this->mapel = Mapel::create([
            'kode' => 'INDO-10',
            'nama' => 'Bahasa Indonesia',
            'kelompok' => 'umum',
            'kkm_default' => 75,
            'is_active' => true,
        ]);

        $this->pm = PenugasanMengajar::create([
            'semester_id' => $this->semGanjil->id,
            'rombel_id' => $this->rombel->id,
            'mapel_id' => $this->mapel->id,
            'pegawai_id' => $pegawai->id,
        ]);
    }

    public function test_submodules_page_access_and_redirection(): void
    {
        $this->actingAs($this->admin);

        // /rapor redirect ke /rapor/wikrama
        $resIndex = $this->get(route('rapor.index'));
        $resIndex->assertRedirect(route('rapor.wikrama'));

        // Akses Rapor Wikrama
        $resWikrama = $this->get(route('rapor.wikrama'));
        $resWikrama->assertOk();
        $resWikrama->assertSee('Rapor Wikrama (9 Minggu)');

        // Akses Rapor Pemerintah
        $resPemerintah = $this->get(route('rapor.pemerintah'));
        $resPemerintah->assertOk();
        $resPemerintah->assertSee('Rapor Pemerintah (Semester)');

        // Akses Catatan Wali Kelas
        $resCatatan = $this->get(route('rapor.catatan-wali-kelas'));
        $resCatatan->assertOk();
        $resCatatan->assertSee('Catatan Wali Kelas / Rayon');
    }

    public function test_rapor_wikrama_component_filters_and_modal_preview(): void
    {
        // Buat record nilai akademik 9M1 untuk siswa 1
        NilaiAsesmenHarian::create(['siswa_id' => $this->siswa1->id, 'penugasan_mengajar_id' => $this->pm->id, 'kode_ah' => 'AH 1', 'nilai' => 85]);
        NilaiAsesmenHarian::create(['siswa_id' => $this->siswa1->id, 'penugasan_mengajar_id' => $this->pm->id, 'kode_ah' => 'AH 2', 'nilai' => 90]);
        NilaiSumatif::create(['siswa_id' => $this->siswa1->id, 'penugasan_mengajar_id' => $this->pm->id, 'sts' => 88]);
        NilaiAkhir::create([
            'siswa_id' => $this->siswa1->id,
            'penugasan_mengajar_id' => $this->pm->id,
            'nilai_akhir' => 88,
            'data_9m' => [
                '9M1' => [
                    'rata_formatif' => 87.5,
                    'sumatif' => 88,
                    'nilai_akhir' => 88.0,
                    'status' => 'kompeten',
                    'deskripsi_tertinggi' => 'Menunjukkan penguasaan sangat baik dalam teks laporan observasi.',
                    'deskripsi_terendah' => '',
                ]
            ]
        ]);

        Livewire::actingAs($this->admin)
            ->test(RaporWikramaIndex::class)
            ->assertSet('filterScope', 'rombel')
            ->assertSet('selectedRombelId', $this->rombel->id)
            ->assertSee($this->siswa1->nama)
            ->assertSee($this->siswa2->nama)
            // Uji ubah ke filter Rayon
            ->set('filterScope', 'rayon')
            ->set('selectedRayonId', $this->rayon->id)
            ->assertSee($this->siswa1->nama)
            // Uji buka modal preview
            ->call('openPreview', $this->siswa1->id)
            ->assertSet('showPreviewModal', true)
            ->assertSet('previewSiswaId', $this->siswa1->id)
            ->assertSee('Bahasa Indonesia')
            ->assertSee('85')
            ->assertSee('90')
            ->assertSee('88')
            ->assertSee('teks laporan observasi')
            ->call('closePreview')
            ->assertSet('showPreviewModal', false);
    }

    public function test_catatan_wali_kelas_isolated_per_period(): void
    {
        Livewire::actingAs($this->admin)
            ->test(CatatanWaliKelasIndex::class)
            ->set('filterScope', 'rombel')
            ->set('selectedRombelId', $this->rombel->id)
            ->set('selectedPeriodeId', $this->periode9M1->id)
            ->set('catatans.' . $this->siswa1->id, 'Catatan khusus periode 9M1 untuk Ahmad.')
            ->call('saveCatatan')
            ->assertHasNoErrors();

        // Verifikasi catatan 9M1 tersimpan di DB
        $cat9M1 = CatatanWaliKelas::where('siswa_id', $this->siswa1->id)
            ->where('periode_rapor_id', $this->periode9M1->id)
            ->first();
        $this->assertNotNull($cat9M1);
        $this->assertEquals('Catatan khusus periode 9M1 untuk Ahmad.', $cat9M1->catatan);

        // Sekarang simpan catatan untuk 9M2 (harus terpisah, tidak menimpa 9M1)
        Livewire::actingAs($this->admin)
            ->test(CatatanWaliKelasIndex::class)
            ->set('selectedPeriodeId', $this->periode9M2->id)
            ->assertSet('catatans.' . $this->siswa1->id, '') // 9M2 awalnya kosong
            ->call('applyTemplate', $this->siswa1->id, 'sangat_baik')
            ->call('saveCatatan');

        $cat9M2 = CatatanWaliKelas::where('siswa_id', $this->siswa1->id)
            ->where('periode_rapor_id', $this->periode9M2->id)
            ->first();
        $this->assertNotNull($cat9M2);
        $this->assertStringContainsString('Pertahankan prestasi', $cat9M2->catatan);

        // Catatan 9M1 tetap utuh
        $cat9M1->refresh();
        $this->assertEquals('Catatan khusus periode 9M1 untuk Ahmad.', $cat9M1->catatan);
    }

    public function test_cetak_single_and_bulk_pdf_wikrama_and_pemerintah(): void
    {
        $this->actingAs($this->admin);

        NilaiAkhir::create([
            'siswa_id' => $this->siswa1->id,
            'penugasan_mengajar_id' => $this->pm->id,
            'nilai_akhir' => 85,
            'data_9m' => [
                '9M1' => [
                    'rata_formatif' => 85,
                    'sumatif' => 85,
                    'nilai_akhir' => 85,
                    'deskripsi_tertinggi' => 'Unggul dalam literasi',
                ]
            ]
        ]);

        // 1. Cetak Rapor Wikrama Single
        $resWikramaSingle = $this->get(route('rapor.cetak.wikrama', [
            'siswa_id' => $this->siswa1->id,
            'periode_id' => $this->periode9M1->id,
        ]));
        $resWikramaSingle->assertOk();

        // 2. Cetak Rapor Wikrama Bulk (Massal by Rombel)
        $resWikramaBulk = $this->get(route('rapor.cetak-massal.wikrama', [
            'scope' => 'rombel',
            'target_id' => $this->rombel->id,
            'periode_id' => $this->periode9M1->id,
        ]));
        $resWikramaBulk->assertOk();

        // 3. Cetak Rapor Pemerintah Single
        $resPemSingle = $this->get(route('rapor.cetak.pemerintah', [
            'siswa_id' => $this->siswa1->id,
            'semester_id' => $this->semGanjil->id,
        ]));
        $resPemSingle->assertOk();

        // 4. Cetak Rapor Pemerintah Bulk (Massal by Rayon)
        $resPemBulk = $this->get(route('rapor.cetak-massal.pemerintah', [
            'scope' => 'rayon',
            'target_id' => $this->rayon->id,
            'semester_id' => $this->semGanjil->id,
        ]));
        $resPemBulk->assertOk();
    }

    public function test_rapor_wikrama_integrates_seni_budaya_and_strict_kkm_rules(): void
    {
        // 1. Daftarkan siswa1 ke Cabang Seni Budaya: Seni Musik
        $seniMusik = SeniBudaya::create([
            'kode' => 'SB-MUSIK',
            'nama' => 'Seni Musik',
            'kkm' => 75,
            'bobot_presensi' => 30,
            'bobot_penilaian' => 70,
            'is_active' => true,
        ]);

        $sbAnggota = SeniBudayaAnggota::create([
            'seni_budaya_id' => $seniMusik->id,
            'siswa_id' => $this->siswa1->id,
            'semester_id' => $this->semGanjil->id,
            'sts_1' => 88,
            'nilai_presensi' => 95,
            'nilai_akhir_sts_1' => 90,
            'status_keanggotaan' => 'aktif',
            'deskripsi_evaluations' => [
                'STS 1' => [
                    'deskripsi_tertinggi' => 'Sangat terampil dalam harmoni vokal dan alat musik ritmis.',
                    'deskripsi_terendah' => '',
                ]
            ],
        ]);

        // Verifikasi pada tampilan tabel dan modal preview
        Livewire::actingAs($this->admin)
            ->test(RaporWikramaIndex::class)
            ->set('selectedRombelId', $this->rombel->id)
            ->set('selectedPeriodeId', $this->periode9M1->id)
            ->assertSee('Seni Musik')
            ->call('openPreview', $this->siswa1->id)
            ->assertSee('Seni Budaya (Seni Musik)')
            ->assertSee('harmoni vokal')
            ->assertSee('90');

        // 2. Uji aturan ketat KKM: jika ada nilai di bawah KKM (65 < 75)
        $sbAnggota->update(['sts_1' => 65, 'nilai_akhir_sts_1' => 68]);

        Livewire::actingAs($this->admin)
            ->test(RaporWikramaIndex::class)
            ->set('selectedRombelId', $this->rombel->id)
            ->set('selectedPeriodeId', $this->periode9M1->id)
            ->call('openPreview', $this->siswa1->id)
            ->assertSee('Nilai belum kompeten, silakan hubungi guru yang bersangkutan.');

        // 3. Uji jika nilai kosong (null)
        $sbAnggota->update(['sts_1' => null, 'nilai_akhir_sts_1' => null]);

        Livewire::actingAs($this->admin)
            ->test(RaporWikramaIndex::class)
            ->set('selectedRombelId', $this->rombel->id)
            ->set('selectedPeriodeId', $this->periode9M1->id)
            ->call('openPreview', $this->siswa1->id)
            ->assertSee('Nilai belum lengkap, silakan hubungi guru yang bersangkutan.');
    }
}

