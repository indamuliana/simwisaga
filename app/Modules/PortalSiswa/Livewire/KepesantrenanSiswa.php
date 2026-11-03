<?php

namespace App\Modules\PortalSiswa\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Kepesantrenan\Models\PesantrenHafalanSiswa;
use App\Modules\Kepesantrenan\Models\PesantrenSetoranHafalan;
use App\Modules\Kepesantrenan\Models\QuranSurah;
use App\Modules\Kepesantrenan\Services\HafalanRankService;
use App\Modules\Kepesantrenan\Services\QuranHelper;
use App\Modules\Kesiswaan\Models\Siswa;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Kepesantrenan & Hafalan Qur\'an')]
class KepesantrenanSiswa extends Component
{
    use GuardsPermission;

    public $selectedJuz = 1;

    public $activeTab = 'kontrol'; // 'kontrol', 'riwayat', 'leaderboard'

    protected $rankService;

    public function boot(HafalanRankService $rankService): void
    {
        $this->rankService = $rankService;
    }

    public function mount(): void
    {
        // View-only untuk siswa
    }

    protected function getSiswa(): ?Siswa
    {
        $user = auth()->user();
        if (! $user) {
            return null;
        }

        if ($user->siswa) {
            return $user->siswa;
        }

        return Siswa::where('user_id', $user->id)
            ->orWhere('nisn', $user->username)
            ->orWhere('nipd', $user->username)
            ->first();
    }

    public function changeJuz(int $juz): void
    {
        $this->selectedJuz = max(1, min(30, $juz));
    }

    public function render()
    {
        $siswa = $this->getSiswa();
        $isUnggulan = $siswa && ($siswa->program?->kode === 'UNG');

        $stats = null;
        $memorizedMap = [];
        $juzSections = [];
        $surahs = collect();
        $setorans = collect();
        $leaderboard = collect();
        $juzProgress = [];

        if ($siswa && $isUnggulan) {
            $stats = $this->rankService->getSiswaStats($siswa->id);

            // Peta ayat yang sudah dihafal siswa ini
            $memorizedRecords = PesantrenHafalanSiswa::where('siswa_id', $siswa->id)
                ->get(['surah_id', 'ayat_number', 'predikat']);
            foreach ($memorizedRecords as $r) {
                $memorizedMap["{$r->surah_id}_{$r->ayat_number}"] = $r->predikat ?: 'mumtaz';
            }

            // Indikator progres per Juz (1 s/d 30)
            for ($j = 1; $j <= 30; $j++) {
                $secList = QuranHelper::getJuzSections($j);
                $totalAyatInJuz = 0;
                $memInJuz = 0;
                foreach ($secList as $sec) {
                    $totalAyatInJuz += ($sec['end'] - $sec['start'] + 1);
                    for ($a = $sec['start']; $a <= $sec['end']; $a++) {
                        if (isset($memorizedMap["{$sec['surah']}_{$a}"])) {
                            $memInJuz++;
                        }
                    }
                }
                $juzProgress[$j] = [
                    'total' => $totalAyatInJuz,
                    'memorized' => $memInJuz,
                    'is_tuntas' => $totalAyatInJuz > 0 && ($memInJuz >= $totalAyatInJuz),
                    'has_progress' => $memInJuz > 0,
                ];
            }

            // Data Juz aktif
            QuranHelper::ensureSurahsSeeded();
            $juzSections = QuranHelper::getJuzSections($this->selectedJuz);
            $surahIds = collect($juzSections)->pluck('surah')->unique();
            $surahs = QuranSurah::whereIn('nomor_surah', $surahIds)->get()->keyBy('nomor_surah');

            // Riwayat setoran siswa
            $setorans = PesantrenSetoranHafalan::with(['surah', 'penguji'])
                ->where('siswa_id', $siswa->id)
                ->latest('tanggal')
                ->latest('id')
                ->take(30)
                ->get();

            // Leaderboard santri unggulan
            $leaderboard = $this->rankService->getLeaderboard(null, null, null, 25);
        }

        return view('portalsiswa::livewire.kepesantrenan-siswa', [
            'siswa' => $siswa,
            'isUnggulan' => $isUnggulan,
            'stats' => $stats,
            'memorizedMap' => $memorizedMap,
            'juzSections' => $juzSections,
            'surahs' => $surahs,
            'juzProgress' => $juzProgress,
            'setorans' => $setorans,
            'leaderboard' => $leaderboard,
        ]);
    }
}
