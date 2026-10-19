<?php

namespace Tests\Feature\KalenderPendidikan;

use App\Models\User;
use App\Modules\KalenderPendidikan\Livewire\KalenderIndex;
use App\Modules\KalenderPendidikan\Models\KalenderAgenda;
use App\Modules\KalenderPendidikan\Models\KalenderKategori;
use Database\Seeders\KalenderPendidikanSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class KalenderPendidikanTest extends TestCase
{
    use RefreshDatabase;

    protected User $guru;
    protected User $kurikulum;
    protected KalenderKategori $kategori;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(KalenderPendidikanSeeder::class);

        $this->guru = User::factory()->create([
            'name' => 'Guru Matematika',
            'username' => 'guru_math',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->guru->assignRole('Guru');

        $this->kurikulum = User::factory()->create([
            'name' => 'Wakasek Kurikulum',
            'username' => 'waka_kurikulum',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->kurikulum->assignRole('Kurikulum');

        $this->kategori = KalenderKategori::firstOrFail();
    }

    public function test_guru_can_access_kalender_and_create_agenda(): void
    {
        Livewire::actingAs($this->guru)
            ->test(KalenderIndex::class)
            ->assertOk()
            ->set('judul', 'Penilaian Harian Bersama')
            ->set('kategori_id', $this->kategori->id)
            ->set('tgl_mulai', '2026-11-02')
            ->set('tgl_selesai', '2026-11-04')
            ->set('is_all_day', true)
            ->set('status', 'internal')
            ->set('audiens', 'semua')
            ->set('recurrence_type', 'none')
            ->call('saveAgenda');

        $agenda = KalenderAgenda::where('judul', 'Penilaian Harian Bersama')->first();
        $this->assertNotNull($agenda);
        $this->assertEquals('2026-11-02', $agenda->tgl_mulai->format('Y-m-d'));
        $this->assertEquals('2026-11-04', $agenda->tgl_selesai->format('Y-m-d'));
        $this->assertEquals($this->guru->id, $agenda->created_by);
        $this->assertFalse($agenda->is_final);
        $this->assertEquals('internal', $agenda->status);
    }

    public function test_final_flag_workflow_permissions(): void
    {
        $agenda = KalenderAgenda::create([
            'kategori_id' => $this->kategori->id,
            'created_by' => $this->guru->id,
            'judul' => 'Ujian Akhir Semester',
            'tgl_mulai' => '2026-12-01',
            'tgl_selesai' => '2026-12-05',
            'status' => 'internal',
            'is_final' => false,
        ]);

        // 1. Guru mencoba memberi flag Final -> ditolak / tidak berubah
        Livewire::actingAs($this->guru)
            ->test(KalenderIndex::class)
            ->call('toggleFlagFinal', $agenda->id);

        $this->assertFalse($agenda->fresh()->is_final);

        // 2. Kurikulum memberikan flag Final -> berhasil
        Livewire::actingAs($this->kurikulum)
            ->test(KalenderIndex::class)
            ->call('toggleFlagFinal', $agenda->id);

        $freshAgenda = $agenda->fresh();
        $this->assertTrue($freshAgenda->is_final);
        $this->assertEquals($this->kurikulum->id, $freshAgenda->finalized_by);

        // 3. Guru mengedit tanggal agenda yang sudah Final -> flag Final otomatis dicabut
        Livewire::actingAs($this->guru)
            ->test(KalenderIndex::class)
            ->call('openDetailModal', $agenda->id)
            ->call('editSelectedAgenda')
            ->set('tgl_mulai', '2026-12-03')
            ->call('saveAgenda');

        $reloaded = $agenda->fresh();
        $this->assertEquals('2026-12-03', $reloaded->tgl_mulai->format('Y-m-d'));
        $this->assertFalse($reloaded->is_final, 'Flag Final harus dicabut jika tanggal diubah oleh guru biasa.');
    }

    public function test_recurring_events_generation(): void
    {
        Livewire::actingAs($this->kurikulum)
            ->test(KalenderIndex::class)
            ->set('judul', 'Upacara Bendera Senin')
            ->set('kategori_id', $this->kategori->id)
            ->set('tgl_mulai', '2026-11-02') // Senin
            ->set('tgl_selesai', '2026-11-02')
            ->set('is_all_day', false)
            ->set('jam_mulai', '07:00')
            ->set('jam_selesai', '08:00')
            ->set('status', 'internal')
            ->set('audiens', 'semua')
            ->set('recurrence_type', 'weekly')
            ->set('recurrence_until', '2026-11-23') // 3 minggu berikutnya
            ->call('saveAgenda');

        $parent = KalenderAgenda::where('judul', 'Upacara Bendera Senin')
            ->where('recurrence_type', 'weekly')
            ->firstOrFail();

        $childrenCount = KalenderAgenda::where('parent_id', $parent->id)->count();
        $this->assertEquals(3, $childrenCount, 'Harus menghasilkan 3 sesi pengulangan upacara mingguan.');
    }

    public function test_calendar_export_endpoints(): void
    {
        $agenda = KalenderAgenda::create([
            'kategori_id' => $this->kategori->id,
            'created_by' => $this->guru->id,
            'judul' => 'Rapat Dewan Guru',
            'tgl_mulai' => '2026-11-10',
            'tgl_selesai' => '2026-11-10',
            'status' => 'internal',
            'is_final' => true,
        ]);

        // Test export iCal (.ics)
        $icsResponse = $this->actingAs($this->guru)->get(route('kalender.export-ics', $agenda->id));
        $icsResponse->assertOk();
        $this->assertStringContainsString('text/calendar', $icsResponse->headers->get('Content-Type'));
        $this->assertStringContainsString('Rapat Dewan Guru', $icsResponse->getContent());

        // Test export PDF
        $pdfResponse = $this->actingAs($this->guru)->get(route('kalender.cetak-pdf'));
        $pdfResponse->assertOk();
        $this->assertStringContainsString('application/pdf', $pdfResponse->headers->get('Content-Type'));
    }

    public function test_kategori_and_libur_management(): void
    {
        // 1. Kurikulum mengelola kategori kegiatan
        Livewire::actingAs($this->kurikulum)
            ->test(\App\Modules\KalenderPendidikan\Livewire\KategoriManager::class)
            ->assertOk()
            ->set('nama', 'Workshop IT')
            ->set('warna', '#6366f1')
            ->set('urutan', 10)
            ->call('save');

        $this->assertDatabaseHas('kalender_kategoris', [
            'nama' => 'Workshop IT',
            'warna' => '#6366f1',
        ]);

        // 2. Kurikulum mengelola libur sekolah
        Livewire::actingAs($this->kurikulum)
            ->test(\App\Modules\KalenderPendidikan\Livewire\LiburManager::class)
            ->assertOk()
            ->set('nama', 'Libur Awal Ramadhan')
            ->set('tgl_mulai', '2026-03-01')
            ->set('tgl_selesai', '2026-03-03')
            ->set('jenis', 'sekolah')
            ->set('is_kbm_libur', true)
            ->call('save');

        $this->assertDatabaseHas('kalender_liburs', [
            'nama' => 'Libur Awal Ramadhan',
            'jenis' => 'sekolah',
        ]);
    }

    public function test_user_cannot_edit_other_users_agenda_without_permission(): void
    {
        $guruLain = User::factory()->create([
            'name' => 'Guru Bahasa',
            'username' => 'guru_bhs',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $guruLain->assignRole('Guru');

        $agendaMilikanGuruA = KalenderAgenda::create([
            'kategori_id' => $this->kategori->id,
            'created_by' => $this->guru->id,
            'judul' => 'Agenda Rahasia Guru A',
            'tgl_mulai' => '2026-11-20',
            'tgl_selesai' => '2026-11-20',
            'status' => 'internal',
        ]);

        // Guru Lain mencoba edit agenda milik Guru A
        Livewire::actingAs($guruLain)
            ->test(KalenderIndex::class)
            ->set('isEditing', true)
            ->set('agendaId', $agendaMilikanGuruA->id)
            ->set('judul', 'Diretas Oleh Guru Lain')
            ->set('kategori_id', $this->kategori->id)
            ->set('tgl_mulai', '2026-11-20')
            ->set('tgl_selesai', '2026-11-20')
            ->set('status', 'internal')
            ->set('audiens', 'semua')
            ->set('recurrence_type', 'none')
            ->call('saveAgenda');

        // Pastikan judul di database tidak berubah karena ditolak otorisasi
        $this->assertEquals('Agenda Rahasia Guru A', $agendaMilikanGuruA->fresh()->judul);
    }

    public function test_calendar_view_contains_weekend_soft_red_styling(): void
    {
        $response = $this->actingAs($this->guru)->get(route('kalender.index'));
        $response->assertOk();
        $response->assertSee('.fc .fc-day-sat', false);
        $response->assertSee('.fc .fc-day-sun', false);
        $response->assertSee('#FFF1F2', false);
        $response->assertSee('.dark .fc .fc-day-sat', false);
    }
}
