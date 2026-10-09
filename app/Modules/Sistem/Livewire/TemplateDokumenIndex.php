<?php

namespace App\Modules\Sistem\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Core\Services\DocumentEngine;
use App\Modules\Sistem\Models\DocumentSigner;
use App\Modules\Sistem\Models\DocumentTemplate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Template Dokumen & Penanda Tangan')]
class TemplateDokumenIndex extends Component
{
    use GuardsPermission;

    public bool $showModal = false;
    public ?int $selectedTemplateId = null;

    // Form fields
    public string $nama = '';
    public ?string $deskripsi = null;
    public string $orientasi = 'portrait';
    public string $titimangsa_default = 'Garut, {tanggal}';
    public bool $tampilkan_kop = true;

    // Signers list
    public array $signers = [];

    protected function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:150'],
            'deskripsi' => ['nullable', 'string', 'max:255'],
            'orientasi' => ['required', 'in:portrait,landscape'],
            'titimangsa_default' => ['required', 'string', 'max:100'],
            'tampilkan_kop' => ['boolean'],
            'signers.*.kolom_ke' => ['required', 'integer', 'between:1,3'],
            'signers.*.judul_jabatan' => ['required', 'string', 'max:100'],
            'signers.*.sumber' => ['required', 'string'],
            'signers.*.nama_pejabat' => ['nullable', 'string', 'max:150'],
            'signers.*.nip_pejabat' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function edit(int $id): void
    {
        $this->guard('sistem.template.edit');
        $tpl = DocumentTemplate::with('signers')->findOrFail($id);
        $this->selectedTemplateId = $tpl->id;
        $this->nama = $tpl->nama;
        $this->deskripsi = $tpl->deskripsi;
        $this->orientasi = $tpl->orientasi;
        $this->titimangsa_default = $tpl->titimangsa_default;
        $this->tampilkan_kop = $tpl->tampilkan_kop;

        $this->signers = $tpl->signers->map(fn ($s) => [
            'id' => $s->id,
            'kolom_ke' => $s->kolom_ke,
            'judul_jabatan' => $s->judul_jabatan,
            'sumber' => $s->sumber,
            'nama_pejabat' => $s->nama_pejabat,
            'nip_pejabat' => $s->nip_pejabat,
        ])->toArray();

        $this->showModal = true;
    }

    public function addSigner(): void
    {
        $nextCol = count($this->signers) + 1;
        if ($nextCol > 3) $nextCol = 3;

        $this->signers[] = [
            'id' => null,
            'kolom_ke' => $nextCol,
            'judul_jabatan' => 'Pejabat',
            'sumber' => 'custom',
            'nama_pejabat' => '',
            'nip_pejabat' => '',
        ];
    }

    public function removeSigner(int $index): void
    {
        unset($this->signers[$index]);
        $this->signers = array_values($this->signers);
    }

    public function save(): void
    {
        $this->guard('sistem.template.edit');
        $this->validate();

        $tpl = DocumentTemplate::findOrFail($this->selectedTemplateId);
        $tpl->update([
            'nama' => $this->nama,
            'deskripsi' => $this->deskripsi,
            'orientasi' => $this->orientasi,
            'titimangsa_default' => $this->titimangsa_default,
            'tampilkan_kop' => $this->tampilkan_kop,
        ]);

        $tpl->signers()->delete();
        foreach ($this->signers as $idx => $s) {
            $tpl->signers()->create([
                'kolom_ke' => $s['kolom_ke'],
                'judul_jabatan' => $s['judul_jabatan'],
                'sumber' => $s['sumber'],
                'nama_pejabat' => $s['nama_pejabat'] ?: null,
                'nip_pejabat' => $s['nip_pejabat'] ?: null,
                'urutan' => $idx + 1,
            ]);
        }

        $this->showModal = false;
        session()->flash('status', "Pengaturan template {$tpl->nama} berhasil disimpan.");
    }

    public function testCetak(int $id)
    {
        $this->guard('sistem.template.lihat');
        $tpl = DocumentTemplate::with('signers')->findOrFail($id);

        $sampleHtml = '
            <p>Ini adalah dokumen pengujian format cetak PDF SIRAGA untuk memverifikasi penataan kop surat, format tabel, titimangsa, dan susunan penanda tangan.</p>
            <table class="content-table">
                <thead>
                    <tr>
                        <th style="width: 30px;">No</th>
                        <th>NISN</th>
                        <th>Nama Siswa (Sensitif)</th>
                        <th>Kelas / Rombel</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>0063473065</td>
                        <td>ABAN GYMNASTIAR (Contoh)</td>
                        <td>X PPLG 1</td>
                        <td>Hadir</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>0071234567</td>
                        <td>SITI AISYAH (Contoh)</td>
                        <td>X PPLG 1</td>
                        <td>Hadir</td>
                    </tr>
                </tbody>
            </table>
        ';

        $pdf = DocumentEngine::createPdf(
            title: strtoupper($tpl->nama),
            content: $sampleHtml,
            options: [
                'template_kode' => $tpl->kode,
                'subtitle' => 'Tahun Ajaran 2024/2025 Semester Ganjil',
                'nomor_surat' => '421.5/001/SMK-WKR/X/2024',
                'context' => [
                    'wali_kelas' => 'Beni Iskandar, S.Kom.',
                    'pembimbing_rayon' => 'Rina Marlina, M.Pd.',
                ],
            ]
        );

        return response()->streamDownload(
            fn () => print($pdf->output()),
            "Uji_Cetak_{$tpl->kode}.pdf"
        );
    }

    public function render()
    {
        $templates = DocumentTemplate::with('signers')->get();

        return view('sistem::livewire.template-dokumen-index', [
            'templates' => $templates,
        ]);
    }
}
