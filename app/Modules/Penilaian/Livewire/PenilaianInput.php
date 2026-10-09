<?php

namespace App\Modules\Penilaian\Livewire;

use Livewire\Component;
use App\Modules\Kepegawaian\Models\PenugasanMengajar;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Penilaian\Models\TujuanPembelajaran;
use App\Modules\Penilaian\Models\NilaiFormatif;
use App\Modules\Penilaian\Models\NilaiSumatif;
use App\Modules\Penilaian\Models\NilaiAkhir;
use Livewire\Attributes\Layout;

class PenilaianInput extends Component
{
    public $penugasan_id;
    public $penugasan;
    public $siswas = [];
    public $tps = [];
    
    // Tab state: 'tp', 'formatif', 'sumatif', 'rekap'
    public $activeTab = 'tp';

    // State for TP Form
    public $tp_kode = '';
    public $tp_deskripsi = '';

    // State for Input Formatif
    public $formatifData = [];

    // State for Input Sumatif
    public $sumatifData = [];

    // State for Rekap
    public $rekapData = [];

    public function mount($penugasan_id)
    {
        $this->penugasan_id = $penugasan_id;
        $this->penugasan = PenugasanMengajar::with(['mapel', 'rombel'])->findOrFail($penugasan_id);
        
        $this->loadTP();
        $this->loadSiswa();
        $this->loadFormatif();
        $this->loadSumatif();
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        if ($tab === 'rekap') {
            $this->calculateRekap();
        }
    }

    public function loadTP()
    {
        $this->tps = TujuanPembelajaran::where('penugasan_mengajar_id', $this->penugasan_id)->orderBy('id')->get();
    }

    public function loadSiswa()
    {
        $this->siswas = Siswa::where('rombel_id', $this->penugasan->rombel_id)->orderBy('nama')->get();
    }

    public function loadFormatif()
    {
        $nilai = NilaiFormatif::whereIn('siswa_id', $this->siswas->pluck('id'))
            ->whereIn('tujuan_pembelajaran_id', $this->tps->pluck('id'))
            ->get();

        foreach ($this->siswas as $siswa) {
            foreach ($this->tps as $tp) {
                $n = $nilai->where('siswa_id', $siswa->id)->where('tujuan_pembelajaran_id', $tp->id)->first();
                $this->formatifData[$siswa->id][$tp->id] = $n ? $n->nilai : null;
            }
        }
    }

    public function loadSumatif()
    {
        $nilai = NilaiSumatif::where('penugasan_mengajar_id', $this->penugasan_id)->get()->keyBy('siswa_id');
        
        foreach ($this->siswas as $siswa) {
            $n = $nilai[$siswa->id] ?? null;
            $this->sumatifData[$siswa->id] = [
                'sts' => $n ? $n->sts : null,
                'sas' => $n ? $n->sas : null,
            ];
        }
    }

    public $edit_tp_id = null;

    public function saveTP()
    {
        $this->validate([
            'tp_kode' => 'required|string|max:20',
            'tp_deskripsi' => 'required|string|max:255',
        ]);

        if ($this->edit_tp_id) {
            $tp = TujuanPembelajaran::findOrFail($this->edit_tp_id);
            $tp->update([
                'kode' => $this->tp_kode,
                'deskripsi' => $this->tp_deskripsi,
            ]);
            $this->edit_tp_id = null;
            session()->flash('message_tp', 'Tujuan Pembelajaran berhasil diperbarui.');
        } else {
            TujuanPembelajaran::create([
                'penugasan_mengajar_id' => $this->penugasan_id,
                'kode' => $this->tp_kode,
                'deskripsi' => $this->tp_deskripsi,
                'is_active' => true,
            ]);
            session()->flash('message_tp', 'Tujuan Pembelajaran berhasil ditambahkan.');
        }

        $this->tp_kode = '';
        $this->tp_deskripsi = '';
        $this->loadTP();
        $this->loadFormatif(); // Reload formatif structure to include new TP column
    }

    public function editTP($tp_id)
    {
        $tp = TujuanPembelajaran::findOrFail($tp_id);
        $this->edit_tp_id = $tp->id;
        $this->tp_kode = $tp->kode;
        $this->tp_deskripsi = $tp->deskripsi;
    }

    public function cancelEditTP()
    {
        $this->edit_tp_id = null;
        $this->tp_kode = '';
        $this->tp_deskripsi = '';
    }

    public function hapusTP($tp_id)
    {
        TujuanPembelajaran::findOrFail($tp_id)->delete();
        $this->loadTP();
        $this->loadFormatif();
    }

