<?php

namespace App\Modules\Kepegawaian\Models;

use App\Models\User;
use App\Modules\Prestasi\Models\Prestasi;
use App\Modules\Prestasi\Models\PrestasiGuru;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class Pegawai extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'user_id',
        'nip',
        'nuptk',
        'nik',
        'nama_lengkap',
        'gelar_depan',
        'gelar_belakang',
        'jenis_kelamin',
        'jenis_gtk',
        'status_kepegawaian',
        'tempat_lahir',
        'tanggal_lahir',
        'no_hp',
        'email',
        'alamat',
        'jenis_wilayah',
        'negara',
        'provinsi_kode',
        'provinsi_nama',
        'kabupaten_kode',
        'kabupaten_nama',
        'kecamatan_kode',
        'kecamatan_nama',
        'desa_kode',
        'desa_nama',
        'kode_pos',
        'jabatan_struktural',
        'nomor_peserta_ukg',
        'nomor_registrasi_guru',
        'nomor_peserta_sertifikasi',
        'nomor_sertifikat_pendidik',
        'tmt_awal_guru',
        'no_sk_mulai_guru',
        'tgl_diangkat_pegawai_tetap',
        'no_sk_tetap_yayasan',
        'tgl_awal_masuk_wikrama',
        'golongan_darah',
        'npwp',
        'nama_wajib_pajak',
        'no_kartu_bpjs_kesehatan',
        'no_kartu_bpjs_ketenagakerjaan',
        'pendidikan_terakhir',
        'jurusan_dan_instansi',
        'tgl_lulus_pendidikan_terakhir',
        'tahun_lulus_pendidikan_terakhir',
        'pengalaman_kerja',
        'status_pernikahan',
        'nama_pasangan',
        'tgl_lahir_pasangan',
        'jumlah_anak',
        'data_anak',
        'nama_ayah',
        'nama_ibu',
        'foto_path',
        'ttd_path',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'tanggal_lahir' => 'date',
        'tmt_awal_guru' => 'date',
        'tgl_diangkat_pegawai_tetap' => 'date',
        'tgl_awal_masuk_wikrama' => 'date',
        'tgl_lulus_pendidikan_terakhir' => 'date',
        'tgl_lahir_pasangan' => 'date',
        'pengalaman_kerja' => 'array',
        'data_anak' => 'array',
        'jumlah_anak' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fotos(): HasMany
    {
        return $this->hasMany(PegawaiFoto::class)->orderBy('slot');
    }

    public function penugasanMengajars(): HasMany
    {
        return $this->hasMany(PenugasanMengajar::class);
    }

    public function penugasanWaliKelas(): HasMany
    {
        return $this->hasMany(PenugasanWaliKelas::class);
    }

    public function penugasanPembimbingRayon(): HasMany
    {
        return $this->hasMany(PenugasanPembimbingRayon::class);
    }

    /**
     * Nama lengkap beserta gelar akademik depan dan belakang.
     */
    public function getNamaGelarAttribute(): string
    {
        $depan = $this->gelar_depan ? trim($this->gelar_depan).' ' : '';
        $belakang = $this->gelar_belakang ? ', '.trim($this->gelar_belakang) : '';

        return $depan.$this->nama_lengkap.$belakang;
    }

    /**
     * Hitung umur otomatis dari tanggal lahir.
     */
    public function getUmurAttribute(): ?int
    {
        return $this->tanggal_lahir ? $this->tanggal_lahir->age : null;
    }

    public function getUmurLabelAttribute(): string
    {
        return $this->umur !== null ? "{$this->umur} Tahun" : '-';
    }

    /**
     * Hitung lama bekerja otomatis (Tahun) dari tanggal awal masuk.
     */
    public function getLamaBekerjaTahunAttribute(): ?int
    {
        return $this->tgl_awal_masuk_wikrama ? (int) $this->tgl_awal_masuk_wikrama->diffInYears(now()) : null;
    }

    public function getLamaBekerjaLabelAttribute(): string
    {
        return $this->lama_bekerja_tahun !== null ? "{$this->lama_bekerja_tahun} Tahun" : '-';
    }

    /**
     * Hitung masa kerja otomatis (X Tahun Y Bulan) dari tanggal awal masuk.
     */
    public function getMasaKerjaLabelAttribute(): string
    {
        if (! $this->tgl_awal_masuk_wikrama) {
            return '-';
        }

        $diff = $this->tgl_awal_masuk_wikrama->diff(now());

        return "{$diff->y} Tahun {$diff->m} Bulan";
    }

    /**
     * URL Foto profil utama.
     */
    public function getFotoUtamaUrlAttribute(): string
    {
        if ($this->foto_path && file_exists(public_path('storage/'.$this->foto_path))) {
            return asset('storage/'.$this->foto_path);
        }

        $name = urlencode($this->nama_lengkap ?: 'User');

        return "https://ui-avatars.com/api/?name={$name}&background=4f46e5&color=ffffff&size=200";
    }

    public function getAlamatLengkapFormattedAttribute(): string
    {
        if ($this->jenis_wilayah === 'luar_negeri') {
            return trim(($this->alamat ? $this->alamat.', ' : '').($this->negara ?: 'Luar Negeri'));
        }

        $parts = array_filter([
            $this->alamat,
            $this->desa_nama ? 'Desa/Kel. '.$this->desa_nama : null,
            $this->kecamatan_nama ? 'Kec. '.$this->kecamatan_nama : null,
            $this->kabupaten_nama,
            $this->provinsi_nama,
            $this->kode_pos,
        ]);

        return ! empty($parts) ? implode(', ', $parts) : ($this->alamat ?: '-');
    }

    public function prestasis(): BelongsToMany
    {
        return $this->belongsToMany(PrestasiGuru::class, 'prestasi_guru_anggotas', 'pegawai_id', 'prestasi_guru_id')
            ->withPivot('peran', 'keterangan')
            ->withTimestamps();
    }

    public function bimbinganPrestasis(): HasMany
    {
        return $this->hasMany(Prestasi::class, 'pembimbing_pegawai_id');
    }
}
