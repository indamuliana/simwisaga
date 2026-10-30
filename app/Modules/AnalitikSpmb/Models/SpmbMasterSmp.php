<?php

namespace App\Modules\AnalitikSpmb\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use OwenIt\Auditing\Contracts\Auditable;

class SpmbMasterSmp extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'spmb_master_smps';

    protected $fillable = [
        'npsn',
        'nama',
        'bentuk',
        'status',
        'jenjang',
        'provinsi',
        'kabupaten',
        'kecamatan',
        'kelurahan',
        'alamat',
    ];

    /**
     * Relasi ke master sekolah asal internal (jika sudah dipetakan).
     */
    public function sekolahAsal(): HasOne
    {
        return $this->hasOne(SpmbSekolahAsal::class, 'master_smp_id');
    }

    /**
     * URL referensi data Kemendikbud resmi.
     */
    public function getReferensiKemendikbudUrlAttribute(): string
    {
        return 'https://referensi.data.kemdikbud.go.id/tabs.php?npsn=' . urlencode($this->npsn);
    }

    /**
     * URL profil sekolah Dapodik Kemendikbud.
     */
    public function getDapodikUrlAttribute(): string
    {
        return 'https://dapo.kemdikbud.go.id/sekolah/' . urlencode($this->npsn);
    }
}
