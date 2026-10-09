<?php

namespace Database\Seeders;

use App\Modules\Sistem\Models\DocumentSigner;
use App\Modules\Sistem\Models\DocumentTemplate;
use Illuminate\Database\Seeder;

class DocumentTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'kode' => 'rekap_absensi_rombel',
                'nama' => 'Rekap Absensi Rombel',
                'deskripsi' => 'Cetak laporan absensi harian dan bulanan per rombel',
                'orientasi' => 'portrait',
                'titimangsa_default' => 'Garut, {tanggal}',
                'signers' => [
                    ['kolom_ke' => 1, 'judul_jabatan' => 'Wali Kelas', 'sumber' => 'wali_kelas'],
                    ['kolom_ke' => 2, 'judul_jabatan' => 'Wakasek Kesiswaan', 'sumber' => 'kesiswaan', 'nama_pejabat' => 'Wakasek Kesiswaan'],
                    ['kolom_ke' => 3, 'judul_jabatan' => 'Mengetahui, Kepala Sekolah', 'sumber' => 'kepala_sekolah'],
                ],
            ],
            [
                'kode' => 'rekap_absensi_rayon',
                'nama' => 'Rekap Absensi Rayon',
                'deskripsi' => 'Cetak laporan absensi rekapitulasi per rayon wilayah',
                'orientasi' => 'portrait',
                'titimangsa_default' => 'Garut, {tanggal}',
                'signers' => [
                    ['kolom_ke' => 1, 'judul_jabatan' => 'Pembimbing Rayon', 'sumber' => 'pembimbing_rayon'],
                    ['kolom_ke' => 2, 'judul_jabatan' => 'Wakasek Kesiswaan', 'sumber' => 'kesiswaan', 'nama_pejabat' => 'Wakasek Kesiswaan'],
                    ['kolom_ke' => 3, 'judul_jabatan' => 'Mengetahui, Kepala Sekolah', 'sumber' => 'kepala_sekolah'],
                ],
            ],
            [
                'kode' => 'surat_peringatan',
                'nama' => 'Surat Peringatan & Panggilan Ortu',
                'deskripsi' => 'Surat resmi peringatan absensi/ketidakhadiran siswa',
                'orientasi' => 'portrait',
                'titimangsa_default' => 'Garut, {tanggal}',
                'signers' => [
                    ['kolom_ke' => 1, 'judul_jabatan' => 'Wali Kelas', 'sumber' => 'wali_kelas'],
                    ['kolom_ke' => 2, 'judul_jabatan' => 'Orang Tua / Wali Siswa', 'sumber' => 'orang_tua'],
                    ['kolom_ke' => 3, 'judul_jabatan' => 'Mengetahui, Kepala Sekolah', 'sumber' => 'kepala_sekolah'],
                ],
            ],
            [
                'kode' => 'leger_nilai',
                'nama' => 'Leger Nilai Semester',
                'deskripsi' => 'Rekapitulasi nilai seluruh mapel per rombel',
                'orientasi' => 'landscape',
                'titimangsa_default' => 'Garut, {tanggal}',
                'signers' => [
                    ['kolom_ke' => 1, 'judul_jabatan' => 'Wali Kelas', 'sumber' => 'wali_kelas'],
                    ['kolom_ke' => 2, 'judul_jabatan' => 'Wakasek Kurikulum', 'sumber' => 'kurikulum', 'nama_pejabat' => 'Wakasek Kurikulum'],
                    ['kolom_ke' => 3, 'judul_jabatan' => 'Mengetahui, Kepala Sekolah', 'sumber' => 'kepala_sekolah'],
                ],
            ],
            [
                'kode' => 'daftar_siswa',
                'nama' => 'Daftar Siswa Rombel',
                'deskripsi' => 'Daftar presensi dan biodata ringkas siswa per kelas',
                'orientasi' => 'portrait',
                'titimangsa_default' => 'Garut, {tanggal}',
                'signers' => [
                    ['kolom_ke' => 1, 'judul_jabatan' => 'Wali Kelas', 'sumber' => 'wali_kelas'],
                    ['kolom_ke' => 2, 'judul_jabatan' => 'Mengetahui, Kepala Sekolah', 'sumber' => 'kepala_sekolah'],
                ],
            ],
        ];

        foreach ($templates as $t) {
            $signers = $t['signers'];
            unset($t['signers']);

            $tpl = DocumentTemplate::updateOrCreate(['kode' => $t['kode']], $t);
            $tpl->signers()->delete();

            foreach ($signers as $idx => $s) {
                $s['urutan'] = $idx + 1;
                $tpl->signers()->create($s);
            }
        }
    }
}
