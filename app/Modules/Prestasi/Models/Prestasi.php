<?php

namespace App\Modules\Prestasi\Models;

use App\Models\User;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Sistem\Models\PeriodeRapor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use OwenIt\Auditing\Contracts\Auditable;

class Prestasi extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'prestasis';

    protected $fillable = [
        'tahun_ajaran_id',
        'semester_id',
        'periode_rapor_id',
        'tipe_peserta',
        'nama_tim',
        'kategori',
        'penyelenggara_lingkup',
        'penyelenggara_nama',
        'nama_prestasi',
        'bidang',
        'tingkat',
        'peringkat',
        'tanggal_perolehan',
        'tanggal_selesai',
        'tempat',
        'pembimbing_pegawai_id',
        'pembimbing_nama_manual',
        'nomor_sertifikat',
        'sertifikat_file_path',
        'foto_file_path',
        'deskripsi',
        'status_verifikasi',
        'verified_by',
        'verified_at',
        'catatan_verifikasi',
        'created_by',
    ];

    protected $casts = [
        'tanggal_perolehan' => 'date',
        'tanggal_selesai' => 'date',
        'verified_at' => 'datetime',
    ];

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function periodeRapor(): BelongsTo
    {
        return $this->belongsTo(PeriodeRapor::class);
    }

    public function pembimbingPegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'pembimbing_pegawai_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function anggotas(): HasMany
    {
        return $this->hasMany(PrestasiSiswaAnggota::class, 'prestasi_id');
    }

    public function siswas(): BelongsToMany
    {
        return $this->belongsToMany(Siswa::class, 'prestasi_siswa_anggotas', 'prestasi_id', 'siswa_id')
            ->withPivot('peran', 'keterangan')
            ->withTimestamps();
    }

    // Scopes
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status_verifikasi', 'approved');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status_verifikasi', 'pending');
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status_verifikasi', 'rejected');
    }

    public function scopeForSemester(Builder $query, int $semesterId): Builder
    {
        return $query->where('semester_id', $semesterId);
    }

    public function scopeForPeriode(Builder $query, int $periodeRaporId): Builder
    {
        return $query->where('periode_rapor_id', $periodeRaporId);
    }

    // Accessors
    public function getDaftarSiswaNamaAttribute(): string
    {
        if ($this->siswas->isEmpty()) {
            return '-';
        }

        return $this->siswas->pluck('nama')->implode(', ');
    }

    public function getPembimbingNamaAttribute(): string
    {
        if ($this->pembimbingPegawai) {
            return $this->pembimbingPegawai->nama_gelar;
        }

        return $this->pembimbing_nama_manual ?: '-';
    }

    public function getKategoriLabelAttribute(): string
    {
        return match ($this->kategori) {
            'akademik' => 'Akademik',
            'non_akademik' => 'Non-Akademik (Kejuaraan)',
            'sertifikasi' => 'Sertifikasi Keahlian',
            default => ucfirst(str_replace('_', ' ', $this->kategori ?? '-')),
        };
    }

    public function getTingkatLabelAttribute(): string
    {
        return match ($this->tingkat) {
            'sekolah' => 'Sekolah',
            'kecamatan' => 'Kecamatan',
            'kabupaten_kota' => 'Kabupaten/Kota',
            'provinsi' => 'Provinsi',
            'nasional' => 'Nasional',
            'internasional' => 'Internasional',
            default => ucfirst(str_replace('_', ' ', $this->tingkat ?? '-')),
        };
    }

    public function getPenyelenggaraLingkupLabelAttribute(): string
    {
        return match ($this->penyelenggara_lingkup) {
            'dalam_sekolah' => 'Internal (Dalam Sekolah)',
            'luar_sekolah' => 'Eksternal (Luar Sekolah)',
            default => ucfirst(str_replace('_', ' ', $this->penyelenggara_lingkup ?? '-')),
        };
    }

    public function getSertifikatUrlAttribute(): ?string
    {
        return $this->sertifikat_file_path ? Storage::url($this->sertifikat_file_path) : null;
    }

    public function getFotoUrlAttribute(): ?string
    {
        return $this->foto_file_path ? Storage::url($this->foto_file_path) : null;
    }
}
