<?php

namespace App\Modules\BeasiswaOta\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\BeasiswaOta\Models\DonasiMasuk;
use App\Modules\BeasiswaOta\Models\Donatur;
use App\Modules\BeasiswaOta\Models\Kelompok;
use App\Modules\BeasiswaOta\Services\OtaKasService;
use Livewire\Attributes\Computed;
use Livewire\Component;

class DonasiMasukCreate extends Component
{
    use GuardsPermission;

    // Mode Input Donasi: 'perorangan' | 'kelompok'
    public string $modeDonasi = 'perorangan';

    // Tahun Ajaran Terpilih
    public ?int $tahunAjaranId = null;

    // Form fields - Mode Perorangan
    public ?int $donaturId = null;
    public ?int $kelompokId = null;
    public string $tanggalDonasi = '';
    public float|int|string $nominal = '';
    public string $keterangan = '';
    public array $selectedBulanPerorangan = [];
    public array $paidMonthsPerorangan = [];
    public float|int $komitmenBulananPerorangan = 0;

    // Form fields - Mode Per Kelompok (Kolektif via Koordinator)
    public ?int $kelompokKolektifId = null;
    public ?int $koordinatorDonaturId = null;
    public string $koordinatorNama = '';
    public string $tanggalDonasiKolektif = '';
    public string $keteranganKolektif = '';
    public array $anggotaItems = [];
    public bool $selectAllAnggotaKolektif = true;

    public function mount()
    {
        $this->tanggalDonasi = now()->toDateString();
        $this->tanggalDonasiKolektif = now()->toDateString();

        $activeTa = TahunAjaran::active()->first() ?? TahunAjaran::latest('id')->first();
        $this->tahunAjaranId = $activeTa?->id;
    }

    public static function cleanNominal(mixed $val): float
    {
        if (is_int($val) || is_float($val)) {
            return (float) $val;
        }
        if (empty($val)) {
            return 0.0;
        }
        $str = trim((string) $val);
        $str = preg_replace('/[.,]00$/', '', $str);
        $cleaned = preg_replace('/[^\d]/', '', $str);
        return (float) ($cleaned ?: 0);
    }

    /**
     * Dapatkan daftar 12 bulan dalam kalender tahun ajaran (Juli Y1 s.d. Juni Y2).
     */
    public static function getMonthsForTahunAjaran(?int $tahunAjaranId = null): array
    {
        $ta = $tahunAjaranId ? TahunAjaran::find($tahunAjaranId) : TahunAjaran::active()->first();
        if (!$ta) {
            $ta = TahunAjaran::latest('id')->first();
        }

        $startYear = (int) date('Y');
        $endYear = $startYear + 1;
        if ($ta && str_contains($ta->nama, '/')) {
            $parts = explode('/', $ta->nama);
            $startYear = (int) trim($parts[0]);
            $endYear = (int) trim($parts[1]);
        }

        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $ganjil = [];
        foreach ([7, 8, 9, 10, 11, 12] as $m) {
            $key = sprintf('%04d-%02d', $startYear, $m);
            $ganjil[] = [
                'key' => $key,
                'label' => $monthNames[$m] . ' ' . $startYear,
                'name' => $monthNames[$m],
                'short' => substr($monthNames[$m], 0, 3),
                'year' => $startYear,
                'month' => $m,
                'semester' => 'ganjil',
            ];
        }

        $genap = [];
        foreach ([1, 2, 3, 4, 5, 6] as $m) {
            $key = sprintf('%04d-%02d', $endYear, $m);
            $genap[] = [
                'key' => $key,
                'label' => $monthNames[$m] . ' ' . $endYear,
                'name' => $monthNames[$m],
                'short' => substr($monthNames[$m], 0, 3),
                'year' => $endYear,
                'month' => $m,
                'semester' => 'genap',
            ];
        }

        return [
            'tahun_ajaran' => $ta,
            'ganjil' => $ganjil,
            'genap' => $genap,
            'all' => array_merge($ganjil, $genap),
        ];
    }

