<?php

namespace App\Modules\Kesiswaan\Models;

use App\Models\User;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use OwenIt\Auditing\Contracts\Auditable;

class Siswa extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'user_id',
        'rombel_id',
        'rayon_id',
        'program_id',
        'nama',
        'nipd',
        'jk',
        'nisn',
        'tempat_lahir',
        'tanggal_lahir',
        'nik',
        'agama',
        'alamat',
        'rt',
        'rw',
        'dusun',
        'kelurahan',
        'kecamatan',
        'kode_pos',
        'jenis_tinggal',
        'alat_transportasi',
        'lintang',
        'bujur',
        'jarak_ke_sekolah_km',
        'telepon',
        'hp',
        'email',
        'skhun',
        'no_peserta_un',
        'no_seri_ijazah',
        'no_akta_lahir',
        'no_kk',
        'sekolah_asal',
        'penerima_kps',
        'no_kps',
        'penerima_kip',
        'nomor_kip',
        'nama_di_kip',
        'nomor_kks',
        'layak_pip',
        'alasan_layak_pip',
        'bank',
        'nomor_rekening_bank',
        'rekening_atas_nama',
        'kebutuhan_khusus',
        'anak_ke_berapa',
        'berat_badan',
        'tinggi_badan',
        'lingkar_kepala',
        'jml_saudara_kandung',
        'status',
        'is_active',
        'foto_path',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'tanggal_lahir' => 'date',
        'penerima_kps' => 'boolean',
        'penerima_kip' => 'boolean',
        'layak_pip' => 'boolean',
        'jarak_ke_sekolah_km' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function rombel(): BelongsTo
    {
        return $this->belongsTo(Rombel::class);
    }

    public function rayon(): BelongsTo
    {
        return $this->belongsTo(Rayon::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function orangTuas(): HasMany
    {
        return $this->hasMany(SiswaOrangTua::class);
    }

    public function riwayatRayon(): HasMany
    {
        return $this->hasMany(RayonSiswa::class)->orderByDesc('tgl_mulai');
    }

    public function riwayatRombel(): HasMany
    {
        return $this->hasMany(RombelSiswa::class);
    }

    public function riwayatAkademiks(): HasMany
    {
        return $this->hasMany(RiwayatAkademik::class)->orderByDesc('id');
    }

    public function alumni(): HasOne
    {
        return $this->hasOne(\App\Modules\Alumni\Models\Alumni::class);
    }

    public function customKelompoks(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            \App\Modules\Akademik\Models\CustomKelompok::class,
            'custom_kelompok_siswas'
        )->withPivot('id', 'keterangan')->withTimestamps();
    }

    public function customKelompokSiswas(): HasMany
    {
        return $this->hasMany(\App\Modules\Akademik\Models\CustomKelompokSiswa::class);
    }

    public function getAyahAttribute(): ?SiswaOrangTua
    {
        return $this->orangTuas->firstWhere('hubungan', 'ayah');
    }

    public function getIbuAttribute(): ?SiswaOrangTua
    {
        return $this->orangTuas->firstWhere('hubungan', 'ibu');
    }

    public function getWaliAttribute(): ?SiswaOrangTua
    {
        return $this->orangTuas->firstWhere('hubungan', 'wali');
    }

    /**
     * Scope query to find students who are currently or historically enrolled in the given rombel.
     * Guarantees ledger & historical integrity across Nilai, Absensi, and Rapor modules.
     */
    public function scopeInRombel($query, int $rombelId)
    {
        return $query->where(function ($q) use ($rombelId) {
            $q->where($this->getTable() . '.rombel_id', $rombelId)
                ->orWhereHas('riwayatRombel', function ($rq) use ($rombelId) {
                    $rq->where('rombel_id', $rombelId);
                });
        });
    }

    public function prestasis(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(\App\Modules\Prestasi\Models\Prestasi::class, 'prestasi_siswa_anggotas', 'siswa_id', 'prestasi_id')
            ->withPivot('peran', 'keterangan')
            ->withTimestamps();
    }

    public function prestasisApproved(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(\App\Modules\Prestasi\Models\Prestasi::class, 'prestasi_siswa_anggotas', 'siswa_id', 'prestasi_id')
            ->where('prestasis.status_verifikasi', 'approved')
            ->withPivot('peran', 'keterangan')
            ->withTimestamps();
    }
}

