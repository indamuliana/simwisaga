<?php

namespace App\Modules\DashboardData\Livewire\Rayon;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Kesiswaan\Models\Siswa;
use Livewire\Attributes\Url;
use Livewire\Component;

class Index extends Component
{
    use GuardsPermission;

    #[Url(as: 'search')]
    public string $search = '';

    #[Url(as: 'tingkat')]
    public string $filterTingkat = '';

    public array $expandedRayons = [];

    public function mount(): void
    {
        $user = auth()->user();
        if ($user && method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return;
        }

        if ($user && ($user->can('dashboard_data.rayon.lihat') || $user->can('dashboard_data.program.lihat') || $user->can('dashboard_data.dashboard.lihat'))) {
            return;
        }

        $this->guard('dashboard_data.rayon.lihat');
    }

    public function toggleRayon(string|int $rayonId): void
    {
        $id = (string) $rayonId;
        if (in_array($id, $this->expandedRayons, true)) {
            $this->expandedRayons = array_values(array_filter($this->expandedRayons, fn($item) => $item !== $id));
        } else {
            $this->expandedRayons[] = $id;
        }
    }

    public function expandAll(array $allIds): void
    {
        $this->expandedRayons = array_values(array_unique(array_map('strval', $allIds)));
    }

    public function collapseAll(): void
    {
        $this->expandedRayons = [];
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterTingkat = '';
    }

