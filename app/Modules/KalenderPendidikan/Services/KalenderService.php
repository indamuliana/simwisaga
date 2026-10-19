<?php

namespace App\Modules\KalenderPendidikan\Services;

use App\Models\User;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\KalenderPendidikan\Models\KalenderAgenda;
use App\Modules\KalenderPendidikan\Models\KalenderLibur;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class KalenderService
{
    /**
     * Format data event & hari libur untuk FullCalendar v6
     */
    public function getCalendarEvents($startDate, $endDate, ?User $user, array $filters = []): array
    {
        $events = [];

        $agendaQuery = KalenderAgenda::with(['kategori', 'creator', 'validator'])
            ->visibleForUser($user)
            ->where('tgl_mulai', '<=', $endDate)
            ->where('tgl_selesai', '>=', $startDate);

        // Filter Kategori
        if (! empty($filters['kategori_id'])) {
            $agendaQuery->where('kategori_id', $filters['kategori_id']);
        }

        // Filter Status
        if (! empty($filters['status'])) {
            $agendaQuery->where('status', $filters['status']);
        }

        // Filter Flag Final
        if (isset($filters['is_final']) && $filters['is_final'] !== '') {
            $agendaQuery->where('is_final', (bool) $filters['is_final']);
        }

        // Filter Audiens
        if (! empty($filters['audiens'])) {
            $agendaQuery->where('audiens', $filters['audiens']);
        }

        $agendas = $agendaQuery->get();

        foreach ($agendas as $agenda) {
            $warna = $agenda->display_warna;

            // FullCalendar treats allDay end date as EXCLUSIVE (+1 day)
            $start = $agenda->is_all_day 
                ? $agenda->tgl_mulai->format('Y-m-d') 
                : $agenda->tgl_mulai->format('Y-m-d') . 'T' . ($agenda->jam_mulai ?? '00:00:00');

            $end = $agenda->is_all_day 
                ? $agenda->tgl_selesai->copy()->addDay()->format('Y-m-d') 
                : $agenda->tgl_selesai->format('Y-m-d') . 'T' . ($agenda->jam_selesai ?? '23:59:59');

            $prefix = $agenda->is_final ? '✓ ' : ($agenda->status === 'draft' ? '✎ [Draft] ' : '');

            $events[] = [
                'id' => (string) $agenda->id,
                'title' => $prefix . $agenda->judul,
                'start' => $start,
                'end' => $end,
                'allDay' => (bool) $agenda->is_all_day,
                'backgroundColor' => $warna,
                'borderColor' => $agenda->is_final ? '#166534' : $warna,
                'textColor' => '#ffffff',
                'classNames' => [
                    'cursor-pointer',
                    $agenda->is_final ? 'font-semibold ring-2 ring-emerald-500/50' : '',
                ],
                'extendedProps' => [
                    'id' => $agenda->id,
                    'is_libur' => false,
                    'judul' => $agenda->judul,
                    'deskripsi' => $agenda->deskripsi,
                    'tgl_mulai' => $agenda->tgl_mulai->format('d/m/Y'),
                    'tgl_selesai' => $agenda->tgl_selesai->format('d/m/Y'),
                    'jam_mulai' => $agenda->jam_mulai ? substr($agenda->jam_mulai, 0, 5) : null,
                    'jam_selesai' => $agenda->jam_selesai ? substr($agenda->jam_selesai, 0, 5) : null,
                    'is_all_day' => (bool) $agenda->is_all_day,
                    'kategori' => $agenda->kategori?->nama ?? 'Umum',
                    'kategori_warna' => $warna,
                    'lokasi' => $agenda->lokasi,
                    'koordinator' => $agenda->koordinator,
                    'audiens' => ucfirst($agenda->audiens),
                    'status' => ucfirst($agenda->status),
                    'is_final' => (bool) $agenda->is_final,
                    'finalized_by' => $agenda->validator?->name,
                    'finalized_at' => $agenda->finalized_at?->format('d/m/Y H:i'),
                    'creator' => $agenda->creator?->name ?? 'Sistem',
                    'creator_id' => $agenda->created_by,
                    'parent_id' => $agenda->parent_id,
                    'has_recurrence' => $agenda->recurrence_type !== 'none',
                ],
            ];
        }

        // Ambil Hari Libur (overlapping query)
        $liburs = KalenderLibur::where('tgl_mulai', '<=', $endDate)
            ->where('tgl_selesai', '>=', $startDate)
            ->get();

        foreach ($liburs as $libur) {
            $events[] = [
                'id' => 'libur_' . $libur->id,
                'title' => '🏖 ' . $libur->nama,
                'start' => $libur->tgl_mulai->format('Y-m-d'),
                'end' => $libur->tgl_selesai->copy()->addDay()->format('Y-m-d'),
                'allDay' => true,
                'backgroundColor' => '#dc2626',
                'borderColor' => '#b91c1c',
                'textColor' => '#ffffff',
                'classNames' => ['fc-holiday-badge font-medium shadow-xs'],
                'extendedProps' => [
                    'id' => $libur->id,
                    'is_libur' => true,
                    'judul' => $libur->nama,
                    'jenis' => ucfirst(str_replace('_', ' ', $libur->jenis)),
                    'keterangan' => $libur->keterangan,
                    'tgl_mulai' => $libur->tgl_mulai->format('d/m/Y'),
                    'tgl_selesai' => $libur->tgl_selesai->format('d/m/Y'),
                ],
            ];
        }

        return $events;
    }

    /**
     * Generate anak kejadian berulang (recurrence)
     */
    public function generateRecurrences(KalenderAgenda $parent): void
    {
        if ($parent->recurrence_type === 'none' || ! $parent->recurrence_until) {
            return;
        }

        // Hapus anak lama jika ada pembaruan rangkaian
        $parent->children()->delete();

        $durationDays = $parent->tgl_mulai->diffInDays($parent->tgl_selesai);
        $currentStart = $parent->tgl_mulai->copy();
        $until = Carbon::parse($parent->recurrence_until);

        $limitIterations = 120; // Batasan aman prevent infinite loop
        $counter = 0;

        while ($counter < $limitIterations) {
            $counter++;

            if ($parent->recurrence_type === 'daily') {
                $currentStart->addDay();
            } elseif ($parent->recurrence_type === 'weekly') {
                $currentStart->addWeek();
            } elseif ($parent->recurrence_type === 'monthly') {
                $currentStart->addMonth();
            } else {
                break;
            }

            if ($currentStart->greaterThan($until)) {
                break;
            }

            $currentEnd = $currentStart->copy()->addDays($durationDays);

            KalenderAgenda::create([
                'parent_id' => $parent->id,
                'tahun_ajaran_id' => $parent->tahun_ajaran_id,
                'semester_id' => $parent->semester_id,
                'kategori_id' => $parent->kategori_id,
                'created_by' => $parent->created_by,
                'finalized_by' => $parent->finalized_by,
                'judul' => $parent->judul,
                'deskripsi' => $parent->deskripsi,
                'tgl_mulai' => $currentStart->toDateString(),
                'tgl_selesai' => $currentEnd->toDateString(),
                'jam_mulai' => $parent->jam_mulai,
                'jam_selesai' => $parent->jam_selesai,
                'is_all_day' => $parent->is_all_day,
                'warna' => $parent->warna,
                'lokasi' => $parent->lokasi,
                'koordinator' => $parent->koordinator,
                'audiens' => $parent->audiens,
                'status' => $parent->status,
                'is_final' => $parent->is_final,
                'finalized_at' => $parent->finalized_at,
                'recurrence_type' => 'none',
            ]);
        }
    }

    /**
     * Buat file string iCalendar (.ics) RFC 5545
     */
    public function generateIcs(KalenderAgenda $agenda): string
    {
        $dtStart = $agenda->is_all_day 
            ? $agenda->tgl_mulai->format('Ymd') 
            : $agenda->tgl_mulai->format('Ymd\THis\Z');

        $dtEnd = $agenda->is_all_day 
            ? $agenda->tgl_selesai->copy()->addDay()->format('Ymd') 
            : $agenda->tgl_selesai->format('Ymd\THis\Z');

        $now = now()->format('Ymd\THis\Z');
        $summary = addcslashes($agenda->judul, ",;");
        $description = addcslashes($agenda->deskripsi ?? '', ",;");
        $location = addcslashes($agenda->lokasi ?? '', ",;");

        $ics = "BEGIN:VCALENDAR\r\n";
        $ics .= "VERSION:2.0\r\n";
        $ics .= "PRODID:-//SIRAGA//Kalender Pendidikan//ID\r\n";
        $ics .= "CALSCALE:GREGORIAN\r\n";
        $ics .= "METHOD:PUBLISH\r\n";
        $ics .= "BEGIN:VEVENT\r\n";
        $ics .= "UID:siraga-agenda-{$agenda->id}@wikrama1garut.sch.id\r\n";
        $ics .= "DTSTAMP:{$now}\r\n";
        if ($agenda->is_all_day) {
            $ics .= "DTSTART;VALUE=DATE:{$dtStart}\r\n";
            $ics .= "DTEND;VALUE=DATE:{$dtEnd}\r\n";
        } else {
            $ics .= "DTSTART:{$dtStart}\r\n";
            $ics .= "DTEND:{$dtEnd}\r\n";
        }
        $ics .= "SUMMARY:{$summary}\r\n";
        $ics .= "DESCRIPTION:{$description}\r\n";
        if ($location) {
            $ics .= "LOCATION:{$location}\r\n";
        }
        $ics .= "STATUS:CONFIRMED\r\n";
        $ics .= "END:VEVENT\r\n";
        $ics .= "END:VCALENDAR\r\n";

        return $ics;
    }

    /**
     * Sinkronisasi data Hari Libur Nasional Indonesia
     */
    public function syncHariLiburNasional(?int $year = null): int
    {
        $year = $year ?? (int) date('Y');
        $activeTahunAjaran = TahunAjaran::active()->first();

        $holidays = [];

        // Coba ambil dari public Indonesian holiday API jika bukan di lingkungan testing
        if (! app()->runningUnitTests()) {
            // Tier 1: API Hari Libur Resmi Indonesia (SKB 3 Menteri)
            try {
                $response = Http::timeout(4)->get("https://api-hari-libur.vercel.app/api?year={$year}");
                if ($response->successful()) {
                    $json = $response->json();
                    $items = $json['data'] ?? (is_array($json) ? $json : []);
                    foreach ($items as $item) {
                        $desc = $item['description'] ?? $item['name'] ?? $item['keterangan'] ?? 'Hari Libur';
                        $isCuti = (bool) preg_match('/cuti\s*bersama/i', $desc);
                        $holidays[] = [
                            'nama' => $desc,
                            'tgl_mulai' => $item['date'] ?? $item['tanggal'],
                            'tgl_selesai' => $item['date'] ?? $item['tanggal'],
                            'jenis' => $isCuti ? KalenderLibur::JENIS_CUTI_BERSAMA : KalenderLibur::JENIS_NASIONAL,
                        ];
                    }
                }
            } catch (\Throwable $e) {
                // Lanjut ke fallback berikutnya
            }

            // Tier 2: Nager.Date Public Holidays API untuk Indonesia (ID) jika Tier 1 gagal
            if (empty($holidays)) {
                try {
                    $response = Http::timeout(4)->get("https://date.nager.at/api/v3/PublicHolidays/{$year}/ID");
                    if ($response->successful()) {
                        $items = $response->json();
                        foreach ($items as $item) {
                            $holidays[] = [
                                'nama' => $item['localName'] ?? $item['name'] ?? 'Hari Libur Nasional',
                                'tgl_mulai' => $item['date'],
                                'tgl_selesai' => $item['date'],
                                'jenis' => KalenderLibur::JENIS_NASIONAL,
                            ];
                        }
                    }
                } catch (\Throwable $e) {
                    // Lanjut ke fallback offline
                }
            }
        }

        // Tier 3: Jika seluruh API eksternal tidak terjangkau, gunakan data standar tahun berjalan
        if (empty($holidays)) {
            $holidays = $this->getDefaultNationalHolidays($year);
        }

        $insertedCount = 0;
        foreach ($holidays as $h) {
            $exists = KalenderLibur::where('tgl_mulai', $h['tgl_mulai'])
                ->where(function ($q) use ($h) {
                    $q->where('nama', $h['nama'])
                      ->orWhere('nama', 'like', '%' . substr($h['nama'], 0, 8) . '%');
                })
                ->exists();

            if (! $exists) {
                KalenderLibur::create([
                    'tahun_ajaran_id' => $activeTahunAjaran?->id,
                    'nama' => $h['nama'],
                    'tgl_mulai' => $h['tgl_mulai'],
                    'tgl_selesai' => $h['tgl_selesai'],
                    'jenis' => $h['jenis'] ?? KalenderLibur::JENIS_NASIONAL,
                    'keterangan' => 'Sinkronisasi Hari Libur Resmi',
                    'is_kbm_libur' => true,
                ]);
                $insertedCount++;
            }
        }

        return $insertedCount;
    }

    /**
     * Fallback hari libur nasional Indonesia
     */
    private function getDefaultNationalHolidays(int $year): array
    {
        return [
            ['nama' => 'Tahun Baru Masehi', 'tgl_mulai' => "{$year}-01-01", 'tgl_selesai' => "{$year}-01-01", 'jenis' => KalenderLibur::JENIS_NASIONAL],
            ['nama' => 'Hari Buruh Internasional', 'tgl_mulai' => "{$year}-05-01", 'tgl_selesai' => "{$year}-05-01", 'jenis' => KalenderLibur::JENIS_NASIONAL],
            ['nama' => 'Hari Lahir Pancasila', 'tgl_mulai' => "{$year}-06-01", 'tgl_selesai' => "{$year}-06-01", 'jenis' => KalenderLibur::JENIS_NASIONAL],
            ['nama' => 'Hari Kemerdekaan RI', 'tgl_mulai' => "{$year}-08-17", 'tgl_selesai' => "{$year}-08-17", 'jenis' => KalenderLibur::JENIS_NASIONAL],
            ['nama' => 'Hari Raya Natal', 'tgl_mulai' => "{$year}-12-25", 'tgl_selesai' => "{$year}-12-25", 'jenis' => KalenderLibur::JENIS_NASIONAL],
        ];
    }
}
