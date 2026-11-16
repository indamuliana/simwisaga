<?php

namespace App\Modules\BeasiswaOta\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\BeasiswaOta\Models\DonasiMasuk;
use App\Modules\BeasiswaOta\Models\Donatur;
use App\Modules\BeasiswaOta\Models\KasMutasi;
use App\Modules\BeasiswaOta\Models\Kelompok;
use App\Modules\BeasiswaOta\Services\OtaExcelService;
use App\Modules\BeasiswaOta\Services\OtaKasService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class DonasiMasukIndex extends Component
{
    use GuardsPermission, WithPagination;

    // Tab Aktif: 'kelompok' (default) | 'transaksi'
    public string $activeTab = 'kelompok';

    // Filter Tahun Ajaran (Untuk Monitoring Kelompok)
    public ?int $tahunAjaranId = null;

    // Filter & Search Tab Kelompok
    public string $searchKelompok = '';

    // Filter & Search Tab Transaksi (Per Donatur)
    public string $filterKelompok = '';
    public string $filterTipe = '';
    public string $filterMetode = ''; // '' | 'perorangan' | 'kolektif'
    public string $search = '';

    // Slide-over Drawer Rincian Kelompok
    public bool $showDrawer = false;
    public ?int $drawerKelompokId = null;
    public string $drawerSubTab = 'matrix'; // 'matrix' | 'transaksi'

    protected $queryString = [
        'activeTab' => ['except' => 'kelompok'],
        'tahunAjaranId' => ['except' => null],
    ];

    public function mount(): void
    {
        $activeTa = TahunAjaran::active()->first() ?? TahunAjaran::latest('id')->first();
        if (!$this->tahunAjaranId) {
            $this->tahunAjaranId = $activeTa?->id;
        }

        if (request()->query('tab') === 'transaksi') {
            $this->activeTab = 'transaksi';
        }
    }

    public function updatedActiveTab(): void
    {
        $this->resetPage();
    }

    public function updatedTahunAjaranId(): void
    {
        $this->resetPage();
    }

    public function updatedSearchKelompok(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openDrawer(int $kelompokId): void
    {
        $this->drawerKelompokId = $kelompokId;
        $this->drawerSubTab = 'matrix';
        $this->showDrawer = true;
    }

    public function closeDrawer(): void
    {
        $this->showDrawer = false;
        $this->drawerKelompokId = null;
    }

    public function exportKelompokExcel()
    {
        $this->guard('beasiswa_ota.kelola');

        $spreadsheet = OtaExcelService::exportRekapMonitoringKelompok($this->tahunAjaranId);
        $ta = $this->tahunAjaranId ? TahunAjaran::find($this->tahunAjaranId) : null;
        $taSlug = $ta ? str_replace(['/', ' '], ['-', '_'], $ta->nama) : 'Semua';
        $filename = "Rekap_Monitoring_Kelompok_OTA_{$taSlug}_" . date('Ymd_His') . ".xlsx";

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename);
    }

    public function delete(int $id)
    {
        $this->guard('beasiswa_ota.kelola');

        $donasi = DonasiMasuk::findOrFail($id);

        if ($donasi->is_kolektif) {
            session()->flash('error', 'Donasi kolektif tidak dapat dihapus satuan per anggota karena tercatat dalam satu transaksi batch kelompok. Gunakan tombol Batal Batch.');
            return;
        }

        $posKas = ($donasi->tipe_donatur === 'rutin' && $donasi->kelompok_id) ? 'kelompok' : 'insidental';
        $currentSaldo = $posKas === 'kelompok'
            ? OtaKasService::getSaldoKelompok($donasi->kelompok_id)
            : OtaKasService::getSaldoInsidental();

        if ($currentSaldo < (float) $donasi->nominal) {
            session()->flash('error', 'Donasi tidak dapat dibatalkan karena dananya telah terpakai/dialokasikan.');
            return;
        }

        DB::transaction(function () use ($donasi) {
            KasMutasi::where('referensi_tipe', 'donasi')->where('referensi_id', $donasi->id)->delete();
            $donasi->delete();
        });

        session()->flash('message', 'Catatan donasi berhasil dihapus.');
    }

    public function deleteBatch(string $batchKode)
    {
        $this->guard('beasiswa_ota.kelola');

        $donasis = DonasiMasuk::where('batch_kode', $batchKode)->get();
        if ($donasis->isEmpty()) {
            return;
        }

        $totalBatch = $donasis->sum('nominal');
        $kelompokId = $donasis->first()->kelompok_id;
        if ($kelompokId && OtaKasService::getSaldoKelompok($kelompokId) < $totalBatch) {
            session()->flash('error', 'Batch setoran kolektif tidak dapat dibatalkan karena dananya telah terpakai/dialokasikan.');
            return;
        }

        DB::transaction(function () use ($donasis, $batchKode) {
            $ids = $donasis->pluck('id');
            KasMutasi::where('referensi_tipe', 'donasi_kolektif')->whereIn('referensi_id', $ids)->delete();
            KasMutasi::where('referensi_tipe', 'donasi')->whereIn('referensi_id', $ids)->delete();

            DonasiMasuk::where('batch_kode', $batchKode)->delete();
        });

        session()->flash('message', "Seluruh transaksi setoran kolektif (Batch {$batchKode}) berhasil dibatalkan dan dihapus.");
    }

    public function render()
    {
        $tahunAjarans = TahunAjaran::orderByDesc('is_active')->orderByDesc('nama')->get();
        $selectedTa = $this->tahunAjaranId ? TahunAjaran::find($this->tahunAjaranId) : null;

        // ================= DATA TAB 1: REKAP MONITORING KELOMPOK =================
        $kelompoksQuery = Kelompok::query()
            ->with([
                'anggotaKelompok.donatur',
                'siswaPenerima.siswa.rombel',
                'koordinator',
                'donasiMasuks' => function ($q) {
                    if ($this->tahunAjaranId) {
                        $q->where('tahun_ajaran_id', $this->tahunAjaranId);
                    }
                },
                'kasMutasis',
            ])
            ->where('is_aktif', true)
            ->when($this->searchKelompok, function ($q) {
                $term = "%{$this->searchKelompok}%";
                $q->where(function ($sq) use ($term) {
                    $sq->where('nama_kelompok', 'like', $term)
                        ->orWhere('kode_kelompok', 'like', $term)
                        ->orWhere('koordinator_nama', 'like', $term)
                        ->orWhereHas('siswaPenerima.siswa', fn($sq2) => $sq2->where('nama', 'like', $term));
                });
            })
            ->orderBy('nama_kelompok');

        $kelompoksPaginated = $kelompoksQuery->paginate(15, ['*'], 'kelompokPage');

        // Ringkasan Kas Bebas (Insidental)
        $insidentalMasukQuery = DonasiMasuk::whereNull('kelompok_id');
        if ($this->tahunAjaranId) {
            $insidentalMasukQuery->where('tahun_ajaran_id', $this->tahunAjaranId);
        }
        $totalMasukInsidental = (float) $insidentalMasukQuery->sum('nominal');
        $saldoInsidental = OtaKasService::getSaldoInsidental();
        $lastDonasiInsidental = DonasiMasuk::whereNull('kelompok_id')->latest('tanggal_donasi')->latest('id')->first();

        // Statistik Keseluruhan pada T.A. ini
        $allActiveKelompoks = Kelompok::where('is_aktif', true)
            ->with(['donasiMasuks' => function ($q) {
                if ($this->tahunAjaranId) {
                    $q->where('tahun_ajaran_id', $this->tahunAjaranId);
                }
            }, 'kasMutasis'])
            ->get();

        $totalMasukKelompokTa = (float) $allActiveKelompoks->sum(fn($k) => $k->donasiMasuks->sum('nominal'));
        $totalDonasiMasukTa = $totalMasukKelompokTa + $totalMasukInsidental;
        $ringkasanKas = OtaKasService::getRingkasanKas();

        // ================= DATA DRAWER RINCIAN KELOMPOK =================
        $drawerKelompok = null;
        $drawerMonthsInfo = [];
        $drawerMembers = [];
        $drawerTransactions = collect();

        if ($this->showDrawer && $this->drawerKelompokId !== null) {
            if ($this->drawerKelompokId > 0) {
                $drawerKelompok = Kelompok::with([
                    'anggotaKelompok.donatur',
                    'siswaPenerima.siswa.rombel',
                    'koordinator',
                ])->find($this->drawerKelompokId);

                if ($drawerKelompok) {
                    $drawerMonthsInfo = DonasiMasukCreate::getMonthsForTahunAjaran($this->tahunAjaranId);

                    // Matrix 12 Bulan per Donatur Anggota
                    foreach ($drawerKelompok->anggotaKelompok as $ang) {
                        $paidMonths = DonasiMasukCreate::getPaidMonthsForDonatur($ang->donatur_id, $this->tahunAjaranId, $drawerKelompok->id);
                        $drawerMembers[] = [
                            'donatur_id' => $ang->donatur_id,
                            'nama' => $ang->donatur?->nama ?? 'Tanpa Nama',
                            'nomor_telepon' => $ang->donatur?->nomor_telepon,
                            'nominal_komitmen' => (float) $ang->nominal_komitmen,
                            'paid_months' => $paidMonths,
                            'total_paid_months' => count($paidMonths),
                            'total_nominal_disetor' => (float) DonasiMasuk::where('donatur_id', $ang->donatur_id)
                                ->where('kelompok_id', $drawerKelompok->id)
                                ->when($this->tahunAjaranId, fn($q) => $q->where('tahun_ajaran_id', $this->tahunAjaranId))
                                ->sum('nominal'),
                        ];
                    }

                    // Riwayat Transaksi Kelompok
                    $drawerTransactions = DonasiMasuk::where('kelompok_id', $drawerKelompok->id)
                        ->when($this->tahunAjaranId, fn($q) => $q->where('tahun_ajaran_id', $this->tahunAjaranId))
                        ->with('donatur')
                        ->latest('tanggal_donasi')
                        ->latest('id')
                        ->get();
                }
            } else {
                // Kas Bebas (drawerKelompokId === 0)
                $drawerTransactions = DonasiMasuk::whereNull('kelompok_id')
                    ->when($this->tahunAjaranId, fn($q) => $q->where('tahun_ajaran_id', $this->tahunAjaranId))
                    ->with('donatur')
                    ->latest('tanggal_donasi')
                    ->latest('id')
                    ->get();
            }
        }

        // ================= DATA TAB 2: RIWAYAT TRANSAKSI (PER DONATUR) =================
        $donasis = DonasiMasuk::query()
            ->with(['donatur', 'kelompok', 'tahunAjaran'])
            ->when($this->search, function ($q) {
                $q->where(function ($sq) {
                    $sq->whereHas('donatur', fn($dq) => $dq->where('nama', 'like', "%{$this->search}%"))
                        ->orWhere('nomor_transaksi', 'like', "%{$this->search}%")
                        ->orWhere('batch_kode', 'like', "%{$this->search}%")
                        ->orWhere('penyetor_nama', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterKelompok, fn($q) => $q->where('kelompok_id', $this->filterKelompok))
            ->when($this->filterTipe, fn($q) => $q->where('tipe_donatur', $this->filterTipe))
            ->when($this->filterMetode, function ($q) {
                if ($this->filterMetode === 'kolektif') {
                    $q->where('is_kolektif', true);
                } elseif ($this->filterMetode === 'perorangan') {
                    $q->where('is_kolektif', false);
                }
            })
            ->latest('tanggal_donasi')
            ->latest('id')
            ->paginate(15, ['*'], 'transaksiPage');

        $donaturs = Donatur::where('is_aktif', true)->orderBy('nama')->get();
        $kelompoks = Kelompok::where('is_aktif', true)->orderBy('nama_kelompok')->get();

        return view('beasiswaota::livewire.donasi-masuk-index', [
            'kelompoksPaginated' => $kelompoksPaginated,
            'totalMasukInsidental' => $totalMasukInsidental,
            'saldoInsidental' => $saldoInsidental,
            'lastDonasiInsidental' => $lastDonasiInsidental,
            'totalMasukKelompokTa' => $totalMasukKelompokTa,
            'totalDonasiMasukTa' => $totalDonasiMasukTa,
            'ringkasanKas' => $ringkasanKas,
            'drawerKelompok' => $drawerKelompok,
            'drawerMonthsInfo' => $drawerMonthsInfo,
            'drawerMembers' => $drawerMembers,
            'drawerTransactions' => $drawerTransactions,
            'selectedTa' => $selectedTa,
            'donasis' => $donasis,
            'donaturs' => $donaturs,
            'kelompoks' => $kelompoks,
            'tahunAjarans' => $tahunAjarans,
        ])->layout('components.layouts.app', ['title' => 'Penerimaan Donasi Masuk OTA']);
    }
}
