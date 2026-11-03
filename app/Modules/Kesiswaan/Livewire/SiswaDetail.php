<?php

namespace App\Modules\Kesiswaan\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Core\Support\DataMasker;
use App\Modules\Absensi\Models\AbsensiHarian;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Kesiswaan\Services\SingleSiswaDocumentExporter;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Detail Profil Siswa')]
class SiswaDetail extends Component
{
    use GuardsPermission;

    public int $siswaId;

    #[Url(as: 'tab')]
    public string $activeTab = 'biodata';

    public function mount(int $id): void
    {
        $this->guard('kesiswaan.siswa.lihat');
        $this->siswaId = $id;

        // Validasi keberadaan siswa
        Siswa::findOrFail($id);

        if (! in_array($this->activeTab, ['biodata', 'pilihan', 'prestasi', 'riwayat'])) {
            $this->activeTab = 'biodata';
        }
    }

    public function setTab(string $tab): void
    {
        if (in_array($tab, ['biodata', 'pilihan', 'prestasi', 'riwayat'])) {
            $this->activeTab = $tab;
        }
    }

    public function downloadDokumen(string $format = 'pdf')
    {
        $this->guard('kesiswaan.siswa.lihat');
        $siswa = Siswa::findOrFail($this->siswaId);
        $exporter = new SingleSiswaDocumentExporter;

        return match ($format) {
            'word' => $exporter->downloadWord($siswa),
            'excel' => $exporter->downloadExcel($siswa),
            default => $exporter->downloadPdf($siswa),
        };
    }

    public function render()
    {
        $siswa = Siswa::with([
            'user',
            'rombel.waliKelas',
            'rayon.pembimbing',
            'program',
            'orangTuas',
            'seniBudayaAnggotas.seniBudaya.pembimbing',
            'seniBudayaAnggotas.semester.tahunAjaran',
            'ekstrakurikulerAnggotas.ekstrakurikuler.pembina',
            'ekstrakurikulerAnggotas.semester.tahunAjaran',
            'mapelPilihanAnggotas.mapelPilihan.guru',
            'mapelPilihanAnggotas.tahunAjaran',
            'customKelompoks.pembimbing',
            'customKelompoks.tahunAjaran',
            'prestasis' => function ($q) {
                $q->orderByDesc('tanggal_perolehan');
            },
            'otaPenerimas.kelompok',
            'otaPenerimas.komponenBiayas',
            'otaPenerimas.alokasis',
            'riwayatRombel.rombel',
            'riwayatRayon.rayon.pembimbing',
            'riwayatAkademiks.rombelAsal',
            'riwayatAkademiks.rombelTujuan',
            'riwayatAkademiks.tahunAjaran',
            'riwayatAkademiks.diprosesOleh',
        ])->findOrFail($this->siswaId);

        // Rekap Presensi Harian
        $absensiStats = [
            'H' => AbsensiHarian::where('siswa_id', $this->siswaId)->where('status', 'H')->count(),
            'S' => AbsensiHarian::where('siswa_id', $this->siswaId)->where('status', 'S')->count(),
            'I' => AbsensiHarian::where('siswa_id', $this->siswaId)->where('status', 'I')->count(),
            'A' => AbsensiHarian::where('siswa_id', $this->siswaId)->where('status', 'A')->count(),
        ];
        $totalAbsen = array_sum($absensiStats);
        $persenHadir = $totalAbsen > 0 ? round(($absensiStats['H'] / $totalAbsen) * 100, 1) : 100;
        $riwayatAbsensi = AbsensiHarian::where('siswa_id', $this->siswaId)
            ->orderByDesc('tanggal')
            ->limit(10)
            ->get();

        $canViewSensitive = DataMasker::canViewSensitive();

        return view('kesiswaan::livewire.siswa-detail', [
            'activeTab' => $this->activeTab,
            'siswa' => $siswa,
            'canViewSensitive' => $canViewSensitive,
            'absensiStats' => $absensiStats,
            'totalAbsen' => $totalAbsen,
            'persenHadir' => $persenHadir,
            'riwayatAbsensi' => $riwayatAbsensi,
        ]);
    }
}
