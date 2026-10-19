<?php

namespace App\Modules\Sarpras\Controllers;

use App\Core\Services\DocumentEngine;
use App\Modules\Sarpras\Models\Aset;
use App\Modules\Sarpras\Models\BeritaAcara;
use App\Modules\Sarpras\Models\Kategori;
use App\Modules\Sarpras\Models\Ruangan;
use App\Modules\Sistem\Models\School;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CetakDokumenController extends Controller
{
    /**
     * Cetak Berita Acara Serah Terima (BAST) Barang / Pengadaan
     */
    public function cetakBast(Request $request, $id = null)
    {
        $school = School::current();
        $nomorSurat = BeritaAcara::generateNomorSurat('serah_terima');
        $tanggalSurat = now()->translatedFormat('d F Y');

        // Ambil daftar aset yang diserahterimakan (bisa dari query params atau aset terbaru)
        $asetIds = $request->input('aset_ids', []);
        if (!empty($id)) {
            $asetIds = [$id];
        }

        $query = Aset::with(['kategori', 'ruangan']);
        if (!empty($asetIds)) {
            $query->whereIn('id', (array) $asetIds);
        } else {
            $query->latest()->take(5);
        }
        $asets = $query->get();

        $pihakPertama = $request->input('pihak_pertama', 'Penyedia Barang / Rekanan Pengadaan');
        $pihakKedua = $request->input('pihak_kedua', 'Koordinator Sarpras ' . ($school->nama ?? 'Sekolah'));

        $html = view('sarpras::pdf.bast', compact('asets', 'pihakPertama', 'pihakKedua', 'nomorSurat', 'tanggalSurat', 'school'))->render();

        $signers = [
            [
                'judul_jabatan' => 'Yang Menyerahkan (Pihak Pertama)',
                'nama_pejabat' => $pihakPertama,
                'nip_pejabat' => '-',
            ],
            [
                'judul_jabatan' => 'Yang Menerima (Pihak Kedua)',
                'nama_pejabat' => auth()->user()?->name ?? 'Koordinator Sarpras',
                'nip_pejabat' => auth()->user()?->pegawai?->nip ?? '-',
            ],
            [
                'judul_jabatan' => 'Mengetahui, Kepala Sekolah',
                'nama_pejabat' => $school?->kepala_sekolah ?? 'Kepala Sekolah',
                'nip_pejabat' => $school?->nip_kepsek ?? '-',
            ],
        ];

        return DocumentEngine::createPdf(
            'BERITA ACARA SERAH TERIMA BARANG (BAST)',
            $html,
            [
                'nomor_surat' => $nomorSurat,
                'subtitle' => 'PENGADAAN DAN PENERIMAAN INVENTARIS SEKOLAH',
                'orientasi' => 'portrait',
                'ukuran_kertas' => 'A4',
                'tampilkan_kop' => true,
                'signers' => $signers,
            ]
        )->stream('BAST_' . date('Ymd') . '.pdf');
    }

    /**
     * Cetak Berita Acara Pemusnahan / Penghapusan Barang Rusak Berat
     */
    public function cetakPemusnahan(Request $request, $id = null)
    {
        $school = School::current();
        $nomorSurat = BeritaAcara::generateNomorSurat('pemusnahan');
        $tanggalSurat = now()->translatedFormat('d F Y');

        $query = Aset::with(['kategori', 'ruangan'])->whereIn('kondisi', ['Rusak Berat', 'Dimusnahkan']);
        if (!empty($id)) {
            $query->where('id', $id);
        }
        $asets = $query->get();

        $html = view('sarpras::pdf.pemusnahan', compact('asets', 'nomorSurat', 'tanggalSurat', 'school'))->render();

        $signers = [
            [
                'judul_jabatan' => 'Panitia Pemeriksa / Sarpras',
                'nama_pejabat' => auth()->user()?->name ?? 'Koordinator Sarpras',
                'nip_pejabat' => auth()->user()?->pegawai?->nip ?? '-',
            ],
            [
                'judul_jabatan' => 'Petugas Tata Usaha',
                'nama_pejabat' => 'Kepala Tata Usaha',
                'nip_pejabat' => '-',
            ],
            [
                'judul_jabatan' => 'Menyetujui, Kepala Sekolah',
                'nama_pejabat' => $school?->kepala_sekolah ?? 'Kepala Sekolah',
                'nip_pejabat' => $school?->nip_kepsek ?? '-',
            ],
        ];

        return DocumentEngine::createPdf(
            'BERITA ACARA PEMUSNAHAN / PENGHAPUSAN BARANG INVENTARIS',
            $html,
            [
                'nomor_surat' => $nomorSurat,
                'subtitle' => 'PENGHAPUSAN ASET RUSAK BERAT DARI BUKU INVENTARIS',
                'orientasi' => 'portrait',
                'ukuran_kertas' => 'A4',
                'tampilkan_kop' => true,
                'signers' => $signers,
            ]
        )->stream('BA_Pemusnahan_' . date('Ymd') . '.pdf');
    }

    /**
     * Cetak Rekapitulasi Laporan Aset Sekolah dalam Format PDF Landscape
     */
    public function cetakLaporanPdf(Request $request)
    {
        $school = School::current();
        $query = Aset::with(['kategori', 'ruangan']);

        if ($request->filled('ruangan')) {
            $query->where('ruangan_id', $request->ruangan);
        }
        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }
        if ($request->filled('kondisi')) {
            $query->where('kondisi', $request->kondisi);
        }
        if ($request->filled('sumber')) {
            $query->where('sumber_dana', $request->sumber);
        }

        $asets = $query->orderBy('nama_barang')->get();
        $totalNilai = $asets->sum('harga_perolehan');

        $html = view('sarpras::pdf.laporan', compact('asets', 'totalNilai'))->render();

        $signers = [
            [
                'judul_jabatan' => 'Pengelola Sarana & Prasarana',
                'nama_pejabat' => auth()->user()?->name ?? 'Koordinator Sarpras',
                'nip_pejabat' => auth()->user()?->pegawai?->nip ?? '-',
            ],
            [
                'judul_jabatan' => 'Mengetahui, Kepala Sekolah',
                'nama_pejabat' => $school?->kepala_sekolah ?? 'Kepala Sekolah',
                'nip_pejabat' => $school?->nip_kepsek ?? '-',
            ],
        ];

        return DocumentEngine::createPdf(
            'DAFTAR REKAPITULASI INVENTARIS SARANA & PRASARANA',
            $html,
            [
                'subtitle' => 'LAPORAN STATUS DAN KONDISI ASET TERKINI',
                'orientasi' => 'landscape',
                'ukuran_kertas' => 'A4',
                'tampilkan_kop' => true,
                'signers' => $signers,
            ]
        )->stream('Rekap_Sarpras_' . date('Ymd') . '.pdf');
    }

    /**
     * Cetak Lembar Stiker Label QR Code Aset
     */
    public function cetakLabelQr(Request $request)
    {
        $school = School::current();
        $query = Aset::with(['kategori', 'ruangan'])->where('kondisi', '!=', 'Dimusnahkan');

        if ($request->filled('ids')) {
            $ids = array_filter(explode(',', (string) $request->ids), 'is_numeric');
            if (!empty($ids)) {
                $query->whereIn('id', $ids);
            }
        }

        if ($request->filled('ruangan')) {
            $query->where('ruangan_id', $request->ruangan);
        }
        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }

        $asets = $query->orderBy('kode_aset')->get();

        return view('sarpras::print.label-qr', compact('asets', 'school'));
    }
}
