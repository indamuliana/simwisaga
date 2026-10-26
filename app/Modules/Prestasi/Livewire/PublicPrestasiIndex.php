<?php

namespace App\Modules\Prestasi\Livewire;

use App\Modules\Prestasi\Models\Prestasi;
use App\Modules\Prestasi\Models\PrestasiGuru;
use App\Modules\Prestasi\Models\PrestasiSekolah;
use App\Modules\Sistem\Models\School;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class PublicPrestasiIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $tab = 'semua'; // 'semua', 'sekolah', 'guru', 'siswa'

    #[Url]
    public string $tingkat = ''; // '', 'internasional', 'nasional', 'provinsi', 'kabupaten_kota'

    #[Url]
    public string $tahun = ''; // '', '2026', '2025', etc.

    #[Url]
    public string $search = '';

    public ?array $modalData = null;

    public function mount(): void
    {
        $uri = request()->path();
        if (str_ends_with($uri, 'prestasi/sekolah')) {
            $this->tab = 'sekolah';
        } elseif (str_ends_with($uri, 'prestasi/guru')) {
            $this->tab = 'guru';
        } elseif (str_ends_with($uri, 'prestasi/siswa')) {
            $this->tab = 'siswa';
        }
    }

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
        $this->resetPage();

        $targetUrl = match($tab) {
            'sekolah' => url('/prestasi/sekolah'),
            'guru'    => url('/prestasi/guru'),
            'siswa'   => url('/prestasi/siswa'),
            default   => url('/prestasi'),
        };
        $this->dispatch('update-url', url: $targetUrl);
    }

    public function updatedTingkat(): void
    {
        $this->resetPage();
    }

    public function updatedTahun(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->tingkat = '';
        $this->tahun = '';
        $this->search = '';
        $this->resetPage();
    }

    public function previewItem(string $type, int $id): void
    {
        if ($type === 'sekolah') {
            $item = PrestasiSekolah::find($id);
            if (!$item) return;
            $this->modalData = [
                'type' => 'Sekolah',
                'title' => $item->nama_prestasi,
                'rank' => $item->capaian,
                'level' => $item->tingkat_label ?? ucfirst($item->tingkat),
                'organizer' => $item->penyelenggara_nama,
                'date' => $item->tanggal_perolehan ? $item->tanggal_perolehan->translatedFormat('d F Y') : '-',
                'description' => $item->deskripsi,
                'recipient' => School::current()->nama ?? 'SMK Wikrama 1 Garut',
                'photo_url' => $item->foto_file_path ? Storage::url($item->foto_file_path) : null,
                'cert_url' => $item->sertifikat_file_path ? Storage::url($item->sertifikat_file_path) : null,
            ];
        } elseif ($type === 'guru') {
            $item = PrestasiGuru::with('pegawais')->find($id);
            if (!$item) return;
            $this->modalData = [
                'type' => 'Guru & Tendik',
                'title' => $item->nama_prestasi,
                'rank' => $item->peringkat,
                'level' => $item->tingkat_label ?? ucfirst($item->tingkat),
                'organizer' => $item->penyelenggara_nama,
                'date' => $item->tanggal_perolehan ? $item->tanggal_perolehan->translatedFormat('d F Y') : '-',
                'description' => $item->deskripsi,
                'recipient' => $item->daftar_guru_nama ?: ($item->nama_tim ?: 'Pendidik Wikrama'),
                'photo_url' => $item->foto_file_path ? Storage::url($item->foto_file_path) : null,
                'cert_url' => $item->sertifikat_file_path ? Storage::url($item->sertifikat_file_path) : null,
            ];
        } else {
            $item = Prestasi::with('siswas')->where('status_verifikasi', 'approved')->find($id);
            if (!$item) return;
            $namaSiswa = $item->siswas->isNotEmpty()
                ? $item->siswas->pluck('nama_lengkap')->implode(', ')
                : ($item->nama_tim ?: 'Siswa Wikrama');

            $this->modalData = [
                'type' => 'Siswa & Santri',
                'title' => $item->nama_prestasi,
                'rank' => $item->peringkat,
                'level' => ucfirst(str_replace('_', ' ', $item->tingkat ?? '-')),
                'organizer' => $item->penyelenggara_nama,
                'date' => $item->tanggal_perolehan ? $item->tanggal_perolehan->translatedFormat('d F Y') : '-',
                'description' => $item->deskripsi,
                'recipient' => $namaSiswa,
                'photo_url' => $item->foto_file_path ? Storage::url($item->foto_file_path) : null,
                'cert_url' => $item->sertifikat_file_path ? Storage::url($item->sertifikat_file_path) : null,
            ];
        }
    }

    public function closeModal(): void
    {
        $this->modalData = null;
    }

    #[Layout('components.layouts.blank')]
    public function render()
    {
        $sekolah = School::current();

        // 1. Metric Counters
        $countSekolah = PrestasiSekolah::count();
        $countGuru = PrestasiGuru::count();
        $countSiswa = Prestasi::where('status_verifikasi', 'approved')->count();
        $countTotal = $countSekolah + $countGuru + $countSiswa;

        $stats = [
            'total' => $countTotal,
            'sekolah' => $countSekolah,
            'guru' => $countGuru,
            'siswa' => $countSiswa,
            'internasional' => PrestasiSekolah::where('tingkat', 'internasional')->count() +
                               PrestasiGuru::where('tingkat', 'internasional')->count() +
                               Prestasi::where('status_verifikasi', 'approved')->where('tingkat', 'internasional')->count(),
            'nasional' => PrestasiSekolah::where('tingkat', 'nasional')->count() +
                          PrestasiGuru::where('tingkat', 'nasional')->count() +
                          Prestasi::where('status_verifikasi', 'approved')->where('tingkat', 'nasional')->count(),
        ];

        // 2. Extract Available Years
        $years = collect();
        $years = $years->merge(PrestasiSekolah::whereNotNull('tanggal_perolehan')->pluck('tanggal_perolehan')->map(fn($d) => $d->format('Y')));
        $years = $years->merge(PrestasiGuru::whereNotNull('tanggal_perolehan')->pluck('tanggal_perolehan')->map(fn($d) => $d->format('Y')));
        $years = $years->merge(Prestasi::where('status_verifikasi', 'approved')->whereNotNull('tanggal_perolehan')->pluck('tanggal_perolehan')->map(fn($d) => $d->format('Y')));
        $availableYears = $years->unique()->sortDesc()->values();

        // 3. Normalized Combined Query
        $items = $this->fetchNormalizedData();

        $pageTitle = match($this->tab) {
            'sekolah' => 'Prestasi Institusi & Akreditasi Unggul',
            'guru'    => 'Prestasi Guru & Tenaga Kependidikan',
            'siswa'   => 'Prestasi Juara Siswa & Santri',
            default   => 'Etalase Prestasi & Capaian Resmi',
        };

        return view('prestasi::livewire.public-prestasi-index', compact(
            'sekolah',
            'stats',
            'availableYears',
            'items'
        ))->layout('components.layouts.blank', [
            'title' => $pageTitle
        ]);
    }

    protected function fetchNormalizedData(): LengthAwarePaginator
    {
        $collection = collect();

        // Fetch Sekolah
        if ($this->tab === 'semua' || $this->tab === 'sekolah') {
            $q = PrestasiSekolah::query();
            if ($this->tingkat) $q->where('tingkat', $this->tingkat);
            if ($this->tahun) $q->whereYear('tanggal_perolehan', $this->tahun);
            if ($this->search) {
                $q->where(fn($sub) => $sub->where('nama_prestasi', 'like', "%{$this->search}%")
                    ->orWhere('penyelenggara_nama', 'like', "%{$this->search}%")
                    ->orWhere('capaian', 'like', "%{$this->search}%"));
            }
            foreach ($q->get() as $item) {
                $collection->push([
                    'type' => 'sekolah',
                    'id' => $item->id,
                    'title' => $item->nama_prestasi,
                    'rank' => $item->capaian,
                    'level' => $item->tingkat_label ?? ucfirst($item->tingkat),
                    'organizer' => $item->penyelenggara_nama,
                    'date' => $item->tanggal_perolehan,
                    'date_formatted' => $item->tanggal_perolehan ? $item->tanggal_perolehan->translatedFormat('d M Y') : '-',
                    'recipient' => School::current()->nama ?? 'SMK Wikrama 1 Garut',
                    'recipient_badge' => 'Sekolah',
                    'recipient_badge_color' => 'bg-purple-50 text-purple-700 border-purple-200',
                    'photo_url' => $item->foto_file_path ? Storage::url($item->foto_file_path) : null,
                    'cert_url' => $item->sertifikat_file_path ? Storage::url($item->sertifikat_file_path) : null,
                    'description' => $item->deskripsi,
                    'theme' => 'purple',
                ]);
            }
        }

        // Fetch Guru
        if ($this->tab === 'semua' || $this->tab === 'guru') {
            $q = PrestasiGuru::with('pegawais');
            if ($this->tingkat) $q->where('tingkat', $this->tingkat);
            if ($this->tahun) $q->whereYear('tanggal_perolehan', $this->tahun);
            if ($this->search) {
                $q->where(fn($sub) => $sub->where('nama_prestasi', 'like', "%{$this->search}%")
                    ->orWhere('penyelenggara_nama', 'like', "%{$this->search}%")
                    ->orWhere('peringkat', 'like', "%{$this->search}%"));
            }
            foreach ($q->get() as $item) {
                $collection->push([
                    'type' => 'guru',
                    'id' => $item->id,
                    'title' => $item->nama_prestasi,
                    'rank' => $item->peringkat,
                    'level' => $item->tingkat_label ?? ucfirst($item->tingkat),
                    'organizer' => $item->penyelenggara_nama,
                    'date' => $item->tanggal_perolehan,
                    'date_formatted' => $item->tanggal_perolehan ? $item->tanggal_perolehan->translatedFormat('d M Y') : '-',
                    'recipient' => $item->daftar_guru_nama ?: ($item->nama_tim ?: 'Pendidik Wikrama'),
                    'recipient_badge' => 'Guru & Tendik',
                    'recipient_badge_color' => 'bg-sky-50 text-sky-700 border-sky-200',
                    'photo_url' => $item->foto_file_path ? Storage::url($item->foto_file_path) : null,
                    'cert_url' => $item->sertifikat_file_path ? Storage::url($item->sertifikat_file_path) : null,
                    'description' => $item->deskripsi,
                    'theme' => 'sky',
                ]);
            }
        }

        // Fetch Siswa (Approved only)
        if ($this->tab === 'semua' || $this->tab === 'siswa') {
            $q = Prestasi::with('siswas')->where('status_verifikasi', 'approved');
            if ($this->tingkat) $q->where('tingkat', $this->tingkat);
            if ($this->tahun) $q->whereYear('tanggal_perolehan', $this->tahun);
            if ($this->search) {
                $q->where(fn($sub) => $sub->where('nama_prestasi', 'like', "%{$this->search}%")
                    ->orWhere('penyelenggara_nama', 'like', "%{$this->search}%")
                    ->orWhere('peringkat', 'like', "%{$this->search}%"));
            }
            foreach ($q->get() as $item) {
                $namaSiswa = $item->siswas->isNotEmpty()
                    ? $item->siswas->pluck('nama_lengkap')->implode(', ')
                    : ($item->nama_tim ?: 'Siswa Wikrama');

                $collection->push([
                    'type' => 'siswa',
                    'id' => $item->id,
                    'title' => $item->nama_prestasi,
                    'rank' => $item->peringkat,
                    'level' => ucfirst(str_replace('_', ' ', $item->tingkat ?? '-')),
                    'organizer' => $item->penyelenggara_nama,
                    'date' => $item->tanggal_perolehan,
                    'date_formatted' => $item->tanggal_perolehan ? $item->tanggal_perolehan->translatedFormat('d M Y') : '-',
                    'recipient' => $namaSiswa,
                    'recipient_badge' => 'Siswa & Santri',
                    'recipient_badge_color' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                    'photo_url' => $item->foto_file_path ? Storage::url($item->foto_file_path) : null,
                    'cert_url' => $item->sertifikat_file_path ? Storage::url($item->sertifikat_file_path) : null,
                    'description' => $item->deskripsi,
                    'theme' => 'emerald',
                ]);
            }
        }

        // Sort descending by date perolehan
        $sorted = $collection->sortByDesc(function ($item) {
            return $item['date'] ? $item['date']->timestamp : 0;
        })->values();

        // Paginate manually
        $perPage = 12;
        $page = $this->getPage();
        $paginated = $sorted->slice(($page - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator(
            $paginated,
            $sorted->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }
}
