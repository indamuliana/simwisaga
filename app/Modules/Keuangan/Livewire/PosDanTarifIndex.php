<?php

namespace App\Modules\Keuangan\Livewire;

use App\Modules\Akademik\Models\Program;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Keuangan\Models\PosBiaya;
use App\Modules\Keuangan\Models\SiswaTarif;
use App\Modules\Keuangan\Models\Tarif;
use App\Modules\Keuangan\Services\KeuanganService;
use Livewire\Component;

class PosDanTarifIndex extends Component
{
    public string $activeTab = 'tarif_standar'; // 'pos', 'tarif_standar', 'tarif_kustom'

    // Form Pos
    public ?int $posId = null;
    public string $posKode = '';
    public string $posNama = '';
    public string $posTipe = 'bulanan';
    public string $posDeskripsi = '';

    // Form Tarif Standar
    public ?int $tarifId = null;
    public ?int $tarifPosId = null;
    public ?int $tarifTahunAjaranId = null;
    public ?string $tarifTingkat = 'X';
    public ?int $tarifProgramId = null;
    public ?float $tarifNominal = 0;

    // Form Tarif Kustom Siswa
    public ?int $kustomId = null;
    public ?int $kustomSiswaId = null;
    public ?int $kustomPosId = null;
    public ?int $kustomTahunAjaranId = null;
    public float $kustomNominal = 0;
    public string $kustomJenis = 'nominal_tetap';
    public string $kustomAlasan = '';
    public string $siswaSearch = '';

    public function mount(): void
    {
        $ta = TahunAjaran::first();
        $this->tarifTahunAjaranId = $ta?->id;
        $this->kustomTahunAjaranId = $ta?->id;
        $firstPos = PosBiaya::first();
        $this->tarifPosId = $firstPos?->id;
        $this->kustomPosId = $firstPos?->id;
    }

    public function simpanPos(): void
    {
        $this->validate([
            'posKode' => 'required|max:20|unique:keuangan_pos_biayas,kode,' . ($this->posId ?? 'NULL') . ',id',
            'posNama' => 'required|max:100',
            'posTipe' => 'required|in:bulanan,bebas,insidental',
        ]);

        PosBiaya::updateOrCreate(
            ['id' => $this->posId],
            [
                'kode' => strtoupper($this->posKode),
                'nama' => $this->posNama,
                'tipe' => $this->posTipe,
                'deskripsi' => $this->posDeskripsi,
                'is_aktif' => true,
            ]
        );

        session()->flash('status', $this->posId ? 'Pos pembayaran berhasil diperbarui.' : 'Pos pembayaran berhasil ditambahkan.');
        $this->batalEditPos();
    }

    public function editPos(int $id): void
    {
        $pos = PosBiaya::findOrFail($id);
        $this->posId = $pos->id;
        $this->posKode = $pos->kode;
        $this->posNama = $pos->nama;
        $this->posTipe = $pos->tipe;
        $this->posDeskripsi = $pos->deskripsi ?? '';
        $this->activeTab = 'pos';
    }

    public function batalEditPos(): void
    {
        $this->reset(['posId', 'posKode', 'posNama', 'posTipe', 'posDeskripsi']);
    }

    public function hapusPos(int $id): void
    {
        $pos = PosBiaya::findOrFail($id);

        // Keamanan data: cegah penghapusan jika pos sudah memiliki riwayat tagihan terbayar
        $hasPaid = $pos->tagihans()->where('nominal_terbayar', '>', 0)->exists();
        if ($hasPaid) {
            session()->flash('error', "Pos '{$pos->nama}' tidak dapat dihapus karena sudah memiliki riwayat pembayaran siswa.");
            return;
        }

        $nama = $pos->nama;
        $pos->delete();

        session()->flash('status', "Pos pembayaran '{$nama}' berhasil dihapus.");
        if ($this->posId === $id) {
            $this->batalEditPos();
        }
    }

    public function simpanTarif(): void
    {
        $this->validate([
            'tarifPosId' => 'required|exists:keuangan_pos_biayas,id',
            'tarifTahunAjaranId' => 'required|exists:tahun_ajarans,id',
            'tarifNominal' => 'required|numeric|min:0',
        ]);

        if ($this->tarifId) {
            $tarif = Tarif::findOrFail($this->tarifId);
            $tarif->update([
                'pos_biaya_id' => $this->tarifPosId,
                'tahun_ajaran_id' => $this->tarifTahunAjaranId,
                'tingkat' => $this->tarifTingkat,
                'program_id' => $this->tarifProgramId ?: null,
                'nominal' => $this->tarifNominal,
            ]);
            session()->flash('status', 'Tarif standar per angkatan/program berhasil diperbarui.');
        } else {
            Tarif::updateOrCreate(
                [
                    'pos_biaya_id' => $this->tarifPosId,
                    'tahun_ajaran_id' => $this->tarifTahunAjaranId,
                    'tingkat' => $this->tarifTingkat,
                    'program_id' => $this->tarifProgramId ?: null,
                ],
                [
                    'nominal' => $this->tarifNominal,
                ]
            );
            session()->flash('status', 'Tarif standar per angkatan/program berhasil disimpan.');
        }

        $this->batalEditTarif();
    }

    public function editTarif(int $id): void
    {
        $tarif = Tarif::findOrFail($id);
        $this->tarifId = $tarif->id;
        $this->tarifPosId = $tarif->pos_biaya_id;
        $this->tarifTahunAjaranId = $tarif->tahun_ajaran_id;
        $this->tarifTingkat = $tarif->tingkat;
        $this->tarifProgramId = $tarif->program_id;
        $this->tarifNominal = (float) $tarif->nominal;
        $this->activeTab = 'tarif_standar';
    }