    public function saveNilai()
    {
        // Save Formatif
        foreach ($this->formatifData as $siswaId => $tpScores) {
            foreach ($tpScores as $tpId => $score) {
                if ($score !== null && $score !== '') {
                    NilaiFormatif::updateOrCreate(
                        ['siswa_id' => $siswaId, 'tujuan_pembelajaran_id' => $tpId],
                        ['nilai' => $score]
                    );
                } else {
                    NilaiFormatif::where('siswa_id', $siswaId)->where('tujuan_pembelajaran_id', $tpId)->delete();
                }
            }
        }
        
        // Save Sumatif
        foreach ($this->sumatifData as $siswaId => $data) {
            if ($data['sts'] !== null || $data['sas'] !== null) {
                NilaiSumatif::updateOrCreate(
                    ['siswa_id' => $siswaId, 'penugasan_mengajar_id' => $this->penugasan_id],
                    ['sts' => $data['sts'] === '' ? null : $data['sts'], 'sas' => $data['sas'] === '' ? null : $data['sas']]
                );
            }
        }
        
        session()->flash('message_nilai', 'Seluruh Nilai Akademik (Formatif & Sumatif) berhasil disimpan.');
    }

    public function calculateRekap()
    {
        // Must save first technically, but we calculate from DB to be safe, or just from active state
        // Let's use DB to ensure accuracy.
        $this->rekapData = [];
        
        $formatifs = NilaiFormatif::whereIn('siswa_id', $this->siswas->pluck('id'))
            ->whereIn('tujuan_pembelajaran_id', $this->tps->pluck('id'))
            ->get();
            
        $sumatifs = NilaiSumatif::where('penugasan_mengajar_id', $this->penugasan_id)->get()->keyBy('siswa_id');

        foreach ($this->siswas as $siswa) {
            $siswaFormatifs = $formatifs->where('siswa_id', $siswa->id);
            
            $rataFormatif = $siswaFormatifs->avg('nilai') ?? 0;
            
            // Find max and min TP
            $maxTpScore = null;
            $minTpScore = null;
            $highestTpId = null;
            $lowestTpId = null;

            foreach ($siswaFormatifs as $nf) {
                if ($maxTpScore === null || $nf->nilai > $maxTpScore) {
                    $maxTpScore = $nf->nilai;
                    $highestTpId = $nf->tujuan_pembelajaran_id;
                }
                if ($minTpScore === null || $nf->nilai < $minTpScore) {
                    $minTpScore = $nf->nilai;
                    $lowestTpId = $nf->tujuan_pembelajaran_id;
                }
            }

            $sts = $sumatifs[$siswa->id]->sts ?? 0;
            $sas = $sumatifs[$siswa->id]->sas ?? 0;

            // Rata-rata 50%, STS 25%, SAS 25%
            $nilaiAkhir = ($rataFormatif * 0.5) + ($sts * 0.25) + ($sas * 0.25);

            $descTinggi = '';
            $descRendah = '';

            if ($highestTpId) {
                $tp = $this->tps->firstWhere('id', $highestTpId);
                $descTinggi = "Menunjukkan penguasaan yang sangat baik dalam " . lcfirst($tp->deskripsi) . ".";
            }
            
            if ($lowestTpId && $lowestTpId !== $highestTpId) {
                $tp = $this->tps->firstWhere('id', $lowestTpId);
                $descRendah = "Perlu bimbingan lebih lanjut dalam " . lcfirst($tp->deskripsi) . ".";
            } elseif ($lowestTpId && $lowestTpId === $highestTpId) {
                $descRendah = ""; // Only 1 TP answered or all same score, handle gracefully
            }

            // Simpan ke db
            $na = NilaiAkhir::updateOrCreate(
                ['siswa_id' => $siswa->id, 'penugasan_mengajar_id' => $this->penugasan_id],
                [
                    'rata_formatif' => $rataFormatif,
                    'sts' => $sts,
                    'sas' => $sas,
                    'nilai_akhir' => $nilaiAkhir,
                    'deskripsi_tertinggi' => $descTinggi,
                    'deskripsi_terendah' => $descRendah,
                ]
            );

            $this->rekapData[$siswa->id] = $na;
        }
    }

    public function updateManualDeskripsi($siswaId, $text)
    {
        $na = NilaiAkhir::where('siswa_id', $siswaId)->where('penugasan_mengajar_id', $this->penugasan_id)->first();
        if ($na) {
            $na->deskripsi_manual = $text;
            $na->save();
        }
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('penilaian::livewire.penilaian-input');
    }
}