    /**
     * Dapatkan daftar bulan yang sudah dibayar oleh donatur pada tahun ajaran tertentu.
     */
    public static function getPaidMonthsForDonatur(int $donaturId, ?int $tahunAjaranId = null): array
    {
        $query = DonasiMasuk::where('donatur_id', $donaturId);
        if ($tahunAjaranId) {
            $query->where('tahun_ajaran_id', $tahunAjaranId);
        }
        $donasis = $query->whereNotNull('periode_bulan')->get(['periode_bulan']);

        $paid = [];
        foreach ($donasis as $d) {
            if (is_array($d->periode_bulan)) {
                foreach ($d->periode_bulan as $m) {
                    $paid[] = (string) $m;
                }
            }
        }
        return array_unique($paid);
    }

    /**
     * Format keterangan periode dari daftar bulan yang dipilih.
     */
    public static function formatPeriodeKeterangan(array $selectedMonths, ?TahunAjaran $ta = null): string
    {
        if (empty($selectedMonths)) {
            return '';
        }
        sort($selectedMonths);
        $count = count($selectedMonths);

        $monthNames = [
            '01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr',
            '05' => 'Mei', '06' => 'Jun', '07' => 'Jul', '08' => 'Agt',
            '09' => 'Sep', '10' => 'Okt', '11' => 'Nov', '12' => 'Des',
        ];

        $taName = $ta ? $ta->nama : '';
        if ($ta && str_contains($ta->nama, '/')) {
            [$sy, $ey] = explode('/', $ta->nama);
            $sy = trim($sy);
            $ey = trim($ey);
            $ganjilKeys = ["$sy-07", "$sy-08", "$sy-09", "$sy-10", "$sy-11", "$sy-12"];
            $genapKeys = ["$ey-01", "$ey-02", "$ey-03", "$ey-04", "$ey-05", "$ey-06"];
            $fullKeys = array_merge($ganjilKeys, $genapKeys);

            if ($selectedMonths === $fullKeys) {
                return "1 Tahun Penuh T.A. {$taName} (12 Bulan)";
            }
            if ($selectedMonths === $ganjilKeys) {
                return "Semester Ganjil T.A. {$taName} (Jul - Des {$sy})";
            }
            if ($selectedMonths === $genapKeys) {
                return "Semester Genap T.A. {$taName} (Jan - Jun {$ey})";
            }
        }

        $labels = [];
        foreach ($selectedMonths as $m) {
            $parts = explode('-', $m);
            if (count($parts) === 2) {
                $labels[] = ($monthNames[$parts[1]] ?? $parts[1]) . "'" . substr($parts[0], 2);
            } else {
                $labels[] = $m;
            }
        }

        return implode(', ', $labels) . " ({$count} Bulan)";
    }

    #[Computed]
    public function availableMonths(): array
    {
        return self::getMonthsForTahunAjaran($this->tahunAjaranId);
    }

    #[Computed]
    public function totalKolektif(): float
    {
        $total = 0;
        foreach ($this->anggotaItems as $item) {
            if (!empty($item['is_selected'])) {
                $total += self::cleanNominal($item['nominal'] ?? 0);
            }
        }
        return (float) $total;
    }

    public function updatedTahunAjaranId()
    {
        unset($this->availableMonths);
        unset($this->totalKolektif);

        if ($this->modeDonasi === 'perorangan') {
            if ($this->donaturId) {
                $this->paidMonthsPerorangan = self::getPaidMonthsForDonatur($this->donaturId, $this->tahunAjaranId);
                $this->selectedBulanPerorangan = [];
                if ($this->komitmenBulananPerorangan > 0) {
                    $this->nominal = $this->komitmenBulananPerorangan;
                }
            }
        } else {
            // Mode Kelompok: update paid months untuk semua anggota
            foreach ($this->anggotaItems as $idx => $item) {
                $paid = self::getPaidMonthsForDonatur($item['donatur_id'], $this->tahunAjaranId);
                $this->anggotaItems[$idx]['paid_bulan'] = $paid;
                $this->anggotaItems[$idx]['selected_bulan'] = [];
                $this->anggotaItems[$idx]['nominal'] = (int) ($item['nominal_komitmen'] ?? 0);
            }
        }
    }

