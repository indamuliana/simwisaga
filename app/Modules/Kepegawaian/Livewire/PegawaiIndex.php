<?php

namespace App\Modules\Kepegawaian\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Core\Services\DocumentEngine;
use App\Core\Support\DataMasker;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kepegawaian\Services\PegawaiExportService;
use App\Modules\Kepegawaian\Services\PegawaiImportService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Data Guru & Karyawan (GTK)')]
class PegawaiIndex extends Component
{
    use \App\Core\Livewire\Traits\WithSorting, GuardsPermission, WithPagination, WithFileUploads;

    public function mount(): void
    {
        $this->sortField = 'nama_lengkap';
    }

    // Filter & Search
    public string $search = '';

    public string $filterGtk = '';

    public string $filterStatus = '';

    // Import Modal State
    public bool $showImportModal = false;

    public $fileImport = null;

    public ?string $importMessage = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterGtk(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function openImportModal(): void
    {
        $this->guard('kepegawaian.pegawai.import');
        $this->showImportModal = true;
        $this->fileImport = null;
        $this->importMessage = null;
    }

    public function closeImportModal(): void
    {
        $this->showImportModal = false;
        $this->fileImport = null;
        $this->importMessage = null;
    }

    public function downloadTemplateCsv(PegawaiExportService $exportService)
    {
        $csvContent = $exportService->downloadTemplateCsv();

        return response()->streamDownload(
            fn () => print ($csvContent),
            'Template_Import_Pegawai_Wikrama.csv',
            ['Content-Type' => 'text/csv']
        );
    }

    public function exportCsv(PegawaiExportService $exportService)
    {
        $this->guard('kepegawaian.pegawai.ekspor');

        $query = $this->filteredQuery();
        $csvContent = $exportService->exportCsv($query->get());

        $filename = 'Data_Pegawai_Wikrama_'.now()->format('Ymd_His').'.csv';

        return response()->streamDownload(
            fn () => print ($csvContent),
            $filename,
            ['Content-Type' => 'text/csv']
        );
    }

    public function exportXlsx(PegawaiExportService $exportService)
    {
        $this->guard('kepegawaian.pegawai.ekspor');

        $query = $this->filteredQuery();
        $xlsxPath = $exportService->exportXlsx($query->get());

        $filename = 'Data_Pegawai_Wikrama_'.now()->format('Ymd_His').'.xlsx';

        return response()->download($xlsxPath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    public function processImport(PegawaiImportService $importService): void
    {
        $this->guard('kepegawaian.pegawai.import');

        $this->validate([
            'fileImport' => 'required|file|max:10240',
        ], [
            'fileImport.required' => 'Pilih file CSV atau Excel terlebih dahulu.',
            'fileImport.max' => 'Ukuran file maksimal 10MB.',
        ]);

        $realPath = $this->fileImport->getRealPath();

        try {
            $result = $importService->importAppend($realPath);
            $msg = "Impor berhasil! {$result['inserted']} data baru ditambahkan, {$result['updated']} data berhasil diperbarui.";
            session()->flash('status', $msg);
            $this->closeImportModal();
        } catch (\Throwable $e) {
            $this->addError('fileImport', 'Gagal memproses file: '.$e->getMessage());
        }
    }

    public function delete(int $id): void
    {
        $this->guard('kepegawaian.pegawai.hapus');
        $p = Pegawai::findOrFail($id);
        $nama = $p->nama_lengkap;
        $user = $p->user;
        $p->delete();
        if ($user) {
            $user->delete();
        }
        session()->flash('status', "Pegawai {$nama} berhasil dihapus.");
    }

    public function cetakDaftar()
    {
        $this->guard('kepegawaian.pegawai.cetak');

        $pegawais = Pegawai::orderBy('jenis_gtk')->orderBy('nama_lengkap')->get();

        $rows = '';
        foreach ($pegawais as $idx => $p) {
            $no = $idx + 1;
            $nama = $p->nama_gelar;
            $niy = $p->niy_label;
            $nip = $p->nip ?? '-';
            $nuptk = $p->nuptk ?? '-';
            $nik = DataMasker::maskNik($p->nik);
            $jenis = strtoupper($p->jenis_gtk);
            $status = strtoupper($p->status_kepegawaian);
            $jk = $p->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';

            $rows .= "
                <tr>
                    <td style='text-align: center;'>{$no}</td>
                    <td><b>{$nama}</b><br><small style='color: #64748b;'>NIY: {$niy} | NIK: {$nik}</small></td>
                    <td>NIP: {$nip}<br>NUPTK: {$nuptk}</td>
                    <td>{$jk}</td>
                    <td>{$jenis} ({$status})</td>
                    <td>{$p->no_hp}</td>
                </tr>
            ";
        }

        $html = "
            <table class='content-table'>
                <thead>
                    <tr>
                        <th style='width: 30px; text-align: center;'>No</th>
                        <th>Nama Lengkap & NIK</th>
                        <th>NIP / NUPTK</th>
                        <th>JK</th>
                        <th>Jenis & Status</th>
                        <th>Kontak</th>
                    </tr>
                </thead>
                <tbody>
                    {$rows}
                </tbody>
            </table>
        ";

        $pdf = DocumentEngine::createPdf(
            title: 'DAFTAR GURU DAN TENAGA KEPENDIDIKAN (GTK)',
            content: $html,
            options: [
                'subtitle' => 'SMK Wikrama 1 Garut — Tahun Ajaran '.now()->year.'/'.(now()->year + 1),
                'template_kode' => 'daftar_siswa',
            ]
        );

        return response()->streamDownload(
            fn () => print ($pdf->output()),
            'Daftar_GTK_Wikrama.pdf'
        );
    }

    protected function filteredQuery()
    {
        $query = Pegawai::with(['user.roles', 'fotos']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nama_lengkap', 'like', "%{$this->search}%")
                    ->orWhere('niy', 'like', "%{$this->search}%")
                    ->orWhere('nip', 'like', "%{$this->search}%")
                    ->orWhere('nuptk', 'like', "%{$this->search}%")
                    ->orWhere('nik', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterGtk) {
            $query->where('jenis_gtk', $this->filterGtk);
        }

        if ($this->filterStatus) {
            $query->where('status_kepegawaian', strtolower($this->filterStatus));
        }

        return $query->orderBy($this->sortField, $this->sortDirection);
    }

    public function generateAccounts(): void
    {
        $this->guard('kepegawaian.pegawai.edit');
        \Illuminate\Support\Facades\Artisan::call('kepegawaian:generate-accounts');
        session()->flash('status', 'Pembuatan akun GTK berhasil dijalankan.');
    }

    public function render()
    {
        $pegawais = $this->filteredQuery()->paginate(15);
        $unlinkedCount = Pegawai::whereNull('user_id')->count();

        return view('kepegawaian::livewire.pegawai-index', [
            'pegawais' => $pegawais,
            'unlinkedCount' => $unlinkedCount,
        ]);
    }
}
