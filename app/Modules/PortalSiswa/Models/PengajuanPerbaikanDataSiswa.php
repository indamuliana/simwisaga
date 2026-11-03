<?php

namespace App\Modules\PortalSiswa\Models;

use App\Models\User;
use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanPerbaikanDataSiswa extends Model
{
    protected $table = 'pengajuan_perbaikan_data_siswas';

    protected $fillable = [
        'siswa_id',
        'kategori',
        'rincian_perubahan',
        'alasan',
        'lampiran',
        'status',
        'catatan_admin',
        'diproses_oleh',
        'diproses_at',
    ];

    protected $casts = [
        'rincian_perubahan' => 'array',
        'diproses_at' => 'datetime',
    ];

    public const STATUS_MENUNGGU = 'menunggu';
    public const STATUS_DISETUJUI = 'disetujui';
    public const STATUS_DITOLAK = 'ditolak';

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function diprosesOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }
}
