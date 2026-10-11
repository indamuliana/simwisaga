<?php

namespace App\Modules\Kesiswaan\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Absensi\Models\AbsensiHarian;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Akademik\Support\AcademicContext;
use App\Modules\Kesiswaan\Models\RiwayatAkademik;
use App\Modules\Kesiswaan\Models\RombelSiswa;
use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Kenaikan Kelas Siswa')]
class KenaikanKelasIndex extends Component
{
    use GuardsPermission;

    // Filter Periode
    public ?int $tahunAjaranAsalId = null;
    public ?int $rombelAsalId = null;

    public ?int $tahunAjaranTujuanId = null;
    public ?int $rombelTujuanUtamaId = null;

    // Data Siswa & Keputusan
    public array $siswaDecisions = [];
    public bool $selectAll = true;

    public function mount(): void
    {
        $currentTa = AcademicContext::currentTahunAjaran();
        $this->tahunAjaranAsalId = $currentTa?->id;

        // Cari TA berikutnya atau TA lain
        $nextTa = TahunAjaran::where('id', '>', $this->tahunAjaranAsalId ?? 0)->orderBy('id')->first();
        if (! $nextTa) {
            $nextTa = TahunAjaran::where('id', '!=', $this->tahunAjaranAsalId ?? 0)->orderByDesc('id')->first();
        }
        $this->tahunAjaranTujuanId = $nextTa?->id;
    }

    public function updatedTahunAjaranAsalId(): void
    {
        $this->rombelAsalId = null;
        $this->resetDecisions();
    }

    public function updatedRombelAsalId(): void
    {
        $this->autoDetectRombelTujuan();
        $this->loadSiswas();
    }

    public function updatedTahunAjaranTujuanId(): void
    {
        $this->autoDetectRombelTujuan();
        $this->updateAllDecisionsTargetRombel();
    }

    public function updatedRombelTujuanUtamaId(): void
    {
        $this->updateAllDecisionsTargetRombel();
    }

    public function autoDetectRombelTujuan(): void
    {
        if (! $this->rombelAsalId || ! $this->tahunAjaranTujuanId) {
            $this->rombelTujuanUtamaId = null;
            return;
        }

        $rombelAsal = Rombel::find($this->rombelAsalId);
        if (! $rombelAsal) {
            $this->rombelTujuanUtamaId = null;
            return;
        }

        // Tentukan tingkat berikutnya (X -> XI, XI -> XII)
        $nextTingkat = match ($rombelAsal->tingkat) {
            'X' => 'XI',
            'XI' => 'XII',
            default => null,
        };

        if ($nextTingkat) {
            // Cari rombel di TA tujuan dengan jurusan & nomor urut yang sama
            $targetRombel = Rombel::where('tahun_ajaran_id', $this->tahunAjaranTujuanId)
                ->where('jurusan_id', $rombelAsal->jurusan_id)
                ->where('tingkat', $nextTingkat)
                ->where('nomor_urut', $rombelAsal->nomor_urut)
                ->first();

            $this->rombelTujuanUtamaId = $targetRombel?->id;
        } else {
            $this->rombelTujuanUtamaId = null;
        }
    }

    public function updateAllDecisionsTargetRombel(): void
    {
        foreach ($this->siswaDecisions as $siswaId => $data) {
            if ($data['status'] === 'naik_kelas') {
                $this->siswaDecisions[$siswaId]['rombel_tujuan_id'] = $this->rombelTujuanUtamaId;
            }
        }
    }

    public function loadSiswas(): void
    {
        $this->resetDecisions();

        if (! $this->rombelAsalId) {
            return;
        }

        $rombelAsal = Rombel::find($this->rombelAsalId);
        if (! $rombelAsal) {
            return;
        }

        $siswas = Siswa::inRombel($this->rombelAsalId)
            ->whereIn('status', ['aktif', 'lulus'])
            ->orderBy('nama')
            ->get();

        $existingDecisions = RiwayatAkademik::where('tahun_ajaran_id', $this->tahunAjaranTujuanId)
            ->where('rombel_asal_id', $this->rombelAsalId)
            ->get()
            ->keyBy('siswa_id');

        foreach ($siswas as $siswa) {
            $rekapAbsen = $this->getRekapAbsenSiswa($siswa->id);
            $existing = $existingDecisions[$siswa->id] ?? null;

            $status = $existing ? $existing->status_keputusan : 'naik_kelas';
            $targetRombelId = $existing ? $existing->rombel_tujuan_id : $this->rombelTujuanUtamaId;
            $catatan = $existing ? ($existing->catatan ?? '') : '';

            $this->siswaDecisions[$siswa->id] = [
                'id' => $siswa->id,
                'nama' => $siswa->nama,
                'nis' => $siswa->nipd ?: $siswa->nisn,
                'jk' => $siswa->jk,
                'rekap_absen' => $rekapAbsen,
                'status' => $status, // 'naik_kelas', 'tinggal_kelas', 'pindah', 'dikeluarkan'
                'rombel_tujuan_id' => $targetRombelId,
                'catatan' => $catatan,
                'sudah_diproses' => $existing !== null,
            ];
        }
    }

