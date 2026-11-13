<?php

namespace Tests\Feature\BeasiswaOta;

use App\Models\User;
use App\Modules\BeasiswaOta\Models\Alokasi;
use App\Modules\BeasiswaOta\Models\AlokasiDetail;
use App\Modules\BeasiswaOta\Models\DonasiMasuk;
use App\Modules\BeasiswaOta\Models\Donatur;
use App\Modules\BeasiswaOta\Models\Kelompok;
use App\Modules\BeasiswaOta\Services\OtaDocumentService;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Sistem\Models\School;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TandaTerimaDonasiPdfTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Donatur $donatur;
    protected Kelompok $kelompok;
    protected DonasiMasuk $donasi;
    protected string $batchKode = 'BATCH-OTA-202611-001';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);

        $this->admin = User::factory()->create([
            'username' => 'admin_ota_cetak',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->admin->assignRole('Super Admin');

        $this->kelompok = Kelompok::create([
            'kode_kelompok' => 'KLP-01',
            'nama_kelompok' => 'Kelompok Peduli 1',
            'target_nominal' => 2000000,
        ]);

        $this->donatur = Donatur::create([
            'nama' => 'Bapak Muhammad Hidayat',
            'tipe' => 'rutin',
            'nomor_telepon' => '081234567890',
            'is_aktif' => true,
        ]);

        $this->donasi = DonasiMasuk::create([
            'donatur_id' => $this->donatur->id,
            'kelompok_id' => $this->kelompok->id,
            'nominal' => 250000,
            'batch_kode' => $this->batchKode,
            'tanggal_donasi' => now(),
            'tipe_donatur' => 'rutin',
            'keterangan_periode' => 'November 2026',
            'keterangan' => 'Ini catatan khusus donatur yang tidak boleh tampil di cetak tanda terima',
        ]);
    }

    public function test_terbilang_has_proper_word_spacing()
    {
        $this->assertEquals('Dua Ratus Lima Puluh Ribu', OtaDocumentService::terbilang(250000));
        $this->assertEquals('Seratus Lima Puluh Ribu', OtaDocumentService::terbilang(150000));
        $this->assertEquals('Dua Juta Lima Ratus Lima Puluh Ribu', OtaDocumentService::terbilang(2550000));
        $this->assertEquals('Seratus Lima Belas Ribu', OtaDocumentService::terbilang(115000));
    }

    public function test_public_verification_page_can_be_accessed_without_login()
    {
        $response = $this->get(route('beasiswa-ota.verifikasi-donasi', $this->donasi->nomor_transaksi));

        $response->assertStatus(200);
        $response->assertSee('Dokumen Resmi Terverifikasi');
        $response->assertSee($this->donasi->nomor_transaksi);
        $response->assertSee('Bapak Muhammad Hidayat');
        $response->assertSee('Kelompok Peduli 1');
        $response->assertSee('250.000');
        $response->assertSee('November 2026');
    }

    public function test_public_short_verification_route_can_be_accessed()
    {
        $response = $this->get(route('beasiswa-ota.v', $this->donasi->nomor_transaksi));

        $response->assertStatus(200);
        $response->assertSee('Dokumen Resmi Terverifikasi');
        $response->assertSee($this->donasi->nomor_transaksi);
    }

    public function test_public_verification_kolektif_page_can_be_accessed_without_login()
    {
        $response = $this->get(route('beasiswa-ota.verifikasi-kolektif', $this->batchKode));

        $response->assertStatus(200);
        $response->assertSee('Setoran Kolektif Terverifikasi');
        $response->assertSee($this->batchKode);
        $response->assertSee('Kelompok Peduli 1');
        $response->assertSee('Bapak Muhammad Hidayat');
        $response->assertSee('250.000');
    }

    public function test_tanda_terima_pdf_generation_single_page_and_contains_qr_code()
    {
        $pdf = OtaDocumentService::createTandaTerimaPdf($this->donasi);
        $domPdf = $pdf->getDomPDF();
        $domPdf->render();

        // Pastikan pas 1 lembar
        $pageCount = $domPdf->getCanvas()->get_page_count();
        $this->assertEquals(1, $pageCount, 'Tanda terima PDF harus pas tepat 1 lembar.');

        // Pastikan mengandung PNG QR Code dan nomor transaksi
        $verifyUrl = route('beasiswa-ota.v', $this->donasi->nomor_transaksi);
        $html = view('beasiswaota::pdf.tanda-terima', [
            'school' => School::current(),
            'donasi' => $this->donasi,
            'terbilang' => OtaDocumentService::terbilang((float) $this->donasi->nominal) . ' Rupiah',
            'bendaharaNama' => 'Bendahara OTA',
            'titimangsa' => 'Garut, ' . now()->translatedFormat('d F Y'),
            'qrCodePng' => \App\Core\Services\QrCodeService::pngDataUri($verifyUrl, 120),
            'qrCodeSvg' => '',
            'kopPath' => null,
        ])->render();

        $this->assertStringContainsString('data:image/png;base64,', $html);
        $this->assertStringContainsString('Scan untuk Verifikasi Sah', $html);
        $this->assertStringContainsString($this->donasi->nomor_transaksi, $html);
        $this->assertStringContainsString('receipt-container', $html);
        $this->assertStringNotContainsString('kop-divider', $html);

        // Pastikan QR code hasil generate dapat didecode secara sempurna oleh scanner QR standar
        $pngDataUri = \App\Core\Services\QrCodeService::pngDataUri($verifyUrl, 180);
        $rawPng = base64_decode(explode(',', $pngDataUri)[1]);
        $decoded = (string)(new \chillerlan\QRCode\QRCode())->readFromBlob($rawPng);
        $this->assertEquals($verifyUrl, $decoded);
    }

    public function test_tanda_terima_pdf_does_not_contain_catatan_keterangan()
    {
        $html = view('beasiswaota::pdf.tanda-terima', [
            'school' => School::current(),
            'donasi' => $this->donasi,
            'terbilang' => OtaDocumentService::terbilang((float) $this->donasi->nominal) . ' Rupiah',
            'bendaharaNama' => 'Bendahara OTA',
            'titimangsa' => 'Garut, ' . now()->translatedFormat('d F Y'),
            'qrCodePng' => '',
            'qrCodeSvg' => '',
            'kopPath' => null,
        ])->render();

        $this->assertStringNotContainsString('Keterangan / Catatan', $html);
        $this->assertStringNotContainsString('Ini catatan khusus donatur yang tidak boleh tampil', $html);
    }

    public function test_tanda_terima_pdf_controller_stream_download()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('beasiswa-ota.cetak.tanda-terima', $this->donasi->id));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_tanda_terima_kolektif_pdf_controller_stream_download()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('beasiswa-ota.cetak.tanda-terima-kolektif', $this->batchKode));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_public_verification_penyaluran_page_can_be_accessed_without_login()
    {
        $siswa = Siswa::create([
            'nama' => 'Asti Wulan Sari',
            'nisn' => '0084832736',
            'nipd' => '12401008',
            'jk' => 'P',
        ]);
        $alokasi = Alokasi::create([
            'nomor_kwitansi' => 'OTA-KW-TEST-0001',
            'tipe_alokasi' => 'insidental_fleksibel',
            'siswa_id' => $siswa->id,
            'tanggal_alokasi' => now(),
            'total_nominal' => 200000,
        ]);
        AlokasiDetail::create([
            'alokasi_id' => $alokasi->id,
            'nama_komponen' => 'SPP Bulanan',
            'jenis_komponen' => 'spp',
            'bulan' => 'November',
            'tahun' => 2026,
            'nominal' => 200000,
        ]);

        $response = $this->get(route('beasiswa-ota.verifikasi-penyaluran', $alokasi->nomor_kwitansi));
        $response->assertStatus(200);
        $response->assertSee('Penyaluran Beasiswa Terverifikasi Sah');
        $response->assertSee($alokasi->nomor_kwitansi);
        $response->assertSee('Asti Wulan Sari');
        $response->assertSee('SPP Bulanan');
    }

    public function test_public_short_verification_penyaluran_route()
    {
        $siswa = Siswa::create([
            'nama' => 'Asti Wulan Sari',
            'nisn' => '0084832736',
            'nipd' => '12401008',
            'jk' => 'P',
        ]);
        $alokasi = Alokasi::create([
            'nomor_kwitansi' => 'OTA-KW-TEST-0002',
            'tipe_alokasi' => 'insidental_fleksibel',
            'siswa_id' => $siswa->id,
            'tanggal_alokasi' => now(),
            'total_nominal' => 200000,
        ]);

        $response = $this->get(route('beasiswa-ota.v-kwitansi', $alokasi->nomor_kwitansi));
        $response->assertStatus(200);
        $response->assertSee('Penyaluran Beasiswa Terverifikasi Sah');
    }

    public function test_kwitansi_penyaluran_pdf_compact_and_contains_qr_code()
    {
        $siswa = Siswa::create([
            'nama' => 'Asti Wulan Sari',
            'nisn' => '0084832736',
            'nipd' => '12401008',
            'jk' => 'P',
        ]);
        $alokasi = Alokasi::create([
            'nomor_kwitansi' => 'OTA-KW-TEST-0003',
            'tipe_alokasi' => 'insidental_fleksibel',
            'siswa_id' => $siswa->id,
            'tanggal_alokasi' => now(),
            'total_nominal' => 200000,
            'surplus_dialihkan' => 50000,
        ]);
        AlokasiDetail::create([
            'alokasi_id' => $alokasi->id,
            'nama_komponen' => 'SPP Bulanan',
            'jenis_komponen' => 'spp',
            'bulan' => 'November',
            'tahun' => 2026,
            'nominal' => 200000,
            'keterangan' => 'Keterangan internal yang harus hilang',
        ]);

        $pdf = OtaDocumentService::createKwitansiPenyaluranPdf($alokasi);
        $this->assertEquals(1, $pdf->getDomPDF()->getCanvas()->get_page_count());

        $html = view('beasiswaota::pdf.kwitansi-penyaluran', [
            'school' => School::current(),
            'alokasi' => $alokasi,
            'terbilang' => OtaDocumentService::terbilang((float) $alokasi->total_nominal) . ' Rupiah',
            'bendaharaNama' => 'Fitria Amalia, S.Pd.',
            'kepsekNama' => 'Kunedi, S.Si.',
            'kepsekNip' => '-',
            'titimangsa' => 'Garut, 13 November 2026',
            'verifyUrl' => route('beasiswa-ota.v-kwitansi', $alokasi->nomor_kwitansi),
            'qrCodePng' => \App\Core\Services\QrCodeService::pngDataUri(route('beasiswa-ota.v-kwitansi', $alokasi->nomor_kwitansi), 180),
            'kopPath' => null,
        ])->render();

        // Validasi header Komponen dan hilangnya Keterangan & Surplus
        $this->assertStringContainsString('<th>Komponen</th>', $html);
        $this->assertStringNotContainsString('<th>Keterangan</th>', $html);
        $this->assertStringNotContainsString('Surplus Kelompok Dialihkan ke Kas Insidental:', $html);
        $this->assertStringNotContainsString('(spp)', strtolower($html));
        $this->assertStringContainsString('data:image/png;base64,', $html);
        $this->assertStringContainsString('Scan untuk Verifikasi Sah', $html);
    }

    public function test_kwitansi_penyaluran_pdf_controller_stream_download()
    {
        $this->actingAs($this->admin);

        $siswa = Siswa::create([
            'nama' => 'Asti Wulan Sari',
            'nisn' => '0084832736',
            'nipd' => '12401008',
            'jk' => 'P',
        ]);
        $alokasi = Alokasi::create([
            'nomor_kwitansi' => 'OTA-KW-TEST-0004',
            'tipe_alokasi' => 'insidental_fleksibel',
            'siswa_id' => $siswa->id,
            'tanggal_alokasi' => now(),
            'total_nominal' => 200000,
        ]);

        $response = $this->get(route('beasiswa-ota.cetak.kwitansi', $alokasi->id));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_sertifikat_donatur_pdf_generation_single_page_and_no_nominal()
    {
        $pdf = OtaDocumentService::createSertifikatPdf($this->donasi);
        $domPdf = $pdf->getDomPDF();

        // Pastikan pas tepat 1 lembar A4 landscape
        $this->assertEquals(1, $domPdf->getCanvas()->get_page_count(), 'Sertifikat donatur PDF harus pas tepat 1 lembar.');

        $html = view('beasiswaota::pdf.sertifikat-donatur', [
            'school' => School::current(),
            'donasi' => $this->donasi,
            'nomorSertifikat' => 'OTA/SRT/2026/11/0001',
            'terbilang' => 'Dua Ratus Lima Puluh Ribu Rupiah',
            'bgBase64' => null,
            'kepsekNama' => 'Kunedi, S.Si., Gr.',
            'kepsekNip' => '-',
            'ketuaOtaNama' => 'Mimin Muhaemin, S.Pd.I.',
            'titimangsa' => 'Garut, 13 November 2026',
            'verifyUrl' => route('beasiswa-ota.v-sertifikat', $this->donasi->id),
            'qrCodePng' => \App\Core\Services\QrCodeService::pngDataUri(route('beasiswa-ota.v-sertifikat', $this->donasi->id), 160),
        ])->render();

        // Cek judul, penerima, dan ketiadaan nominal uang
        $this->assertStringContainsString('PIAGAM PENGHARGAAN', $html);
        $this->assertStringContainsString('OTA/SRT/2026/11/0001', $html);
        $this->assertStringContainsString($this->donasi->donatur->nama, $html);
        $this->assertStringContainsString('Orang Tua Asuh (OTA)', $html);
        $this->assertStringNotContainsString('250.000', $html);
        $this->assertStringNotContainsString('Rp ', $html);
        $this->assertStringNotContainsString('Dua Ratus Lima Puluh Ribu', $html);
        $this->assertStringContainsString('data:image/png;base64,', $html);
    }

    public function test_public_verification_sertifikat_page()
    {
        $response = $this->get(route('beasiswa-ota.v-sertifikat', $this->donasi->id));

        $response->assertStatus(200);
        $response->assertSee('Piagam Penghargaan Resmi Terverifikasi');
        $response->assertSee($this->donasi->donatur->nama);
        $response->assertSee('Koordinator OTA');
        $response->assertSee('Kepala Sekolah');
    }

    public function test_sertifikat_pdf_controller_stream_download()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('beasiswa-ota.cetak.sertifikat', $this->donasi->id));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}
