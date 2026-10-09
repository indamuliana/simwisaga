<?php

namespace App\Modules\Akademik\Support;

use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\TahunAjaran;

class AcademicContext
{
    public const SESSION_KEY = 'academic_working_semester_id';

    /**
     * Mengambil semester kerja saat ini.
     * Jika user memilih semester tertentu di topbar, gunakan semester tsb (arsip).
     * Jika tidak, gunakan semester yang sedang aktif secara global di database.
     */
    public static function currentSemester(): ?Semester
    {
        $id = session(self::SESSION_KEY);
        if ($id && ($semester = Semester::with('tahunAjaran')->find($id))) {
            return $semester;
        }

        return Semester::with('tahunAjaran')->where('is_active', true)->first();
    }

    public static function currentTahunAjaran(): ?TahunAjaran
    {
        return self::currentSemester()?->tahunAjaran ?? TahunAjaran::where('is_active', true)->first();
    }

    public static function setWorkingSemester(int $semesterId): void
    {
        session([self::SESSION_KEY => $semesterId]);
    }

    public static function resetToActive(): void
    {
        session()->forget(self::SESSION_KEY);
    }
}
