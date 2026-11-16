<?php

namespace App\Modules\Keuangan\Services;

use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Keuangan\Models\PosBiaya;
use App\Modules\Keuangan\Models\Rekening;
use App\Modules\Keuangan\Models\SiswaTarif;
use App\Modules\Keuangan\Models\Tagihan;
use App\Modules\Keuangan\Models\Tarif;
use App\Modules\Keuangan\Models\Transaksi;
use App\Modules\Keuangan\Models\TransaksiItem;
use App\Modules\Keuangan\Models\PembayaranOnline;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KeuanganService
{
    /**
     * Dapatkan nominal tarif yang berlaku untuk seorang siswa pada suatu pos dan tahun ajaran
     */
    public function getNominalTarifSiswa(Siswa $siswa, PosBiaya $posBiaya, int $tahunAjaranId): float
    {
        // 1. Cek apakah ada tarif khusus / beasiswa / dispensasi perorangan
        $kustom = SiswaTarif::where('siswa_id', $siswa->id)
            ->where('pos_biaya_id', $posBiaya->id)
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->first();

        if ($kustom) {
            if ($kustom->jenis_potongan === 'gratis') {
                return 0.0;
            }
            return (float) $kustom->nominal_kustom;
        }

        // 2. Dapatkan tingkat rombel siswa (X, XI, XII) dan program siswa
        $tingkat = $siswa->rombel?->tingkat ?? 'X';
        $programId = $siswa->program_id;

        // 3. Ambil tarif standar per angkatan / program / tingkat dengan Fallback Cerdas
        $tarif = Tarif::where('pos_biaya_id', $posBiaya->id)
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->where(function ($q) use ($tingkat) {
                $q->whereNull('tingkat')->orWhere('tingkat', $tingkat);
            })
            ->where(function ($q) use ($programId) {
                $q->whereNull('program_id');
                if ($programId) {
                    $q->orWhere('program_id', $programId);
                }
            })
            ->where(function ($q) use ($siswa) {
                $q->whereNull('jurusan_id');
                if ($siswa->rombel?->jurusan_id) {
                    $q->orWhere('jurusan_id', $siswa->rombel->jurusan_id);
                }
            })
            // Urutan prioritas: Program spesifik dahulu, lalu Tingkat spesifik, lalu Jurusan spesifik
            ->orderByRaw('program_id IS NOT NULL DESC, tingkat IS NOT NULL DESC, jurusan_id IS NOT NULL DESC')
            ->first();

        return $tarif ? (float) $tarif->nominal : 0.0;
    }

    /**
     * Sinkronkan tagihan yang belum terbayar sama sekali (nominal_terbayar = 0)
     * agar sesuai dengan tarif program & tingkat siswa terkini, serta pastikan
     * tagihan pos aktif (SPP & DSP) telah diterbitkan untuk semua siswa aktif.
     */
    public function sinkronkanTagihanSiswa(int $tahunAjaranId, ?int $posBiayaId = null): int
    {
        // 1. Terbitkan tagihan yang belum ada untuk seluruh siswa aktif (SPP & DSP)
        $siswas = Siswa::where('status', 'aktif')->get();
        foreach ($siswas as $siswa) {
            $this->generateSemuaTagihanSiswa($siswa, $tahunAjaranId);
        }

        // 2. Sinkronkan nominal tagihan belum bayar dengan tarif terkini
        $query = Tagihan::with(['siswa.rombel', 'posBiaya'])
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->where('nominal_terbayar', 0)
            ->whereHas('siswa', fn ($q) => $q->where('status', 'aktif'));

        if ($posBiayaId) {
            $query->where('pos_biaya_id', $posBiayaId);
        }

        $tagihans = $query->get();
        $updatedCount = 0;

        foreach ($tagihans as $tagihan) {
            if (! $tagihan->siswa || ! $tagihan->posBiaya) {
                continue;
            }

            $nominalTerbaru = $this->getNominalTarifSiswa($tagihan->siswa, $tagihan->posBiaya, $tahunAjaranId);

            if ((float) $tagihan->nominal_tagihan !== (float) $nominalTerbaru) {
                $tagihan->update([
                    'nominal_tagihan' => $nominalTerbaru,
                    'sisa_tagihan' => $nominalTerbaru,
                    'status' => $nominalTerbaru <= 0 ? 'lunas' : 'belum_bayar',
                ]);
                $updatedCount++;
            }
        }

        return $updatedCount;
    }

    /**
     * Terbitkan seluruh tagihan (SPP bulanan 12 bulan dan DSP / pos bebas)
     * yang relevan untuk seorang siswa sesuai tarif angkatan/programnya.
     */
    public function generateSemuaTagihanSiswa(Siswa $siswa, int $tahunAjaranId): int
    {
        $activePosBiayas = PosBiaya::where('is_aktif', true)->get();
        $totalCreated = 0;

        foreach ($activePosBiayas as $pos) {
            $nominal = $this->getNominalTarifSiswa($siswa, $pos, $tahunAjaranId);
            $hasKustom = SiswaTarif::where('siswa_id', $siswa->id)
                ->where('pos_biaya_id', $pos->id)
                ->where('tahun_ajaran_id', $tahunAjaranId)
                ->exists();

            // Lewati jika nominal 0 dan bukan penerima tarif kustom gratis
            if ($nominal <= 0 && ! $hasKustom) {
                continue;
            }

            if ($pos->tipe === 'bulanan') {
                $totalCreated += $this->generateTagihanBulananSiswa($siswa, $pos, $tahunAjaranId);
            } else {
                // Pos Bebas (DSP) atau Insidental
                $existing = Tagihan::where('siswa_id', $siswa->id)
                    ->where('pos_biaya_id', $pos->id)
                    ->where('tahun_ajaran_id', $tahunAjaranId)
                    ->exists();

                if (! $existing) {
                    $this->generateTagihanBebas($siswa, $pos, $tahunAjaranId, $nominal);
                    $totalCreated++;
                }
            }
        }

        return $totalCreated;
    }

    /**
     * Generate tagihan bulanan 12 bulan (Juli s.d Juni) untuk satu pos bulanan
     */
    public function generateTagihanBulananSiswa(Siswa $siswa, PosBiaya $posBiaya, int $tahunAjaranId): int
    {
        if ($posBiaya->tipe !== 'bulanan') {
            return 0;
        }

        $nominal = $this->getNominalTarifSiswa($siswa, $posBiaya, $tahunAjaranId);
        if ($nominal <= 0) {
            $hasKustom = SiswaTarif::where('siswa_id', $siswa->id)
                ->where('pos_biaya_id', $posBiaya->id)
                ->where('tahun_ajaran_id', $tahunAjaranId)
                ->exists();
            if (! $hasKustom) {
                return 0;
            }
        }

        $ta = TahunAjaran::find($tahunAjaranId);
        if (! $ta) {
            return 0;
        }

        $tahunAwal = (int) substr($ta->nama, 0, 4);
        if (! $tahunAwal) {
            $tahunAwal = (int) date('Y');
        }

        $count = 0;

        // 12 bulan: Juli(7) s.d Desember(12) pada tahun awal, Januari(1) s.d Juni(6) pada tahun berikutnya
        $urutanBulan = [
            ['bulan' => 7, 'tahun' => $tahunAwal],
            ['bulan' => 8, 'tahun' => $tahunAwal],
            ['bulan' => 9, 'tahun' => $tahunAwal],
            ['bulan' => 10, 'tahun' => $tahunAwal],
            ['bulan' => 11, 'tahun' => $tahunAwal],
            ['bulan' => 12, 'tahun' => $tahunAwal],
            ['bulan' => 1, 'tahun' => $tahunAwal + 1],
            ['bulan' => 2, 'tahun' => $tahunAwal + 1],
            ['bulan' => 3, 'tahun' => $tahunAwal + 1],
            ['bulan' => 4, 'tahun' => $tahunAwal + 1],
            ['bulan' => 5, 'tahun' => $tahunAwal + 1],
            ['bulan' => 6, 'tahun' => $tahunAwal + 1],
        ];

        $existingMonths = Tagihan::where('siswa_id', $siswa->id)
            ->where('pos_biaya_id', $posBiaya->id)
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->pluck('bulan')
            ->toArray();

        foreach ($urutanBulan as $item) {
            if (! in_array($item['bulan'], $existingMonths, true)) {
                Tagihan::create([
                    'kode_tagihan' => strtoupper($posBiaya->kode) . '-' . $siswa->id . '-' . $item['tahun'] . str_pad($item['bulan'], 2, '0', STR_PAD_LEFT),
                    'siswa_id' => $siswa->id,
                    'pos_biaya_id' => $posBiaya->id,
                    'tahun_ajaran_id' => $tahunAjaranId,
                    'bulan' => $item['bulan'],
                    'tahun' => $item['tahun'],
                    'nominal_tagihan' => $nominal,
                    'nominal_terbayar' => 0,
                    'sisa_tagihan' => $nominal,
                    'status' => $nominal <= 0 ? 'lunas' : 'belum_bayar',
                    'jatuh_tempo' => date('Y-m-10', strtotime("{$item['tahun']}-{$item['bulan']}-10")),
                ]);
                $count++;
            }
        }

        return $count;
    }

    /**
     * Generate tagihan SPP bulanan 12 bulan (Juli s.d Juni) untuk satu siswa
     */
    public function generateTagihanSppSiswa(Siswa $siswa, int $tahunAjaranId): int
    {
        $bulananPos = PosBiaya::where('tipe', 'bulanan')->where('is_aktif', true)->get();
        if ($bulananPos->isEmpty()) {
            return 0;
        }

        $totalCreated = 0;
        foreach ($bulananPos as $pos) {
            $totalCreated += $this->generateTagihanBulananSiswa($siswa, $pos, $tahunAjaranId);
        }

        return $totalCreated;
    }

    /**
     * Generate tagihan non-bulanan (DSP plafon cicilan atau kegiatan insidental)
     */
    public function generateTagihanBebas(Siswa $siswa, PosBiaya $posBiaya, int $tahunAjaranId, ?float $customNominal = null): Tagihan
    {
        $existing = Tagihan::where('siswa_id', $siswa->id)
            ->where('pos_biaya_id', $posBiaya->id)
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->first();

        if ($existing) {
            return $existing;
        }

        $nominal = $customNominal ?? $this->getNominalTarifSiswa($siswa, $posBiaya, $tahunAjaranId);

        return Tagihan::create([
            'kode_tagihan' => strtoupper($posBiaya->kode) . '-' . $siswa->id . '-' . $tahunAjaranId,
            'siswa_id' => $siswa->id,
            'pos_biaya_id' => $posBiaya->id,
            'tahun_ajaran_id' => $tahunAjaranId,
            'nominal_tagihan' => $nominal,
            'nominal_terbayar' => 0,
            'sisa_tagihan' => $nominal,
            'status' => $nominal <= 0 ? 'lunas' : 'belum_bayar',
        ]);
    }

    /**
     * Memproses pembayaran loket kasir / offline
     * $alokasi: array of ['tagihan_id' => int, 'nominal' => float, 'keterangan' => string|null]
     */
    public function prosesPembayaranKasir(
        int $siswaId,
        int $rekeningId,
        array $alokasi,
        string $channel = 'offline_kasir',
        ?string $namaPenyetor = null,
        ?string $catatan = null,
        ?int $userId = null
    ): Transaksi {
        return DB::transaction(function () use ($siswaId, $rekeningId, $alokasi, $channel, $namaPenyetor, $catatan, $userId) {
            $totalBayar = 0;
            foreach ($alokasi as $item) {
                $totalBayar += (float) ($item['nominal'] ?? 0);
            }

            if ($totalBayar <= 0) {
                throw new \InvalidArgumentException('Total nominal pembayaran harus lebih dari 0.');
            }

            $nomorTransaksi = Transaksi::generateNomorTransaksi();
            $kodeVerifikasi = Str::random(32);

            $transaksi = Transaksi::create([
                'nomor_transaksi' => $nomorTransaksi,
                'kode_verifikasi' => $kodeVerifikasi,
                'siswa_id' => $siswaId,
                'user_id' => $userId ?? auth()->id(),
                'rekening_id' => $rekeningId,
                'channel_pembayaran' => $channel,
                'total_bayar' => $totalBayar,
                'tanggal_bayar' => now(),
                'nama_penyetor' => $namaPenyetor,
                'catatan' => $catatan,
            ]);

            foreach ($alokasi as $item) {
                $nominal = (float) ($item['nominal'] ?? 0);
                if ($nominal <= 0) {
                    continue;
                }

                $tagihan = Tagihan::where('siswa_id', $siswaId)->lockForUpdate()->findOrFail($item['tagihan_id']);
                if ($nominal > (float) $tagihan->sisa_tagihan) {
                    throw new \InvalidArgumentException("Nominal bayar Rp " . number_format($nominal, 0, ',', '.') . " melebihi sisa tagihan {$tagihan->label_tagihan} (Rp " . number_format($tagihan->sisa_tagihan, 0, ',', '.') . ").");
                }
                $terbayarBaru = (float) $tagihan->nominal_terbayar + $nominal;
                $sisaBaru = max(0, (float) $tagihan->nominal_tagihan - $terbayarBaru);

                $tagihan->update([
                    'nominal_terbayar' => $terbayarBaru,
                    'sisa_tagihan' => $sisaBaru,
                    'status' => $sisaBaru <= 0 ? 'lunas' : 'dicicil',
                ]);

                TransaksiItem::create([
                    'transaksi_id' => $transaksi->id,
                    'tagihan_id' => $tagihan->id,
                    'pos_biaya_id' => $tagihan->pos_biaya_id,
                    'nominal_dibayar' => $nominal,
                    'sisa_setelah_bayar' => $sisaBaru,
                    'keterangan' => $item['keterangan'] ?? null,
                ]);
            }

            // Update Saldo Rekening Kas
            $rekening = Rekening::lockForUpdate()->find($rekeningId);
            if ($rekening) {
                $rekening->increment('saldo_saat_ini', $totalBayar);
            }

            return $transaksi;
        });
    }

    /**
     * Konfirmasi atau Tolak bukti transfer pembayaran online orang tua
     */
    public function verifikasiPembayaranOnline(
        PembayaranOnline $online,
        string $status,
        ?string $alasanPenolakan = null,
        ?int $verifikatorId = null
    ): PembayaranOnline {
        return DB::transaction(function () use ($online, $status, $alasanPenolakan, $verifikatorId) {
            $verifikatorId ??= auth()->id();

            // Lock record to avoid double-processing race conditions
            $online = PembayaranOnline::lockForUpdate()->findOrFail($online->id);
            if ($online->status !== 'menunggu') {
                throw new \DomainException("Pengajuan transfer {$online->kode_pengajuan} sudah diproses sebelumnya ({$online->status}).");
            }

            if ($status === 'disetujui') {
                $alokasi = $online->alokasi_pos ?? [];
                if (empty($alokasi)) {
                    throw new \RuntimeException('Alokasi pos tagihan belum ditentukan pada pengajuan ini.');
                }

                $transaksi = $this->prosesPembayaranKasir(
                    siswaId: $online->siswa_id,
                    rekeningId: $online->rekening_tujuan_id,
                    alokasi: $alokasi,
                    channel: 'online_transfer',
                    namaPenyetor: $online->nama_pemilik_rekening . ' (Transfer ' . ($online->bank_pengirim ?: 'Bank') . ')',
                    catatan: 'Verifikasi transfer online kode: ' . $online->kode_pengajuan,
                    userId: $verifikatorId
                );

                $online->update([
                    'status' => 'disetujui',
                    'diverifikasi_oleh' => $verifikatorId,
                    'diverifikasi_pada' => now(),
                    'transaksi_id' => $transaksi->id,
                ]);
            } elseif ($status === 'ditolak') {
                $online->update([
                    'status' => 'ditolak',
                    'alasan_penolakan' => $alasanPenolakan ?: 'Bukti transfer tidak valid atau dana belum masuk.',
                    'diverifikasi_oleh' => $verifikatorId,
                    'diverifikasi_pada' => now(),
                ]);
            }

            return $online;
        });
    }

    /**
     * Generate teks WhatsApp tagihan siap kirim ke orang tua
     */
    public function generateWhatsappTagihan(Siswa $siswa, ?string $targetHubungan = 'ayah'): array
    {
        $tagihans = Tagihan::with('posBiaya')
            ->where('siswa_id', $siswa->id)
            ->belumLunas()
            ->urutTahunAjaran()
            ->get();

        $ortu = $siswa->orangTuas->firstWhere('hubungan', $targetHubungan)
            ?? $siswa->orangTuas->firstWhere('hubungan', 'ayah')
            ?? $siswa->orangTuas->firstWhere('hubungan', 'ibu')
            ?? $siswa->orangTuas->firstWhere('hubungan', 'wali');

        $namaOrtu = $ortu?->nama ?? 'Bapak/Ibu Orang Tua/Wali';
        $noHp = $ortu?->no_hp ?? $siswa->hp ?? $siswa->telepon;

        // Normalisasi nomor telepon ke 62...
        $cleanPhone = preg_replace('/[^0-9]/', '', (string) $noHp);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        }

        $totalTunggakan = $tagihans->sum('sisa_tagihan');
        $rekeningList = Rekening::where('is_aktif', true)->where('is_kas_tunai', false)->get();

        $rekeningText = "";
        foreach ($rekeningList as $rek) {
            $rekeningText .= "- *{$rek->nama_bank}*: `{$rek->nomor_rekening}` a.n {$rek->atas_nama}\n";
        }
        if (empty($rekeningText)) {
            $rekeningText = "- Loket Kasir Bendahara Sekolah\n";
        }

        $rincian = "";
        $no = 1;
        foreach ($tagihans as $tgh) {
            $label = $tgh->label_tagihan;
            $sisa = 'Rp ' . number_format($tgh->sisa_tagihan, 0, ',', '.');
            $rincian .= "{$no}. {$label}: *{$sisa}*\n";
            $no++;
        }

        if ($tagihans->isEmpty()) {
            $pesan = "Yth. {$namaOrtu},\n\nAlhamdulillah, seluruh administrasi pembayaran sekolah ananda *{$siswa->nama}* ({$siswa->rombel?->nama}) saat ini telah *LUNAS*. Terima kasih atas kerjasamanya.\n\n_SMK Wikrama 1 Garut_";
        } else {
            $totalFmt = 'Rp ' . number_format($totalTunggakan, 0, ',', '.');
            $pesan = "Assalamu'alaikum Wr. Wb.\n"
                . "Yth. {$namaOrtu},\n"
                . "Wali dari ananda: *{$siswa->nama}*\n"
                . "Kelas: *{$siswa->rombel?->nama}* | Rayon: *{$siswa->rayon?->nama}*\n\n"
                . "Dengan ini kami sampaikan rincian informasi tagihan administrasi sekolah:\n"
                . "----------------------------------------\n"
                . $rincian
                . "----------------------------------------\n"
                . "Total Kewajiban: *{$totalFmt}*\n\n"
                . "Pembayaran dapat dilakukan melalui transfer rekening resmi sekolah:\n"
                . $rekeningText . "\n"
                . "Setelah transfer, bukti pembayaran dapat diunggah melalui Portal Orang Tua atau konfirmasi ke Bendahara Sekolah.\n\n"
                . "Terima kasih atas perhatian dan kerjasamanya.\n"
                . "Wassalamu'alaikum Wr. Wb.\n"
                . "_Bendahara SMK Wikrama 1 Garut_";
        }

        $waLink = ! empty($cleanPhone)
            ? 'https://wa.me/' . $cleanPhone . '?text=' . rawurlencode($pesan)
            : null;

        return [
            'nama_ortu' => $namaOrtu,
            'no_hp' => $cleanPhone,
            'pesan' => $pesan,
            'wa_link' => $waLink,
            'total_tunggakan' => $totalTunggakan,
            'jumlah_item' => $tagihans->count(),
        ];
    }
}

