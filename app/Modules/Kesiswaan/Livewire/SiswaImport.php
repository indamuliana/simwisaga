<?php

namespace App\Modules\Kesiswaan\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Kesiswaan\Services\DapodikImporter;
use App\Modules\Kesiswaan\Services\SiswaExporter;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

#[Layout('components.layouts.app')]
#[Title('Import Data Siswa DAPODIK')]
class SiswaImport extends Component
{
    use GuardsPermission, WithFileUploads;

    public $file;
    public bool $isProcessing = false;
    public ?array $resultSummary = null;

    protected function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:20480'],
        ];
    }

    public function processImport(): void
    {
        $this->guard('kesiswaan.siswa.tambah');
        $this->validate();

        $path = $this->file->getRealPath();
        $importer = new DapodikImporter();

        $this->resultSummary = $importer->import($path);
        session()->flash('status', "Import berhasil! {$this->resultSummary['imported']} siswa baru ditambahkan, {$this->resultSummary['updated']} data siswa diperbarui.");
    }

    /**
     * Memproses file sample bawaan (DATABASE_SISWA_SAMPLE.xlsx) secara langsung
     */
    public function importSample(): void
    {
        $this->guard('kesiswaan.siswa.tambah');
        $samplePath = base_path('DATABASE_SISWA_SAMPLE.xlsx');

        if (!file_exists($samplePath)) {
            session()->flash('error', 'File DATABASE_SISWA_SAMPLE.xlsx tidak ditemukan di direktori utama.');
            return;
        }

        $importer = new DapodikImporter();
        $this->resultSummary = $importer->import($samplePath);
        session()->flash('status', "Import Sample Berhasil! {$this->resultSummary['imported']} siswa berhasil diimpor.");
    }

    /**
     * Mengunduh Template Resmi DAPODIK (66-67 kolom + baris contoh panduan)
     */
    public function downloadTemplate()
    {
        $this->guard('kesiswaan.siswa.tambah');

        $exporter = new SiswaExporter();
        $spreadsheet = $exporter->downloadTemplate();

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 'Template_Import_Dapodik_Siswa.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Mengekspor seluruh data siswa aktif dalam format resmi DAPODIK
     */
    public function exportDapodik()
    {
        $this->guard('kesiswaan.siswa.lihat');

        $siswas = Siswa::with(['rombel', 'rayon', 'program', 'orangTuas'])->orderBy('nama')->get();

        $exporter = new SiswaExporter();
        $spreadsheet = $exporter->exportDapodik($siswas);
        $timestamp = now()->format('Ymd_His');

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, "Data_Siswa_DAPODIK_{$timestamp}.xlsx", [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Menghapus seluruh data siswa dan akun user siswa & orang tua
     */
    public function resetAllSiswa(): void
    {
        $this->guard('kesiswaan.siswa.hapus');

        $cleaned = DapodikImporter::cleanSiswaData();
        $this->resultSummary = null;

        session()->flash('status', "Seluruh data siswa ({$cleaned['siswas']} siswa) dan {$cleaned['users']} akun pengguna (Siswa & Orang Tua) telah berhasil dihapus bersih dari sistem.");
    }

    public function render()
    {
        return view('kesiswaan::livewire.siswa-import');
    }
}
