<?php

namespace App\Modules\Kesiswaan\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Akademik\Support\AcademicContext;
use App\Modules\Alumni\Models\Alumni;
use App\Modules\Kesiswaan\Models\RiwayatAkademik;
use App\Modules\Kesiswaan\Models\RombelSiswa;
use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Kelulusan Siswa Kelas XII')]
class KelulusanIndex extends Component
{
    use GuardsPermission;

    public ?int $tahunAjaranId = null;
    public ?int $rombelId = null;

    public array $kelulusanDecisions = [];
    public string $batchNoIjazahPrefix = '';

    public function mount(): void
    {
        $currentTa = AcademicContext::currentTahunAjaran();
        $this->tahunAjaranId = $currentTa?->id;
        $this->loadSiswas();
    }

    public function updatedTahunAjaranId(): void
    {
        $this->rombelId = null;
        $this->loadSiswas();
    }

    public function updatedRombelId(): void
    {
        $this->loadSiswas();
    }

    public function loadSiswas(): void
    {
        $this->kelulusanDecisions = [];

        if (! $this->tahunAjaranId) {
            return;
        }

        $query = Siswa::with('rombel')
            ->whereIn('status', ['aktif', 'lulus'])
            ->where(function ($q) {
                $q->whereHas('rombel', function ($rq) {
                    $rq->where('tahun_ajaran_id', $this->tahunAjaranId)
                        ->where('tingkat', 'XII');
                })->orWhereHas('riwayatRombel', function ($rq) {
                    $rq->where('tahun_ajaran_id', $this->tahunAjaranId)
                        ->whereHas('rombel', fn ($r) => $r->where('tingkat', 'XII'));
                });
            });

        if ($this->rombelId) {
            $query->inRombel($this->rombelId);
        }

        $siswas = $query->orderBy('nama')->get();

        $existingDecisions = RiwayatAkademik::where('tahun_ajaran_id', $this->tahunAjaranId)
            ->whereIn('status_keputusan', ['lulus', 'tidak_lulus'])
            ->get()
            ->keyBy('siswa_id');

        foreach ($siswas as $siswa) {
            $existing = $existingDecisions[$siswa->id] ?? null;

            $this->kelulusanDecisions[$siswa->id] = [
                'id' => $siswa->id,
                'nama' => $siswa->nama,
                'nis' => $siswa->nipd ?: $siswa->nisn,
                'jk' => $siswa->jk,
                'rombel_nama' => $siswa->rombel?->nama ?? 'Kelas XII',
                'rombel_id' => $siswa->rombel_id,
                'status' => $existing ? $existing->status_keputusan : 'lulus',
                'nomor_ijazah' => $siswa->no_seri_ijazah ?? '',
                'catatan' => $existing ? ($existing->catatan ?? '') : '',
                'sudah_diproses' => $existing !== null,
            ];
        }
    }

    public function setSemuaStatus(string $status): void
    {
        foreach ($this->kelulusanDecisions as $siswaId => $item) {
            $this->kelulusanDecisions[$siswaId]['status'] = $status;
        }
    }

    public function applyBatchIjazahPrefix(): void
    {
        if (empty($this->batchNoIjazahPrefix)) {
            return;
        }

        $no = 1;
        foreach ($this->kelulusanDecisions as $siswaId => $item) {
            if (empty($this->kelulusanDecisions[$siswaId]['nomor_ijazah'])) {
                $suffix = str_pad((string) $no, 4, '0', STR_PAD_LEFT);
                $this->kelulusanDecisions[$siswaId]['nomor_ijazah'] = "{$this->batchNoIjazahPrefix}/{$suffix}";
                $no++;
            }
        }
    }

