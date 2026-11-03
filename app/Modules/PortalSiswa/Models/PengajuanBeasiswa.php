<?php

namespace App\Modules\PortalSiswa\Models;

use App\Models\User;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanBeasiswa extends Model
{
    protected $table = 'pengajuan_beasiswas';

    protected $fillable = [
        'siswa_id',
        'tahun_ajaran_id',
        'semester_id',
        'jenis_beasiswa',
        'nominal_pengajuan',
        'alasan',
        'penghasilan_orang_tua',
        'jumlah_tanggungan',
        'lampiran_dokumen',
        'status',
        'catatan_admin',
        'diproses_oleh',
        'diproses_at',
    ];

    protected $casts = [
        'nominal_pengajuan' => 'decimal:2',
        'penghasilan_orang_tua' => 'decimal:2',
        'diproses_at' => 'datetime',
    ];

    public const STATUS_MENUNGGU = 'menunggu';
    public const STATUS_DISETUJUI = 'disetujui';
    public const STATUS_DITOLAK = 'ditolak';

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function diprosesOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }
}
