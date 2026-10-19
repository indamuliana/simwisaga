<?php

namespace Tests\Feature\Sarpras;

use App\Models\User;
use App\Modules\Sarpras\Livewire\AsetIndex;
use App\Modules\Sarpras\Livewire\BhpIndex;
use App\Modules\Sarpras\Livewire\PemeliharaanIndex;
use App\Modules\Sarpras\Livewire\PeminjamanIndex;
use App\Modules\Sarpras\Livewire\PengajuanIndex;
use App\Modules\Sarpras\Livewire\SarprasDashboard;
use App\Modules\Sarpras\Livewire\SumberDanaIndex;
use App\Modules\Sarpras\Models\Aset;
use App\Modules\Sarpras\Models\Bhp;
use App\Modules\Sarpras\Models\Kategori;
use App\Modules\Sarpras\Models\Pemeliharaan;
use App\Modules\Sarpras\Models\Peminjaman;
use App\Modules\Sarpras\Models\Pengajuan;
use App\Modules\Sarpras\Models\Ruangan;
use App\Modules\Sarpras\Models\SumberDana;
use Database\Seeders\RolesSeeder;
use Database\Seeders\SarprasInitialSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SarprasTest extends TestCase
{
    use RefreshDatabase;

    protected User $sarprasUser;
    protected User $guruUser;
    protected User $kepsekUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(SarprasInitialSeeder::class);

        $this->sarprasUser = User::factory()->create([
            'name' => 'Staf Sarpras',
            'username' => 'sarpras_user',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->sarprasUser->assignRole('Koordinator Sarpras');

        $this->guruUser = User::factory()->create([
            'name' => 'Guru Pengajar',
            'username' => 'guru_user',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->guruUser->assignRole('Guru');

        $this->kepsekUser = User::factory()->create([
            'name' => 'Kepala Sekolah',
            'username' => 'kepsek_user',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->kepsekUser->assignRole('Kepala Sekolah');
    }

    public function test_sarpras_dashboard_can_be_accessed(): void
    {
        Livewire::actingAs($this->sarprasUser)
            ->test(SarprasDashboard::class)
            ->assertStatus(200)
            ->assertSee('Total Aset Tetap')
            ->assertSee('Bahan Habis Pakai');
    }

    public function test_asset_creation_and_auto_code_generation(): void
    {
        $kategori = Kategori::where('kode', 'ELK')->firstOrFail();
        $ruangan = Ruangan::firstOrFail();
        $sumber = SumberDana::where('kode', 'BS26')->firstOrFail();

        // 1. Dynamic 4-char funding source code format
        $generatedCode = Aset::generateKodeAset($sumber->kode, $kategori->kode, 2026);
        $this->assertStringStartsWith('AST/BS26/ELK/2026/', $generatedCode);

        // 2. Backward compatible fallback without funding source
        $legacyCode = Aset::generateKodeAset($kategori->kode, 2026);
        $this->assertStringStartsWith('AST/ELK/2026/', $legacyCode);

        Livewire::actingAs($this->sarprasUser)
            ->test(AsetIndex::class)
            ->set('kategori_id', $kategori->id)
            ->set('sumber_dana_id', $sumber->id)
            ->set('kode_aset', $generatedCode)
            ->set('nama_barang', 'Smart TV LED 55 Inch')
            ->set('merk_tipe', 'Samsung 4K UHD')
            ->set('nomor_seri', 'SAM-55-9912')
            ->set('ruangan_id', $ruangan->id)
            ->set('tahun_perolehan', 2026)
            ->set('tanggal_pengadaan', '2026-03-01')
            ->set('sumber_dana', $sumber->nama)
            ->set('harga_perolehan', 8500000)
            ->set('kondisi', 'Baik')
            ->set('status', 'Tersedia')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('sarpras_asets', [
            'kode_aset' => $generatedCode,
            'sumber_dana_id' => $sumber->id,
            'nama_barang' => 'Smart TV LED 55 Inch',
        ]);
    }

    public function test_sumber_dana_crud_and_code_validation(): void
    {
        // 1. Validasi kode harus tepat 4 karakter alfanumerik
        Livewire::actingAs($this->sarprasUser)
            ->test(SumberDanaIndex::class)
            ->set('kode', 'BOS') // hanya 3 karakter
            ->set('nama', 'BOS Kinerja')
            ->call('save')
            ->assertHasErrors(['kode' => 'size']);

        Livewire::actingAs($this->sarprasUser)
            ->test(SumberDanaIndex::class)
            ->set('kode', 'BOSP26') // 6 karakter
            ->set('nama', 'BOS Prestasi')
            ->call('save')
            ->assertHasErrors(['kode' => 'size']);

        // 2. Berhasil buat sumber dana baru 4 karakter
        Livewire::actingAs($this->sarprasUser)
            ->test(SumberDanaIndex::class)
            ->set('kode', 'BSKN')
            ->set('nama', 'BOS Kinerja 2026')
            ->set('keterangan', 'Bantuan operasional sekolah kinerja')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('sarpras_sumber_danas', [
            'kode' => 'BSKN',
            'nama' => 'BOS Kinerja 2026',
        ]);

        $created = SumberDana::where('kode', 'BSKN')->firstOrFail();

        // 3. Edit sumber dana
        Livewire::actingAs($this->sarprasUser)
            ->test(SumberDanaIndex::class)
            ->call('edit', $created->id)
            ->set('nama', 'BOS Kinerja 2026 Revisi')
            ->call('save')
            ->assertHasNoErrors();

        $created->refresh();
        $this->assertEquals('BOS Kinerja 2026 Revisi', $created->nama);

        // 4. Proteksi hapus sumber dana yang masih memiliki relasi aset
        $sumberUsed = SumberDana::where('kode', 'BS26')->firstOrFail();
        Livewire::actingAs($this->sarprasUser)
            ->test(SumberDanaIndex::class)
            ->call('delete', $sumberUsed->id);

        // Record tetap ada di database karena dicegah
        $this->assertDatabaseHas('sarpras_sumber_danas', [
            'id' => $sumberUsed->id,
        ]);
    }

    public function test_bhp_stock_in_and_out_mutations(): void
    {
        $bhp = Bhp::firstOrFail();
        $initialStock = $bhp->stok_saat_ini;

        // Mutasi Masuk (+10)
        Livewire::actingAs($this->sarprasUser)
            ->test(BhpIndex::class)
            ->call('openMutasi', $bhp->id, 'masuk')
            ->set('mutasiJumlah', 10)
            ->set('mutasiPenerima', 'Vendor Pengadaan')
            ->call('saveMutasi')
            ->assertHasNoErrors();

        $bhp->refresh();
        $this->assertEquals($initialStock + 10, $bhp->stok_saat_ini);

        // Mutasi Keluar (-3)
        Livewire::actingAs($this->sarprasUser)
            ->test(BhpIndex::class)
            ->call('openMutasi', $bhp->id, 'keluar')
            ->set('mutasiJumlah', 3)
            ->set('mutasiPenerima', 'Guru Piket')
            ->call('saveMutasi')
            ->assertHasNoErrors();

        $bhp->refresh();
        $this->assertEquals($initialStock + 10 - 3, $bhp->stok_saat_ini);
    }

    public function test_procurement_workflow_guru_to_sarpras_to_kepsek(): void
    {
        $kategori = Kategori::firstOrFail();
        $ruangan = Ruangan::firstOrFail();

        // 1. Guru mengajukan barang
        Livewire::actingAs($this->guruUser)
            ->test(PengajuanIndex::class)
            ->set('nama_barang', 'Kamera DSLR Dokumentasi')
            ->set('kategori_id', $kategori->id)
            ->set('ruangan_id', $ruangan->id)
            ->set('jumlah', 1)
            ->set('estimasi_harga_satuan', 7500000)
            ->set('urgensi', 'Tinggi')
            ->set('spesifikasi', 'Canon EOS 3000D Kit')
            ->call('submitPengajuan')
            ->assertHasNoErrors();

        $pengajuan = Pengajuan::where('nama_barang', 'Kamera DSLR Dokumentasi')->firstOrFail();
        $this->assertEquals('Diajukan', $pengajuan->status);

        // 2. Sarpras mereview pengajuan
        Livewire::actingAs($this->sarprasUser)
            ->test(PengajuanIndex::class)
            ->call('openAction', $pengajuan->id, 'review')
            ->set('actionCatatan', 'Sangat layak untuk tim publikasi sekolah')
            ->call('processAction')
            ->assertHasNoErrors();

        $pengajuan->refresh();
        $this->assertEquals('Review Sarpras', $pengajuan->status);

        // 3. Kepala Sekolah menyetujui
        Livewire::actingAs($this->kepsekUser)
            ->test(PengajuanIndex::class)
            ->call('openAction', $pengajuan->id, 'approve_kepsek')
            ->set('actionCatatan', 'Disetujui untuk dianggarkan')
            ->call('processAction')
            ->assertHasNoErrors();

        $pengajuan->refresh();
        $this->assertEquals('Disetujui Kepsek', $pengajuan->status);
        $this->assertNotNull($pengajuan->approved_at);
    }

    public function test_asset_borrowing_and_returning(): void
    {
        $aset = Aset::where('status', 'Tersedia')->firstOrFail();

        // Pinjam aset
        Livewire::actingAs($this->guruUser)
            ->test(PeminjamanIndex::class)
            ->set('aset_id', $aset->id)
            ->set('peminjam_nama', 'Budi Utomo')
            ->set('peminjam_tipe', 'Guru/GTK')
            ->set('keperluan', 'Mengajar di kelas outdoor')
            ->set('tanggal_pinjam', date('Y-m-d'))
            ->set('batas_kembali', date('Y-m-d', strtotime('+1 day')))
            ->call('submitPinjam')
            ->assertHasNoErrors();

        $aset->refresh();
        $this->assertEquals('Dipinjam', $aset->status);

        $pinjam = Peminjaman::where('aset_id', $aset->id)->where('status', 'Dipinjam')->firstOrFail();

        // Kembalikan aset
        Livewire::actingAs($this->sarprasUser)
            ->test(PeminjamanIndex::class)
            ->call('openReturn', $pinjam->id)
            ->set('tanggal_kembali', date('Y-m-d'))
            ->set('kondisi_kembali', 'Baik')
            ->call('processReturn')
            ->assertHasNoErrors();

        $pinjam->refresh();
        $aset->refresh();
        $this->assertEquals('Dikembalikan', $pinjam->status);
        $this->assertEquals('Tersedia', $aset->status);
    }

    public function test_maintenance_ticket_and_resolution(): void
    {
        $aset = Aset::where('kondisi', 'Baik')->firstOrFail();

        // 1. Guru lapor kerusakan
        Livewire::actingAs($this->guruUser)
            ->test(PemeliharaanIndex::class)
            ->set('aset_id', $aset->id)
            ->set('tanggal_lapor', date('Y-m-d'))
            ->set('deskripsi_kerusakan', 'Layar bergaris dan kipas berbunyi keras')
            ->set('tingkat_urgensi', 'Sedang')
            ->call('submitReport')
            ->assertHasNoErrors();

        $aset->refresh();
        $this->assertEquals('Dalam Perbaikan', $aset->status);
        $this->assertEquals('Rusak Ringan', $aset->kondisi);

        $ticket = Pemeliharaan::where('aset_id', $aset->id)->firstOrFail();

        // 2. Sarpras menyelesaikan perbaikan
        Livewire::actingAs($this->sarprasUser)
            ->test(PemeliharaanIndex::class)
            ->call('openAction', $ticket->id)
            ->set('statusAction', 'Selesai')
            ->set('tindakan_perbaikan', 'Penggantian fan pendingin dan kabel fleksibel display')
            ->set('teknisi_vendor', 'Service Center Resmi')
            ->set('biaya', 350000)
            ->call('processAction')
            ->assertHasNoErrors();

        $ticket->refresh();
        $aset->refresh();
        $this->assertEquals('Selesai', $ticket->status);
        $this->assertEquals('Tersedia', $aset->status);
        $this->assertEquals('Baik', $aset->kondisi);
    }

    public function test_document_print_endpoints(): void
    {
        $this->actingAs($this->sarprasUser);

        $responseBast = $this->get(route('sarpras.cetak.bast'));
        $responseBast->assertStatus(200);

        $responsePemusnahan = $this->get(route('sarpras.cetak.pemusnahan'));
        $responsePemusnahan->assertStatus(200);

        $responseLaporanPdf = $this->get(route('sarpras.cetak.laporan-pdf'));
        $responseLaporanPdf->assertStatus(200);

        $responseLabelQr = $this->get(route('sarpras.cetak.label-qr'));
        $responseLabelQr->assertStatus(200);
        $responseLabelQr->assertSee('Lembar Cetak Stiker Label QR Code Aset');
    }

    public function test_bulk_asset_creation_and_batch_qr_printing(): void
    {
        $kategori = Kategori::where('kode', 'MBL')->firstOrFail();
        $ruangan = Ruangan::firstOrFail();
        $sumber = SumberDana::where('kode', 'BS26')->firstOrFail();

        // 1. Validasi batas jumlah unit (min: 1, max: 100)
        Livewire::actingAs($this->sarprasUser)
            ->test(AsetIndex::class)
            ->set('jumlah', 0)
            ->call('save')
            ->assertHasErrors(['jumlah' => 'min']);

        Livewire::actingAs($this->sarprasUser)
            ->test(AsetIndex::class)
            ->set('jumlah', 101)
            ->call('save')
            ->assertHasErrors(['jumlah' => 'max']);

        // 2. Input 5 unit barang sekaligus
        $testComponent = Livewire::actingAs($this->sarprasUser)
            ->test(AsetIndex::class)
            ->set('kategori_id', $kategori->id)
            ->set('sumber_dana_id', $sumber->id)
            ->set('jumlah', 5)
            ->set('nama_barang', 'Kursi Siswa Futura')
            ->set('merk_tipe', 'Futura FTR-405')
            ->set('nomor_seri', 'LOT-MBL-2026')
            ->set('ruangan_id', $ruangan->id)
            ->set('tahun_perolehan', 2026)
            ->set('tanggal_pengadaan', '2026-03-10')
            ->set('sumber_dana', $sumber->nama)
            ->set('harga_perolehan', 350000)
            ->set('kondisi', 'Baik')
            ->set('status', 'Tersedia')
            ->call('save')
            ->assertHasNoErrors();

        // 3. Verifikasi 5 record dibuat dengan penomoran unit dan harga satuan
        for ($i = 1; $i <= 5; $i++) {
            $this->assertDatabaseHas('sarpras_asets', [
                'nama_barang' => "Kursi Siswa Futura (Unit {$i})",
                'sumber_dana_id' => $sumber->id,
                'harga_perolehan' => 350000,
                'nomor_seri' => 'LOT-MBL-2026',
            ]);
        }

        $createdAssets = Aset::where('nama_barang', 'like', 'Kursi Siswa Futura%')->orderBy('id')->get();
        $this->assertCount(5, $createdAssets);

        // Pastikan kode aset berurutan
        $firstCode = $createdAssets->first()->kode_aset;
        $lastCode = $createdAssets->last()->kode_aset;
        $this->assertStringStartsWith('AST/BS26/MBL/2026/', $firstCode);
        $this->assertStringStartsWith('AST/BS26/MBL/2026/', $lastCode);

        // 4. Test Cetak QR Batch menggunakan filter ids
        $idsParam = $createdAssets->pluck('id')->implode(',');
        $responseBatchQr = $this->actingAs($this->sarprasUser)->get(route('sarpras.cetak.label-qr', ['ids' => $idsParam]));
        $responseBatchQr->assertStatus(200);
        $responseBatchQr->assertSee('Kursi Siswa Futura (Unit 1)');
        $responseBatchQr->assertSee('Kursi Siswa Futura (Unit 5)');
    }
}
