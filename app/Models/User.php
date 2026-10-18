<?php

namespace App\Models;

use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements Auditable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;
    use \OwenIt\Auditing\Auditable;

    public const SUPER_ADMIN_ROLE = 'Super Admin';

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'is_active',
        'must_change_password',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /** Kolom yang tidak boleh masuk audit log. */
    protected $auditExclude = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
            'must_change_password' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(self::SUPER_ADMIN_ROLE);
    }

    public function pegawai(): HasOne
    {
        return $this->hasOne(Pegawai::class);
    }

    public function siswa(): HasOne
    {
        return $this->hasOne(Siswa::class);
    }

    /**
     * Mendapatkan metadata profil Pegawai atau Siswa yang tertaut ke akun ini
     */
    public function getLinkedProfileAttribute(): ?array
    {
        if ($this->relationLoaded('pegawai') ? $this->pegawai : $this->pegawai()->first()) {
            $p = $this->pegawai;
            return [
                'type' => 'pegawai',
                'label' => 'Pegawai',
                'name' => $p->nama_lengkap,
                'identifier' => $p->nip ?: ($p->nuptk ?: '-'),
                'detail' => ucfirst($p->jenis_gtk ?? 'GTK'),
            ];
        }

        if ($this->relationLoaded('siswa') ? $this->siswa : $this->siswa()->with('rombel')->first()) {
            $s = $this->siswa;
            return [
                'type' => 'siswa',
                'label' => 'Siswa',
                'name' => $s->nama,
                'identifier' => $s->nisn ?: ($s->nipd ?: '-'),
                'detail' => $s->rombel?->nama ?? 'Tanpa Rombel',
            ];
        }

        return null;
    }

    /**
     * Memeriksa apakah pengguna memiliki hak akses untuk melihat data/rapor siswa tertentu.
     */
    public function canViewStudent(Siswa $siswa): bool
    {
        if ($this->isSuperAdmin() || $this->can('rapor.cetak') || $this->can('kesiswaan.siswa.lihat')) {
            return true;
        }

        $pegawai = $this->relationLoaded('pegawai') ? $this->pegawai : $this->pegawai()->first();
        if ($pegawai) {
            $rombel = $siswa->relationLoaded('rombel') ? $siswa->rombel : $siswa->rombel()->first();
            if ($rombel) {
                if ($rombel->wali_kelas_id === $pegawai->id) {
                    return true;
                }
                if ($pegawai->penugasanMengajars()->where('rombel_id', $rombel->id)->exists()) {
                    return true;
                }
            }
            $rayon = $siswa->relationLoaded('rayon') ? $siswa->rayon : $siswa->rayon()->first();
            if ($rayon && $rayon->pembimbing_id === $pegawai->id) {
                return true;
            }
        }

        if ($this->hasRole('Siswa') && ($this->username === $siswa->nipd || $this->username === $siswa->nisn)) {
            return true;
        }

        if ($this->hasRole('Orang Tua') && ($this->username === 'ortu_' . $siswa->nipd || $this->username === 'ortu_' . $siswa->nisn)) {
            return true;
        }

        return false;
    }
}
