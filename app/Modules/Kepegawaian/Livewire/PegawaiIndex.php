<?php

namespace App\Modules\Kepegawaian\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Core\Services\DocumentEngine;
use App\Core\Support\DataMasker;
use App\Modules\Kepegawaian\Models\Pegawai;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Data Guru & Karyawan (GTK)')]
class PegawaiIndex extends Component
{
    use \App\Core\Livewire\Traits\WithSorting, GuardsPermission, WithPagination;

    public function mount(): void
    {
        $this->sortField = 'nama_lengkap';
    }

    // Filter & Search
    public string $search = '';

    public string $filterGtk = '';

    public string $filterStatus = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterGtk(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        $this->guard('kepegawaian.pegawai.hapus');
        $p = Pegawai::findOrFail($id);
        $nama = $p->nama_lengkap;
        $user = $p->user;
        $p->delete();
        if ($user) {
            $user->delete();
        }
        session()->flash('status', "Pegawai {$nama} berhasil dihapus.");
    }

    public function cetakDaftar()
    {
        $this->guard('kepegawaian.pegawai.cetak');

        $pegawais = Pegawai::orderBy('jenis_gtk')->orderBy('nama_lengkap')->get();

        $rows = '';
        foreach ($pegawais as $idx => $p) {
            $no = $idx + 1;
            $nama = $p->nama_gelar;
            $nip = $p->nip ?? '-';
            $nuptk = $p->nuptk ?? '-';
            $nik = DataMasker::maskNik($p->nik);
            $jenis = strtoupper($p->jenis_gtk);
            $status = strtoupper($p->status_kepegawaian);
            $jk = $p->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';

            $rows .= "
                <tr>
                    <td style='text-align: center;'>{$no}</td>
                    <td><b>{$nama}</b><br><small style='color: #64748b;'>NIK: {$nik}</small></td>
                    <td>NIP: {$nip}<br>NUPTK: {$nuptk}</td>
                    <td>{$jk}</td>
                    <td>{$jenis} ({$status})</td>
                    <td>{$p->no_hp}</td>
                </tr>
            ";
        }

        $html = "
            <table class='content-table'>
                <thead>
                    <tr>
                        <th style='width: 30px; text-align: center;'>No</th>
                        <th>Nama Lengkap & NIK</th>
                        <th>NIP / NUPTK</th>
                        <th>JK</th>
                        <th>Jenis & Status</th>
                        <th>Kontak</th>
                    </tr>
                </thead>
                <tbody>
                    {$rows}
                </tbody>
            </table>
        ";

        $pdf = DocumentEngine::createPdf(
            title: 'DAFTAR GURU DAN TENAGA KEPENDIDIKAN (GTK)',
            content: $html,
            options: [
                'subtitle' => 'SMK Wikrama 1 Garut — Tahun Ajaran '.now()->year.'/'.(now()->year + 1),
                'template_kode' => 'daftar_siswa',
            ]
        );

        return response()->streamDownload(
            fn () => print ($pdf->output()),
            'Daftar_GTK_Wikrama.pdf'
        );
    }

    public function render()
    {
        $query = Pegawai::with(['user.roles', 'fotos']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nama_lengkap', 'like', "%{$this->search}%")
                    ->orWhere('nip', 'like', "%{$this->search}%")
                    ->orWhere('nuptk', 'like', "%{$this->search}%")
                    ->orWhere('nik', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterGtk) {
            $query->where('jenis_gtk', $this->filterGtk);
        }

        if ($this->filterStatus) {
            $query->where('status_kepegawaian', $this->filterStatus);
        }

        $pegawais = $query->orderBy($this->sortField, $this->sortDirection)->paginate(15);

        return view('kepegawaian::livewire.pegawai-index', [
            'pegawais' => $pegawais,
        ]);
    }
}
