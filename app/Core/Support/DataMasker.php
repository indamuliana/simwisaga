<?php

namespace App\Core\Support;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DataMasker
{
    /**
     * Memeriksa apakah pengguna berhak melihat data pribadi sensitif tanpa sensor.
     * Hak akses penuh diberikan kepada Super Admin, Operator, Kepala Sekolah,
     * atau pengguna yang memiliki permission khusus '.lihat_sensitif'.
     */
    public static function canViewSensitive(?User $user = null): bool
    {
        $user ??= Auth::user();

        if (! $user) {
            return false;
        }

        if ($user->hasRole(['Super Admin', 'Operator', 'Kepala Sekolah'])) {
            return true;
        }

        return $user->can('kesiswaan.siswa.lihat_sensitif');
    }

    /**
     * Sensor NIK (16 digit).
     * Contoh: 3205020807060003 -> 3205********0003
     */
    public static function maskNik(?string $nik, ?User $user = null): string
    {
        if (empty($nik)) {
            return '-';
        }

        if (static::canViewSensitive($user)) {
            return $nik;
        }

        $clean = trim((string) $nik);
        $len = strlen($clean);

        if ($len >= 12) {
            $prefix = substr($clean, 0, 4);
            $suffix = substr($clean, -4);
            $masked = str_repeat('*', $len - 8);
            return $prefix . $masked . $suffix;
        }

        return str_repeat('*', max(4, $len - 2)) . substr($clean, -2);
    }

    /**
     * Sensor Nomor Kartu Keluarga (16 digit).
     * Contoh: 3205021008070335 -> 3205********0335
     */
    public static function maskKk(?string $kk, ?User $user = null): string
    {
        return static::maskNik($kk, $user);
    }

    /**
     * Sensor NISN (10 digit).
     * Contoh: 0063473065 -> 006*****65
     */
    public static function maskNisn(?string $nisn, ?User $user = null): string
    {
        if (empty($nisn)) {
            return '-';
        }

        if (static::canViewSensitive($user)) {
            return $nisn;
        }

        $clean = trim((string) $nisn);
        $len = strlen($clean);

        if ($len >= 8) {
            $prefix = substr($clean, 0, 3);
            $suffix = substr($clean, -2);
            $masked = str_repeat('*', $len - 5);
            return $prefix . $masked . $suffix;
        }

        return str_repeat('*', max(2, $len - 2)) . substr($clean, -2);
    }
}
