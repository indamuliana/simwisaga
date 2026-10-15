<?php

namespace App\Modules\BeasiswaOta\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\BeasiswaOta\Models\Kelompok;
use App\Modules\BeasiswaOta\Models\KomponenBiaya;
use App\Modules\BeasiswaOta\Models\SiswaPenerima;
use App\Modules\Kesiswaan\Models\Siswa;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class PenerimaBeasiswaIndex extends Component
{
    use GuardsPermission, WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public ?int $penerimaId = null;

    // Form fields
    public ?int $siswaId = null;
    public ?int $kelompokId = null;
    public string $status = 'aktif';
    public string $keterangan = '';

    // Dynamic Komponen Biaya fields
    public array $komponens = [];

    // Siswa search dropdown state
    public string $searchSiswaQuery = '';
    public array $siswaOptions = [];

    public function mount()
    {
        $this->initDefaultKomponens();
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
        // Hapus desimal sen di akhir (.00 atau ,00) jika ada
        $str = preg_replace('/[.,]00$/', '', $str);
        // Hapus semua karakter selain angka
        $cleaned = preg_replace('/[^\d]/', '', $str);
        return (float) ($cleaned ?: 0);
    }

    #[Computed]
    public function grandTotal(): float
    {
        $total = 0;
        foreach ($this->komponens as $kmp) {
            $total += self::cleanNominal($kmp['total_target_semester'] ?? 0);
        }
        return (float) $total;
    }

    public function getGrandTotalProperty(): float
    {
        return $this->grandTotal();
    }

    public function initDefaultKomponens()
    {
        $this->komponens = [
            [
                'id' => null,
                'jenis_komponen' => 'spp',
                'nama_komponen' => 'SPP Bulanan',
                'nominal_per_bulan' => 200000,
                'total_target_semester' => 1200000,
                'keterangan' => '6 Bulan @ Rp 200.000',
            ],
            [
                'id' => null,
                'jenis_komponen' => 'asrama',
                'nama_komponen' => 'Asrama Bulanan',
                'nominal_per_bulan' => 100000,
                'total_target_semester' => 600000,
                'keterangan' => '6 Bulan @ Rp 100.000',
            ],
            [
                'id' => null,
                'jenis_komponen' => 'dsp',
                'nama_komponen' => 'Cicilan DSP',
                'nominal_per_bulan' => null,
                'total_target_semester' => 200000,
                'keterangan' => 'Cicilan Gedung / DSP',
            ],
        ];
    }

    public function updatedSearchSiswaQuery()
    {
        if (strlen($this->searchSiswaQuery) < 2) {
            $this->siswaOptions = [];
            return;
        }

        $this->siswaOptions = Siswa::with('rombel')
            ->where(function ($q) {
                $q->where('nama', 'like', "%{$this->searchSiswaQuery}%")
                    ->orWhere('nisn', 'like', "%{$this->searchSiswaQuery}%")
                    ->orWhere('nipd', 'like', "%{$this->searchSiswaQuery}%");
            })
            ->where('is_active', true)
            ->take(10)
            ->get(['id', 'nama', 'nisn', 'rombel_id'])
            ->toArray();
    }

    public function selectSiswa(int $id, string $nama)
    {
        $this->siswaId = $id;
        $this->searchSiswaQuery = $nama;
        $this->siswaOptions = [];
    }

    public function resetSelectedSiswa()
    {
        $this->siswaId = null;
        $this->searchSiswaQuery = '';
        $this->siswaOptions = [];
    }

    public function openCreate()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id)
    {
        $penerima = SiswaPenerima::with(['siswa.rombel', 'komponenBiayas', 'kelompok'])->findOrFail($id);
        $this->penerimaId = $penerima->id;
        $this->siswaId = $penerima->siswa_id;
        $this->searchSiswaQuery = $penerima->siswa->nama . ' (' . ($penerima->siswa->rombel?->nama ?? '-') . ')';
        $this->kelompokId = $penerima->kelompok_id;
        $this->status = $penerima->status;
        $this->keterangan = $penerima->keterangan ?? '';

        $this->komponens = [];
        foreach ($penerima->komponenBiayas as $k) {
            $this->komponens[] = [
                'id' => $k->id,
                'jenis_komponen' => $k->jenis_komponen,
                'nama_komponen' => $k->nama_komponen,
                'nominal_per_bulan' => $k->nominal_per_bulan !== null ? (int) $k->nominal_per_bulan : null,
                'total_target_semester' => (int) $k->total_target_semester,
                'keterangan' => $k->keterangan ?? '',
            ];
        }

        if (empty($this->komponens)) {
            $this->initDefaultKomponens();
        }

        $this->showModal = true;
    }

    public function addKomponenRow()
    {
        $this->komponens[] = [
            'id' => null,
            'jenis_komponen' => 'lainnya',
            'nama_komponen' => '',
            'nominal_per_bulan' => null,
            'total_target_semester' => 0,
            'keterangan' => '',
        ];
    }

    public function removeKomponenRow(int $index)
    {
        unset($this->komponens[$index]);
        $this->komponens = array_values($this->komponens);
    }

    public function updated($propertyName): void
    {
        if (str_starts_with($propertyName, 'komponens.')) {
            $parts = explode('.', $propertyName);
            if (isset($parts[1], $parts[2])) {
                $idx = (int) $parts[1];
                $field = $parts[2];

                if (!isset($this->komponens[$idx])) {
                    return;
                }

                if ($field === 'jenis_komponen') {
                    $jenis = $this->komponens[$idx]['jenis_komponen'] ?? 'lainnya';
                    if ($jenis === 'spp') {
                        $this->komponens[$idx]['nama_komponen'] = 'SPP Bulanan';
                        $perBulan = self::cleanNominal($this->komponens[$idx]['nominal_per_bulan'] ?? 200000);
                        if ($perBulan <= 0) $perBulan = 200000;
                        $this->komponens[$idx]['nominal_per_bulan'] = $perBulan;
                        $this->komponens[$idx]['total_target_semester'] = $perBulan * 6;
                        $this->komponens[$idx]['keterangan'] = '6 Bulan @ Rp ' . number_format($perBulan, 0, ',', '.');
                    } elseif ($jenis === 'asrama') {
                        $this->komponens[$idx]['nama_komponen'] = 'Asrama Bulanan';
                        $perBulan = self::cleanNominal($this->komponens[$idx]['nominal_per_bulan'] ?? 100000);
                        if ($perBulan <= 0) $perBulan = 100000;
                        $this->komponens[$idx]['nominal_per_bulan'] = $perBulan;
                        $this->komponens[$idx]['total_target_semester'] = $perBulan * 6;
                        $this->komponens[$idx]['keterangan'] = '6 Bulan @ Rp ' . number_format($perBulan, 0, ',', '.');
                    } elseif ($jenis === 'dsp') {
                        if (empty($this->komponens[$idx]['nama_komponen']) || in_array($this->komponens[$idx]['nama_komponen'], ['SPP Bulanan', 'Asrama Bulanan'])) {
                            $this->komponens[$idx]['nama_komponen'] = 'Cicilan DSP';
                        }
                        $this->komponens[$idx]['nominal_per_bulan'] = null;
                        if (empty($this->komponens[$idx]['keterangan'])) {
                            $this->komponens[$idx]['keterangan'] = 'Cicilan Gedung / DSP';
                        }
                    } else {
                        $this->komponens[$idx]['nominal_per_bulan'] = null;
                    }
                } elseif ($field === 'nominal_per_bulan') {
                    $cleaned = self::cleanNominal($this->komponens[$idx]['nominal_per_bulan'] ?? 0);
                    $this->komponens[$idx]['nominal_per_bulan'] = $cleaned > 0 ? $cleaned : null;

                    if (in_array($this->komponens[$idx]['jenis_komponen'] ?? '', ['spp', 'asrama'])) {
                        $this->komponens[$idx]['total_target_semester'] = $cleaned * 6;
                        $this->komponens[$idx]['keterangan'] = '6 Bulan @ Rp ' . number_format($cleaned, 0, ',', '.');
                    }
                } elseif ($field === 'total_target_semester') {
                    $cleaned = self::cleanNominal($this->komponens[$idx]['total_target_semester'] ?? 0);
                    $this->komponens[$idx]['total_target_semester'] = $cleaned;
                }
            }
        }
    }

    public function save()
    {
        // 1. Bersihkan semua angka sebelum divalidasi dan disimpan
        foreach ($this->komponens as $idx => $kmp) {
            $this->komponens[$idx]['nominal_per_bulan'] = !empty($kmp['nominal_per_bulan'])
                ? self::cleanNominal($kmp['nominal_per_bulan'])
                : null;
            $this->komponens[$idx]['total_target_semester'] = self::cleanNominal($kmp['total_target_semester'] ?? 0);
        }

        $this->validate([
            'siswaId' => 'required|exists:siswas,id',
            'kelompokId' => 'nullable|exists:ota_kelompoks,id',
            'status' => 'required|in:aktif,selesai,dibatalkan',
            'komponens' => 'required|array|min:1',
            'komponens.*.nama_komponen' => 'required|string|max:100',
            'komponens.*.total_target_semester' => 'required|numeric|min:0',
        ], [
            'siswaId.required' => 'Pilih siswa penerima beasiswa terlebih dahulu.',
            'komponens.min' => 'Minimal harus ada 1 komponen pembiayaan.',
            'komponens.*.nama_komponen.required' => 'Nama komponen pembiayaan tidak boleh kosong.',
            'komponens.*.total_target_semester.required' => 'Total nominal semester harus diisi.',
            'komponens.*.total_target_semester.min' => 'Nominal target tidak boleh minus.',
        ]);

        // Cek jika siswa sudah aktif di penerima lain
        $duplicate = SiswaPenerima::where('siswa_id', $this->siswaId)
            ->where('status', 'aktif')
            ->when($this->penerimaId, fn($q) => $q->where('id', '!=', $this->penerimaId))
            ->exists();

        if ($duplicate) {
            session()->flash('modal_error', 'Siswa ini sudah terdaftar sebagai penerima beasiswa OTA aktif.');
            return;
        }

        // Cek jika kelompok sudah membiayai siswa lain
        if ($this->kelompokId) {
            $duplicateKelompok = SiswaPenerima::where('kelompok_id', $this->kelompokId)
                ->where('status', 'aktif')
                ->when($this->penerimaId, fn($q) => $q->where('id', '!=', $this->penerimaId))
                ->exists();

            if ($duplicateKelompok) {
                session()->flash('modal_error', 'Kelompok ini sudah memiliki siswa binaan aktif. 1 Kelompok hanya membiayai 1 Siswa.');
                return;
            }
        }

        $penerima = SiswaPenerima::updateOrCreate(
            ['id' => $this->penerimaId],
            [
                'siswa_id' => $this->siswaId,
                'kelompok_id' => $this->kelompokId,
                'status' => $this->status,
                'keterangan' => $this->keterangan,
                'tanggal_mulai' => now()->toDateString(),
            ]
        );

        // Update / Sync Komponen Biaya
        $keepIds = [];
        foreach ($this->komponens as $item) {
            $komponen = KomponenBiaya::updateOrCreate(
                ['id' => $item['id'] ?? null],
                [
                    'siswa_penerima_id' => $penerima->id,
                    'jenis_komponen' => $item['jenis_komponen'],
                    'nama_komponen' => $item['nama_komponen'],
                    'nominal_per_bulan' => $item['nominal_per_bulan'] ?: null,
                    'total_target_semester' => $item['total_target_semester'] ?: 0,
                    'keterangan' => $item['keterangan'] ?? null,
                ]
            );
            $keepIds[] = $komponen->id;
        }

        KomponenBiaya::where('siswa_penerima_id', $penerima->id)
            ->whereNotIn('id', $keepIds)
            ->delete();

        $this->showModal = false;
        $this->resetForm();
        session()->flash('message', 'Data penerima beasiswa dan komponen pembiayaan berhasil disimpan!');
    }

    public function delete(int $id)
    {
        $p = SiswaPenerima::findOrFail($id);
        $p->delete();
        session()->flash('message', 'Data penerima beasiswa berhasil dihapus.');
    }

    protected function resetForm()
    {
        $this->penerimaId = null;
        $this->siswaId = null;
        $this->searchSiswaQuery = '';
        $this->siswaOptions = [];
        $this->kelompokId = null;
        $this->status = 'aktif';
        $this->keterangan = '';
        $this->initDefaultKomponens();
    }

    public function render()
    {
        $penerimas = SiswaPenerima::query()
            ->with(['siswa.rombel', 'kelompok', 'komponenBiayas'])
            ->when($this->search, function ($q) {
                $q->whereHas('siswa', function ($sq) {
                    $sq->where('nama', 'like', "%{$this->search}%")
                        ->orWhere('nisn', 'like', "%{$this->search}%");
                });
            })
            ->latest('id')
            ->paginate(10);

        // Ambil kelompok yang belum ada siswa binaan aktif (atau kelompok yang sedang dipilih)
        $availableKelompoks = Kelompok::where('is_aktif', true)
            ->where(function ($q) {
                $q->whereDoesntHave('siswaPenerima', function ($sq) {
                    $sq->where('status', 'aktif');
                });
                if ($this->kelompokId) {
                    $q->orWhere('id', $this->kelompokId);
                }
            })
            ->orderBy('nama_kelompok')
            ->get();

        return view('beasiswaota::livewire.penerima-beasiswa-index', [
            'penerimas' => $penerimas,
            'availableKelompoks' => $availableKelompoks,
        ])->layout('components.layouts.app', ['title' => 'Siswa Penerima & Komponen Pembiayaan OTA']);
    }
}
