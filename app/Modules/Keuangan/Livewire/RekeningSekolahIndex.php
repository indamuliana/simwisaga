<?php

namespace App\Modules\Keuangan\Livewire;

use App\Modules\Keuangan\Models\Rekening;
use Livewire\Component;

class RekeningSekolahIndex extends Component
{
    public ?int $rekeningId = null;
    public string $namaBank = '';
    public string $nomorRekening = '';
    public string $atasNama = '';
    public bool $isKasTunai = false;
    public float $saldoAwal = 0;
    public string $keterangan = '';

    public function simpan(): void
    {
        $this->validate([
            'namaBank' => 'required|max:100',
            'atasNama' => 'required|max:100',
        ]);

        Rekening::updateOrCreate(
            ['id' => $this->rekeningId],
            [
                'nama_bank' => $this->namaBank,
                'nomor_rekening' => $this->nomorRekening ?: null,
                'atas_nama' => $this->atasNama,
                'is_kas_tunai' => $this->isKasTunai,
                'saldo_awal' => $this->saldoAwal,
                'saldo_saat_ini' => $this->rekeningId ? Rekening::find($this->rekeningId)->saldo_saat_ini : $this->saldoAwal,
                'keterangan' => $this->keterangan,
                'is_aktif' => true,
            ]
        );

        session()->flash('status', 'Rekening / Kas sekolah berhasil disimpan.');
        $this->reset(['rekeningId', 'namaBank', 'nomorRekening', 'atasNama', 'isKasTunai', 'saldoAwal', 'keterangan']);
    }

    public function edit(int $id): void
    {
        $r = Rekening::findOrFail($id);
        $this->rekeningId = $r->id;
        $this->namaBank = $r->nama_bank;
        $this->nomorRekening = $r->nomor_rekening ?? '';
        $this->atasNama = $r->atas_nama;
        $this->isKasTunai = $r->is_kas_tunai;
        $this->saldoAwal = (float) $r->saldo_awal;
        $this->keterangan = $r->keterangan ?? '';
    }

    public function render()
    {
        $rekenings = Rekening::all();

        return view('keuangan::livewire.rekening-sekolah-index', [
            'rekenings' => $rekenings,
        ])->layout('components.layouts.app', ['title' => 'Pengaturan Rekening Kas & Bank']);
    }
}