    public function updatedDonaturId()
    {
        $this->selectedBulanPerorangan = [];
        $this->paidMonthsPerorangan = [];
        $this->komitmenBulananPerorangan = 0;

        if ($this->donaturId) {
            $donatur = Donatur::with('kelompoks')->find($this->donaturId);
            if ($donatur && $donatur->tipe === 'rutin' && $donatur->kelompoks->isNotEmpty()) {
                $this->kelompokId = $donatur->kelompoks->first()->id;
                $anggota = $donatur->kelompokAnggotas()->where('kelompok_id', $this->kelompokId)->first();
                if ($anggota) {
                    $this->komitmenBulananPerorangan = (float) $anggota->nominal_komitmen;
                    $this->nominal = (int) $this->komitmenBulananPerorangan;
                }
                $this->paidMonthsPerorangan = self::getPaidMonthsForDonatur($this->donaturId, $this->tahunAjaranId);
            } else {
                $this->kelompokId = null;
                $this->nominal = '';
            }
        }
    }

    /**
     * Toggle checklist bulan individual untuk donatur perorangan.
     */
    public function toggleBulanPerorangan(string $monthKey)
    {
        if (in_array($monthKey, $this->paidMonthsPerorangan)) {
            return; // Bulan sudah lunas
        }

        if (in_array($monthKey, $this->selectedBulanPerorangan)) {
            $this->selectedBulanPerorangan = array_values(array_diff($this->selectedBulanPerorangan, [$monthKey]));
        } else {
            $this->selectedBulanPerorangan[] = $monthKey;
        }

        if ($this->komitmenBulananPerorangan > 0) {
            $count = count($this->selectedBulanPerorangan);
            $this->nominal = $count > 0 ? ($count * $this->komitmenBulananPerorangan) : (int) $this->komitmenBulananPerorangan;
        }
    }

    /**
     * Terapkan preset bulan cepat untuk perorangan (Ganjil, Genap, Full, Clear).
     */
    public function applyPresetPerorangan(string $preset)
    {
        $monthsInfo = self::getMonthsForTahunAjaran($this->tahunAjaranId);
        $targetKeys = match ($preset) {
            'ganjil' => array_column($monthsInfo['ganjil'], 'key'),
            'genap' => array_column($monthsInfo['genap'], 'key'),
            'full' => array_column($monthsInfo['all'], 'key'),
            'clear' => [],
            default => [],
        };

        if ($preset === 'clear') {
            $this->selectedBulanPerorangan = [];
            if ($this->komitmenBulananPerorangan > 0) {
                $this->nominal = (int) $this->komitmenBulananPerorangan;
            }
            return;
        }

        // Filter keluar bulan yang sudah lunas
        $available = array_values(array_diff($targetKeys, $this->paidMonthsPerorangan));
        $this->selectedBulanPerorangan = $available;

        if ($this->komitmenBulananPerorangan > 0) {
            $count = count($this->selectedBulanPerorangan);
            $this->nominal = $count > 0 ? ($count * $this->komitmenBulananPerorangan) : (int) $this->komitmenBulananPerorangan;
        }
    }

