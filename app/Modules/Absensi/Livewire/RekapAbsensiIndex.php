<?php

namespace App\Modules\Absensi\Livewire;

use App\Modules\Absensi\Services\RekapAbsensiService;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Kesiswaan\Models\Program;
use Livewire\Component;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class RekapAbsensiIndex extends Component
{
    public $tgl_mulai;
    public $tgl_selesai;
    public $rombelId = '';
    public $rayonId = '';
    public $gender = '';
    public $programId = '';
    public $search = '';

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
