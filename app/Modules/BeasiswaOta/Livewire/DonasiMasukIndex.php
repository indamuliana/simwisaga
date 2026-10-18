<?php

namespace App\Modules\BeasiswaOta\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\BeasiswaOta\Models\DonasiMasuk;
use App\Modules\BeasiswaOta\Models\Donatur;
use App\Modules\BeasiswaOta\Models\KasMutasi;
use App\Modules\BeasiswaOta\Models\Kelompok;
use App\Modules\BeasiswaOta\Services\OtaKasService;
use Livewire\Component;
use Livewire\WithPagination;

class DonasiMasukIndex extends Component
{
    use GuardsPermission, WithPagination;

    // Filter fields
    public string $filterKelompok = '';
    public string $filterTipe = '';
    public string $filterMetode = ''; // '' | 'perorangan' | 'kolektif'
    public string $search = '';

    public function delete(int $id)
    {
        $donasi = DonasiMasuk::findOrFail($id);

        $posKas = ($donasi->tipe_donatur === 'rutin' && $donasi->kelompok_id) ? 'kelompok' : 'insidental';
        $currentSaldo = $posKas === 'kelompok'
            ? OtaKasService::getSaldoKelompok($donasi->kelompok_id)
            : OtaKasService::getSaldoInsidental();

        if ($currentSaldo < (float) $donasi->nominal) {
            session()->flash('error', 'Donasi tidak dapat dibatalkan karena dananya telah terpakai/dialokasikan.');
            return;
        }

        // Hapus mutasi kas terkait
        KasMutasi::where(function ($q) use ($donasi) {
            $q->where('referensi_tipe', 'donasi')->where('referensi_id', $donasi->id);
            if ($donasi->is_kolektif) {
                $q->orWhere(function ($sq) use ($donasi) {
                    $sq->where('referensi_tipe', 'donasi_kolektif')->where('referensi_id', $donasi->id);
                });
            }
        })->delete();

        $donasi->delete();
        session()->flash('message', 'Catatan donasi berhasil dihapus.');
    }

    public function deleteBatch(string $batchKode)
    {
        $donasis = DonasiMasuk::where('batch_kode', $batchKode)->get();
        if ($donasis->isEmpty()) {
            return;
        }

        $totalBatch = $donasis->sum('nominal');
        $kelompokId = $donasis->first()->kelompok_id;
        if ($kelompokId && OtaKasService::getSaldoKelompok($kelompokId) < $totalBatch) {
            session()->flash('error', 'Batch setoran kolektif tidak dapat dibatalkan karena dananya telah terpakai/dialokasikan.');
            return;
        }

        $ids = $donasis->pluck('id');
        KasMutasi::where('referensi_tipe', 'donasi_kolektif')->whereIn('referensi_id', $ids)->delete();
        KasMutasi::where('referensi_tipe', 'donasi')->whereIn('referensi_id', $ids)->delete();

        DonasiMasuk::where('batch_kode', $batchKode)->delete();

        session()->flash('message', "Seluruh transaksi setoran kolektif (Batch {$batchKode}) berhasil dibatalkan dan dihapus.");
    }

    public function render()
    {
        $donasis = DonasiMasuk::query()
            ->with(['donatur', 'kelompok', 'tahunAjaran'])
            ->when($this->search, function ($q) {
                $q->where(function ($sq) {
                    $sq->whereHas('donatur', fn($dq) => $dq->where('nama', 'like', "%{$this->search}%"))
                        ->orWhere('nomor_transaksi', 'like', "%{$this->search}%")
                        ->orWhere('batch_kode', 'like', "%{$this->search}%")
                        ->orWhere('penyetor_nama', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterKelompok, fn($q) => $q->where('kelompok_id', $this->filterKelompok))
            ->when($this->filterTipe, fn($q) => $q->where('tipe_donatur', $this->filterTipe))
            ->when($this->filterMetode, function ($q) {
                if ($this->filterMetode === 'kolektif') {
                    $q->where('is_kolektif', true);
                } elseif ($this->filterMetode === 'perorangan') {
                    $q->where('is_kolektif', false);
                }
            })
            ->latest('tanggal_donasi')
            ->latest('id')
            ->paginate(15);

        $donaturs = Donatur::where('is_aktif', true)->orderBy('nama')->get();
        $kelompoks = Kelompok::where('is_aktif', true)->orderBy('nama_kelompok')->get();
        $tahunAjarans = TahunAjaran::orderByDesc('is_active')->orderByDesc('nama')->get();

        return view('beasiswaota::livewire.donasi-masuk-index', [
            'donasis' => $donasis,
            'donaturs' => $donaturs,
            'kelompoks' => $kelompoks,
            'tahunAjarans' => $tahunAjarans,
        ])->layout('components.layouts.app', ['title' => 'Penerimaan Donasi Masuk OTA']);
    }
}
