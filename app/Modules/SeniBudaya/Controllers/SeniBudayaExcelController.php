<?php

namespace App\Modules\SeniBudaya\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\SeniBudaya\Services\SeniBudayaExcelService;
use App\Modules\Akademik\Support\AcademicContext;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SeniBudayaExcelController extends Controller
{
    public function __construct(protected SeniBudayaExcelService $excelService) {}

    /**
     * Download Template Pendaftaran Siswa
     */
    public function downloadTemplate(): StreamedResponse
    {
        $rombelId = request('rombel_id') ? (int) request('rombel_id') : null;
        $tingkat = request('tingkat') ?: null;

        $spreadsheet = $this->excelService->generateTemplate($rombelId, $tingkat);
        $fileName = 'Template_Pendaftaran_Seni_Budaya_' . date('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Export Rekapitulasi Nilai & Kehadiran Cabang Seni Budaya
     */
    public function exportRekap(int $seni_budaya_id): StreamedResponse
    {
        $currentSem = AcademicContext::currentSemester();
        $semesterId = request('semester_id') ? (int) request('semester_id') : ($currentSem?->id ?? 1);

        $spreadsheet = $this->excelService->exportRekap($seni_budaya_id, $semesterId);
        $fileName = 'Rekap_Nilai_Seni_Budaya_' . $seni_budaya_id . '_' . date('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
