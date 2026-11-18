<?php

namespace Tests\Feature\Akademik;

use App\Models\User;
use App\Modules\Akademik\Livewire\PenugasanMengajarIndex;
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

class PenugasanMengajarTampilanInformatifTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected TahunAjaran $ta;
    protected Semester $smtGanjil;
    protected Jurusan $jurusan;
    protected Mapel $mapel1;
    protected Mapel $mapel2;
    protected Rombel $rombel1;
    protected Rombel $rombel2;
    protected Pegawai $guruA;
    protected Pegawai $guruB;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AkademikInitialSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'admin_penugasan_info',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');

        $this->ta = TahunAjaran::first();
        $this->smtGanjil = Semester::where('tahun_ajaran_id', $this->ta->id)->where('semester', 'ganjil')->firstOrFail();

        $this->jurusan = Jurusan::first();
        $this->mapel1 = Mapel::first();
        $this->mapel2 = Mapel::skip(1)->first();

        // Buat rombel bersih
        Rombel::where('tahun_ajaran_id', $this->ta->id)->delete();

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

        $this->guruA = Pegawai::create([
            'nama_lengkap' => 'Ahmad Dahlan, M.Pd.',
            'nip' => '198501012010011001',
            'jenis_kelamin' => 'L',
            'jenis_gtk' => 'guru',
            'status' => 'aktif',
            'is_active' => true,
        ]);

        $this->guruB = Pegawai::create([
            'nama_lengkap' => 'Budi Santoso, S.Kom.',
            'nip' => '199002022015021002',
            'jenis_kelamin' => 'L',
            'jenis_gtk' => 'guru',
            'status' => 'aktif',
            'is_active' => true,
        ]);
    }

    public function test_tab_switcher_dan_rekap_beban_per_guru(): void
    {
        // Guru A: 20 JP (Standar / Ideal 18 - 24 JP)
        PenugasanMengajar::create([
            'semester_id' => $this->smtGanjil->id,
            'pegawai_id' => $this->guruA->id,
            'mapel_id' => $this->mapel1->id,
            'rombel_id' => $this->rombel1->id,
            'jp' => 20,
            'is_active' => true,
        ]);

        // Guru B: 12 JP (Beban Kurang < 18 JP)
        PenugasanMengajar::create([
            'semester_id' => $this->smtGanjil->id,
            'pegawai_id' => $this->guruB->id,
            'mapel_id' => $this->mapel2->id,
            'rombel_id' => $this->rombel2->id,
            'jp' => 12,
            'is_active' => true,
        ]);

        $test = Livewire::actingAs($this->admin)
            ->test(PenugasanMengajarIndex::class)
            ->set('filterTahunAjaran', $this->ta->id)
            ->set('filterSemester', 'ganjil');

        $this->assertEquals('guru', $test->get('activeTab'));

        // Cek data kartu guru
        $paginatedGuruCards = $test->viewData('paginatedGuruCards');
        $this->assertCount(2, $paginatedGuruCards);

        $cardA = collect($paginatedGuruCards->items())->firstWhere('pegawai_id', $this->guruA->id);
        $this->assertEquals(20, $cardA['total_jp']);
        $this->assertEquals('ideal', $cardA['beban_status']);
        $this->assertStringContainsString('Standar / Ideal', $cardA['beban_label']);

        $cardB = collect($paginatedGuruCards->items())->firstWhere('pegawai_id', $this->guruB->id);
        $this->assertEquals(12, $cardB['total_jp']);
        $this->assertEquals('kurang', $cardB['beban_status']);
        $this->assertStringContainsString('Beban Kurang', $cardB['beban_label']);

        // Ganti ke Tab Rinci
        $test->call('setActiveTab', 'rinci');
        $this->assertEquals('rinci', $test->get('activeTab'));
    }

    public function test_deteksi_team_teaching_dan_target_kurikulum(): void
    {
        // Alokasi Kurikulum untuk Mapel 1 di Jurusan & Tingkat X adalah 4 JP
        StrukturKurikulum::create([
            'tahun_ajaran_id' => $this->ta->id,
            'jurusan_id' => $this->jurusan->id,
            'tingkat' => 'X',
            'mapel_id' => $this->mapel1->id,
            'jp_ganjil' => 4,
            'jp_genap' => 4,
            'urutan' => 1,
            'is_active' => true,
        ]);

        // Team Teaching di Rombel 1: Guru A (2 JP) dan Guru B (2 JP)
        PenugasanMengajar::create([
            'semester_id' => $this->smtGanjil->id,
            'pegawai_id' => $this->guruA->id,
            'mapel_id' => $this->mapel1->id,
            'rombel_id' => $this->rombel1->id,
            'jp' => 2,
            'is_active' => true,
        ]);
        PenugasanMengajar::create([
            'semester_id' => $this->smtGanjil->id,
            'pegawai_id' => $this->guruB->id,
            'mapel_id' => $this->mapel1->id,
            'rombel_id' => $this->rombel1->id,
            'jp' => 2,
            'is_active' => true,
        ]);

        $test = Livewire::actingAs($this->admin)
            ->test(PenugasanMengajarIndex::class)
            ->set('filterTahunAjaran', $this->ta->id)
            ->set('filterSemester', 'ganjil')
            ->call('setActiveTab', 'rinci');

        $penugasans = $test->viewData('penugasans');
        $this->assertCount(2, $penugasans);

        foreach ($penugasans as $p) {
            $this->assertTrue($p->is_team_teaching);
            $this->assertEquals(4, $p->team_total_jp);
            $this->assertEquals(4, $p->curriculum_target_jp);
            $this->assertCount(1, $p->team_partners);
        }
    }

    public function test_accordion_guru_expand_dan_collapse(): void
    {
        PenugasanMengajar::create([
            'semester_id' => $this->smtGanjil->id,
            'pegawai_id' => $this->guruA->id,
            'mapel_id' => $this->mapel1->id,
            'rombel_id' => $this->rombel1->id,
            'jp' => 4,
            'is_active' => true,
        ]);

        $test = Livewire::actingAs($this->admin)
            ->test(PenugasanMengajarIndex::class)
            ->set('filterTahunAjaran', $this->ta->id);

        $this->assertEquals([], $test->get('expandedGurus'));

        // Expand guru A
        $test->call('toggleExpandGuru', $this->guruA->id);
        $this->assertEquals([$this->guruA->id], $test->get('expandedGurus'));

        // Collapse guru A
        $test->call('toggleExpandGuru', $this->guruA->id);
        $this->assertEquals([], $test->get('expandedGurus'));

        // Expand All & Collapse All
        $test->call('expandAllGurus', [$this->guruA->id, $this->guruB->id]);
        $this->assertEquals([$this->guruA->id, $this->guruB->id], $test->get('expandedGurus'));

        $test->call('collapseAllGurus');
        $this->assertEquals([], $test->get('expandedGurus'));
    }

    public function test_pencarian_dan_filter(): void
    {
        PenugasanMengajar::create([
            'semester_id' => $this->smtGanjil->id,
            'pegawai_id' => $this->guruA->id,
            'mapel_id' => $this->mapel1->id,
            'rombel_id' => $this->rombel1->id,
            'jp' => 4,
            'is_active' => true,
        ]);

        PenugasanMengajar::create([
            'semester_id' => $this->smtGanjil->id,
            'pegawai_id' => $this->guruB->id,
            'mapel_id' => $this->mapel2->id,
            'rombel_id' => $this->rombel2->id,
            'jp' => 4,
            'is_active' => true,
        ]);

        $test = Livewire::actingAs($this->admin)
            ->test(PenugasanMengajarIndex::class)
            ->set('filterTahunAjaran', $this->ta->id);

        // Search guru A
        $test->set('search', 'Ahmad Dahlan');
        $this->assertCount(1, $test->viewData('penugasans'));
        $this->assertEquals($this->guruA->id, $test->viewData('penugasans')->first()->pegawai_id);

        // Reset filter
        $test->call('resetFilters');
        $this->assertEquals('', $test->get('search'));
        $this->assertCount(2, $test->viewData('penugasans'));
    }

    public function test_sorting_guru_dan_rinci(): void
    {
        // Guru A: 10 JP
        PenugasanMengajar::create([
            'semester_id' => $this->smtGanjil->id,
            'pegawai_id' => $this->guruA->id,
            'mapel_id' => $this->mapel1->id,
            'rombel_id' => $this->rombel1->id,
            'jp' => 10,
            'is_active' => true,
        ]);

        // Guru B: 24 JP
        PenugasanMengajar::create([
            'semester_id' => $this->smtGanjil->id,
            'pegawai_id' => $this->guruB->id,
            'mapel_id' => $this->mapel2->id,
            'rombel_id' => $this->rombel2->id,
            'jp' => 24,
            'is_active' => true,
        ]);

        $test = Livewire::actingAs($this->admin)
            ->test(PenugasanMengajarIndex::class)
            ->set('filterTahunAjaran', $this->ta->id);

        // Default sorting: Nama A-Z (Ahmad Dahlan duluan)
        $cards = $test->viewData('paginatedGuruCards')->items();
        $this->assertEquals($this->guruA->id, $cards[0]['pegawai_id']);

        // Sort by JP Desc (Budi Santoso 24 JP duluan)
        $test->set('sortGuruBy', 'jp_desc');
        $cardsDesc = $test->viewData('paginatedGuruCards')->items();
        $this->assertEquals($this->guruB->id, $cardsDesc[0]['pegawai_id']);

        // Sort by JP Asc (Ahmad Dahlan 10 JP duluan)
        $test->set('sortGuruBy', 'jp_asc');
        $cardsAsc = $test->viewData('paginatedGuruCards')->items();
        $this->assertEquals($this->guruA->id, $cardsAsc[0]['pegawai_id']);
    }

    public function test_blade_render_rekap_guru_dan_flat_table(): void
    {
        PenugasanMengajar::create([
            'semester_id' => $this->smtGanjil->id,
            'pegawai_id' => $this->guruA->id,
            'mapel_id' => $this->mapel1->id,
            'rombel_id' => $this->rombel1->id,
            'jp' => 20,
            'is_active' => true,
        ]);

        $test = Livewire::actingAs($this->admin)
            ->test(PenugasanMengajarIndex::class)
            ->set('filterTahunAjaran', $this->ta->id);

        // Di Tab Guru
        $test->assertSeeHtml('Rekap Beban per Guru')
            ->assertSeeHtml('Daftar Rinci Penugasan')
            ->assertSeeHtml('Ahmad Dahlan, M.Pd.')
            ->assertSeeHtml('Standar / Ideal (18-24 JP)')
            ->assertSeeHtml('20')
            ->assertSeeHtml('JP');

        // Buka accordion di Tab Guru
        $test->call('toggleExpandGuru', $this->guruA->id)
            ->assertSeeHtml('Model Mengajar & Kurikulum')
            ->assertSeeHtml('Mandiri');

        // Pindah ke Tab Rinci
        $test->call('setActiveTab', 'rinci')
            ->assertSeeHtml('Guru Pengampu')
            ->assertSeeHtml('Kesesuaian Kurikulum & Model')
            ->assertSeeHtml('Periode Semester');
    }
}
