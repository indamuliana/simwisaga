<?php

namespace App\Modules\Keuangan\Livewire;

use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Keuangan\Models\Rekening;
use App\Modules\Keuangan\Models\Tagihan;
use App\Modules\Keuangan\Services\KeuanganService;
use Livewire\Component;

class TransaksiKasirIndex extends Component
{
    public string $search = '';
    public ?int $selectedSiswaId = null;
    public ?int $selectedRekeningId = null;
    public string $channel = 'offline_kasir';
    public string $namaPenyetor = '';
    public string $catatan = '';

    /**
     * Map of [tagihan_id => nominal_bayar]
     */
    public array $bayarNominal = [];

    public function mount(): void
    {
        $kas = Rekening::where('is_kas_tunai', true)->where('is_aktif', true)->first()
            ?? Rekening::where('is_aktif', true)->first();
        $this->selectedRekeningId = $kas?->id;
    }

    public function selectSiswa(int $siswaId): void
    {
        $this->selectedSiswaId = $siswaId;
        $this->bayarNominal = [];

        $siswa = Siswa::find($siswaId);
        $this->namaPenyetor = $siswa?->nama ?? '';

        // Pastikan seluruh pos tagihan aktif (SPP bulanan & DSP cicilan) telah terbit untuk siswa ini
        $ta = \App\Modules\Akademik\Models\TahunAjaran::first();
        if ($siswa && $ta) {
            app(KeuanganService::class)->generateSemuaTagihanSiswa($siswa, $ta->id);
        }
    }

    public function setNominalPenuh(int $tagihanId, float $sisa): void
    {
        $this->bayarNominal[$tagihanId] = $sisa;
    }

    public function resetSiswa(): void
    {
        $this->selectedSiswaId = null;
        $this->bayarNominal = [];
        $this->namaPenyetor = '';
        $this->catatan = '';
    }

    public function prosesBayar(): void
    {
        if (! $this->selectedSiswaId) {
            session()->flash('error', 'Silakan pilih siswa terlebih dahulu.');
            return;
        }

        if (! $this->selectedRekeningId) {
            session()->flash('error', 'Silakan pilih rekening kas penampung.');
            return;
        }

        $alokasi = [];
        foreach ($this->bayarNominal as $tagihanId => $nom) {
            $val = (float) $nom;
            if ($val > 0) {
                $alokasi[] = [
                    'tagihan_id' => $tagihanId,
                    'nominal' => $val,
                    'keterangan' => null,
                ];
            }
        }

        if (empty($alokasi)) {
            session()->flash('error', 'Masukkan nominal pembayaran minimal pada satu tagihan.');
            return;
        }

        try {
            $service = app(KeuanganService::class);
            $transaksi = $service->prosesPembayaranKasir(
                siswaId: $this->selectedSiswaId,
                rekeningId: $this->selectedRekeningId,
                alokasi: $alokasi,
                channel: $this->channel,
                namaPenyetor: $this->namaPenyetor,
                catatan: $this->catatan,
            );

            session()->flash('status', "Pembayaran {$transaksi->nomor_transaksi} sebesar Rp " . number_format($transaksi->total_bayar, 0, ',', '.') . " berhasil dicatat.");
            $this->bayarNominal = [];

            // Dispatch event / redirect to receipt
            $this->dispatch('buka-kwitansi', url: route('keuangan.cetak.kwitansi', $transaksi->id));
        } catch (\Throwable $e) {
            session()->flash('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $searchResults = [];
        if (strlen(trim($this->search)) >= 2 && ! $this->selectedSiswaId) {
            $term = '%' . trim($this->search) . '%';
            $searchResults = Siswa::with(['rombel', 'rayon'])
                ->where('is_active', true)
                ->where(function ($q) use ($term) {
                    $q->where('nama', 'like', $term)
                        ->orWhere('nipd', 'like', $term)
                        ->orWhere('nisn', 'like', $term);
                })
                ->take(10)
                ->get();
        }

        $siswa = null;
        $tagihans = collect();
        if ($this->selectedSiswaId) {
            $siswa = Siswa::with(['rombel.jurusan', 'rayon.pembimbing', 'orangTuas'])->find($this->selectedSiswaId);
            $tagihans = Tagihan::with('posBiaya')
                ->where('siswa_id', $this->selectedSiswaId)
                ->belumLunas()
                ->urutTahunAjaran()
                ->get();
        }

        $rekenings = Rekening::where('is_aktif', true)->get();

        $totalAkanDibayar = 0;
        foreach ($this->bayarNominal as $nom) {
            $totalAkanDibayar += (float) $nom;
        }

        return view('keuangan::livewire.transaksi-kasir-index', [
            'searchResults' => $searchResults,
            'siswa' => $siswa,
            'tagihans' => $tagihans,
            'rekenings' => $rekenings,
            'totalAkanDibayar' => $totalAkanDibayar,
        ])->layout('components.layouts.app', ['title' => 'Loket Pembayaran Kasir Siswa']);
    }
}

