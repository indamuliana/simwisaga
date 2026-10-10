<?php

namespace App\Enums;

enum StatusAbsensi: string
{
    case HADIR = 'Hadir';
    case SAKIT = 'Sakit';
    case IZIN = 'Izin';
    case ALPA = 'Alpa';
    case DISPEN_CBT_PLH = 'Dispen_CBT_PLH';
    case DISPEN_CBT_KWH = 'Dispen_CBT_KWH';
    case DISPEN_LAINNYA = 'Dispen_Lainnya';

    public function label(): string
    {
        return match($this) {
            self::HADIR => 'Hadir',
            self::SAKIT => 'Sakit',
            self::IZIN => 'Izin',
            self::ALPA => 'Alpa',
            self::DISPEN_CBT_PLH => 'Dispen CBT PLH',
            self::DISPEN_CBT_KWH => 'Dispen CBT KWH',
            self::DISPEN_LAINNYA => 'Dispen Lainnya',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::HADIR => 'bg-green-100 text-green-800',
            self::SAKIT => 'bg-yellow-100 text-yellow-800',
            self::IZIN => 'bg-blue-100 text-blue-800',
            self::ALPA => 'bg-red-100 text-red-800',
            self::DISPEN_CBT_PLH, self::DISPEN_CBT_KWH => 'bg-purple-100 text-purple-800',
            self::DISPEN_LAINNYA => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-950/40 dark:text-indigo-300',
        };
    }

    public function isHadirStatistik(): bool
    {
        return match($this) {
            self::HADIR, self::DISPEN_CBT_PLH, self::DISPEN_CBT_KWH, self::DISPEN_LAINNYA => true,
            default => false,
        };
    }
}
