<?php

namespace App\Modules\Penilaian\Services;

use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Kepegawaian\Models\PenugasanMengajar;
use App\Modules\Penilaian\Models\TujuanPembelajaran;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TujuanPembelajaranCopier
{
    /**
     * Mengambil daftar rombel tujuan yang berada pada tingkat dan tahun ajaran yang sama.
     * Mengembalikan rombel lengkap dengan jurusan dan jumlah TP yang sudah dimiliki.
     */
    public function getAvailableTargetRombels(PenugasanMengajar $sourcePenugasan): Collection
    {
        $currentRombel = $sourcePenugasan->rombel;
        if (!$currentRombel) {
            return collect();
        }

        $tingkat = $currentRombel->tingkat;
        $tahunAjaranId = $currentRombel->tahun_ajaran_id;

        // Ambil semua rombel se-tingkat di tahun ajaran aktif kecuali rombel saat ini
        $rombels = Rombel::with('jurusan')
            ->where('tingkat', $tingkat)
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->where('id', '!=', $currentRombel->id)
            ->orderBy('nama')
            ->get();

        // Ambil penugasan mengajar untuk mapel ini di tahun ajaran terkait untuk seluruh rombel tersebut
        $allPmList = PenugasanMengajar::whereIn('rombel_id', $rombels->pluck('id'))
            ->where('mapel_id', $sourcePenugasan->mapel_id)
            ->whereHas('semester', function ($q) use ($tahunAjaranId) {
                $q->where('tahun_ajaran_id', $tahunAjaranId);
            })
            ->withCount('tujuanPembelajarans')
            ->get()
            ->groupBy('rombel_id');

        return $rombels->map(function ($rombel) use ($allPmList) {
            $pms = $allPmList->get($rombel->id, collect());
            $tpCount = $pms->sum('tujuan_pembelajarans_count');

            return [
                'id' => $rombel->id,
                'nama' => $rombel->nama,
                'tingkat' => $rombel->tingkat,
                'jurusan' => $rombel->jurusan?->nama ?? '-',
                'jurusan_singkatan' => $rombel->jurusan?->singkatan ?? ($rombel->jurusan?->kode ?? '-'),
                'existing_tp_count' => $tpCount,
            ];
        });
    }

    /**
     * Menyalin Tujuan Pembelajaran dari source penugasan ke rombel tujuan terpilih.
     *
     * @param PenugasanMengajar $sourcePenugasan
     * @param array $targetRombelIds
     * @param string $semesterScope 'all' | 'ganjil' | 'genap'
     * @return array
     */
    public function copyTps(PenugasanMengajar $sourcePenugasan, array $targetRombelIds, string $semesterScope = 'all'): array
    {
        if (empty($targetRombelIds)) {
            throw new \InvalidArgumentException('Pilih minimal satu rombel tujuan.');
        }

        $sourceRombel = $sourcePenugasan->rombel;
        $tahunAjaranId = $sourcePenugasan->semester?->tahun_ajaran_id ?? $sourceRombel?->tahun_ajaran_id;

        // Ambil semua semester di tahun ajaran ini (Ganjil & Genap)
        $allSemesters = Semester::where('tahun_ajaran_id', $tahunAjaranId)
            ->orderBy('semester')
            ->get();

        // Ambil semua penugasan mengajar rombel asal untuk mapel ini di tahun ajaran ini
        $sourcePmList = PenugasanMengajar::where('rombel_id', $sourcePenugasan->rombel_id)
            ->where('mapel_id', $sourcePenugasan->mapel_id)
            ->whereIn('semester_id', $allSemesters->pluck('id'))
            ->get();

        // Ambil seluruh TP dari rombel asal
        $sourceTps = TujuanPembelajaran::with(['penugasanMengajar.semester'])
            ->whereIn('penugasan_mengajar_id', $sourcePmList->pluck('id'))
            ->get();

        // Filter TP berdasarkan cakupan semester yang dipilih
        if ($semesterScope === 'ganjil') {
            $sourceTps = $sourceTps->filter(function ($tp) {
                return str_contains(strtolower($tp->penugasanMengajar?->semester?->semester ?? ''), 'ganjil');
            });
        } elseif ($semesterScope === 'genap') {
            $sourceTps = $sourceTps->filter(function ($tp) {
                return str_contains(strtolower($tp->penugasanMengajar?->semester?->semester ?? ''), 'genap');
            });
        }

        if ($sourceTps->isEmpty()) {
            throw new \RuntimeException('Tidak ada Tujuan Pembelajaran pada rombel asal untuk cakupan semester yang dipilih.');
        }

        $createdCount = 0;
        $updatedCount = 0;
        $processedRombels = 0;

        DB::transaction(function () use (
            $targetRombelIds,
            $allSemesters,
            $sourcePenugasan,
            $sourceTps,
            &$createdCount,
            &$updatedCount,
            &$processedRombels
        ) {
            foreach ($targetRombelIds as $targetRombelId) {
                // Pastikan setiap semester memiliki PenugasanMengajar di rombel target
                $targetPmBySemesterId = [];

                foreach ($allSemesters as $sem) {
                    $pm = PenugasanMengajar::firstOrCreate(
                        [
                            'semester_id' => $sem->id,
                            'rombel_id' => $targetRombelId,
                            'mapel_id' => $sourcePenugasan->mapel_id,
                        ],
                        [
                            'pegawai_id' => $sourcePenugasan->pegawai_id,
                            'jp' => $sourcePenugasan->jp,
                            'keterangan' => $sourcePenugasan->keterangan ?? 'Penugasan salin TP',
                        ]
                    );

                    $targetPmBySemesterId[$sem->id] = $pm;
                }

                // Sinkronkan setiap TP ke penugasan semester yang sesuai di rombel tujuan
                foreach ($sourceTps as $srcTp) {
                    $semId = $srcTp->penugasanMengajar->semester_id;
                    $targetPm = $targetPmBySemesterId[$semId] ?? null;

                    if (!$targetPm) {
                        continue;
                    }

                    $existingTp = TujuanPembelajaran::where('penugasan_mengajar_id', $targetPm->id)
                        ->where('kode', $srcTp->kode)
                        ->first();

                    if ($existingTp) {
                        $existingTp->update([
                            'deskripsi' => $srcTp->deskripsi,
                            'alokasi_ah' => $srcTp->alokasi_ah,
                            'is_active' => $srcTp->is_active ?? true,
                        ]);
                        $updatedCount++;
                    } else {
                        TujuanPembelajaran::create([
                            'penugasan_mengajar_id' => $targetPm->id,
                            'kode' => $srcTp->kode,
                            'deskripsi' => $srcTp->deskripsi,
                            'alokasi_ah' => $srcTp->alokasi_ah,
                            'is_active' => $srcTp->is_active ?? true,
                        ]);
                        $createdCount++;
                    }
                }

                $processedRombels++;
            }
        });

        return [
            'rombels_count' => $processedRombels,
            'source_tps_count' => $sourceTps->count(),
            'created_count' => $createdCount,
            'updated_count' => $updatedCount,
        ];
    }
}
