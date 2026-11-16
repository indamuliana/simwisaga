<?php

namespace App\Modules\Keuangan\Models;

use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class AlumniTunggakan extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'keuangan_alumni_tunggakans';

    protected $fillable = [
        'siswa_id',
        'status_ijazah',
        'tanggal_serah_terima',
        'catatan',
    ];

    protected $casts = [
        'tanggal_serah_terima' => 'date',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function getBadgeStatusIjazahAttribute(): string
    {
        return match ($this->status_ijazah) {
            'ditahan' => 'Ijazah Ditahan',
            'boleh_diambil' => 'Boleh Diambil',
            'sudah_diambil' => 'Sudah Diambil',
            default => ucfirst($this->status_ijazah),
        };
    }
}