    public function updatedKelompokKolektifId()
    {
        $this->anggotaItems = [];
        $this->koordinatorDonaturId = null;
        $this->koordinatorNama = '';
        unset($this->totalKolektif);

        if (!$this->kelompokKolektifId) {
            return;
        }

        $kelompok = Kelompok::with(['anggotaKelompok.donatur', 'koordinator'])->find($this->kelompokKolektifId);
        if (!$kelompok) {
            return;
        }

        // Tentukan koordinator default
        if ($kelompok->koordinator_donatur_id) {
            $this->koordinatorDonaturId = $kelompok->koordinator_donatur_id;
            $this->koordinatorNama = $kelompok->koordinator?->nama ?? $kelompok->koordinator_nama ?? '';
        } elseif ($kelompok->koordinator_nama) {
            $this->koordinatorNama = $kelompok->koordinator_nama;
        } elseif ($kelompok->anggotaKelompok->isNotEmpty()) {
            $firstAnggota = $kelompok->anggotaKelompok->first();
            $this->koordinatorDonaturId = $firstAnggota->donatur_id;
            $this->koordinatorNama = $firstAnggota->donatur->nama;
        }

        // Siapkan daftar anggota kelompok dengan komitmen bulanan & status riwayat bulan
        foreach ($kelompok->anggotaKelompok as $ang) {
            $paid = self::getPaidMonthsForDonatur($ang->donatur_id, $this->tahunAjaranId);
            $komitmen = (float) $ang->nominal_komitmen;

            $this->anggotaItems[] = [
                'donatur_id' => $ang->donatur_id,
                'nama' => $ang->donatur->nama,
                'nomor_telepon' => $ang->donatur->nomor_telepon ?? '',
                'nominal_komitmen' => $komitmen,
                'selected_bulan' => [],
                'paid_bulan' => $paid,
                'nominal' => (int) $komitmen,
                'is_selected' => true,
                'keterangan' => '',
            ];
        }

        $this->selectAllAnggotaKolektif = true;
    }

    /**
     * Terapkan preset bulan massal ke seluruh anggota kelompok yang dicentang.
     */
    public function applyBulkMonths(string $preset)
    {
        $monthsInfo = self::getMonthsForTahunAjaran($this->tahunAjaranId);
        $targetKeys = match ($preset) {
            'ganjil' => array_column($monthsInfo['ganjil'], 'key'),
            'genap' => array_column($monthsInfo['genap'], 'key'),
            'full' => array_column($monthsInfo['all'], 'key'),
            'clear' => [],
            default => [],
        };

        foreach ($this->anggotaItems as $idx => $item) {
            if (!empty($item['is_selected'])) {
                if ($preset === 'clear') {
                    $this->anggotaItems[$idx]['selected_bulan'] = [];
                    $this->anggotaItems[$idx]['nominal'] = (int) ($item['nominal_komitmen'] ?? 0);
                } else {
                    $paid = $item['paid_bulan'] ?? [];
                    $available = array_values(array_diff($targetKeys, $paid));
                    $this->anggotaItems[$idx]['selected_bulan'] = $available;
                    $komitmen = (float) ($item['nominal_komitmen'] ?? 0);
                    $count = count($available);
                    $this->anggotaItems[$idx]['nominal'] = $count > 0 ? ($count * $komitmen) : (int) $komitmen;
                }
            }
        }

        unset($this->totalKolektif);
    }

    /**
     * Toggle checklist bulan untuk donatur anggota tertentu dalam kelompok.
     */
    public function toggleMemberMonth(int $memberIndex, string $monthKey)
    {
        if (!isset($this->anggotaItems[$memberIndex])) {
            return;
        }

        $item = &$this->anggotaItems[$memberIndex];
        if (in_array($monthKey, $item['paid_bulan'] ?? [])) {
            return;
        }

        $selected = $item['selected_bulan'] ?? [];
        if (in_array($monthKey, $selected)) {
            $selected = array_values(array_diff($selected, [$monthKey]));
        } else {
            $selected[] = $monthKey;
        }

        $item['selected_bulan'] = $selected;
        $komitmen = (float) ($item['nominal_komitmen'] ?? 0);
        $count = count($selected);
        $item['nominal'] = $count > 0 ? ($count * $komitmen) : (int) $komitmen;

        unset($this->totalKolektif);
    }

