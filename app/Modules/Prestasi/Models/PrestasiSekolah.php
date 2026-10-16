<?php

namespace App\Modules\Prestasi\Models;

use App\Models\User;
use App\Modules\Akademik\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use OwenIt\Auditing\Contracts\Auditable;

class PrestasiSekolah extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'prestasi_sekolahs';

    protected $fillable = [
        'tahun_ajaran_id',
        'kategori',
        'penyelenggara_nama',
        'nama_prestasi',
        'bidang',
        'tingkat',
        'capaian',
        'tanggal_perolehan',
        'masa_berlaku_sampai',
        'nomor_sk',
        'sertifikat_file_path',
        'foto_file_path',
        'deskripsi',
        'created_by',
    ];

    protected $casts = [
        'tanggal_perolehan' => 'date',
        'masa_berlaku_sampai' => 'date',
    ];

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getKategoriLabelAttribute(): string
    {
        return match ($this->kategori) {
            'akreditasi' => 'Akreditasi Sekolah',
            'kejuaraan_institusi' => 'Kejuaraan / Lomba Sekolah',
            'sertifikasi_iso_lsp' => 'Sertifikasi Mutu (ISO/LSP)',
            'penghargaan_khusus' => 'Penghargaan / Apresiasi Khusus',
            default => ucfirst(str_replace('_', ' ', $this->kategori ?? '-')),
        };
    }

    public function getTingkatLabelAttribute(): string
    {
        return match ($this->tingkat) {
            'kabupaten_kota' => 'Kabupaten/Kota',
            'provinsi' => 'Provinsi',
            'nasional' => 'Nasional',
            'internasional' => 'Internasional',
            default => ucfirst(str_replace('_', ' ', $this->tingkat ?? '-')),
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

    public function getStatusBerlakuAttribute(): string
    {
        if (!$this->masa_berlaku_sampai) {
            return 'Permanen / Terbuka';
        }

        return $this->masa_berlaku_sampai->isPast() ? 'Kadaluarsa' : 'Masih Berlaku';
    }
}
