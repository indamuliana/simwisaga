<?php

namespace App\Modules\Sistem\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class DocumentSigner extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public const SUMBER_KEPALA_SEKOLAH = 'kepala_sekolah';
    public const SUMBER_WALI_KELAS = 'wali_kelas';
    public const SUMBER_PEMBIMBING_RAYON = 'pembimbing_rayon';
    public const SUMBER_ORANG_TUA = 'orang_tua';
    public const SUMBER_TATA_USAHA = 'tata_usaha';
    public const SUMBER_KURIKULUM = 'kurikulum';
    public const SUMBER_KESISWAAN = 'kesiswaan';
    public const SUMBER_CUSTOM = 'custom';

    protected $fillable = [
        'document_template_id',
        'kolom_ke',
        'judul_jabatan',
        'sumber',
        'nama_pejabat',
        'nip_pejabat',
        'urutan',
    ];

    protected $casts = [
        'kolom_ke' => 'integer',
        'urutan' => 'integer',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(DocumentTemplate::class, 'document_template_id');
    }
}