    /**
     * Terapkan preset bulan individual untuk salah satu anggota dalam kelompok.
     */
    public function applyMemberPreset(int $memberIndex, string $preset)
    {
        if (!isset($this->anggotaItems[$memberIndex])) {
            return;
        }

        $monthsInfo = self::getMonthsForTahunAjaran($this->tahunAjaranId);
        $targetKeys = match ($preset) {
            'ganjil' => array_column($monthsInfo['ganjil'], 'key'),
            'genap' => array_column($monthsInfo['genap'], 'key'),
            'full' => array_column($monthsInfo['all'], 'key'),
            'clear' => [],
            default => [],
        };

        $item = &$this->anggotaItems[$memberIndex];
        if ($preset === 'clear') {
            $item['selected_bulan'] = [];
            $item['nominal'] = (int) ($item['nominal_komitmen'] ?? 0);
            unset($this->totalKolektif);
            return;
        }

        $paid = $item['paid_bulan'] ?? [];
        $available = array_values(array_diff($targetKeys, $paid));
        $item['selected_bulan'] = $available;
        $komitmen = (float) ($item['nominal_komitmen'] ?? 0);
        $count = count($available);
        $item['nominal'] = $count > 0 ? ($count * $komitmen) : (int) $komitmen;

        unset($this->totalKolektif);
    }

    public function updatedKoordinatorDonaturId($value)
    {
        if ($value) {
            $d = Donatur::find($value);
            if ($d) {
                $this->koordinatorNama = $d->nama;
            }
        }
    }

    public function updatedSelectAllAnggotaKolektif($value)
    {
        foreach ($this->anggotaItems as $i => $item) {
            $this->anggotaItems[$i]['is_selected'] = (bool) $value;
        }
        unset($this->totalKolektif);
    }

    public function save()
    {
        $this->guard('ota.donasi.tambah');

        if ($this->modeDonasi === 'perorangan') {
            return $this->savePerorangan();
        } else {
            return $this->saveKelompokKolektif();
        }
    }

    protected function savePerorangan()
    {
        $this->validate([
            'donaturId' => 'required|exists:ota_donaturs,id',
            'tanggalDonasi' => 'required|date',
            'nominal' => 'required',
        ], [
            'donaturId.required' => 'Pilih donatur terlebih dahulu.',
            'nominal.required' => 'Nominal donasi wajib diisi.',
        ]);

        $nominalClean = self::cleanNominal($this->nominal);
        if ($nominalClean < 1000) {
            session()->flash('error', 'Nominal donasi minimal Rp 1.000.');
            return null;
        }

        $donatur = Donatur::findOrFail($this->donaturId);
        $ta = $this->tahunAjaranId ? TahunAjaran::find($this->tahunAjaranId) : null;

        $selMonths = !empty($this->selectedBulanPerorangan) ? array_values($this->selectedBulanPerorangan) : null;
        $jmlBulan = $selMonths ? count($selMonths) : 1;
        $ketPeriode = $selMonths ? self::formatPeriodeKeterangan($selMonths, $ta) : null;

        $donasi = OtaKasService::catatDonasiMasuk(
            donatur: $donatur,
            nominal: $nominalClean,
            tanggal: $this->tanggalDonasi,
            kelompokId: $this->kelompokId,
            keterangan: $this->keterangan ?: null,
            userId: auth()->id(),
            tahunAjaranId: $this->tahunAjaranId,
            periodeBulan: $selMonths,
            jumlahBulan: $jmlBulan,
            keteranganPeriode: $ketPeriode
        );

        $infoPeriode = $ketPeriode ? " [{$ketPeriode}]" : "";
        session()->flash('message', "Donasi masuk perorangan{$infoPeriode} berhasil dicatat! Nomor Transaksi: {$donasi->nomor_transaksi}");

        return $this->redirect(route('beasiswa-ota.donasi-masuk'), navigate: true);
    }

