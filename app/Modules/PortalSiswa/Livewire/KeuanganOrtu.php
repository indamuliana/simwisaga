<?php

namespace App\Modules\PortalSiswa\Livewire;

use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Keuangan\Models\PembayaranOnline;
use App\Modules\Keuangan\Models\Rekening;
use App\Modules\Keuangan\Models\Tagihan;
use App\Modules\Keuangan\Models\Transaksi;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
#[Title('Keuangan & Pembayaran Sekolah (Orang Tua)')]
class KeuanganOrtu extends Component
{
    use WithFileUploads;

    public string $activeTab = 'tagihan'; // 'tagihan', 'upload', 'riwayat'

    // Form Upload Bukti Transfer
    public ?int $rekeningTujuanId = null;
    public string $namaPemilikRekening = '';
    public string $bankPengirim = '';
    public string $nomorRekeningPengirim = '';
    public float $nominalTransfer = 0;
    public string $tanggalTransfer = '';
    public $fileBukti = null;
    public string $catatan = '';
    public array $alokasiBayar = [];

    public function mount(): void
    {
        $this->tanggalTransfer = date('Y-m-d');
        $rek = Rekening::where('is_aktif', true)->where('is_kas_tunai', false)->first();
        $this->rekeningTujuanId = $rek?->id;
    }

    protected function getSiswa(): ?Siswa
    {
        $user = auth()->user();
        if (! $user) {
            return null;
        }

        // Siswa dilarang melihat tunggakan biaya
        if ($user->hasRole('Siswa')) {
            return null;
        }

        if (str_starts_with($user->username, 'ortu_')) {
            $nipd = substr($user->username, 5);
            return Siswa::with(['rombel', 'rayon'])->where('nipd', $nipd)->first();
        }

        if ($user->hasRole('Orang Tua')) {
            return Siswa::with(['rombel', 'rayon'])->where('user_id', $user->id)->first();
        }

        if ($user->hasRole('Super Admin') || $user->hasRole('Bendahara')) {
            return Siswa::with(['rombel', 'rayon'])->first();
        }

        return null;
    }

    public function uploadBuktiBayar(): void
    {
        $siswa = $this->getSiswa();
        if (! $siswa) {
            session()->flash('error', 'Akses dibatasi. Halaman ini hanya diperuntukkan bagi Orang Tua / Wali.');
            return;
        }

        $this->validate([
            'rekeningTujuanId' => 'required|exists:keuangan_rekenings,id',
            'namaPemilikRekening' => 'required|max:100',
            'nominalTransfer' => 'required|numeric|min:1000',
            'tanggalTransfer' => 'required|date',
            'fileBukti' => 'required|file|mimes:jpg,jpeg,png,pdf|max:3072',
        ]);

        $filePath = $this->fileBukti->store('bukti_transfer', 'public');

        // Susun alokasi pos
        $alokasi = [];
        foreach ($this->alokasiBayar as $tagihanId => $nom) {
            $val = (float) $nom;
            if ($val > 0) {
                $alokasi[] = [
                    'tagihan_id' => $tagihanId,
                    'nominal' => $val,
                ];
            }
        }

        // Jika alokasi kosong, alokasikan ke tagihan pertama/tertua otomatis
        if (empty($alokasi)) {
            $tagihanTertua = Tagihan::where('siswa_id', $siswa->id)->belumLunas()->orderBy('id')->first();
            if ($tagihanTertua) {
                $alokasi[] = [
                    'tagihan_id' => $tagihanTertua->id,
                    'nominal' => $this->nominalTransfer,
                ];
            }
        }

        PembayaranOnline::create([
            'kode_pengajuan' => PembayaranOnline::generateKodePengajuan(),
            'siswa_id' => $siswa->id,
            'user_id' => auth()->id(),
            'rekening_tujuan_id' => $this->rekeningTujuanId,
            'bank_pengirim' => $this->bankPengirim,
            'nomor_rekening_pengirim' => $this->nomorRekeningPengirim,
            'nama_pemilik_rekening' => $this->namaPemilikRekening,
            'nominal_transfer' => $this->nominalTransfer,
            'tanggal_transfer' => $this->tanggalTransfer,
            'file_bukti_path' => $filePath,
            'alokasi_pos' => $alokasi,
            'catatan' => $this->catatan,
            'status' => 'menunggu',
        ]);

        session()->flash('status', 'Bukti pembayaran berhasil diunggah! Bendahara sekolah akan segera memverifikasi mutasi dana Anda.');
        $this->reset(['namaPemilikRekening', 'bankPengirim', 'nomorRekeningPengirim', 'nominalTransfer', 'fileBukti', 'catatan', 'alokasiBayar']);
        $this->activeTab = 'riwayat';
    }

    public function render()
    {
        $user = auth()->user();
        $isSiswa = $user && $user->hasRole('Siswa');
        $siswa = $this->getSiswa();

        $tagihans = collect();
        $riwayatTransaksis = collect();
        $pengajuansOnline = collect();
        $rekenings = Rekening::where('is_aktif', true)->where('is_kas_tunai', false)->get();
        $totalTunggakan = 0;

        if ($siswa) {
            $ta = \App\Modules\Akademik\Models\TahunAjaran::first();
            if ($ta) {
                app(\App\Modules\Keuangan\Services\KeuanganService::class)->generateSemuaTagihanSiswa($siswa, $ta->id);
            }

            $tagihans = Tagihan::with('posBiaya')
                ->where('siswa_id', $siswa->id)
                ->belumLunas()
                ->urutTahunAjaran()
                ->get();

            $totalTunggakan = $tagihans->sum('sisa_tagihan');

            $riwayatTransaksis = Transaksi::with(['items.posBiaya', 'rekening'])
                ->where('siswa_id', $siswa->id)
                ->latest('tanggal_bayar')
                ->get();

            $pengajuansOnline = PembayaranOnline::with('rekeningTujuan')
                ->where('siswa_id', $siswa->id)
                ->latest()
                ->get();
        }

        return view('portalsiswa::livewire.keuangan-ortu', [
            'isSiswa' => $isSiswa,
            'siswa' => $siswa,
            'tagihans' => $tagihans,
            'totalTunggakan' => $totalTunggakan,
            'riwayatTransaksis' => $riwayatTransaksis,
            'pengajuansOnline' => $pengajuansOnline,
            'rekenings' => $rekenings,
        ]);
    }
}

