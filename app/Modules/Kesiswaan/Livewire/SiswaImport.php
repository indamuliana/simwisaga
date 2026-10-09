<?php

namespace App\Modules\Kesiswaan\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Kesiswaan\Services\DapodikImporter;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

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

    public function render()
    {
        return view('kesiswaan::livewire.siswa-import');
    }
}