    protected function saveKelompokKolektif()
    {
        $this->validate([
            'kelompokKolektifId' => 'required|exists:ota_kelompoks,id',
            'koordinatorNama' => 'required|string|max:255',
            'tanggalDonasiKolektif' => 'required|date',
        ], [
            'kelompokKolektifId.required' => 'Pilih kelompok donatur terlebih dahulu.',
            'koordinatorNama.required' => 'Nama koordinator penyetor wajib diisi.',
        ]);

        $kelompok = Kelompok::findOrFail($this->kelompokKolektifId);
        $ta = $this->tahunAjaranId ? TahunAjaran::find($this->tahunAjaranId) : null;

        // Filter anggota yang dicentang
        $selectedItems = [];
        foreach ($this->anggotaItems as $item) {
            if (!empty($item['is_selected'])) {
                $nom = self::cleanNominal($item['nominal'] ?? 0);
                if ($nom > 0) {
                    $selMonths = !empty($item['selected_bulan']) ? array_values($item['selected_bulan']) : null;
                    $jmlBulan = $selMonths ? count($selMonths) : 1;
                    $ketPeriode = $selMonths ? self::formatPeriodeKeterangan($selMonths, $ta) : null;

                    $selectedItems[] = [
                        'donatur_id' => $item['donatur_id'],
                        'nominal' => $nom,
                        'periode_bulan' => $selMonths,
                        'jumlah_bulan' => $jmlBulan,
                        'keterangan_periode' => $ketPeriode,
                        'keterangan' => $item['keterangan'] ?? null,
                    ];
                }
            }
        }

        if (empty($selectedItems)) {
            session()->flash('error', 'Centang minimal satu anggota donatur dengan nominal iuran lebih dari Rp 0.');
            return null;
        }

        $result = OtaKasService::catatDonasiKolektifKelompok(
            kelompok: $kelompok,
            koordinatorNama: $this->koordinatorNama,
            items: $selectedItems,
            tanggal: $this->tanggalDonasiKolektif,
            keteranganGlobal: $this->keteranganKolektif ?: "Setoran Iuran {$kelompok->nama_kelompok} via Koordinator {$this->koordinatorNama}",
            userId: auth()->id(),
            tahunAjaranId: $this->tahunAjaranId
        );

        // Jika kelompok belum punya koordinator tersimpan, simpan koordinator ini
        if (!$kelompok->koordinator_nama && $this->koordinatorNama) {
            $kelompok->update([
                'koordinator_donatur_id' => $this->koordinatorDonaturId,
                'koordinator_nama' => $this->koordinatorNama,
            ]);
        }

        $formattedTotal = number_format($result['total_nominal'], 0, ',', '.');
        session()->flash('message', "Setoran kolektif {$kelompok->nama_kelompok} ({$result['count']} donatur) total Rp {$formattedTotal} via Koordinator {$this->koordinatorNama} berhasil dicatat! Kode Batch: {$result['batch_kode']}");

        return $this->redirect(route('beasiswa-ota.donasi-masuk'), navigate: true);
    }

    public function render()
    {
        $donaturs = Donatur::where('is_aktif', true)->orderBy('nama')->get();
        $kelompoks = Kelompok::where('is_aktif', true)->with(['anggotaKelompok.donatur', 'koordinator'])->orderBy('nama_kelompok')->get();
        $tahunAjarans = TahunAjaran::orderByDesc('is_active')->orderByDesc('nama')->get();

        return view('beasiswaota::livewire.donasi-masuk-create', [
            'donaturs' => $donaturs,
            'kelompoks' => $kelompoks,
            'tahunAjarans' => $tahunAjarans,
        ])->layout('components.layouts.app', ['title' => 'Catat Penerimaan Donasi Masuk OTA']);
    }
}
