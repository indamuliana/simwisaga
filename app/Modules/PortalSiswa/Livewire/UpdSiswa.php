<?php

namespace App\Modules\PortalSiswa\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Kesiswaan\Models\Siswa;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Unit Pengembangan Diri (UPD)')]
class UpdSiswa extends Component
{
    use GuardsPermission;

    public string $filterSemester = 'semua';

    public function mount(): void
    {
        if (auth()->user()?->can('portal_siswa.upd.lihat') || auth()->user()?->can('portal_siswa.upd_seni.lihat')) {
            // Authorized
        } else {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat Unit Pengembangan Diri (UPD).');
        }
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

    public function render()
    {
        $siswa = $this->getSiswa();

        $semesters = Semester::with('tahunAjaran')->orderByDesc('id')->get();

        $ekskulList = collect();

        if ($siswa) {
            $siswa->loadMissing([
                'ekstrakurikulerAnggotas.ekstrakurikuler.pembina',
                'ekstrakurikulerAnggotas.semester.tahunAjaran',
            ]);

            $query = $siswa->ekstrakurikulerAnggotas();

            if ($this->filterSemester !== 'semua') {
                $query->where('semester_id', (int) $this->filterSemester);
            }

            $ekskulList = $query->with([
                'ekstrakurikuler.pembina',
                'semester.tahunAjaran',
            ])->get();
        }

        return view('portalsiswa::livewire.upd-siswa', [
            'siswa' => $siswa,
            'semesters' => $semesters,
            'ekskulList' => $ekskulList,
        ]);
    }
}
