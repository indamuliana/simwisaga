<?php

namespace App\Modules\AdministrasiWeb\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class WebMenu extends Model
{
    use HasFactory;

    protected $table = 'web_menus';

    protected $fillable = [
        'lokasi',
        'label',
        'url',
        'target',
        'urutan',
        'is_active',
        'parent_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'urutan' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget('web_menus_header');
            Cache::forget('web_menus_footer');
        });

        static::deleted(function () {
            Cache::forget('web_menus_header');
            Cache::forget('web_menus_footer');
        });
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')->where('is_active', true)->orderBy('urutan');
    }

    public function allChildren()
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('urutan');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeHeader($query)
    {
        return $query->where('lokasi', 'header');
    }

    public function scopeFooter($query)
    {
        return $query->where('lokasi', 'footer');
    }

    public static function getHeaderMenu()
    {
        return Cache::rememberForever('web_menus_header', function () {
            try {
                if (self::header()->count() === 0) {
                    self::seedDefaultMenus();
                }

                return self::header()
                    ->active()
                    ->whereNull('parent_id')
                    ->with('children')
                    ->orderBy('urutan')
                    ->get();
            } catch (\Throwable $e) {
                report($e);
                return collect();
            }
        });
    }

    public static function getFooterMenu()
    {
        return Cache::rememberForever('web_menus_footer', function () {
            try {
                if (self::footer()->count() === 0) {
                    self::seedDefaultMenus();
                }

                return self::footer()
                    ->active()
                    ->orderBy('urutan')
                    ->get();
            } catch (\Throwable $e) {
                report($e);
                return collect();
            }
        });
    }

    /**
     * Otomatis inisialisasi menu standar sekolah jika tabel masih kosong
     */
    public static function seedDefaultMenus(): void
    {
        if (self::count() > 0) {
            return;
        }

        // ── 1. Header Navigation ──
        // Beranda
        self::create([
            'lokasi' => 'header',
            'label' => 'Beranda',
            'url' => '#beranda',
            'target' => '_self',
            'urutan' => 1,
            'is_active' => true,
            'parent_id' => null,
        ]);

        // Profil
        $profil = self::create([
            'lokasi' => 'header',
            'label' => 'Profil',
            'url' => '#filosofi',
            'target' => '_self',
            'urutan' => 2,
            'is_active' => true,
            'parent_id' => null,
        ]);
        self::create(['lokasi' => 'header', 'label' => 'Filosofi Pendidikan', 'url' => '#filosofi', 'target' => '_self', 'urutan' => 1, 'is_active' => true, 'parent_id' => $profil->id]);
        self::create(['lokasi' => 'header', 'label' => 'Mengenal Wikrama', 'url' => '#mengenal', 'target' => '_self', 'urutan' => 2, 'is_active' => true, 'parent_id' => $profil->id]);
        self::create(['lokasi' => 'header', 'label' => 'Rapor Mutu Resmi', 'url' => '#rapor-mutu', 'target' => '_self', 'urutan' => 3, 'is_active' => true, 'parent_id' => $profil->id]);
        self::create(['lokasi' => 'header', 'label' => 'Sekolah Teladan', 'url' => '#sekolah-teladan', 'target' => '_self', 'urutan' => 4, 'is_active' => true, 'parent_id' => $profil->id]);

        // Jurusan
        $jurusan = self::create([
            'lokasi' => 'header',
            'label' => 'Jurusan',
            'url' => '#jurusan',
            'target' => '_self',
            'urutan' => 3,
            'is_active' => true,
            'parent_id' => null,
        ]);
        self::create(['lokasi' => 'header', 'label' => 'PPLG', 'url' => '#jurusan', 'target' => '_self', 'urutan' => 1, 'is_active' => true, 'parent_id' => $jurusan->id]);
        self::create(['lokasi' => 'header', 'label' => 'TJKT', 'url' => '#jurusan', 'target' => '_self', 'urutan' => 2, 'is_active' => true, 'parent_id' => $jurusan->id]);
        self::create(['lokasi' => 'header', 'label' => 'Bisnis Digital & Pemasaran', 'url' => '#jurusan', 'target' => '_self', 'urutan' => 3, 'is_active' => true, 'parent_id' => $jurusan->id]);
        self::create(['lokasi' => 'header', 'label' => 'Perhotelan', 'url' => '#jurusan', 'target' => '_self', 'urutan' => 4, 'is_active' => true, 'parent_id' => $jurusan->id]);

        // Kesiswaan
        $kesiswaan = self::create([
            'lokasi' => 'header',
            'label' => 'Kesiswaan',
            'url' => '#dinamika-siswa',
            'target' => '_self',
            'urutan' => 4,
            'is_active' => true,
            'parent_id' => null,
        ]);
        self::create(['lokasi' => 'header', 'label' => 'Dinamika Siswa', 'url' => '#dinamika-siswa', 'target' => '_self', 'urutan' => 1, 'is_active' => true, 'parent_id' => $kesiswaan->id]);
        self::create(['lokasi' => 'header', 'label' => 'Pilar Karakter Santri', 'url' => '#pilar-karakter', 'target' => '_self', 'urutan' => 2, 'is_active' => true, 'parent_id' => $kesiswaan->id]);
        self::create(['lokasi' => 'header', 'label' => 'Suara Komunitas', 'url' => '#suara-komunitas', 'target' => '_self', 'urutan' => 3, 'is_active' => true, 'parent_id' => $kesiswaan->id]);

        // Informasi
        $informasi = self::create([
            'lokasi' => 'header',
            'label' => 'Informasi',
            'url' => '#agenda',
            'target' => '_self',
            'urutan' => 5,
            'is_active' => true,
            'parent_id' => null,
        ]);
        self::create(['lokasi' => 'header', 'label' => 'Agenda Sekolah', 'url' => '#agenda', 'target' => '_self', 'urutan' => 1, 'is_active' => true, 'parent_id' => $informasi->id]);
        self::create(['lokasi' => 'header', 'label' => 'Berita & Prestasi', 'url' => '#berita', 'target' => '_self', 'urutan' => 2, 'is_active' => true, 'parent_id' => $informasi->id]);
        self::create(['lokasi' => 'header', 'label' => 'Fasilitas Kampus', 'url' => '#fasilitas', 'target' => '_self', 'urutan' => 3, 'is_active' => true, 'parent_id' => $informasi->id]);
        self::create(['lokasi' => 'header', 'label' => 'Mitra Industri', 'url' => '#mitra', 'target' => '_self', 'urutan' => 4, 'is_active' => true, 'parent_id' => $informasi->id]);

        // Kontak
        self::create([
            'lokasi' => 'header',
            'label' => 'Kontak',
            'url' => '#kontak',
            'target' => '_self',
            'urutan' => 6,
            'is_active' => true,
            'parent_id' => null,
        ]);

        // ── 2. Footer Navigation ──
        $footerLinks = [
            ['label' => 'Beranda Utama', 'url' => '#beranda'],
            ['label' => 'Filosofi & Visi Misi', 'url' => '#filosofi'],
            ['label' => 'Mengenal Lebih Dekat', 'url' => '#mengenal'],
            ['label' => '4 Jurusan Kejuruan Unggulan', 'url' => '#jurusan'],
            ['label' => 'Agenda Kegiatan Terdekat', 'url' => '#agenda'],
            ['label' => 'Dinamika Siswa & Prestasi', 'url' => '#dinamika-siswa'],
            ['label' => 'Kehidupan Siswa (Fast Facts)', 'url' => '#fakta'],
            ['label' => 'Suara Komunitas (Testimoni)', 'url' => '#suara-komunitas'],
            ['label' => 'Fasilitas & Lab Praktik', 'url' => '#fasilitas'],
            ['label' => 'Berita & Prestasi', 'url' => '#berita'],
            ['label' => 'Rapor Mutu Kemendikdasmen', 'url' => '#rapor-mutu'],
            ['label' => 'Pilar Karakter & Mutu', 'url' => '#pilar-karakter'],
            ['label' => 'Sekolah Teladan Nasional', 'url' => '#sekolah-teladan'],
            ['label' => 'Unduh Brosur Resmi', 'url' => 'http://brosur.smkwikrama1garut.sch.id/', 'target' => '_blank'],
        ];

        foreach ($footerLinks as $fIdx => $flink) {
            self::create([
                'lokasi' => 'footer',
                'label' => $flink['label'],
                'url' => $flink['url'],
                'target' => $flink['target'] ?? '_self',
                'urutan' => $fIdx + 1,
                'is_active' => true,
                'parent_id' => null,
            ]);
        }
    }
}
