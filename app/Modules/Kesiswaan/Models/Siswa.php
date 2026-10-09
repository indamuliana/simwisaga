<?php

namespace App\Modules\Kesiswaan\Models;

use App\Models\User;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
}
