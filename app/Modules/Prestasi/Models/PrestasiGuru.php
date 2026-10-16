<?php

namespace App\Modules\Prestasi\Models;

use App\Models\User;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Kepegawaian\Models\Pegawai;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use OwenIt\Auditing\Contracts\Auditable;

class PrestasiGuru extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'prestasi_gurus';

    protected $fillable = [
        'tahun_ajaran_id',
        'semester_id',
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
        'tempat',
        'nomor_sertifikat',
        'sertifikat_file_path',
        'foto_file_path',
        'deskripsi',
        'created_by',
    ];

    protected $casts = [
        'tanggal_perolehan' => 'date',
    ];

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function anggotas(): HasMany
    {
        return $this->hasMany(PrestasiGuruAnggota::class, 'prestasi_guru_id');
    }

    public function pegawais(): BelongsToMany
    {
        return $this->belongsToMany(Pegawai::class, 'prestasi_guru_anggotas', 'prestasi_guru_id', 'pegawai_id')
            ->withPivot('peran', 'keterangan')
            ->withTimestamps();
    }

    public function getDaftarGuruNamaAttribute(): string
    {
        if ($this->pegawais->isEmpty()) {
            return '-';
        }

        return $this->pegawais->map(fn ($p) => $p->nama_gelar)->implode(', ');
    }

    public function getKategoriLabelAttribute(): string
    {
        return match ($this->kategori) {
            'karya_ilmiah' => 'Karya Ilmiah / Inovasi',
            'kejuaraan_gtk' => 'Lomba / Kejuaraan GTK',
            'sertifikasi_keahlian' => 'Sertifikasi Keahlian / Asesor',
            'penghargaan' => 'Penghargaan / Tanda Kehormatan',
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
