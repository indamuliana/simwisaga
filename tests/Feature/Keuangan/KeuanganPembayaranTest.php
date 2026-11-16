<?php

namespace Tests\Feature\Keuangan;

use App\Models\User;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Program;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Keuangan\Controllers\KeuanganPrintController;
use App\Modules\Keuangan\Models\PosBiaya;
use App\Modules\Keuangan\Models\Rekening;
use App\Modules\Keuangan\Models\SiswaTarif;
use App\Modules\Keuangan\Models\Tagihan;
use App\Modules\Keuangan\Models\Tarif;
use App\Modules\Keuangan\Models\Transaksi;
use App\Modules\Keuangan\Services\KeuanganService;
use Database\Seeders\AkademikInitialSeeder;
use Database\Seeders\KeuanganInitialSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class KeuanganPembayaranTest extends TestCase
{
    use RefreshDatabase;

    protected User $bendahara;
    protected User $siswaUser;
    protected Siswa $siswa;
    protected TahunAjaran $ta;
    protected KeuanganService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AkademikInitialSeeder::class);
        $this->seed(\Database\Seeders\ProgramInitialSeeder::class);
        $this->seed(KeuanganInitialSeeder::class);

        $this->service = app(KeuanganService::class);
        $this->ta = TahunAjaran::firstOrFail();

        $rombel = Rombel::first();
        if (! $rombel) {
            $jurusan = Jurusan::first();
            $rombel = Rombel::create([
                'tahun_ajaran_id' => $this->ta->id,
                'jurusan_id' => $jurusan?->id,
                'tingkat' => 'X',
                'nomor_urut' => 1,
                'nama' => 'X PPLG 1',
            ]);
        }

        $rayon = Rayon::firstOrCreate([
            'nama' => 'Cib 1',
        ]);

        $this->siswaUser = User::create([
            'name' => 'Ahmad Fulan',
            'username' => '23241001',
            'email' => 'ahmad@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $this->siswaUser->assignRole('Siswa');

        $progReguler = Program::where('kode', 'REG')->first();

        $this->siswa = Siswa::create([
            'user_id' => $this->siswaUser->id,
            'rombel_id' => $rombel->id,
            'rayon_id' => $rayon->id,
            'program_id' => $progReguler?->id,
            'nama' => 'Ahmad Fulan',
            'nipd' => '23241001',
            'nisn' => '0081234567',
            'jk' => 'L',
            'status' => 'aktif',
            'is_active' => true,
        ]);

        $this->bendahara = User::create([
            'name' => 'Bendahara Sekolah',
            'username' => 'bendahara1',
            'email' => 'bendahara@wikrama.sch.id',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $this->bendahara->assignRole('Bendahara');
    }

    public function test_generate_tagihan_spp_berhasil_12_bulan(): void
    {
        $count = $this->service->generateTagihanSppSiswa($this->siswa, $this->ta->id);
        $this->assertEquals(12, $count);

        $tagihans = Tagihan::where('siswa_id', $this->siswa->id)->get();
        $this->assertCount(12, $tagihans);

        // Verifikasi bulan 7 (Juli) dan bulan 6 (Juni)
        $juli = $tagihans->firstWhere('bulan', 7);
        $this->assertNotNull($juli);
        $this->assertEquals(350000, $juli->nominal_tagihan);
        $this->assertEquals(350000, $juli->sisa_tagihan);
        $this->assertEquals('belum_bayar', $juli->status);
    }

    public function test_pembayaran_kasir_multi_pos_dan_cicilan_mengurangi_sisa_tagihan(): void
    {
        // Generate SPP & DSP
        $this->service->generateTagihanSppSiswa($this->siswa, $this->ta->id);
        $posDsp = PosBiaya::where('kode', 'DSP')->firstOrFail();
        $dspTagihan = $this->service->generateTagihanBebas($this->siswa, $posDsp, $this->ta->id);

        $sppJuli = Tagihan::where('siswa_id', $this->siswa->id)->where('bulan', 7)->firstOrFail();
        $rekening = Rekening::where('is_kas_tunai', true)->firstOrFail();

        // Bayar SPP Juli lunas (350rb) + cicil DSP 500rb dari total 3.5jt
        $alokasi = [
            ['tagihan_id' => $sppJuli->id, 'nominal' => 350000],
            ['tagihan_id' => $dspTagihan->id, 'nominal' => 500000],
        ];

        $transaksi = $this->service->prosesPembayaranKasir(
            siswaId: $this->siswa->id,
            rekeningId: $rekening->id,
            alokasi: $alokasi,
            namaPenyetor: 'Ahmad Fulan',
            userId: $this->bendahara->id
        );

        $this->assertNotNull($transaksi);
        $this->assertEquals(850000, $transaksi->total_bayar);
        $this->assertNotEmpty($transaksi->kode_verifikasi);

        // Cek status SPP Juli -> harus LUNAS
        $sppJuli->refresh();
        $this->assertEquals('lunas', $sppJuli->status);
        $this->assertEquals(0, $sppJuli->sisa_tagihan);

        // Cek status DSP -> harus DICICIL, sisa 3jt
        $dspTagihan->refresh();
        $this->assertEquals('dicicil', $dspTagihan->status);
        $this->assertEquals(3000000, $dspTagihan->sisa_tagihan);

        // Cek saldo rekening kas bertambah
        $rekening->refresh();
        $this->assertEquals(850000, $rekening->saldo_saat_ini);
    }

    public function test_halaman_verifikasi_publik_menampilkan_kwitansi_sah(): void
    {
        $this->service->generateTagihanSppSiswa($this->siswa, $this->ta->id);
        $sppJuli = Tagihan::where('siswa_id', $this->siswa->id)->where('bulan', 7)->firstOrFail();
        $rekening = Rekening::firstOrFail();

        $transaksi = $this->service->prosesPembayaranKasir(
            siswaId: $this->siswa->id,
            rekeningId: $rekening->id,
            alokasi: [['tagihan_id' => $sppJuli->id, 'nominal' => 350000]],
            userId: $this->bendahara->id
        );

        // Request ke rute publik
        $response = $this->get(route('keuangan.verifikasi-kwitansi', $transaksi->kode_verifikasi));
        $response->assertStatus(200);
        $response->assertSee('Kwitansi Resmi Terverifikasi Sah');
        $response->assertSee($transaksi->nomor_transaksi);
        $response->assertSee($this->siswa->nama);
    }

    public function test_siswa_tidak_bisa_mengakses_tunggakan_biaya(): void
    {
        // Login sebagai Siswa
        $this->actingAs($this->siswaUser);

        // Coba akses portal keuangan orang tua
        $response = $this->get(route('portal-siswa.keuangan'));
        // Harus menampilkan pesan pembatasan akses siswa
        $response->assertSee('Akses Khusus Orang Tua / Wali');
        $response->assertDontSee('Total Sisa Kewajiban Administrasi');
    }

    public function test_generator_whatsapp_tagihan_membuat_pesan_rapi(): void
    {
        $this->service->generateTagihanSppSiswa($this->siswa, $this->ta->id);
        $dataWa = $this->service->generateWhatsappTagihan($this->siswa);

        $this->assertArrayHasKey('pesan', $dataWa);
        $this->assertStringContainsString($this->siswa->nama, $dataWa['pesan']);
        $this->assertStringContainsString('SPP Bulanan', $dataWa['pesan']);
        $this->assertGreaterThan(0, $dataWa['total_tunggakan']);
    }

    public function test_verifikasi_approval_transfer_online_membuat_kwitansi_dan_potong_tagihan(): void
    {
        $this->service->generateTagihanSppSiswa($this->siswa, $this->ta->id);
        $sppJuli = Tagihan::where('siswa_id', $this->siswa->id)->where('bulan', 7)->firstOrFail();
        $rekening = Rekening::where('is_kas_tunai', false)->firstOrFail();

        $pengajuan = \App\Modules\Keuangan\Models\PembayaranOnline::create([
            'kode_pengajuan' => \App\Modules\Keuangan\Models\PembayaranOnline::generateKodePengajuan(),
            'siswa_id' => $this->siswa->id,
            'user_id' => $this->siswaUser->id,
            'rekening_tujuan_id' => $rekening->id,
            'bank_pengirim' => 'BCA',
            'nama_pemilik_rekening' => 'Bapak Fulan',
            'nominal_transfer' => 350000,
            'tanggal_transfer' => now()->toDateString(),
            'file_bukti_path' => 'bukti_transfer/dummy.jpg',
            'alokasi_pos' => [
                ['tagihan_id' => $sppJuli->id, 'nominal' => 350000],
            ],
            'status' => 'menunggu',
        ]);

        $this->service->verifikasiPembayaranOnline($pengajuan, 'disetujui', verifikatorId: $this->bendahara->id);

        $pengajuan->refresh();
        $this->assertEquals('disetujui', $pengajuan->status);
        $this->assertNotNull($pengajuan->transaksi_id);

        $sppJuli->refresh();
        $this->assertEquals('lunas', $sppJuli->status);
        $this->assertEquals(0, $sppJuli->sisa_tagihan);
    }

    public function test_cetak_kwitansi_pdf_dan_surat_tagihan_pdf(): void
    {
        $this->service->generateTagihanSppSiswa($this->siswa, $this->ta->id);
        $sppJuli = Tagihan::where('siswa_id', $this->siswa->id)->where('bulan', 7)->firstOrFail();
        $rekening = Rekening::firstOrFail();

        $transaksi = $this->service->prosesPembayaranKasir(
            siswaId: $this->siswa->id,
            rekeningId: $rekening->id,
            alokasi: [['tagihan_id' => $sppJuli->id, 'nominal' => 350000]],
            userId: $this->bendahara->id
        );

        $this->actingAs($this->bendahara);

        // Cetak Kwitansi
        $resKwitansi = $this->get(route('keuangan.cetak.kwitansi', $transaksi->id));
        $resKwitansi->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $resKwitansi->headers->get('Content-Type'));

        // Cetak Surat Tagihan
        $resSurat = $this->get(route('keuangan.cetak.surat-tagihan', $this->siswa->id));
        $resSurat->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $resSurat->headers->get('Content-Type'));
    }

    public function test_tarif_membedakan_tingkat_x_unggulan_dan_reguler(): void
    {
        $posSpp = PosBiaya::where('kode', 'SPP')->firstOrFail();
        $progUnggulan = Program::where('kode', 'UNG')->firstOrFail();

        // Siswa Reguler (default dari setUp)
        $nominalReguler = $this->service->getNominalTarifSiswa($this->siswa, $posSpp, $this->ta->id);
        $this->assertEquals(350000, $nominalReguler);

        // Buat Siswa Unggulan
        $siswaUnggulan = Siswa::create([
            'user_id' => null,
            'rombel_id' => $this->siswa->rombel_id,
            'rayon_id' => $this->siswa->rayon_id,
            'program_id' => $progUnggulan->id,
            'nama' => 'Siswa Unggulan',
            'nipd' => '23241099',
            'nisn' => '0089999999',
            'jk' => 'P',
            'status' => 'aktif',
            'is_active' => true,
        ]);

        $nominalUnggulan = $this->service->getNominalTarifSiswa($siswaUnggulan, $posSpp, $this->ta->id);
        $this->assertEquals(450000, $nominalUnggulan);

        // Uji Fallback: Tingkat XI tidak membedakan program (program_id null di seeder)
        $rombelXI = Rombel::create([
            'tahun_ajaran_id' => $this->ta->id,
            'jurusan_id' => $this->siswa->rombel?->jurusan_id,
            'tingkat' => 'XI',
            'nomor_urut' => 2,
            'nama' => 'XI PPLG 2',
        ]);
        $siswaXIUnggulan = Siswa::create([
            'rombel_id' => $rombelXI->id,
            'rayon_id' => $this->siswa->rayon_id,
            'program_id' => $progUnggulan->id,
            'nama' => 'Siswa XI Unggulan',
            'nipd' => '22231088',
            'nisn' => '0078888888',
            'jk' => 'L',
            'status' => 'aktif',
            'is_active' => true,
        ]);

        $nominalXI = $this->service->getNominalTarifSiswa($siswaXIUnggulan, $posSpp, $this->ta->id);
        $this->assertEquals(325000, $nominalXI);
    }

    public function test_sinkronisasi_tagihan_hanya_mengubah_tagihan_belum_bayar(): void
    {
        $progUnggulan = Program::where('kode', 'UNG')->firstOrFail();

        // 1. Generate SPP untuk siswa yang saat ini Reguler (350rb)
        $this->service->generateTagihanSppSiswa($this->siswa, $this->ta->id);
        $sppJuli = Tagihan::where('siswa_id', $this->siswa->id)->where('bulan', 7)->firstOrFail();
        $sppAgustus = Tagihan::where('siswa_id', $this->siswa->id)->where('bulan', 8)->firstOrFail();

        // 2. Bayar SPP Juli lunas (350rb)
        $rekening = Rekening::firstOrFail();
        $this->service->prosesPembayaranKasir(
            siswaId: $this->siswa->id,
            rekeningId: $rekening->id,
            alokasi: [['tagihan_id' => $sppJuli->id, 'nominal' => 350000]],
            userId: $this->bendahara->id
        );

        // 3. Siswa berpindah program ke Unggulan (tarif 450rb)
        $this->siswa->update(['program_id' => $progUnggulan->id]);

        // 4. Jalankan sinkronisasi
        $updatedCount = $this->service->sinkronkanTagihanSiswa($this->ta->id);
        $this->assertGreaterThan(0, $updatedCount);

        // 5. Cek: SPP Juli yang sudah dibayar TIDAK boleh berubah
        $sppJuli->refresh();
        $this->assertEquals(350000, $sppJuli->nominal_tagihan);
        $this->assertEquals(350000, $sppJuli->nominal_terbayar);
        $this->assertEquals(0, $sppJuli->sisa_tagihan);
        $this->assertEquals('lunas', $sppJuli->status);

        // 6. Cek: SPP Agustus yang belum dibayar HARUS terupdate ke tarif Unggulan (450rb)
        $sppAgustus->refresh();
        $this->assertEquals(450000, $sppAgustus->nominal_tagihan);
        $this->assertEquals(450000, $sppAgustus->sisa_tagihan);
        $this->assertEquals('belum_bayar', $sppAgustus->status);
    }

    public function test_pos_biaya_bisa_diedit_dan_dihapus(): void
    {
        $this->actingAs($this->bendahara);

        // 1. Buat pos baru
        $pos = PosBiaya::create([
            'kode' => 'SERAGAM',
            'nama' => 'Uang Seragam',
            'tipe' => 'insidental',
            'is_aktif' => true,
        ]);

        // 2. Edit pos via Livewire
        Livewire::test(\App\Modules\Keuangan\Livewire\PosDanTarifIndex::class)
            ->call('editPos', $pos->id)
            ->set('posNama', 'Paket Seragam Lengkap')
            ->call('simpanPos');

        $pos->refresh();
        $this->assertEquals('Paket Seragam Lengkap', $pos->nama);

        // 3. Hapus pos via Livewire
        Livewire::test(\App\Modules\Keuangan\Livewire\PosDanTarifIndex::class)
            ->call('hapusPos', $pos->id);

        $this->assertNull(PosBiaya::find($pos->id));
    }

    public function test_pos_biaya_dengan_riwayat_pembayaran_dicegah_dihapus(): void
    {
        $this->actingAs($this->bendahara);

        $posSpp = PosBiaya::where('kode', 'SPP')->firstOrFail();

        // Generate dan bayar tagihan SPP
        $this->service->generateTagihanSppSiswa($this->siswa, $this->ta->id);
        $sppJuli = Tagihan::where('siswa_id', $this->siswa->id)->where('bulan', 7)->firstOrFail();
        $rekening = Rekening::firstOrFail();

        $this->service->prosesPembayaranKasir(
            siswaId: $this->siswa->id,
            rekeningId: $rekening->id,
            alokasi: [['tagihan_id' => $sppJuli->id, 'nominal' => 350000]],
            userId: $this->bendahara->id
        );

        // Coba hapus pos SPP
        Livewire::test(\App\Modules\Keuangan\Livewire\PosDanTarifIndex::class)
            ->call('hapusPos', $posSpp->id)
            ->assertSee('tidak dapat dihapus karena sudah memiliki riwayat pembayaran');

        // Pastikan pos SPP tetap ada di database
        $this->assertNotNull(PosBiaya::find($posSpp->id));
    }

    public function test_tarif_standar_bisa_diedit_dan_dihapus(): void
    {
        $this->actingAs($this->bendahara);

        $pos = PosBiaya::firstOrFail();
        $tarif = Tarif::firstOrFail();

        // 1. Edit tarif via Livewire
        Livewire::test(\App\Modules\Keuangan\Livewire\PosDanTarifIndex::class)
            ->call('editTarif', $tarif->id)
            ->set('tarifNominal', 550000)
            ->call('simpanTarif');

        $tarif->refresh();
        $this->assertEquals(550000, $tarif->nominal);

        // 2. Hapus tarif via Livewire
        Livewire::test(\App\Modules\Keuangan\Livewire\PosDanTarifIndex::class)
            ->call('hapusTarif', $tarif->id);

        $this->assertNull(Tarif::find($tarif->id));
    }

    public function test_tarif_khusus_siswa_bisa_diedit_dan_dihapus(): void
    {
        $this->actingAs($this->bendahara);

        $pos = PosBiaya::firstOrFail();

        // 1. Buat tarif kustom
        $kustom = SiswaTarif::create([
            'siswa_id' => $this->siswa->id,
            'pos_biaya_id' => $pos->id,
            'tahun_ajaran_id' => $this->ta->id,
            'nominal_kustom' => 100000,
            'jenis_potongan' => 'nominal_tetap',
            'alasan' => 'Dispensasi Yatim',
        ]);

        // 2. Edit tarif kustom via Livewire
        Livewire::test(\App\Modules\Keuangan\Livewire\PosDanTarifIndex::class)
            ->call('editKustom', $kustom->id)
            ->set('kustomNominal', 75000)
            ->set('kustomAlasan', 'Dispensasi Yatim Piatu')
            ->call('simpanKustom');

        $kustom->refresh();
        $this->assertEquals(75000, $kustom->nominal_kustom);
        $this->assertEquals('Dispensasi Yatim Piatu', $kustom->alasan);

        // 3. Hapus tarif kustom via Livewire
        Livewire::test(\App\Modules\Keuangan\Livewire\PosDanTarifIndex::class)
            ->call('hapusKustom', $kustom->id);

        $this->assertNull(SiswaTarif::find($kustom->id));
    }

    public function test_verifikasi_ganda_pembayaran_online_dicegah(): void
    {
        $this->service->generateTagihanSppSiswa($this->siswa, $this->ta->id);
        $sppJuli = Tagihan::where('siswa_id', $this->siswa->id)->where('bulan', 7)->firstOrFail();
        $rekening = Rekening::firstOrFail();

        $online = \App\Modules\Keuangan\Models\PembayaranOnline::create([
            'kode_pengajuan' => 'ONL-TEST-DOUBLE',
            'siswa_id' => $this->siswa->id,
            'user_id' => $this->siswaUser->id,
            'rekening_tujuan_id' => $rekening->id,
            'nama_pemilik_rekening' => 'Orang Tua Siswa',
            'nominal_transfer' => 350000,
            'tanggal_transfer' => now()->toDateString(),
            'file_bukti_path' => 'dummy.jpg',
            'alokasi_pos' => [['tagihan_id' => $sppJuli->id, 'nominal' => 350000]],
            'status' => 'menunggu',
        ]);

        // Verifikasi pertama: sukses
        $this->service->verifikasiPembayaranOnline($online, 'disetujui', null, $this->bendahara->id);
        $this->assertEquals('disetujui', $online->fresh()->status);

        // Verifikasi kedua: HARUS melempar DomainException
        $this->expectException(\DomainException::class);
        $this->service->verifikasiPembayaranOnline($online, 'disetujui', null, $this->bendahara->id);
    }

    public function test_kelebihan_bayar_melebihi_sisa_tagihan_ditolak(): void
    {
        $this->service->generateTagihanSppSiswa($this->siswa, $this->ta->id);
        $sppJuli = Tagihan::where('siswa_id', $this->siswa->id)->where('bulan', 7)->firstOrFail();
        $rekening = Rekening::firstOrFail();

        // Nominal tagihan 350rb, input bayar 500rb
        $this->expectException(\InvalidArgumentException::class);
        $this->service->prosesPembayaranKasir(
            siswaId: $this->siswa->id,
            rekeningId: $rekening->id,
            alokasi: [['tagihan_id' => $sppJuli->id, 'nominal' => 500000]],
            userId: $this->bendahara->id
        );
    }

    public function test_scope_lunas_tidak_bocor_ke_siswa_lain(): void
    {
        $this->service->generateTagihanSppSiswa($this->siswa, $this->ta->id);
        $sppJuli = Tagihan::where('siswa_id', $this->siswa->id)->where('bulan', 7)->firstOrFail();
        $sppJuli->update(['status' => 'lunas', 'sisa_tagihan' => 0]);

        // Cek query lunas untuk siswa lain (ID 99999) tidak boleh mengembalikan tagihan milik $this->siswa
        $lunasLain = Tagihan::where('siswa_id', 99999)->lunas()->get();
        $this->assertCount(0, $lunasLain);
    }

    public function test_terbilang_nol_mengembalikan_kata_nol(): void
    {
        $this->assertEquals('Nol', KeuanganPrintController::terbilang(0));
    }

    public function test_generate_semua_tagihan_menerbitkan_spp_dan_dsp_dengan_urutan_tahun_ajaran(): void
    {
        // 1. Ambil Pos DSP (tipe: bebas) dan setel tarifnya
        $posDsp = PosBiaya::firstOrCreate(
            ['kode' => 'DSP'],
            [
                'nama' => 'Dana Sumbangan Pendidikan',
                'tipe' => 'bebas',
                'is_aktif' => true,
            ]
        );

        Tarif::updateOrCreate(
            [
                'pos_biaya_id' => $posDsp->id,
                'tahun_ajaran_id' => $this->ta->id,
                'tingkat' => 'X',
            ],
            [
                'nominal' => 3000000,
            ]
        );

        // 2. Terbitkan semua tagihan untuk siswa
        $count = $this->service->generateSemuaTagihanSiswa($this->siswa, $this->ta->id);
        // 12 bulan SPP + 1 tagihan DSP
        $this->assertEquals(13, $count);

        // 3. Cek pengurutan: DSP di urutan pertama (bulan IS NULL), lalu SPP Juli s.d. Juni
        $tagihans = Tagihan::where('siswa_id', $this->siswa->id)->urutTahunAjaran()->get();
        $this->assertCount(13, $tagihans);

        // Tagihan pertama harus DSP
        $this->assertEquals($posDsp->id, $tagihans->first()->pos_biaya_id);
        $this->assertNull($tagihans->first()->bulan);
        $this->assertEquals(3500000, $tagihans->first()->nominal_tagihan);

        // Tagihan kedua harus SPP bulan 7 (Juli)
        $this->assertEquals(7, $tagihans->get(1)->bulan);

        // Tagihan terakhir harus SPP bulan 6 (Juni tahun depan)
        $this->assertEquals(6, $tagihans->last()->bulan);
    }
}
