<?php

namespace App\Core\Services;

use App\Modules\Sistem\Models\DocumentTemplate;
use App\Modules\Sistem\Models\School;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdfWrapper;

class DocumentEngine
{
    /**
     * Membuat dokumen PDF standar A4 SIRAGA lengkap dengan kop surat dan penanda tangan.
     *
     * @param string $title Judul dokumen utama (misal: "DAFTAR SISWA KELAS X PPLG 1")
     * @param string $content HTML isi tabel/laporan
     * @param array $options Opsi tambahan (template_kode, orientasi, titimangsa, signers, context)
     */
    public static function createPdf(string $title, string $content, array $options = []): DomPdfWrapper
    {
        $school = School::current();
        $template = null;

        if (!empty($options['template_kode'])) {
            $template = DocumentTemplate::findByKode($options['template_kode']);
        }

        $orientasi = $options['orientasi'] ?? $template?->orientasi ?? 'portrait';
        $ukuranKertas = $options['ukuran_kertas'] ?? $template?->ukuran_kertas ?? 'A4';
        $tampilkanKop = $options['tampilkan_kop'] ?? $template?->tampilkan_kop ?? true;

        $titimangsa = $options['titimangsa'] ?? null;
        if (!$titimangsa) {
            $pattern = $template?->titimangsa_default ?? 'Garut, {tanggal}';
            $titimangsa = str_replace('{tanggal}', now()->translatedFormat('d F Y'), $pattern);
        }

        // Resolusi daftar penanda tangan
        $signers = $options['signers'] ?? static::resolveSigners($template, $school, $options['context'] ?? []);

        $viewData = [
            'school' => $school,
            'title' => $title,
            'subtitle' => $options['subtitle'] ?? null,
            'nomorSurat' => $options['nomor_surat'] ?? null,
            'content' => $content,
            'tampilkanKop' => $tampilkanKop,
            'titimangsa' => $titimangsa,
            'signers' => $signers,
        ];

        return Pdf::loadView('pdf.layout', $viewData)
            ->setPaper($ukuranKertas, $orientasi);
    }

    /**
     * Menyusun penanda tangan dinamis dari template dan konteks data.
     */
    protected static function resolveSigners(?DocumentTemplate $template, ?School $school, array $context = []): array
    {
        if (!$template || $template->signers->isEmpty()) {
            return [
                [
                    'judul_jabatan' => 'Mengetahui, Kepala Sekolah',
                    'nama_pejabat' => $school?->kepala_sekolah ?? 'H. Dedik, M.Pd.',
                    'nip_pejabat' => $school?->nip_kepsek ?? '-',
                ]
            ];
        }

        $resolved = [];

        foreach ($template->signers as $signer) {
            $nama = $signer->nama_pejabat;
            $nip = $signer->nip_pejabat;

            switch ($signer->sumber) {
                case 'kepala_sekolah':
                    $nama = $school?->kepala_sekolah ?? $nama ?? 'Kepala Sekolah';
                    $nip = $school?->nip_kepsek ?? $nip;
                    break;

                case 'wali_kelas':
                    if (isset($context['wali_kelas'])) {
                        $wk = $context['wali_kelas'];
                        $nama = is_object($wk) ? ($wk->name ?? $wk->nama) : (string) $wk;
                        $nip = is_object($wk) ? ($wk->nip ?? '-') : '-';
                    }
                    break;

                case 'pembimbing_rayon':
                    if (isset($context['pembimbing_rayon'])) {
                        $pr = $context['pembimbing_rayon'];
                        $nama = is_object($pr) ? ($pr->name ?? $pr->nama) : (string) $pr;
                        $nip = is_object($pr) ? ($pr->nip ?? '-') : '-';
                    }
                    break;

                case 'orang_tua':
                    $nama = '( ........................................ )';
                    $nip = null;
                    break;

                case 'kurikulum':
                    $nama = $context['kurikulum'] ?? $nama ?? 'Wakasek Bid. Kurikulum';
                    break;

                case 'kesiswaan':
                    $nama = $context['kesiswaan'] ?? $nama ?? 'Wakasek Bid. Kesiswaan';
                    break;
            }

            $resolved[] = [
                'kolom_ke' => $signer->kolom_ke,
                'judul_jabatan' => $signer->judul_jabatan,
                'nama_pejabat' => $nama ?: '....................................',
                'nip_pejabat' => $nip,
            ];
        }

        return $resolved;
    }
}