    public function batalEditTarif(): void
    {
        $this->reset(['tarifId', 'tarifNominal', 'tarifProgramId']);
    }

    public function hapusTarif(int $id): void
    {
        $tarif = Tarif::findOrFail($id);
        $tarif->delete();

        session()->flash('status', 'Tarif standar berhasil dihapus.');
        if ($this->tarifId === $id) {
            $this->batalEditTarif();
        }
    }

    public function simpanKustom(): void
    {
        $this->validate([
            'kustomSiswaId' => 'required|exists:siswas,id',
            'kustomPosId' => 'required|exists:keuangan_pos_biayas,id',
            'kustomTahunAjaranId' => 'required|exists:tahun_ajarans,id',
            'kustomNominal' => 'required|numeric|min:0',
        ]);

        $data = [
            'nominal_kustom' => $this->kustomNominal,
            'jenis_potongan' => $this->kustomJenis,
            'alasan' => $this->kustomAlasan,
        ];

        if ($this->kustomId) {
            $kustom = SiswaTarif::findOrFail($this->kustomId);
            $kustom->update(array_merge($data, [
                'siswa_id' => $this->kustomSiswaId,
                'pos_biaya_id' => $this->kustomPosId,
                'tahun_ajaran_id' => $this->kustomTahunAjaranId,
            ]));
            session()->flash('status', 'Tarif kustom/dispensasi siswa berhasil diperbarui.');
        } else {
            SiswaTarif::updateOrCreate(
                [
                    'siswa_id' => $this->kustomSiswaId,
                    'pos_biaya_id' => $this->kustomPosId,
                    'tahun_ajaran_id' => $this->kustomTahunAjaranId,
                ],
                $data
            );
            session()->flash('status', 'Tarif kustom/dispensasi siswa berhasil disimpan.');
        }

        $this->batalEditKustom();
    }

    public function editKustom(int $id): void
    {
        $kustom = SiswaTarif::with('siswa')->findOrFail($id);
        $this->kustomId = $kustom->id;
        $this->kustomSiswaId = $kustom->siswa_id;
        $this->kustomPosId = $kustom->pos_biaya_id;
        $this->kustomTahunAjaranId = $kustom->tahun_ajaran_id;
        $this->kustomNominal = (float) $kustom->nominal_kustom;
        $this->kustomJenis = $kustom->jenis_potongan ?? 'nominal_tetap';
        $this->kustomAlasan = $kustom->alasan ?? '';
        $this->siswaSearch = $kustom->siswa?->nama ?? '';
        $this->activeTab = 'tarif_kustom';
    }

    public function batalEditKustom(): void
    {
        $this->reset(['kustomId', 'kustomSiswaId', 'kustomNominal', 'kustomAlasan', 'siswaSearch']);
    }

    public function hapusKustom(int $id): void
    {
        $kustom = SiswaTarif::findOrFail($id);
        $kustom->delete();

        session()->flash('status', 'Tarif kustom/dispensasi siswa berhasil dihapus.');
        if ($this->kustomId === $id) {
            $this->batalEditKustom();
        }
    }

    public function generateSppMassal(): void
    {
        if (! $this->tarifTahunAjaranId) {
            return;
        }

        $service = app(KeuanganService::class);
        $totalCreated = 0;
        $totalSiswa = 0;

        Siswa::where('status', 'aktif')->chunkById(100, function ($siswas) use ($service, &$totalCreated, &$totalSiswa) {
            foreach ($siswas as $s) {
                $totalCreated += $service->generateSemuaTagihanSiswa($s, $this->tarifTahunAjaranId);
                $totalSiswa++;
            }
        });

        session()->flash('status', "Berhasil menerbitkan {$totalCreated} tagihan (SPP 12 Bulan & DSP Cicilan) untuk {$totalSiswa} siswa aktif.");
    }

    public function sinkronkanTagihan(): void
    {
        if (! $this->tarifTahunAjaranId) {
            return;
        }

        $service = app(KeuanganService::class);
        $count = $service->sinkronkanTagihanSiswa($this->tarifTahunAjaranId);

        session()->flash('status', "Berhasil menyinkronkan {$count} data tagihan belum bayar sesuai tarif Program & Tingkat terbaru.");
    }

    public function render()
    {
        $posBiayas = PosBiaya::all();
        $tarifs = Tarif::with(['posBiaya', 'tahunAjaran', 'program'])->get();
        $kustoms = SiswaTarif::with(['siswa.rombel', 'posBiaya', 'tahunAjaran'])->latest()->paginate(10);
        $tahunAjarans = TahunAjaran::all();
        $programs = Program::where('is_active', true)->get();

        $siswaOptions = [];
        if (strlen(trim($this->siswaSearch)) >= 2 && ! $this->kustomSiswaId) {
            $siswaOptions = Siswa::with('rombel')
                ->where('is_active', true)
                ->where('nama', 'like', '%' . trim($this->siswaSearch) . '%')
                ->take(8)
                ->get();
        }

        return view('keuangan::livewire.pos-dan-tarif-index', [
            'posBiayas' => $posBiayas,
            'tarifs' => $tarifs,
            'kustoms' => $kustoms,
            'tahunAjarans' => $tahunAjarans,
            'programs' => $programs,
            'siswaOptions' => $siswaOptions,
        ])->layout('components.layouts.app', ['title' => 'Pos Pembayaran & Skema Tarif']);
    }
}

