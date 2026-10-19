<?php

namespace App\Modules\Sarpras\Livewire;

use App\Modules\Sarpras\Exports\AsetExport;
use App\Modules\Sarpras\Models\Aset;
use App\Modules\Sarpras\Models\Kategori;
use App\Modules\Sarpras\Models\Ruangan;
use App\Modules\Sarpras\Models\SumberDana;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('components.layouts.app')]
#[Title('Rekap & Laporan Aset')]
class LaporanIndex extends Component
{
    public ?int $filterRuangan = null;
    public ?int $filterKategori = null;
    public string $filterKondisi = '';
    public string $filterSumberDana = '';

    public function exportExcel()
    {
        $fileName = 'Laporan_Aset_Sarpras_' . date('Ymd_His') . '.xlsx';
        return Excel::download(
            new AsetExport($this->filterRuangan, $this->filterKategori, $this->filterKondisi, $this->filterSumberDana),
            $fileName
        );
    }

    public function render()
    {
        $query = Aset::with(['kategori', 'ruangan']);

        if ($this->filterRuangan) {
            $query->where('ruangan_id', $this->filterRuangan);
        }
        if ($this->filterKategori) {
            $query->where('kategori_id', $this->filterKategori);
        }
        if ($this->filterKondisi) {
            $query->where('kondisi', $this->filterKondisi);
        }
        if ($this->filterSumberDana) {
            $query->where('sumber_dana', $this->filterSumberDana);
        }

        $totalItems = (clone $query)->count();
        $totalNilai = (clone $query)->sum('harga_perolehan');
        $kondisiBaik = (clone $query)->where('kondisi', 'Baik')->count();
        $kondisiRusak = (clone $query)->whereIn('kondisi', ['Rusak Ringan', 'Rusak Berat'])->count();

        $asets = $query->orderBy('nama_barang')->get();

        $ruangans = Ruangan::where('is_active', true)->orderBy('nama_ruangan')->get();
        $kategoris = Kategori::asetTetap()->orderBy('nama')->get();
        $sumberDanas = SumberDana::where('is_active', true)->orderBy('nama')->get();

        return view('sarpras::livewire.laporan-index', compact(
            'asets',
            'totalItems',
            'totalNilai',
            'kondisiBaik',
            'kondisiRusak',
            'ruangans',
            'kategoris',
            'sumberDanas'
        ));
    }
}
