<?php

namespace App\Modules\BeasiswaOta\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\BeasiswaOta\Models\Alokasi;
use App\Modules\BeasiswaOta\Models\KasMutasi;
use App\Modules\BeasiswaOta\Models\Kelompok;
use App\Modules\BeasiswaOta\Models\SiswaPenerima;
use App\Modules\BeasiswaOta\Services\OtaKasService;
use App\Modules\Kesiswaan\Models\Siswa;
use Livewire\Component;
use Livewire\WithPagination;

class PenyaluranIndex extends Component
{
    use GuardsPermission, WithPagination;

    public string $activeTab = 'rutin'; // 'rutin' | 'insidental' | 'riwayat'

    // Form Alokasi Rutin 6 Bulanan
    public ?int $rutinKelompokId = null;
    public ?int $rutinSemesterId = null;
    public ?int $rutinTahunAjaranId = null;
    public string $rutinTanggal = '';
    public string $rutinCatatan = '';
    public array $rutinDetails = [];
    public float $rutinTotalAlokasi = 0;
    public float $rutinSurplus = 0;

    // Form Alokasi Insidental Fleksibel
    public ?int $insidentalSiswaId = null;
    public string $insidentalSearchSiswa = '';
    public array $insidentalSiswaOptions = [];
    public string $insidentalTanggal = '';
    public string $insidentalCatatan = '';
    public array $insidentalDetails = [];
    public float $insidentalTotal = 0;

    public function mount()
    {
        $this->rutinTanggal = now()->toDateString();
        $this->insidentalTanggal = now()->toDateString();

        $activeSemester = Semester::where('is_active', true)->first();
        if ($activeSemester) {
            $this->rutinSemesterId = $activeSemester->id;
            $this->rutinTahunAjaranId = $activeSemester->tahun_ajaran_id;
        }

        $this->initDefaultInsidentalDetails();
    }

    public function initDefaultInsidentalDetails()
    {
        $this->insidentalDetails = [
            [
                'jenis_komponen' => 'spp',
                'nama_komponen' => 'Bantuan SPP',
                'bulan' => null,
                'tahun' => (int) date('Y'),
                'nominal' => 0,
                'keterangan' => 'Bantuan dari Donatur Insidental',
            ]
        ];
        $this->calculateInsidentalTotal();
    }