    protected function getRekapAbsenSiswa(int $siswaId): array
    {
        $query = AbsensiHarian::where('siswa_id', $siswaId);
        if ($this->tahunAjaranAsalId) {
            $query->whereHas('rombel', fn ($q) => $q->where('tahun_ajaran_id', $this->tahunAjaranAsalId));
        }

        return [
            'sakit' => (clone $query)->where('status', 'Sakit')->count(),
            'izin' => (clone $query)->where('status', 'Izin')->count(),
            'alpa' => (clone $query)->where('status', 'Alpa')->count(),
        ];
    }

    public function setStatusIndividual(int $siswaId, string $status): void
    {
        if (! isset($this->siswaDecisions[$siswaId])) {
            return;
        }

        $this->siswaDecisions[$siswaId]['status'] = $status;

        if ($status === 'naik_kelas') {
            $this->siswaDecisions[$siswaId]['rombel_tujuan_id'] = $this->rombelTujuanUtamaId;
        } elseif ($status === 'tinggal_kelas') {
            // Cari rombel di TA tujuan dengan tingkat yang SAMA dengan rombel asal
            $rombelAsal = Rombel::find($this->rombelAsalId);
            if ($rombelAsal) {
                $sameLevelRombel = Rombel::where('tahun_ajaran_id', $this->tahunAjaranTujuanId)
                    ->where('jurusan_id', $rombelAsal->jurusan_id)
                    ->where('tingkat', $rombelAsal->tingkat)
                    ->first();
                $this->siswaDecisions[$siswaId]['rombel_tujuan_id'] = $sameLevelRombel?->id;
            }
        } else {
            $this->siswaDecisions[$siswaId]['rombel_tujuan_id'] = null;
        }
    }

    public function setSemuaNaikKelas(): void
    {
        foreach ($this->siswaDecisions as $siswaId => $item) {
            $this->setStatusIndividual($siswaId, 'naik_kelas');
        }
    }

    public function resetDecisions(): void
    {
        $this->siswaDecisions = [];
    }

