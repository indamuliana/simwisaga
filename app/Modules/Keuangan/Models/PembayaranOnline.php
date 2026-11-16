<?php

namespace App\Modules\Keuangan\Models;

use App\Models\User;
use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class PembayaranOnline extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'keuangan_pembayaran_onlines';

    protected $fillable = [
        'kode_pengajuan',
        'siswa_id',
        'user_id',
        'rekening_tujuan_id',
        'bank_pengirim',
        'nomor_rekening_pengirim',
        'nama_pemilik_rekening',
        'nominal_transfer',
        'tanggal_transfer',
        'file_bukti_path',
        'alokasi_pos',
        'catatan',
        'status',
        'alasan_penolakan',
        'diverifikasi_oleh',
        'diverifikasi_pada',
        'transaksi_id',
    ];

    protected $casts = [
        'nominal_transfer' => 'decimal:2',
        'tanggal_transfer' => 'date',
        'diverifikasi_pada' => 'datetime',
        'alokasi_pos' => 'array',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function rekeningTujuan(): BelongsTo
    {
        return $this->belongsTo(Rekening::class, 'rekening_tujuan_id');
    }

    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }

    public static function generateKodePengajuan(): string
    {
        return 'ONL-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
    }
}

