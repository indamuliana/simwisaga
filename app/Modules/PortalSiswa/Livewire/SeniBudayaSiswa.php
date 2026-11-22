<?php

namespace App\Modules\PortalSiswa\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Kesiswaan\Models\Siswa;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Seni Budaya')]
class SeniBudayaSiswa extends Component
{
    use GuardsPermission;

    public string $filterSemester = 'semua';

    public function mount(): void
    {
        if (auth()->user()?->can('portal_siswa.seni_budaya.lihat') || auth()->user()?->can('portal_siswa.upd_seni.lihat')) {
            // Authorized
        } else {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat Seni Budaya.');
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

        $seniList = collect();

        if ($siswa) {
            $siswa->loadMissing([
                'seniBudayaAnggotas.seniBudaya.guru',
                'seniBudayaAnggotas.semester.tahunAjaran',
            ]);

            $query = $siswa->seniBudayaAnggotas();

            if ($this->filterSemester !== 'semua') {
                $query->where('semester_id', (int) $this->filterSemester);
            }

            $seniList = $query->with([
                'seniBudaya.guru',
                'semester.tahunAjaran',
            ])->get();
        }

        return view('portalsiswa::livewire.seni-budaya-siswa', [
            'siswa' => $siswa,
            'semesters' => $semesters,
            'seniList' => $seniList,
        ]);
    }
}
