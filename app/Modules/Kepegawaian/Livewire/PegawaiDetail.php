<?php

namespace App\Modules\Kepegawaian\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Kepegawaian\Models\Pegawai;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Detail Profil Pegawai (GTK)')]
class PegawaiDetail extends Component
{
    use GuardsPermission;

    public int $pegawaiId;

    #[Url(as: 'tab')]
    public string $activeTab = 'biodata';

    public function mount(int $id): void
    {
        $this->guard('kepegawaian.pegawai.lihat');
        $this->pegawaiId = $id;

        Pegawai::findOrFail($id);

        if (! in_array($this->activeTab, ['biodata', 'kepegawaian', 'pajak', 'pendidikan', 'keluarga', 'mapel', 'foto'])) {
            $this->activeTab = 'biodata';
        }
    }

    public function setTab(string $tab): void
    {
        if (in_array($tab, ['biodata', 'kepegawaian', 'pajak', 'pendidikan', 'keluarga', 'mapel', 'foto'])) {
            $this->activeTab = $tab;
        }
    }

    public function render()
    {
        $pegawai = Pegawai::with([
            'user.roles',
            'fotos',
            'penugasanMengajars.semester.tahunAjaran',
            'penugasanMengajars.mapel',
            'penugasanMengajars.rombel',
            'penugasanWaliKelas.rombel',
            'penugasanWaliKelas.tahunAjaran',
            'penugasanPembimbingRayon.rayon',
            'penugasanPembimbingRayon.tahunAjaran',
        ])->findOrFail($this->pegawaiId);

        return view('kepegawaian::livewire.pegawai-detail', [
            'pegawai' => $pegawai,
        ]);
    }
}