    public function prosesKenaikanKelas(): void
    {
        $this->guard('kesiswaan.siswa.edit');

        if (! $this->rombelAsalId || ! $this->tahunAjaranTujuanId) {
            session()->flash('error', 'Silakan pilih Rombel Asal dan Tahun Ajaran Tujuan terlebih dahulu.');
            return;
        }

        if (empty($this->siswaDecisions)) {
            session()->flash('error', 'Tidak ada data siswa untuk diproses.');
            return;
        }

        $rombelAsal = Rombel::findOrFail($this->rombelAsalId);
        $naikCount = 0;
        $tinggalCount = 0;
        $mutasiCount = 0;

        DB::transaction(function () use ($rombelAsal, &$naikCount, &$tinggalCount, &$mutasiCount) {
            foreach ($this->siswaDecisions as $siswaId => $decision) {
                $siswa = Siswa::find($siswaId);
                if (! $siswa) {
                    continue;
                }

                // Pastikan rombel asal tercatat di rombel_siswa untuk menjaga histori & ledger masa lalu
                if ($rombelAsal->tahun_ajaran_id) {
                    RombelSiswa::firstOrCreate(
                        ['tahun_ajaran_id' => $rombelAsal->tahun_ajaran_id, 'siswa_id' => $siswa->id],
                        ['rombel_id' => $rombelAsal->id]
                    );
                }

                $status = $decision['status'];
                $targetRombelId = $decision['rombel_tujuan_id'];
                $targetRombel = $targetRombelId ? Rombel::find($targetRombelId) : null;
                $catatan = $decision['catatan'] ?: null;

                if ($status === 'naik_kelas') {
                    if ($targetRombel) {
                        $siswa->update([
                            'rombel_id' => $targetRombel->id,
                            'status' => 'aktif',
                            'is_active' => true,
                        ]);

                        RombelSiswa::updateOrCreate(
                            ['tahun_ajaran_id' => $this->tahunAjaranTujuanId, 'siswa_id' => $siswa->id],
                            ['rombel_id' => $targetRombel->id]
                        );

                        RiwayatAkademik::updateOrCreate(
                            ['siswa_id' => $siswa->id, 'tahun_ajaran_id' => $this->tahunAjaranTujuanId],
                            [
                                'rombel_asal_id' => $rombelAsal->id,
                                'rombel_tujuan_id' => $targetRombel->id,
                                'tingkat_asal' => $rombelAsal->tingkat,
                                'tingkat_tujuan' => $targetRombel->tingkat,
                                'status_keputusan' => 'naik_kelas',
                                'catatan' => $catatan,
                                'diproses_oleh' => auth()->id(),
                            ]
                        );

                        $naikCount++;
                    }
                } elseif ($status === 'tinggal_kelas') {
                    if ($targetRombel) {
                        $siswa->update([
                            'rombel_id' => $targetRombel->id,
                            'status' => 'aktif',
                            'is_active' => true,
                        ]);

                        RombelSiswa::updateOrCreate(
                            ['tahun_ajaran_id' => $this->tahunAjaranTujuanId, 'siswa_id' => $siswa->id],
                            ['rombel_id' => $targetRombel->id]
                        );

                        RiwayatAkademik::updateOrCreate(
                            ['siswa_id' => $siswa->id, 'tahun_ajaran_id' => $this->tahunAjaranTujuanId],
                            [
                                'rombel_asal_id' => $rombelAsal->id,
                                'rombel_tujuan_id' => $targetRombel->id,
                                'tingkat_asal' => $rombelAsal->tingkat,
                                'tingkat_tujuan' => $targetRombel->tingkat,
                                'status_keputusan' => 'tinggal_kelas',
                                'catatan' => $catatan ?: 'Dinyatakan tinggal di kelas ' . $rombelAsal->tingkat,
                                'diproses_oleh' => auth()->id(),
                            ]
                        );

                        $tinggalCount++;
                    }
                } elseif (in_array($status, ['pindah', 'dikeluarkan'])) {
                    $siswa->update([
                        'status' => $status,
                        'is_active' => false,
                        'rombel_id' => null,
                    ]);

                    RiwayatAkademik::updateOrCreate(
                        ['siswa_id' => $siswa->id, 'tahun_ajaran_id' => $this->tahunAjaranTujuanId],
                        [
                            'rombel_asal_id' => $rombelAsal->id,
                            'rombel_tujuan_id' => null,
                            'tingkat_asal' => $rombelAsal->tingkat,
                            'tingkat_tujuan' => null,
                            'status_keputusan' => $status,
                            'catatan' => $catatan,
                            'diproses_oleh' => auth()->id(),
                        ]
                    );

                    $mutasiCount++;
                }
            }
        });

        $msg = "Proses Kenaikan Kelas Berhasil! Sebanyak {$naikCount} siswa naik kelas";
        if ($tinggalCount > 0) {
            $msg .= ", {$tinggalCount} siswa tinggal kelas";
        }
        if ($mutasiCount > 0) {
            $msg .= ", {$mutasiCount} siswa dimutasi/keluar";
        }
        $msg .= ". Data historis telah tersimpan di riwayat akademik.";

        session()->flash('status', $msg);
        $this->loadSiswas();
    }

    public function render()
    {
        $tahunAjarans = TahunAjaran::orderByDesc('id')->get();

        // Rombel asal khusus kelas X dan XI (kelas XII masuk kelulusan)
        $rombelsAsal = Rombel::with(['jurusan', 'tahunAjaran'])
            ->where('tahun_ajaran_id', $this->tahunAjaranAsalId)
            ->whereIn('tingkat', ['X', 'XI'])
            ->orderBy('nama')
            ->get();

        // Rombel tujuan di TA tujuan
        $rombelsTujuan = Rombel::with('jurusan')
            ->where('tahun_ajaran_id', $this->tahunAjaranTujuanId)
            ->orderBy('nama')
            ->get();

        $rombelAsal = $this->rombelAsalId ? Rombel::with('jurusan')->find($this->rombelAsalId) : null;
        $rombelTujuanUtama = $this->rombelTujuanUtamaId ? Rombel::find($this->rombelTujuanUtamaId) : null;

        // Statistik keputusan saat ini
        $totalSiswa = count($this->siswaDecisions);
        $totalNaik = collect($this->siswaDecisions)->where('status', 'naik_kelas')->count();
        $totalTinggal = collect($this->siswaDecisions)->where('status', 'tinggal_kelas')->count();
        $totalLainnya = $totalSiswa - ($totalNaik + $totalTinggal);

        return view('kesiswaan::livewire.kenaikan-kelas-index', [
            'tahunAjarans' => $tahunAjarans,
            'rombelsAsal' => $rombelsAsal,
            'rombelsTujuan' => $rombelsTujuan,
            'rombelAsal' => $rombelAsal,
            'rombelTujuanUtama' => $rombelTujuanUtama,
            'totalSiswa' => $totalSiswa,
            'totalNaik' => $totalNaik,
            'totalTinggal' => $totalTinggal,
            'totalLainnya' => $totalLainnya,
        ]);
    }
}