    public function render()
    {
        // 1. Ambil seluruh jurusan aktif
        $jurusans = Jurusan::where('is_active', true)->orderBy('nama')->get();

        // 2. Query agregasi data siswa aktif
        $siswaQuery = Siswa::query()
            ->selectRaw('siswas.rayon_id, rombels.jurusan_id, siswas.rombel_id, rombels.nama as rombel_nama, rombels.tingkat as rombel_tingkat, siswas.jk, count(*) as total')
            ->leftJoin('rombels', 'siswas.rombel_id', '=', 'rombels.id')
            ->where('siswas.status', 'aktif');

        if ($this->filterTingkat !== '') {
            $siswaQuery->where('rombels.tingkat', $this->filterTingkat);
        }

        $rawCounts = $siswaQuery
            ->groupBy('siswas.rayon_id', 'rombels.jurusan_id', 'siswas.rombel_id', 'rombels.nama', 'rombels.tingkat', 'siswas.jk')
            ->get();

        // Index agregasi: [rayon_key][jurusan_id][jk] => count
        // dan per rombel: [rayon_key][rombel_id] => details
        $rayonJurusanAgg = [];
        $rayonRombelAgg = [];

        foreach ($rawCounts as $row) {
            $rKey = $row->rayon_id ? (string) $row->rayon_id : 'tanpa_rayon';
            $jId = (int) $row->jurusan_id;
            $jk = strtoupper((string) $row->jk);
            $count = (int) $row->total;

            // Jurusan summary per rayon
            $rayonJurusanAgg[$rKey][$jId][$jk] = ($rayonJurusanAgg[$rKey][$jId][$jk] ?? 0) + $count;

            // Rombel breakdown per rayon
            $rombId = $row->rombel_id ? (string) $row->rombel_id : 'tanpa_rombel';
            if (!isset($rayonRombelAgg[$rKey][$rombId])) {
                $rayonRombelAgg[$rKey][$rombId] = [
                    'id' => $row->rombel_id,
                    'nama' => $row->rombel_nama ?? '(Tanpa Rombel)',
                    'tingkat' => $row->rombel_tingkat ?? '-',
                    'jurusan_id' => $jId,
                    'l' => 0,
                    'p' => 0,
                    'total' => 0,
                ];
            }
            if ($jk === 'L') {
                $rayonRombelAgg[$rKey][$rombId]['l'] += $count;
            } elseif ($jk === 'P') {
                $rayonRombelAgg[$rKey][$rombId]['p'] += $count;
            }
            $rayonRombelAgg[$rKey][$rombId]['total'] += $count;
        }

        // 3. Ambil Rayon dengan pembimbing
        $rayonQuery = Rayon::with('pembimbing')->orderBy('nama');

        if (trim($this->search) !== '') {
            $term = '%' . strtolower(trim($this->search)) . '%';
            $rayonQuery->where(function ($q) use ($term) {
                $q->whereRaw('LOWER(nama) like ?', [$term])
                  ->orWhereHas('pembimbing', function ($sub) use ($term) {
                      $sub->whereRaw('LOWER(name) like ?', [$term]);
                  });
            });
        }

        $rayons = $rayonQuery->get();

        // 4. Susun Baris Matriks Rayon
        $rows = [];
        $allRayonIds = [];

        // Inisialisasi Grand Total
        $grandTotal = [
            'jurusans' => [],
            'total_l' => 0,
            'total_p' => 0,
            'total_all' => 0,
        ];
        foreach ($jurusans as $j) {
            $grandTotal['jurusans'][$j->id] = ['l' => 0, 'p' => 0, 'total' => 0];
        }

        foreach ($rayons as $rayon) {
            $rKey = (string) $rayon->id;
            $allRayonIds[] = $rKey;

            $jurusanData = [];
            // Hitung total L dan P untuk rayon ini (mencakup seluruh siswa, termasuk jika belum terpetakan ke jurusan)
            $rowTotalL = 0;
            $rowTotalP = 0;
            if (isset($rayonJurusanAgg[$rKey])) {
                foreach ($rayonJurusanAgg[$rKey] as $jId => $counts) {
                    $rowTotalL += $counts['L'] ?? 0;
                    $rowTotalP += $counts['P'] ?? 0;
                }
            }

            foreach ($jurusans as $j) {
                $l = $rayonJurusanAgg[$rKey][$j->id]['L'] ?? 0;
                $p = $rayonJurusanAgg[$rKey][$j->id]['P'] ?? 0;
                $subtotal = $l + $p;

                $jurusanData[$j->id] = [
                    'l' => $l,
                    'p' => $p,
                    'total' => $subtotal,
                ];

                // Tambahkan ke Grand Total jurusan
                $grandTotal['jurusans'][$j->id]['l'] += $l;
                $grandTotal['jurusans'][$j->id]['p'] += $p;
                $grandTotal['jurusans'][$j->id]['total'] += $subtotal;
            }

            $rowTotalAll = $rowTotalL + $rowTotalP;
            $grandTotal['total_l'] += $rowTotalL;
            $grandTotal['total_p'] += $rowTotalP;
            $grandTotal['total_all'] += $rowTotalAll;

            // Rombel list untuk rayon ini (diurutkan berdasarkan nama rombel)
            $rombelList = $rayonRombelAgg[$rKey] ?? [];
            usort($rombelList, fn($a, $b) => strcmp($a['nama'], $b['nama']));

            $rows[] = [
                'id' => $rKey,
                'nama' => $rayon->nama,
                'pembimbing' => $rayon->pembimbing?->name ?? 'Belum Ditugaskan',
                'is_tanpa_rayon' => false,
                'jurusans' => $jurusanData,
                'total_l' => $rowTotalL,
                'total_p' => $rowTotalP,
                'total_all' => $rowTotalAll,
                'rombels' => $rombelList,
            ];
        }

        // 5. Tambahkan baris "(Tanpa Rayon)" jika terdapat data siswa tanpa rayon
        $tanpaRayonCountL = 0;
        $tanpaRayonCountP = 0;
        if (isset($rayonJurusanAgg['tanpa_rayon'])) {
            foreach ($rayonJurusanAgg['tanpa_rayon'] as $jId => $counts) {
                $tanpaRayonCountL += $counts['L'] ?? 0;
                $tanpaRayonCountP += $counts['P'] ?? 0;
            }
        }

        $tanpaRayonJurusan = [];
        foreach ($jurusans as $j) {
            $l = $rayonJurusanAgg['tanpa_rayon'][$j->id]['L'] ?? 0;
            $p = $rayonJurusanAgg['tanpa_rayon'][$j->id]['P'] ?? 0;
            $sub = $l + $p;
            $tanpaRayonJurusan[$j->id] = ['l' => $l, 'p' => $p, 'total' => $sub];
        }

        $tanpaRayonTotal = $tanpaRayonCountL + $tanpaRayonCountP;

        // Tampilkan baris tanpa rayon jika ada siswanya dan tidak sedang difilter pencarian nama rayon yang tidak cocok
        if ($tanpaRayonTotal > 0 && ($this->search === '' || str_contains(strtolower('tanpa rayon'), strtolower($this->search)))) {
            $allRayonIds[] = 'tanpa-rayon';

            $grandTotal['total_l'] += $tanpaRayonCountL;
            $grandTotal['total_p'] += $tanpaRayonCountP;
            $grandTotal['total_all'] += $tanpaRayonTotal;

            foreach ($jurusans as $j) {
                $grandTotal['jurusans'][$j->id]['l'] += $tanpaRayonJurusan[$j->id]['l'];
                $grandTotal['jurusans'][$j->id]['p'] += $tanpaRayonJurusan[$j->id]['p'];
                $grandTotal['jurusans'][$j->id]['total'] += $tanpaRayonJurusan[$j->id]['total'];
            }

            $tanpaRayonRombels = $rayonRombelAgg['tanpa_rayon'] ?? [];
            usort($tanpaRayonRombels, fn($a, $b) => strcmp($a['nama'], $b['nama']));

            $rows[] = [
                'id' => 'tanpa-rayon',
                'nama' => '(Tanpa Rayon)',
                'pembimbing' => 'Perlu Pemetaan Rayon',
                'is_tanpa_rayon' => true,
                'jurusans' => $tanpaRayonJurusan,
                'total_l' => $tanpaRayonCountL,
                'total_p' => $tanpaRayonCountP,
                'total_all' => $tanpaRayonTotal,
                'rombels' => $tanpaRayonRombels,
            ];
        }

        // 6. Ringkasan KPI Tab Rayon
        $totalRayonMaster = Rayon::count();

        if ($this->filterTingkat !== '') {
            $siswaTingkatQuery = Siswa::where('status', 'aktif')
                ->whereHas('rombel', fn($q) => $q->where('tingkat', $this->filterTingkat));
            $totalSiswaBerRayon = (clone $siswaTingkatQuery)->whereNotNull('rayon_id')->count();
            $totalSiswaTanpaRayon = (clone $siswaTingkatQuery)->whereNull('rayon_id')->count();
            $totalSiswaKeseluruhan = $totalSiswaBerRayon + $totalSiswaTanpaRayon;
        } else {
            $totalSiswaBerRayon = Siswa::where('status', 'aktif')->whereNotNull('rayon_id')->count();
            $totalSiswaTanpaRayon = Siswa::where('status', 'aktif')->whereNull('rayon_id')->count();
            $totalSiswaKeseluruhan = $totalSiswaBerRayon + $totalSiswaTanpaRayon;
        }

        $genderRatioL = $grandTotal['total_all'] > 0
            ? round(($grandTotal['total_l'] / $grandTotal['total_all']) * 100, 1)
            : 0;
        $genderRatioP = $grandTotal['total_all'] > 0
            ? round(($grandTotal['total_p'] / $grandTotal['total_all']) * 100, 1)
            : 0;

        return view('dashboarddata::livewire.rayon.index', [
            'jurusans' => $jurusans,
            'rows' => $rows,
            'grandTotal' => $grandTotal,
            'allRayonIds' => $allRayonIds,
            'kpi' => [
                'total_rayon' => $totalRayonMaster,
                'siswa_ber_rayon' => $totalSiswaBerRayon,
                'siswa_tanpa_rayon' => $totalSiswaTanpaRayon,
                'total_siswa' => $totalSiswaKeseluruhan,
                'ratio_l' => $genderRatioL,
                'ratio_p' => $genderRatioP,
            ],
        ]);
    }
}