    // --- Rutin Methods ---
    public function updatedRutinKelompokId()
    {
        $this->rutinDetails = [];
        $this->rutinTotalAlokasi = 0;
        $this->rutinSurplus = 0;

        if (!$this->rutinKelompokId) {
            return;
        }

        $kelompok = Kelompok::with(['siswaPenerima.komponenBiayas'])->find($this->rutinKelompokId);
        if (!$kelompok || !$kelompok->siswaPenerima) {
            return;
        }

        $penerima = $kelompok->siswaPenerima;
        $saldoKas = OtaKasService::getSaldoKelompok($kelompok->id);

        $activeSemester = Semester::find($this->rutinSemesterId);
        $isGanjil = $activeSemester ? ($activeSemester->semester === 'ganjil') : true;

        $bulanList = $isGanjil
            ? ['Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
            : ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'];

        $tahun = (int) date('Y');

        foreach ($penerima->komponenBiayas as $kmp) {
            if (in_array($kmp->jenis_komponen, ['spp', 'asrama'])) {
                $tarifBulan = (float) $kmp->nominal_per_bulan;
                foreach ($bulanList as $bln) {
                    $this->rutinDetails[] = [
                        'komponen_biaya_id' => $kmp->id,
                        'jenis_komponen' => $kmp->jenis_komponen,
                        'nama_komponen' => $kmp->nama_komponen,
                        'bulan' => $bln,
                        'tahun' => $tahun,
                        'nominal' => $tarifBulan,
                        'selected' => true,
                        'keterangan' => "Alokasi {$kmp->nama_komponen} Bulan {$bln}",
                    ];
                }
            } else {
                $this->rutinDetails[] = [
                    'komponen_biaya_id' => $kmp->id,
                    'jenis_komponen' => $kmp->jenis_komponen,
                    'nama_komponen' => $kmp->nama_komponen,
                    'bulan' => null,
                    'tahun' => $tahun,
                    'nominal' => (float) $kmp->total_target_semester,
                    'selected' => true,
                    'keterangan' => $kmp->keterangan ?: 'Penyaluran Semester',
                ];
            }
        }

        $this->calculateRutinTotal();
    }

    public function toggleRutinItem(int $index)
    {
        if (isset($this->rutinDetails[$index])) {
            $this->rutinDetails[$index]['selected'] = !$this->rutinDetails[$index]['selected'];
            $this->calculateRutinTotal();
        }
    }

    public function calculateRutinTotal()
    {
        $total = 0;
        foreach ($this->rutinDetails as $item) {
            if (!empty($item['selected'])) {
                $total += (float) ($item['nominal'] ?? 0);
            }
        }
        $this->rutinTotalAlokasi = $total;

        if ($this->rutinKelompokId) {
            $saldoKas = OtaKasService::getSaldoKelompok($this->rutinKelompokId);
            $this->rutinSurplus = max(0, $saldoKas - $total);
        } else {
            $this->rutinSurplus = 0;
        }
    }

    public function saveAlokasiRutin()
    {
        $this->validate([
            'rutinKelompokId' => 'required|exists:ota_kelompoks,id',
            'rutinTanggal' => 'required|date',
        ]);

        $kelompok = Kelompok::with('siswaPenerima.siswa')->findOrFail($this->rutinKelompokId);
        if (!$kelompok->siswaPenerima) {
            session()->flash('error_rutin', 'Kelompok ini belum memiliki siswa penerima asuh.');
            return;
        }

        $saldoKas = OtaKasService::getSaldoKelompok($kelompok->id);
        $totalDibutuhkan = $this->rutinTotalAlokasi + $this->rutinSurplus;

        if ($saldoKas < $this->rutinTotalAlokasi) {
            session()->flash('error_rutin', 'Saldo kas kelompok tidak mencukupi untuk alokasi yang dipilih (Saldo: Rp ' . number_format($saldoKas, 0, ',', '.') . ', Dibutuhkan: Rp ' . number_format($this->rutinTotalAlokasi, 0, ',', '.') . ').');
            return;
        }

        $selectedDetails = array_filter($this->rutinDetails, fn($d) => !empty($d['selected']));
        if (empty($selectedDetails)) {
            session()->flash('error_rutin', 'Pilih minimal 1 komponen pembiayaan yang akan disalurkan.');
            return;
        }

        $alokasi = OtaKasService::catatAlokasiRutin(
            kelompok: $kelompok,
            siswa: $kelompok->siswaPenerima->siswa,
            details: $selectedDetails,
            totalNominal: $this->rutinTotalAlokasi,
            surplusDialihkan: $this->rutinSurplus,
            tanggal: $this->rutinTanggal,
            semesterId: $this->rutinSemesterId,
            tahunAjaranId: $this->rutinTahunAjaranId,
            catatan: $this->rutinCatatan,
            userId: auth()->id()
        );

        $this->rutinKelompokId = null;
        $this->rutinDetails = [];
        $this->rutinTotalAlokasi = 0;
        $this->rutinSurplus = 0;
        $this->rutinCatatan = '';

        session()->flash('message', "Alokasi beasiswa 6 bulanan berhasil disalurkan! Nomor Kwitansi: {$alokasi->nomor_kwitansi}");
        $this->activeTab = 'riwayat';
    }

    // --- Insidental Methods ---
    public function updatedInsidentalSearchSiswa()
    {
        if (strlen($this->insidentalSearchSiswa) < 2) {
            $this->insidentalSiswaOptions = [];
            return;
        }

        $this->insidentalSiswaOptions = Siswa::with('rombel')
            ->where(function ($q) {
                $q->where('nama', 'like', "%{$this->insidentalSearchSiswa}%")
                    ->orWhere('nisn', 'like', "%{$this->insidentalSearchSiswa}%");
            })
            ->where('is_active', true)
            ->take(8)
            ->get(['id', 'nama', 'nisn', 'rombel_id'])
            ->toArray();
    }

    public function selectInsidentalSiswa(int $id, string $nama)
    {
        $this->insidentalSiswaId = $id;
        $this->insidentalSearchSiswa = $nama;
        $this->insidentalSiswaOptions = [];
    }

    public function addInsidentalRow()
    {
        $this->insidentalDetails[] = [
            'jenis_komponen' => 'lainnya',
            'nama_komponen' => '',
            'bulan' => null,
            'tahun' => (int) date('Y'),
            'nominal' => 0,
            'keterangan' => '',
        ];
        $this->calculateInsidentalTotal();
    }

    public function removeInsidentalRow(int $index)
    {
        unset($this->insidentalDetails[$index]);
        $this->insidentalDetails = array_values($this->insidentalDetails);
        $this->calculateInsidentalTotal();
    }

    public function calculateInsidentalTotal()
    {
        $total = 0;
        foreach ($this->insidentalDetails as $item) {
            $total += (float) ($item['nominal'] ?? 0);
        }
        $this->insidentalTotal = $total;
    }

    public function saveAlokasiInsidental()
    {
        $this->validate([
            'insidentalSiswaId' => 'required|exists:siswas,id',
            'insidentalTanggal' => 'required|date',
            'insidentalDetails.*.nama_komponen' => 'required|string',
            'insidentalDetails.*.nominal' => 'required|numeric|min:1000',
        ]);

        $saldoInsidental = OtaKasService::getSaldoInsidental();
        if ($this->insidentalTotal > $saldoInsidental) {
            session()->flash('error_insidental', 'Saldo Kas Insidental tidak mencukupi (Saldo: Rp ' . number_format($saldoInsidental, 0, ',', '.') . ', Diminta: Rp ' . number_format($this->insidentalTotal, 0, ',', '.') . ').');
            return;
        }

        $siswa = Siswa::findOrFail($this->insidentalSiswaId);

        $alokasi = OtaKasService::catatAlokasiInsidental(
            siswa: $siswa,
            details: $this->insidentalDetails,
            totalNominal: $this->insidentalTotal,
            tanggal: $this->insidentalTanggal,
            semesterId: $this->rutinSemesterId,
            tahunAjaranId: $this->rutinTahunAjaranId,
            catatan: $this->insidentalCatatan,
            userId: auth()->id()
        );

        $this->insidentalSiswaId = null;
        $this->insidentalSearchSiswa = '';
        $this->insidentalCatatan = '';
        $this->initDefaultInsidentalDetails();

        session()->flash('message', "Penyaluran dana insidental berhasil! Nomor Kwitansi: {$alokasi->nomor_kwitansi}");
        $this->activeTab = 'riwayat';
    }

    public function deleteAlokasi(int $id)
    {
        $alokasi = Alokasi::findOrFail($id);

        if ($alokasi->surplus_dialihkan > 0 && OtaKasService::getSaldoInsidental() < (float) $alokasi->surplus_dialihkan) {
            session()->flash('error_rutin', 'Penyaluran tidak dapat dibatalkan karena surplus yang dialihkan ke kas insidental telah terpakai.');
            return;
        }

        KasMutasi::where('referensi_tipe', 'alokasi')
            ->where('referensi_id', $alokasi->id)
            ->delete();

        KasMutasi::where('referensi_tipe', 'surplus_transfer')
            ->where('referensi_id', $alokasi->id)
            ->delete();

        $alokasi->delete();
        session()->flash('message', 'Catatan penyaluran berhasil dibatalkan dan kas telah dikembalikan.');
    }

    public function render()
    {
        $saldoInsidental = OtaKasService::getSaldoInsidental();

        $kelompoks = Kelompok::with(['siswaPenerima.siswa.rombel'])
            ->where('is_aktif', true)
            ->get();

        $semesters = Semester::with('tahunAjaran')->orderByDesc('id')->get();

        $alokasis = Alokasi::with(['siswa.rombel', 'kelompok', 'semester', 'details'])
            ->latest('tanggal_alokasi')
            ->latest('id')
            ->paginate(15);

        $selectedKelompok = $this->rutinKelompokId ? Kelompok::with('siswaPenerima.siswa')->find($this->rutinKelompokId) : null;
        $saldoKelompok = $this->rutinKelompokId ? OtaKasService::getSaldoKelompok($this->rutinKelompokId) : 0;

        return view('beasiswaota::livewire.penyaluran-index', [
            'saldoInsidental' => $saldoInsidental,
            'kelompoks' => $kelompoks,
            'semesters' => $semesters,
            'alokasis' => $alokasis,
            'selectedKelompok' => $selectedKelompok,
            'saldoKelompok' => $saldoKelompok,
        ])->layout('components.layouts.app', ['title' => 'Penyaluran Beasiswa OTA']);
    }
}