    public function prosesKelulusan(): void
    {
        $this->guard('kesiswaan.siswa.edit');

        if (empty($this->kelulusanDecisions)) {
            session()->flash('error', 'Tidak ada data siswa kelas XII untuk diproses.');
            return;
        }

        $ta = TahunAjaran::findOrFail($this->tahunAjaranId);

        // Ambil tahun kelulusan dari nama TA (misal "2024/2025" -> "2025")
        $tahunLulus = date('Y');
        if (preg_match('/\/(\d{4})/', $ta->nama, $matches)) {
            $tahunLulus = $matches[1];
        }

        $lulusCount = 0;
        $tidakLulusCount = 0;

        DB::transaction(function () use ($ta, $tahunLulus, &$lulusCount, &$tidakLulusCount) {
            foreach ($this->kelulusanDecisions as $siswaId => $decision) {
                $siswa = Siswa::find($siswaId);
                if (! $siswa) {
                    continue;
                }

                $status = $decision['status'];
                $noIjazah = ! empty($decision['nomor_ijazah']) ? trim($decision['nomor_ijazah']) : null;
                $catatan = $decision['catatan'] ?: null;
                $rombelAsalId = $decision['rombel_id'] ?? $siswa->rombel_id;

                // Pastikan rombel kelas XII tercatat di rombel_siswa untuk arsip ledger & histori
                if ($rombelAsalId) {
                    RombelSiswa::firstOrCreate(
                        ['tahun_ajaran_id' => $ta->id, 'siswa_id' => $siswa->id],
                        ['rombel_id' => $rombelAsalId]
                    );
                }

                if ($status === 'lulus') {
                    // 1. Update Siswa
                    $siswa->update([
                        'status' => 'lulus',
                        'is_active' => false,
                        'no_seri_ijazah' => $noIjazah ?: $siswa->no_seri_ijazah,
                    ]);

                    // 2. Buat / Perbarui Record di Alumni
                    Alumni::updateOrCreate(
                        ['siswa_id' => $siswa->id],
                        [
                            'tahun_ajaran_id' => $ta->id,
                            'tahun_lulus' => $tahunLulus,
                            'nomor_ijazah' => $noIjazah,
                            'status_karir' => 'belum_memutuskan',
                            'catatan' => $catatan,
                        ]
                    );

                    // 3. Catat di Riwayat Akademik (idempoten)
                    RiwayatAkademik::updateOrCreate(
                        ['siswa_id' => $siswa->id, 'tahun_ajaran_id' => $ta->id],
                        [
                            'rombel_asal_id' => $rombelAsalId,
                            'rombel_tujuan_id' => null,
                            'tingkat_asal' => 'XII',
                            'tingkat_tujuan' => 'LULUS',
                            'status_keputusan' => 'lulus',
                            'catatan' => $catatan ?: "Lulus dari Satuan Pendidikan SMK Wikrama 1 Garut (T.A. {$ta->nama})",
                            'diproses_oleh' => auth()->id(),
                        ]
                    );

                    $lulusCount++;
                } elseif ($status === 'tidak_lulus') {
                    // Siswa tetap aktif untuk mengulang
                    $siswa->update([
                        'status' => 'aktif',
                        'is_active' => true,
                    ]);

                    RiwayatAkademik::updateOrCreate(
                        ['siswa_id' => $siswa->id, 'tahun_ajaran_id' => $ta->id],
                        [
                            'rombel_asal_id' => $rombelAsalId,
                            'rombel_tujuan_id' => null,
                            'tingkat_asal' => 'XII',
                            'tingkat_tujuan' => 'XII',
                            'status_keputusan' => 'tidak_lulus',
                            'catatan' => $catatan ?: 'Dinyatakan belum lulus / mengulang tingkat XII',
                            'diproses_oleh' => auth()->id(),
                        ]
                    );

                    $tidakLulusCount++;
                }
            }
        });

        $msg = "Eksekusi Kelulusan Berhasil! Sebanyak {$lulusCount} siswa dinyatakan Lulus dan otomatis tercatat di Modul Alumni.";
        if ($tidakLulusCount > 0) {
            $msg .= " Sebanyak {$tidakLulusCount} siswa dinyatakan belum lulus / mengulang.";
        }

        session()->flash('status', $msg);
        $this->loadSiswas();
    }

    public function render()
    {
        $tahunAjarans = TahunAjaran::orderByDesc('id')->get();

        // Rombel khusus tingkat XII
        $rombelsXII = Rombel::where('tahun_ajaran_id', $this->tahunAjaranId)
            ->where('tingkat', 'XII')
            ->orderBy('nama')
            ->get();

        $totalSiswa = count($this->kelulusanDecisions);
        $totalLulus = collect($this->kelulusanDecisions)->where('status', 'lulus')->count();
        $totalTidakLulus = collect($this->kelulusanDecisions)->where('status', 'tidak_lulus')->count();

        return view('kesiswaan::livewire.kelulusan-index', [
            'tahunAjarans' => $tahunAjarans,
            'rombelsXII' => $rombelsXII,
            'totalSiswa' => $totalSiswa,
            'totalLulus' => $totalLulus,
            'totalTidakLulus' => $totalTidakLulus,
        ]);
    }
}
