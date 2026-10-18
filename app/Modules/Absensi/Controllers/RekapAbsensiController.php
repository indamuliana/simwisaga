<?php

namespace App\Modules\Absensi\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Absensi\Services\RekapAbsensiService;
use App\Modules\Sistem\Models\School;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class RekapAbsensiController extends Controller
{
    public function cetakPdf(Request $request, RekapAbsensiService $service)
    {
        abort_unless(auth()->user()->isSuperAdmin() || auth()->user()->can('absensi.rekap.cetak') || auth()->user()->can('absensi.rekap.lihat'), 403, 'Anda tidak memiliki wewenang untuk mencetak rekap absensi.');

        $data = $service->getRekapData($request->all());
        $data['school'] = School::current();

        $pdf = Pdf::loadView('absensi::pdf.rekap-absensi', $data)
            ->setPaper('A4', 'landscape');

        $filename = 'Rekap_Absensi_' . str_replace([' ', '/', '\\'], '_', $data['periode_label']) . '.pdf';

        return $pdf->stream($filename);
    }
}
