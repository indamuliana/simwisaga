<?php

namespace App\Modules\Kesiswaan\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Core\Services\DocumentEngine;
use App\Core\Support\DataMasker;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Kesiswaan\Models\Program;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Kesiswaan\Services\DapodikImporter;
use App\Modules\Kesiswaan\Services\SingleSiswaDocumentExporter;
use App\Modules\Kesiswaan\Services\SiswaExporter;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

#[Layout('components.layouts.app')]
#[Title('Data Induk Siswa')]
class SiswaIndex extends Component
{
    use GuardsPermission, WithPagination, \App\Core\Livewire\Traits\WithSorting;

    public function mount(): void
    {
        $this->sortField = 'nama';
    }

    // Filters & Search
    public string $search = '';
    public ?int $filterRombel = null;
    public ?int $filterRayon = null;
    public ?int $filterProgram = null;
    public string $filterStatus = '';

    // Detail Modal
    public bool $showDetailModal = false;
    public ?Siswa $selectedSiswa = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterRombel(): void
    {
        $this->resetPage();
    }

    public function updatedFilterRayon(): void
    {
        $this->resetPage();
    }

    public function updatedFilterProgram(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function showDetail(int $id): void
    {
        $this->selectedSiswa = Siswa::with(['rombel', 'rayon', 'program', 'orangTuas'])->findOrFail($id);
        $this->showDetailModal = true;
    }

    public function delete(int $id): void
    {
        $this->guard('kesiswaan.siswa.hapus');
        $siswa = Siswa::findOrFail($id);
        $nama = $siswa->nama;
        $user = $siswa->user;
        $siswa->delete();
        if ($user) {
            $user->delete();
        }
        session()->flash('status', "Data siswa {$nama} berhasil dihapus.");
    }

    public function cetakDaftar()
    {
        $this->guard('kesiswaan.siswa.cetak');

        $query = Siswa::with(['rombel', 'rayon', 'program']);
        if ($this->filterRombel) {
            $query->where('rombel_id', $this->filterRombel);
        }
        if ($this->filterRayon) {
            $query->where('rayon_id', $this->filterRayon);
        }
        if ($this->filterProgram) {
            $query->where('program_id', $this->filterProgram);
        }
        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $siswas = $query->orderBy('nama')->get();
        $rombelNama = $this->filterRombel ? Rombel::find($this->filterRombel)?->nama : 'SEMUA KELAS';
        $rayonNama = $this->filterRayon ? Rayon::find($this->filterRayon)?->nama : null;

        $rows = '';
        foreach ($siswas as $idx => $s) {
            $no = $idx + 1;
            $nis = $s->nipd ?: $s->nisn;
            $jk = $s->jk === 'L' ? 'L' : 'P';
            $rombel = $s->rombel?->nama ?? '-';
            $rayon = $s->rayon?->nama ?? '-';
            $program = $s->program?->nama ?? 'Reguler';

            $rows .= "
                <tr>
                    <td style='text-align: center;'>{$no}</td>
                    <td style='text-align: center; font-family: monospace; font-weight: bold;'>{$nis}</td>
                    <td><b>{$s->nama}</b></td>
                    <td style='text-align: center;'>{$jk}</td>
                    <td style='text-align: center;'><b>{$rombel}</b></td>
                    <td style='text-align: center;'>{$rayon}</td>
                    <td style='text-align: center;'>{$program}</td>
                </tr>
            ";
        }

        $html = "
            <table class='content-table'>
                <thead>
                    <tr>
                        <th style='width: 30px; text-align: center;'>No</th>
                        <th style='width: 80px; text-align: center;'>NIS</th>
                        <th>Nama Siswa</th>
                        <th style='width: 30px; text-align: center;'>JK</th>
                        <th style='width: 80px; text-align: center;'>Rombel</th>
                        <th style='width: 80px; text-align: center;'>Rayon</th>
                        <th style='width: 80px; text-align: center;'>Program</th>
                    </tr>
                </thead>
                <tbody>
                    {$rows}
                </tbody>
            </table>
        ";

        $subtitle = "Rombel: {$rombelNama}";
        if ($rayonNama) {
            $subtitle .= " | Rayon: {$rayonNama}";
        }

        $pdf = DocumentEngine::createPdf(
            title: 'DAFTAR INDUK SISWA SMK WIKRAMA 1 GARUT',
            content: $html,
            options: [
                'subtitle' => $subtitle,
                'template_kode' => 'daftar_siswa',
                'context' => [
                    'wali_kelas' => $this->filterRombel ? Rombel::find($this->filterRombel)?->waliKelas : null,
                    'pembimbing_rayon' => $this->filterRayon ? Rayon::find($this->filterRayon)?->pembimbing : null,
                ],
            ]
        );

        return response()->streamDownload(
            fn () => print($pdf->output()),
            "Daftar_Siswa_{$rombelNama}.pdf"
        );
    }

    public function exportExcel(string $type = 'ringkasan')
    {
        $this->guard('kesiswaan.siswa.lihat');

        $query = Siswa::with(['rombel', 'rayon', 'program', 'orangTuas']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nama', 'like', "%{$this->search}%")
                    ->orWhere('nisn', 'like', "%{$this->search}%")
                    ->orWhere('nipd', 'like', "%{$this->search}%")
                    ->orWhere('nik', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterRombel) {
            $query->where('rombel_id', $this->filterRombel);
        }

        if ($this->filterRayon) {
            $query->where('rayon_id', $this->filterRayon);
        }

        if ($this->filterProgram) {
            $query->where('program_id', $this->filterProgram);
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $siswas = $query->orderBy('nama')->get();

        $rombelNama = $this->filterRombel ? (Rombel::find($this->filterRombel)?->nama ?? 'Kelas') : 'Semua_Rombel';
        $rombelNamaClean = preg_replace('/[^A-Za-z0-9_\-]/', '_', $rombelNama);
        $timestamp = now()->format('Ymd_His');

        $filename = ($type === 'lengkap')
            ? "Data_Siswa_Lengkap_DAPODIK_{$rombelNamaClean}_{$timestamp}.xlsx"
            : "Data_Siswa_Ringkasan_{$rombelNamaClean}_{$timestamp}.xlsx";

        $exporter = new SiswaExporter();
        $spreadsheet = ($type === 'lengkap')
            ? $exporter->exportLengkap($siswas)
            : $exporter->exportRingkasan($siswas);

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    public function downloadDetailSiswa(int $id, string $format = 'pdf')
    {
        $this->guard('kesiswaan.siswa.lihat');
        $siswa = Siswa::findOrFail($id);
        $exporter = new SingleSiswaDocumentExporter();

        return match ($format) {
            'word' => $exporter->downloadWord($siswa),
            'excel' => $exporter->downloadExcel($siswa),
            default => $exporter->downloadPdf($siswa),
        };
    }

    /**
     * Menghapus seluruh data siswa dan akun user siswa & orang tua
     */
    public function resetAllSiswa(): void
    {
        $this->guard('kesiswaan.siswa.hapus');

        $cleaned = DapodikImporter::cleanSiswaData();
        $this->resetPage();

        session()->flash('status', "Seluruh data siswa ({$cleaned['siswas']} siswa) dan {$cleaned['users']} akun pengguna (Siswa & Orang Tua) telah berhasil dihapus bersih dari sistem.");
    }

    public function render()
    {
        $query = Siswa::with(['rombel', 'rayon', 'program']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nama', 'like', "%{$this->search}%")
                    ->orWhere('nisn', 'like', "%{$this->search}%")
                    ->orWhere('nipd', 'like', "%{$this->search}%")
                    ->orWhere('nik', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterRombel) {
            $query->where('rombel_id', $this->filterRombel);
        }

        if ($this->filterRayon) {
            $query->where('rayon_id', $this->filterRayon);
        }

        if ($this->filterProgram) {
            $query->where('program_id', $this->filterProgram);
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $siswas = $query->orderBy($this->sortField, $this->sortDirection)->paginate(20);
        $rombels = Rombel::orderBy('tingkat')->orderBy('nama')->get();
        $rayons = Rayon::orderBy('nama')->get();
        $programs = Program::where('is_active', true)->orderBy('nama')->get();

        return view('kesiswaan::livewire.siswa-index', [
            'siswas' => $siswas,
            'rombels' => $rombels,
            'rayons' => $rayons,
            'programs' => $programs,
        ]);
    }
}
