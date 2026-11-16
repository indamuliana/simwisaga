<?php

namespace App\Modules\Keuangan\Livewire;

use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Keuangan\Models\AlumniTunggakan;
use App\Modules\Keuangan\Models\Rekening;
use App\Modules\Keuangan\Models\Tagihan;
use App\Modules\Keuangan\Services\KeuanganService;
use Livewire\Component;
use Livewire\WithPagination;

class PiutangAlumniIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusIjazahFilter = '';

    // Modal Cicilan
    public ?int $selectedSiswaId = null;
    public ?int $selectedRekeningId = null;
    public array $bayarNominal = [];
    public bool $modalCicilOpen = false;

    // Modal Ijazah
    public ?int $ijazahSiswaId = null;
    public string $statusIjazah = 'ditahan';
    public ?string $tglSerahTerima = null;
    public string $catatanIjazah = '';
    public bool $modalIjazahOpen = false;

    public function mount(): void
    {
        $kas = Rekening::where('is_kas_tunai', true)->first() ?? Rekening::first();
        $this->selectedRekeningId = $kas?->id;
    }

    public function openCicil(int $siswaId): void
    {
        $this->selectedSiswaId = $siswaId;
        $this->bayarNominal = [];
        $this->modalCicilOpen = true;
    }

    public function openIjazah(int $siswaId): void
    {
        $this->ijazahSiswaId = $siswaId;
        $record = AlumniTunggakan::where('siswa_id', $siswaId)->first();
        if ($record) {
            $this->statusIjazah = $record->status_ijazah;
            $this->tglSerahTerima = $record->tanggal_serah_terima?->format('Y-m-d');
            $this->catatanIjazah = $record->catatan ?? '';
        } else {
            $this->statusIjazah = 'ditahan';
            $this->tglSerahTerima = null;
            $this->catatanIjazah = '';
        }
        $this->modalIjazahOpen = true;
    }

    public function simpanIjazah(): void
    {
        if (! $this->ijazahSiswaId) {
            return;
        }

        AlumniTunggakan::updateOrCreate(
            ['siswa_id' => $this->ijazahSiswaId],
            [
                'status_ijazah' => $this->statusIjazah,
                'tanggal_serah_terima' => $this->tglSerahTerima ?: null,
                'catatan' => $this->catatanIjazah,
            ]
        );

        session()->flash('status', 'Status penyerahan ijazah alumni berhasil diperbarui.');
        $this->modalIjazahOpen = false;
    }

    public function prosesCicilAlumni(): void
    {
        if (! $this->selectedSiswaId || ! $this->selectedRekeningId) {
            return;
        }

        $alokasi = [];
        foreach ($this->bayarNominal as $tagihanId => $nom) {
            $val = (float) $nom;
            if ($val > 0) {
                $alokasi[] = [
                    'tagihan_id' => $tagihanId,
                    'nominal' => $val,
                    'keterangan' => 'Cicilan Tunggakan Alumni Pasca-Lulus',
                ];
            }
        }

        if (empty($alokasi)) {
            session()->flash('error', 'Masukkan nominal cicilan.');
            return;
        }

        try {
            $service = app(KeuanganService::class);
            $siswa = Siswa::find($this->selectedSiswaId);
            $transaksi = $service->prosesPembayaranKasir(
                siswaId: $this->selectedSiswaId,
                rekeningId: $this->selectedRekeningId,
                alokasi: $alokasi,
                channel: 'offline_kasir',
                namaPenyetor: 'Alumni: ' . ($siswa?->nama ?? '-'),
                catatan: 'Pembayaran piutang alumni',
            );

            session()->flash('status', "Cicilan alumni berhasil dicatat ({$transaksi->nomor_transaksi}).");
            $this->modalCicilOpen = false;
        } catch (\Throwable $e) {
            session()->flash('error', 'Gagal memproses cicilan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $query = Siswa::with(['alumni', 'alumniTunggakan'])
            ->withSum('tagihans as total_tunggakan', 'sisa_tagihan')
            ->where(function ($q) {
                $q->where('status', 'lulus')
                    ->orWhereHas('alumni');
            });

        if ($this->search) {
            $term = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('nama', 'like', $term)
                    ->orWhere('nipd', 'like', $term)
                    ->orWhere('nisn', 'like', $term);
            });
        }

        if ($this->statusIjazahFilter) {
            $query->whereHas('alumniTunggakan', fn ($q) => $q->where('status_ijazah', $this->statusIjazahFilter));
        }

        $alumnis = $query->orderByDesc('total_tunggakan')->paginate(15);

        $selectedSiswa = $this->selectedSiswaId ? Siswa::with('rombel')->find($this->selectedSiswaId) : null;
        $tagihans = $this->selectedSiswaId
            ? Tagihan::with('posBiaya')->where('siswa_id', $this->selectedSiswaId)->belumLunas()->urutTahunAjaran()->get()
            : collect();

        $rekenings = Rekening::where('is_aktif', true)->get();

        return view('keuangan::livewire.piutang-alumni-index', [
            'alumnis' => $alumnis,
            'selectedSiswa' => $selectedSiswa,
            'tagihans' => $tagihans,
            'rekenings' => $rekenings,
        ])->layout('components.layouts.app', ['title' => 'Piutang Alumni & Penahanan Ijazah']);
    }
}

