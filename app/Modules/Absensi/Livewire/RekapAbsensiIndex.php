<?php

namespace App\Modules\Absensi\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Absensi\Services\MatriksAbsensiService;
use App\Modules\Absensi\Services\RekapAbsensiService;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Kesiswaan\Models\Program;
use Livewire\Component;
use Livewire\WithFileUploads;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class RekapAbsensiIndex extends Component
{
    use GuardsPermission, WithFileUploads;

    public $tgl_mulai;
    public $tgl_selesai;
    public $rombelId = '';
    public $rayonId = '';
    public $gender = '';
    public $programId = '';
    public $search = '';

    // State untuk Modal Import & Preview
    public bool $showImportModal = false;
    public $file;
    public ?array $previewData = null;
    public ?string $importErrorMessage = null;
    public bool $isProcessing = false;

    public function mount()
    {
        $this->tgl_mulai = now()->startOfMonth()->toDateString();
        $this->tgl_selesai = now()->toDateString();
    }

    public function resetFilters()
    {
        $this->tgl_mulai = now()->startOfMonth()->toDateString();
        $this->tgl_selesai = now()->toDateString();
        $this->rombelId = '';
        $this->rayonId = '';
        $this->gender = '';
        $this->programId = '';
        $this->search = '';
    }

    public function cetakPdf()
    {
        return redirect()->route('absensi.rekap.pdf', [
            'tgl_mulai' => $this->tgl_mulai,
            'tgl_selesai' => $this->tgl_selesai,
            'rombel_id' => $this->rombelId,
            'rayon_id' => $this->rayonId,
            'gender' => $this->gender,
            'program_id' => $this->programId,
            'search' => $this->search,
        ]);
    }

    /**
     * Export Rekapitulasi Ringkasan (Format Rekap Standar: H, S, I, A, % Hadir)
     */
    public function exportExcel(RekapAbsensiService $service)
    {
        $spreadsheet = $service->generateExcel([
            'tgl_mulai' => $this->tgl_mulai,
            'tgl_selesai' => $this->tgl_selesai,
            'rombel_id' => $this->rombelId,
            'rayon_id' => $this->rayonId,
            'gender' => $this->gender,
            'program_id' => $this->programId,
            'search' => $this->search,
        ]);

        $timestamp = now()->format('Ymd_His');
        $filename = "Rekap_Presensi_{$timestamp}.xlsx";

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Export Matriks Presensi / Template Import (Format Kalender: Tanggal per Kolom)
     */
    public function exportMatriksExcel(MatriksAbsensiService $service)
    {
        $spreadsheet = $service->generateMatriksExcel([
            'tgl_mulai' => $this->tgl_mulai,
            'tgl_selesai' => $this->tgl_selesai,
            'rombel_id' => $this->rombelId,
            'rayon_id' => $this->rayonId,
            'gender' => $this->gender,
            'program_id' => $this->programId,
            'search' => $this->search,
        ]);

        $timestamp = now()->format('Ymd_His');
        $filename = "Matriks_Presensi_{$timestamp}.xlsx";

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Download Template Kosong Matriks Import
     */
    public function downloadTemplate(MatriksAbsensiService $service)
    {
        return $this->exportMatriksExcel($service);
    }

    /**
     * Membuka Modal Import
     */
    public function openImportModal()
    {
        $this->guard('absensi.harian.input');
        $this->resetImport();
        $this->showImportModal = true;
    }

    /**
     * Menutup Modal Import
     */
    public function closeImportModal()
    {
        $this->resetImport();
        $this->showImportModal = false;
    }

    /**
     * Reset variabel import
     */
    public function resetImport()
    {
        $this->file = null;
        $this->previewData = null;
        $this->importErrorMessage = null;
        $this->isProcessing = false;
    }

    /**
     * Event saat file selesai diupload: lakukan analisis & validasi preview
     */
    public function updatedFile()
    {
        $this->guard('absensi.harian.input');
        $this->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt|max:20480',
        ]);

        $this->importErrorMessage = null;
        $this->previewData = null;

        try {
            $service = app(MatriksAbsensiService::class);
            $filePath = $this->file->getRealPath();
            $this->previewData = $service->parseMatriksFile($filePath);
        } catch (\Throwable $e) {
            $this->previewData = null;
            $this->importErrorMessage = $e->getMessage();
        }
    }

    /**
     * Konfirmasi dan simpan data absensi dari preview ke database
     */
    public function confirmImport(MatriksAbsensiService $service)
    {
        $this->guard('absensi.harian.input');

        if (!$this->previewData || empty($this->previewData['records'])) {
            $this->importErrorMessage = 'Tidak ada record data presensi yang valid untuk diimpor.';
            return;
        }

        $this->isProcessing = true;

        try {
            $records = $this->previewData['records'];
            $totalCount = $service->importMatriksRecords($records, auth()->id());

            session()->flash('message', "Sukses! Berhasil mengimpor {$totalCount} record presensi siswa ke database.");
            $this->closeImportModal();
        } catch (\Throwable $e) {
            $this->isProcessing = false;
            $this->importErrorMessage = "Gagal menyimpan data: " . $e->getMessage();
        }
    }

    public function render(RekapAbsensiService $service)
    {
        $rombels = Rombel::orderBy('nama')->get();
        $rayons = Rayon::orderBy('nama')->get();
        $programs = Program::where('is_active', true)->orderBy('nama')->get();

        $data = $service->getRekapData([
            'tgl_mulai' => $this->tgl_mulai,
            'tgl_selesai' => $this->tgl_selesai,
            'rombel_id' => $this->rombelId,
            'rayon_id' => $this->rayonId,
            'gender' => $this->gender,
            'program_id' => $this->programId,
            'search' => $this->search,
        ]);

        return view('absensi::livewire.rekap-absensi-index', [
            'rombels' => $rombels,
            'rayons' => $rayons,
            'programs' => $programs,
            'rekap' => $data['rekap'],
            'hari_efektif' => $data['hari_efektif'],
            'total_siswa' => $data['total_siswa'],
            'periode_label' => $data['periode_label'],
            'rombel_nama' => $data['rombel_nama'],
            'rayon_nama' => $data['rayon_nama'],
            'program_nama' => $data['program_nama'],
            'gender_nama' => $data['gender_nama'],
        ]);
    }
}
