<?php

namespace App\Modules\Kesiswaan\Models;

use App\Models\User;
use App\Modules\Absensi\Models\AbsensiHarian;
use App\Modules\Akademik\Models\CustomKelompok;
use App\Modules\Akademik\Models\CustomKelompokSiswa;
use App\Modules\Akademik\Models\MapelPilihanAnggota;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Alumni\Models\Alumni;
use App\Modules\BeasiswaOta\Models\SiswaPenerima;
use App\Modules\Ekstrakurikuler\Models\EkstrakurikulerAnggota;
use App\Modules\Kepesantrenan\Models\PesantrenHafalanSiswa;
use App\Modules\Kepesantrenan\Models\PesantrenHalaqoh;
use App\Modules\Kepesantrenan\Models\PesantrenHalaqohSiswa;
use App\Modules\Kepesantrenan\Models\PesantrenKamar;
use App\Modules\Kepesantrenan\Models\PesantrenKamarSiswa;
use App\Modules\Kepesantrenan\Models\PesantrenSetoranHafalan;
use App\Modules\PortalSiswa\Models\PengajuanBeasiswa;
use App\Modules\PortalSiswa\Models\PengajuanPerbaikanDataSiswa;
use App\Modules\Prestasi\Models\Prestasi;
use App\Modules\SeniBudaya\Models\SeniBudayaAnggota;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        'negara',
        'provinsi',
        'kabupaten_kota',
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
        'sekolah_asal_raw',
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

    protected static function booted(): void
    {
        static::creating(function (Siswa $siswa) {
            if (! empty($siswa->sekolah_asal) && empty($siswa->sekolah_asal_raw)) {
                $siswa->sekolah_asal_raw = $siswa->sekolah_asal;
            }
        });

        static::saving(function (Siswa $siswa) {
            if ($siswa->isDirty('status')) {
                $siswa->is_active = ($siswa->status === 'aktif');
            } elseif ($siswa->isDirty('is_active')) {
                $siswa->status = $siswa->is_active ? 'aktif' : ($siswa->status === 'aktif' ? 'mutasi' : $siswa->status);
            }
        });
    }

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
        return $this->hasOne(Alumni::class);
    }

    public function customKelompoks(): BelongsToMany
    {
        return $this->belongsToMany(
            CustomKelompok::class,
            'custom_kelompok_siswas'
        )->withPivot('id', 'keterangan')->withTimestamps();
    }

    public function customKelompokSiswas(): HasMany
    {
        return $this->hasMany(CustomKelompokSiswa::class);
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
            $q->where($this->getTable().'.rombel_id', $rombelId)
                ->orWhereHas('riwayatRombel', function ($rq) use ($rombelId) {
                    $rq->where('rombel_id', $rombelId);
                });
        });
    }

    public function prestasis(): BelongsToMany
    {
        return $this->belongsToMany(Prestasi::class, 'prestasi_siswa_anggotas', 'siswa_id', 'prestasi_id')
            ->withPivot('peran', 'keterangan')
            ->withTimestamps();
    }

    public function prestasisApproved(): BelongsToMany
    {
        return $this->belongsToMany(Prestasi::class, 'prestasi_siswa_anggotas', 'siswa_id', 'prestasi_id')
            ->where('prestasis.status_verifikasi', 'approved')
            ->withPivot('peran', 'keterangan')
            ->withTimestamps();
    }

    public function seniBudayaAnggotas(): HasMany
    {
        return $this->hasMany(SeniBudayaAnggota::class, 'siswa_id');
    }

    public function ekstrakurikulerAnggotas(): HasMany
    {
        return $this->hasMany(EkstrakurikulerAnggota::class, 'siswa_id');
    }

    public function mapelPilihanAnggotas(): HasMany
    {
        return $this->hasMany(MapelPilihanAnggota::class, 'siswa_id');
    }

    public function otaPenerimas(): HasMany
    {
        return $this->hasMany(SiswaPenerima::class, 'siswa_id');
    }

    public function absensiHarians(): HasMany
    {
        return $this->hasMany(AbsensiHarian::class, 'siswa_id');
    }

    public function pengajuanPerbaikanDatas(): HasMany
    {
        return $this->hasMany(PengajuanPerbaikanDataSiswa::class, 'siswa_id');
    }

    public function pengajuanBeasiswas(): HasMany
    {
        return $this->hasMany(PengajuanBeasiswa::class, 'siswa_id');
    }

    public function pesantrenKamarSiswas(): HasMany
    {
        return $this->hasMany(PesantrenKamarSiswa::class, 'siswa_id');
    }

    public function pesantrenKamars(): BelongsToMany
    {
        return $this->belongsToMany(
            PesantrenKamar::class,
            'pesantren_kamar_siswas',
            'siswa_id',
            'kamar_id'
        )->withPivot('peran', 'nomor_ranjang')->withTimestamps();
    }

    public function pesantrenHalaqohSiswas(): HasMany
    {
        return $this->hasMany(PesantrenHalaqohSiswa::class, 'siswa_id');
    }

    public function pesantrenHalaqohs(): BelongsToMany
    {
        return $this->belongsToMany(
            PesantrenHalaqoh::class,
            'pesantren_halaqoh_siswas',
            'siswa_id',
            'halaqoh_id'
        )->withTimestamps();
    }

    public function pesantrenHafalans(): HasMany
    {
        return $this->hasMany(PesantrenHafalanSiswa::class, 'siswa_id');
    }

    public function pesantrenSetorans(): HasMany
    {
        return $this->hasMany(PesantrenSetoranHafalan::class, 'siswa_id');
    }
}
