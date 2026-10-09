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

    public function label(): string
    {
        return match($this) {
            self::HADIR => 'Hadir',
            self::SAKIT => 'Sakit',
            self::IZIN => 'Izin',
            self::ALPA => 'Alpa',
            self::DISPEN_CBT_PLH => 'Dispen CBT PLH',
            self::DISPEN_CBT_KWH => 'Dispen CBT KWH',
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
        };
    }

    public function isHadirStatistik(): bool
    {
        return match($this) {
            self::HADIR, self::DISPEN_CBT_PLH, self::DISPEN_CBT_KWH => true,
            default => false,
        };
    }
}
