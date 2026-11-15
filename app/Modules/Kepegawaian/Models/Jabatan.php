<?php

namespace App\Modules\Kepegawaian\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class Jabatan extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'jabatans';

    protected $fillable = [
        'parent_id',
        'nama',
        'kode',
        'bobot_jam',
        'keterangan',
        'urutan',
        'is_active',
    ];

    protected $casts = [
        'bobot_jam' => 'float',
        'urutan' => 'integer',
        'is_active' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Jabatan::class, 'parent_id')->orderBy('urutan')->orderBy('nama');
    }

    public function pejabats(): HasMany
    {
        return $this->hasMany(JabatanPejabat::class, 'jabatan_id')->orderBy('urutan');
    }

    /**
     * Hitung kedalaman tingkat (depth level) hierarki dari root.
     */
    public function getDepth(): int
    {
        $depth = 0;
        $curr = $this->parent;
        while ($curr) {
            $depth++;
            $curr = $curr->parent;
        }
        return $depth;
    }
}
