<?php

namespace Tests\Feature\Akademik;

use App\Models\User;
use App\Modules\Akademik\Livewire\PenugasanMengajarIndex;
use App\Modules\Akademik\Livewire\StrukturKurikulumIndex;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Mapel;
use App\Modules\Akademik\Models\PenugasanMengajar;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\StrukturKurikulum;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Kepegawaian\Models\Pegawai;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class StrukturKurikulumHarmonisasiPenugasanTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected TahunAjaran $ta;
    protected Semester $smtGanjil;
    protected Semester $smtGenap;
    protected Jurusan $jurusan;
    protected Mapel $mapel;
    protected Rombel $rombel1;
    protected Rombel $rombel2;
    protected Pegawai $guru1;
    protected Pegawai $guru2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AkademikInitialSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'admin_harmonisasi',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');

        $this->ta = TahunAjaran::first();
        $this->smtGanjil = Semester::where('tahun_ajaran_id', $this->ta->id)->where('semester', 'ganjil')->firstOrFail();
        $this->smtGenap = Semester::where('tahun_ajaran_id', $this->ta->id)->where('semester', 'genap')->firstOrFail();

        $this->jurusan = Jurusan::first();
        $this->mapel = Mapel::first();

        // Bersihkan rombel default agar terkontrol 2 rombel untuk jurusan ini
        Rombel::where('tahun_ajaran_id', $this->ta->id)->delete();

        // Siapkan 2 rombel untuk jurusan & tingkat X
        $this->rombel1 = Rombel::create([
            'tahun_ajaran_id' => $this->ta->id,
            'jurusan_id' => $this->jurusan->id,
            'tingkat' => 'X',
            'nomor_urut' => 1,
            'nama' => 'X ' . $this->jurusan->kode . ' 1',
            'is_active' => true,
        ]);

        $this->rombel2 = Rombel::create([
            'tahun_ajaran_id' => $this->ta->id,
            'jurusan_id' => $this->jurusan->id,
            'tingkat' => 'X',
            'nomor_urut' => 2,
            'nama' => 'X ' . $this->jurusan->kode . ' 2',
            'is_active' => true,
        ]);

        // Siapkan 2 guru
        $this->guru1 = Pegawai::create([
            'nama_lengkap' => 'Guru Pertama, S.Pd.',
            'jenis_kelamin' => 'L',
            'status' => 'aktif',
        ]);

        $this->guru2 = Pegawai::create([
            'nama_lengkap' => 'Guru Kedua, M.Pd.',
            'jenis_kelamin' => 'P',
            'status' => 'aktif',
        ]);
    }

    public function test_harmonisasi_menghitung_keterisian_jp_tunggal_dan_team_teaching(): void
    {
        // Setup alokasi kurikulum: 4 JP Ganjil, 4 JP Genap
        $kurikulum = StrukturKurikulum::create([
            'tahun_ajaran_id' => $this->ta->id,
            'jurusan_id' => $this->jurusan->id,
            'tingkat' => 'X',
            'mapel_id' => $this->mapel->id,
            'jp_ganjil' => 4,
            'jp_genap' => 4,
            'urutan' => 1,
            'is_active' => true,
        ]);

        // Rombel 1: 1 guru mengajar full 4 JP
        PenugasanMengajar::create([
            'semester_id' => $this->smtGanjil->id,
            'pegawai_id' => $this->guru1->id,
            'mapel_id' => $this->mapel->id,
            'rombel_id' => $this->rombel1->id,
            'jp' => 4,
            'is_active' => true,
        ]);

        // Rombel 2: Team Teaching (2 guru: Guru 1 = 2 JP, Guru 2 = 2 JP, total 4 JP)
        PenugasanMengajar::create([
            'semester_id' => $this->smtGanjil->id,
            'pegawai_id' => $this->guru1->id,
            'mapel_id' => $this->mapel->id,
            'rombel_id' => $this->rombel2->id,
            'jp' => 2,
            'is_active' => true,
        ]);
        PenugasanMengajar::create([
            'semester_id' => $this->smtGanjil->id,
            'pegawai_id' => $this->guru2->id,
            'mapel_id' => $this->mapel->id,
            'rombel_id' => $this->rombel2->id,
            'jp' => 2,
            'is_active' => true,
        ]);

        $test = Livewire::actingAs($this->admin)
            ->test(StrukturKurikulumIndex::class)
            ->set('filterTahunAjaran', $this->ta->id)
            ->set('filterJurusan', $this->jurusan->id)
            ->set('filterTingkat', 'X')
            ->set('filterSemester', 'ganjil');

        $penugasanMap = $test->viewData('penugasanMap');

        $this->assertArrayHasKey($kurikulum->id, $penugasanMap);
        $pInfo = $penugasanMap[$kurikulum->id];

        // Karena kedua rombel terpenuhi (4 JP / 4 JP), status aggregate adalah 'fulfilled'
        $this->assertEquals('fulfilled', $pInfo['agg_status']);
        $this->assertEquals('✓ Lengkap (2/2 Rombel)', $pInfo['agg_badge']);
        $this->assertEquals(2, $pInfo['fulfilled_count']);

        // Cek Rombel 1
        $r1 = $pInfo['rombels'][$this->rombel1->id];
        $this->assertEquals(4, $r1['assigned_jp']);
        $this->assertEquals(4, $r1['target_jp']);
        $this->assertEquals('fulfilled', $r1['status']);
        $this->assertEquals('Terpenuhi', $r1['label']);
        $this->assertCount(1, $r1['gurus']);
        $this->assertEquals('Guru Pertama, S.Pd.', $r1['gurus'][0]['nama']);
        $this->assertEquals(4, $r1['gurus'][0]['jp']);

        // Cek Rombel 2 (Team Teaching)
        $r2 = $pInfo['rombels'][$this->rombel2->id];
        $this->assertEquals(4, $r2['assigned_jp']);
        $this->assertEquals(4, $r2['target_jp']);
        $this->assertEquals('fulfilled', $r2['status']);
        $this->assertEquals('Terpenuhi', $r2['label']);
        $this->assertCount(2, $r2['gurus']);
        $this->assertEquals(2, $r2['gurus'][0]['jp']);
        $this->assertEquals(2, $r2['gurus'][1]['jp']);
    }

    public function test_harmonisasi_menampilkan_status_kurang_dan_belum_ada_guru(): void
    {
        // Kurikulum: 4 JP Ganjil
        $kurikulum = StrukturKurikulum::create([
            'tahun_ajaran_id' => $this->ta->id,
            'jurusan_id' => $this->jurusan->id,
            'tingkat' => 'X',
            'mapel_id' => $this->mapel->id,
            'jp_ganjil' => 4,
            'jp_genap' => 4,
            'urutan' => 1,
            'is_active' => true,
        ]);

        // Rombel 1: hanya 2 JP (kurang 2 JP)
        PenugasanMengajar::create([
            'semester_id' => $this->smtGanjil->id,
            'pegawai_id' => $this->guru1->id,
            'mapel_id' => $this->mapel->id,
            'rombel_id' => $this->rombel1->id,
            'jp' => 2,
            'is_active' => true,
        ]);

        // Rombel 2: belum ada penugasan sama sekali (empty)

        $test = Livewire::actingAs($this->admin)
            ->test(StrukturKurikulumIndex::class)
            ->set('filterTahunAjaran', $this->ta->id)
            ->set('filterJurusan', $this->jurusan->id)
            ->set('filterTingkat', 'X')
            ->set('filterSemester', 'ganjil');

        $penugasanMap = $test->viewData('penugasanMap');
        $pInfo = $penugasanMap[$kurikulum->id];

        $this->assertEquals('under', $pInfo['agg_status']);
        $this->assertStringContainsString('Kurang', $pInfo['agg_badge']);

        $r1 = $pInfo['rombels'][$this->rombel1->id];
        $this->assertEquals('under', $r1['status']);
        $this->assertEquals('Kurang 2 JP', $r1['label']);

        $r2 = $pInfo['rombels'][$this->rombel2->id];
        $this->assertEquals('empty', $r2['status']);
        $this->assertEquals('Belum Ada Guru', $r2['label']);
        $this->assertCount(0, $r2['gurus']);
    }

    public function test_accordion_toggle_dan_switch_semester(): void
    {
        $kurikulum = StrukturKurikulum::create([
            'tahun_ajaran_id' => $this->ta->id,
            'jurusan_id' => $this->jurusan->id,
            'tingkat' => 'X',
            'mapel_id' => $this->mapel->id,
            'jp_ganjil' => 4,
            'jp_genap' => 2,
            'urutan' => 1,
            'is_active' => true,
        ]);

        $test = Livewire::actingAs($this->admin)
            ->test(StrukturKurikulumIndex::class)
            ->set('filterTahunAjaran', $this->ta->id)
            ->set('filterJurusan', $this->jurusan->id)
            ->set('filterTingkat', 'X')
            ->call('setFilterSemester', 'ganjil');

        $this->assertEquals([], $test->get('expandedMapels'));

        // Toggle expand
        $test->call('toggleExpand', $kurikulum->id);
        $this->assertEquals([$kurikulum->id], $test->get('expandedMapels'));

        // Toggle collapse
        $test->call('toggleExpand', $kurikulum->id);
        $this->assertEquals([], $test->get('expandedMapels'));

        // Switch semester resets expansion
        $test->call('toggleExpand', $kurikulum->id);
        $this->assertEquals([$kurikulum->id], $test->get('expandedMapels'));
        $test->call('setFilterSemester', 'genap');
        $this->assertEquals([], $test->get('expandedMapels'));
        $this->assertEquals('genap', $test->get('filterSemester'));

        // Target JP di semester genap adalah 2
        $penugasanMap = $test->viewData('penugasanMap');
        $this->assertEquals(2, $penugasanMap[$kurikulum->id]['target_jp']);
    }

    public function test_harmonisasi_menampilkan_status_kelebihan_jp(): void
    {
        $kurikulum = StrukturKurikulum::create([
            'tahun_ajaran_id' => $this->ta->id,
            'jurusan_id' => $this->jurusan->id,
            'tingkat' => 'X',
            'mapel_id' => $this->mapel->id,
            'jp_ganjil' => 4,
            'jp_genap' => 4,
            'urutan' => 1,
            'is_active' => true,
        ]);

        // Rombel 1: 6 JP (Kelebihan 2 JP)
        PenugasanMengajar::create([
            'semester_id' => $this->smtGanjil->id,
            'pegawai_id' => $this->guru1->id,
            'mapel_id' => $this->mapel->id,
            'rombel_id' => $this->rombel1->id,
            'jp' => 4,
            'is_active' => true,
        ]);
        PenugasanMengajar::create([
            'semester_id' => $this->smtGanjil->id,
            'pegawai_id' => $this->guru2->id,
            'mapel_id' => $this->mapel->id,
            'rombel_id' => $this->rombel1->id,
            'jp' => 2,
            'is_active' => true,
        ]);

        $test = Livewire::actingAs($this->admin)
            ->test(StrukturKurikulumIndex::class)
            ->set('filterTahunAjaran', $this->ta->id)
            ->set('filterJurusan', $this->jurusan->id)
            ->set('filterTingkat', 'X')
            ->set('filterSemester', 'ganjil');

        $penugasanMap = $test->viewData('penugasanMap');
        $pInfo = $penugasanMap[$kurikulum->id];

        $this->assertEquals('over', $pInfo['agg_status']);
        $this->assertStringContainsString('Kelebihan JP', $pInfo['agg_badge']);

        $r1 = $pInfo['rombels'][$this->rombel1->id];
        $this->assertEquals('over', $r1['status']);
        $this->assertEquals('Kelebihan 2 JP', $r1['label']);
        $this->assertEquals(6, $r1['assigned_jp']);
    }

    public function test_tampilan_blade_merender_badge_dan_accordion_rincian(): void
    {
        $kurikulum = StrukturKurikulum::create([
            'tahun_ajaran_id' => $this->ta->id,
            'jurusan_id' => $this->jurusan->id,
            'tingkat' => 'X',
            'mapel_id' => $this->mapel->id,
            'jp_ganjil' => 4,
            'jp_genap' => 4,
            'urutan' => 1,
            'is_active' => true,
        ]);

        PenugasanMengajar::create([
            'semester_id' => $this->smtGanjil->id,
            'pegawai_id' => $this->guru1->id,
            'mapel_id' => $this->mapel->id,
            'rombel_id' => $this->rombel1->id,
            'jp' => 4,
            'is_active' => true,
        ]);

        PenugasanMengajar::create([
            'semester_id' => $this->smtGanjil->id,
            'pegawai_id' => $this->guru2->id,
            'mapel_id' => $this->mapel->id,
            'rombel_id' => $this->rombel2->id,
            'jp' => 4,
            'is_active' => true,
        ]);

        $test = Livewire::actingAs($this->admin)
            ->test(StrukturKurikulumIndex::class)
            ->set('filterTahunAjaran', $this->ta->id)
            ->set('filterJurusan', $this->jurusan->id)
            ->set('filterTingkat', 'X')
            ->set('filterSemester', 'ganjil');

        // Sebelum expand, badge agregat dan tombol rincian terlihat
        $test->assertSeeHtml('Keterisian Guru & JP (Ganjil)')
            ->assertSeeHtml('✓ Lengkap (2/2 Rombel)')
            ->assertSeeHtml('Rincian');

        // Buka accordion
        $test->call('toggleExpand', $kurikulum->id)
            ->assertSeeHtml('Tutup')
            ->assertSeeHtml('Guru Pertama, S.Pd.')
            ->assertSeeHtml('Guru Kedua, M.Pd.')
            ->assertSeeHtml('Kelola Penugasan');
    }

    public function test_penugasan_mengajar_mendukung_query_param_prefilter(): void
    {
        // Testing pre-filter dari link struktur kurikulum ke halaman penugasan
        $this->actingAs($this->admin)
            ->get(route('akademik.penugasan', [
                'filterTahunAjaran' => $this->ta->id,
                'filterSemester' => 'ganjil',
                'filterRombel' => $this->rombel1->id,
            ]))
            ->assertOk();

        $test = Livewire::actingAs($this->admin)
            ->withQueryParams([
                'filterTahunAjaran' => $this->ta->id,
                'filterSemester' => 'ganjil',
                'filterRombel' => $this->rombel1->id,
            ])
            ->test(PenugasanMengajarIndex::class);

        $test->assertSet('filterTahunAjaran', $this->ta->id)
            ->assertSet('filterSemester', 'ganjil')
            ->assertSet('filterRombel', $this->rombel1->id);
    }
}
