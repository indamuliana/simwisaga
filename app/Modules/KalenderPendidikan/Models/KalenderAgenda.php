<?php

namespace App\Modules\KalenderPendidikan\Models;

use App\Models\User;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class KalenderAgenda extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'kalender_agendas';

    protected $fillable = [
        'parent_id',
        'tahun_ajaran_id',
        'semester_id',
        'kategori_id',
        'created_by',
        'finalized_by',
        'judul',
        'deskripsi',
        'tgl_mulai',
        'tgl_selesai',
        'jam_mulai',
        'jam_selesai',
        'is_all_day',
        'warna',
        'lokasi',
        'audiens',
        'status',
        'is_final',
        'finalized_at',
        'recurrence_type',
        'recurrence_days',
        'recurrence_until',
    ];

    protected $casts = [
        'tgl_mulai' => 'date',
        'tgl_selesai' => 'date',
        'is_all_day' => 'boolean',
        'is_final' => 'boolean',
        'finalized_at' => 'datetime',
        'recurrence_days' => 'array',
        'recurrence_until' => 'date',
    ];

    public const STATUS_DRAFT = 'draft';
    public const STATUS_INTERNAL = 'internal';
    public const STATUS_PUBLIK = 'publik';

    public const AUDIENS_SEMUA = 'semua';
    public const AUDIENS_GURU = 'guru';
    public const AUDIENS_KELAS = 'kelas';
    public const AUDIENS_ORTU = 'ortu';

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KalenderKategori::class, 'kategori_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'finalized_by');
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    public function getDisplayWarnaAttribute(): string
    {
        return $this->warna ?: ($this->kategori?->warna ?? '#014694');
    }

    public function scopePublicOnly($query)
    {
        return $query->where('status', self::STATUS_PUBLIK);
    }

    public function scopeVisibleForUser($query, ?User $user)
    {
        if (! $user) {
            return $query->where('status', self::STATUS_PUBLIK);
        }

        if ($user->isSuperAdmin() || $user->hasAnyRole(['Kepala Sekolah', 'Kurikulum', 'Kesiswaan', 'Operator'])) {
            return $query;
        }

        // Guru dan staf dapat melihat internal + publik + draft miliknya sendiri
        return $query->where(function ($q) use ($user) {
            $q->whereIn('status', [self::STATUS_INTERNAL, self::STATUS_PUBLIK])
              ->orWhere(function ($draftQuery) use ($user) {
                  $draftQuery->where('status', self::STATUS_DRAFT)
                             ->where('created_by', $user->id);
              });
        });
    }

    /**
     * Cek apakah user berwenang mengubah status / flag Final
     */
    public static function canManageFinalFlag(?User $user): bool
    {
        if (! $user) return false;
        return $user->isSuperAdmin() || $user->hasAnyRole(['Kepala Sekolah', 'Kurikulum', 'Kesiswaan']);
    }
}
