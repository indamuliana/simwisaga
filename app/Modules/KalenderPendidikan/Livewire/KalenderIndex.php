<?php

namespace App\Modules\KalenderPendidikan\Livewire;

use App\Models\User;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\KalenderPendidikan\Models\KalenderAgenda;
use App\Modules\KalenderPendidikan\Models\KalenderKategori;
use App\Modules\KalenderPendidikan\Services\KalenderService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Kalender Pendidikan & Agenda Sekolah')]
class KalenderIndex extends Component
{
    // Filter properties
    public $filterKategori = '';
    public $filterStatus = '';
    public $filterFinal = '';
    public $filterAudiens = '';
    public string $eventsJson = '[]';

    // Modal Form State
    public bool $showFormModal = false;
    public bool $isEditing = false;
    public ?int $agendaId = null;

    // Form fields
    public $created_by = null;
    public $koordinator = '';
    public $judul = '';
    public $deskripsi = '';
    public $tgl_mulai = '';
    public $tgl_selesai = '';
    public bool $is_all_day = true;
    public $jam_mulai = '';
    public $jam_selesai = '';
    public $kategori_id = '';
    public $warna = '';
    public $lokasi = '';
    public $audiens = 'semua';
    public $status = 'internal'; // draft, internal, publik
    public $recurrence_type = 'none'; // none, daily, weekly, monthly
    public $recurrence_until = '';

    // Modal Detail State
    public bool $showDetailModal = false;
    public ?KalenderAgenda $selectedAgenda = null;

    protected function rules(): array
    {
        return [
            'created_by' => 'required|exists:users,id',
            'koordinator' => 'nullable|string|max:255',
            'judul' => 'required|string|max:255',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            'kategori_id' => 'required|exists:kalender_kategoris,id',
            'status' => 'required|in:draft,internal,publik',
            'audiens' => 'required|in:semua,guru,kelas,ortu',
            'jam_mulai' => 'nullable|required_if:is_all_day,false',
            'jam_selesai' => 'nullable|required_if:is_all_day,false',
            'recurrence_type' => 'required|in:none,daily,weekly,monthly',
            'recurrence_until' => 'nullable|required_unless:recurrence_type,none|date|after:tgl_mulai',
        ];
    }

    public function mount(): void
    {
        $defaultKategori = KalenderKategori::active()->first();
        if ($defaultKategori) {
            $this->kategori_id = $defaultKategori->id;
        }
        $this->tgl_mulai = now()->toDateString();
        $this->tgl_selesai = now()->toDateString();
        $this->created_by = Auth::id();
    }

    public function openCreateModal(?string $date = null): void
    {
        $this->resetValidation();
        $this->isEditing = false;
        $this->agendaId = null;

        $targetDate = $date ?: now()->toDateString();
        $this->tgl_mulai = $targetDate;
        $this->tgl_selesai = $targetDate;
        $this->created_by = Auth::id();
        $this->koordinator = '';
        $this->judul = '';
        $this->deskripsi = '';
        $this->is_all_day = true;
        $this->jam_mulai = '07:30';
        $this->jam_selesai = '09:00';
        $this->warna = '';
        $this->lokasi = '';
        $this->audiens = 'semua';
        $this->status = 'internal';
        $this->recurrence_type = 'none';
        $this->recurrence_until = '';

        $defaultKategori = KalenderKategori::active()->first();
        if ($defaultKategori) {
            $this->kategori_id = $defaultKategori->id;
        }

        $this->showDetailModal = false;
        $this->showFormModal = true;
    }

    public function openDetailModal(int $id): void
    {
        $this->selectedAgenda = KalenderAgenda::with(['kategori', 'creator', 'validator', 'parent'])->find($id);
        if ($this->selectedAgenda) {
            $this->showFormModal = false;
            $this->showDetailModal = true;
        }
    }

    public function editSelectedAgenda(): void
    {
        if (! $this->selectedAgenda) return;

        $this->resetValidation();
        $this->isEditing = true;
        $this->agendaId = $this->selectedAgenda->id;

        $this->created_by = $this->selectedAgenda->created_by;
        $this->koordinator = $this->selectedAgenda->koordinator ?? '';
        $this->judul = $this->selectedAgenda->judul;
        $this->deskripsi = $this->selectedAgenda->deskripsi;
        $this->tgl_mulai = $this->selectedAgenda->tgl_mulai->format('Y-m-d');
        $this->tgl_selesai = $this->selectedAgenda->tgl_selesai->format('Y-m-d');
        $this->is_all_day = (bool) $this->selectedAgenda->is_all_day;
        $this->jam_mulai = $this->selectedAgenda->jam_mulai ? substr($this->selectedAgenda->jam_mulai, 0, 5) : '';
        $this->jam_selesai = $this->selectedAgenda->jam_selesai ? substr($this->selectedAgenda->jam_selesai, 0, 5) : '';
        $this->kategori_id = $this->selectedAgenda->kategori_id;
        $this->warna = $this->selectedAgenda->warna ?? '';
        $this->lokasi = $this->selectedAgenda->lokasi ?? '';
        $this->audiens = $this->selectedAgenda->audiens ?? 'semua';
        $this->status = $this->selectedAgenda->status ?? 'internal';
        $this->recurrence_type = $this->selectedAgenda->recurrence_type ?? 'none';
        $this->recurrence_until = $this->selectedAgenda->recurrence_until ? $this->selectedAgenda->recurrence_until->format('Y-m-d') : '';

        $this->showDetailModal = false;
        $this->showFormModal = true;
    }

    public function saveAgenda(KalenderService $service): void
    {
        $this->validate();

        $activeTa = TahunAjaran::active()->first();
        $activeSm = Semester::active()->first();
        $user = Auth::user();

        if ($this->isEditing && $this->agendaId) {
            $agenda = KalenderAgenda::findOrFail($this->agendaId);

            // Validasi hak edit (pemilik agenda atau pimpinan)
            if ($agenda->created_by !== $user->id && ! KalenderAgenda::canManageFinalFlag($user)) {
                session()->flash('error', 'Anda tidak memiliki hak akses untuk mengubah agenda ini.');
                $this->showFormModal = false;
                return;
            }

            $dateOrTimeChanged = ($agenda->tgl_mulai->format('Y-m-d') !== $this->tgl_mulai)
                || ($agenda->tgl_selesai->format('Y-m-d') !== $this->tgl_selesai)
                || ($agenda->jam_mulai !== $this->jam_mulai)
                || ($agenda->jam_selesai !== $this->jam_selesai);

            $data = [
                'kategori_id' => $this->kategori_id,
                'created_by' => $this->created_by ?: $agenda->created_by,
                'koordinator' => $this->koordinator ?: null,
                'judul' => $this->judul,
                'deskripsi' => $this->deskripsi,
                'tgl_mulai' => $this->tgl_mulai,
                'tgl_selesai' => $this->tgl_selesai,
                'is_all_day' => $this->is_all_day,
                'jam_mulai' => $this->is_all_day ? null : $this->jam_mulai,
                'jam_selesai' => $this->is_all_day ? null : $this->jam_selesai,
                'warna' => $this->warna ?: null,
                'lokasi' => $this->lokasi ?: null,
                'audiens' => $this->audiens,
                'status' => $this->status,
                'recurrence_type' => $this->recurrence_type,
                'recurrence_until' => $this->recurrence_type !== 'none' ? $this->recurrence_until : null,
            ];

            // Jika tanggal/waktu diubah dan bukan evaluator/pimpinan, reset status Final
            if ($dateOrTimeChanged && $agenda->is_final && ! KalenderAgenda::canManageFinalFlag($user)) {
                $data['is_final'] = false;
                $data['finalized_by'] = null;
                $data['finalized_at'] = null;
                session()->flash('warning', 'Jadwal diubah sehingga status "Final" dicabut untuk verifikasi ulang.');
            }

            $agenda->update($data);

            if ($this->recurrence_type !== 'none' && $this->recurrence_until) {
                $service->generateRecurrences($agenda);
            }

            session()->flash('status', 'Agenda berhasil diperbarui.');
        } else {
            $agenda = KalenderAgenda::create([
                'tahun_ajaran_id' => $activeTa?->id,
                'semester_id' => $activeSm?->id,
                'kategori_id' => $this->kategori_id,
                'created_by' => $this->created_by ?: $user->id,
                'koordinator' => $this->koordinator ?: null,
                'judul' => $this->judul,
                'deskripsi' => $this->deskripsi,
                'tgl_mulai' => $this->tgl_mulai,
                'tgl_selesai' => $this->tgl_selesai,
                'is_all_day' => $this->is_all_day,
                'jam_mulai' => $this->is_all_day ? null : $this->jam_mulai,
                'jam_selesai' => $this->is_all_day ? null : $this->jam_selesai,
                'warna' => $this->warna ?: null,
                'lokasi' => $this->lokasi ?: null,
                'audiens' => $this->audiens,
                'status' => $this->status,
                'is_final' => false,
                'recurrence_type' => $this->recurrence_type,
                'recurrence_until' => $this->recurrence_type !== 'none' ? $this->recurrence_until : null,
            ]);

            if ($this->recurrence_type !== 'none' && $this->recurrence_until) {
                $service->generateRecurrences($agenda);
            }

            session()->flash('status', 'Agenda baru berhasil ditambahkan.');
        }

        $this->showFormModal = false;
    }

    public function toggleFlagFinal(int $id): void
    {
        $user = Auth::user();
        if (! KalenderAgenda::canManageFinalFlag($user)) {
            session()->flash('error', 'Anda tidak memiliki hak akses untuk mengubah flag Final.');
            return;
        }

        $agenda = KalenderAgenda::findOrFail($id);
        $newFinal = ! $agenda->is_final;

        $agenda->update([
            'is_final' => $newFinal,
            'finalized_by' => $newFinal ? $user->id : null,
            'finalized_at' => $newFinal ? now() : null,
        ]);

        // Update anak perulangan juga jika ada
        if ($agenda->children()->exists()) {
            $agenda->children()->update([
                'is_final' => $newFinal,
                'finalized_by' => $newFinal ? $user->id : null,
                'finalized_at' => $newFinal ? now() : null,
            ]);
        }

        $this->selectedAgenda = $agenda->fresh(['kategori', 'creator', 'validator']);

        session()->flash('status', $newFinal ? 'Agenda berhasil ditandai sebagai FINAL.' : 'Flag FINAL berhasil dicabut.');
    }

    public function deleteAgenda(int $id, bool $deleteSeries = false): void
    {
        $agenda = KalenderAgenda::findOrFail($id);
        $user = Auth::user();

        // Validasi hak hapus (pemilik atau pimpinan)
        if ($agenda->created_by !== $user->id && ! KalenderAgenda::canManageFinalFlag($user)) {
            session()->flash('error', 'Anda tidak berhak menghapus agenda ini.');
            return;
        }

        if ($deleteSeries && $agenda->parent_id) {
            // Hapus seluruh rangkaian melalui induk
            $parent = KalenderAgenda::find($agenda->parent_id);
            if ($parent) {
                $parent->children()->delete();
                $parent->delete();
            }
        } elseif ($deleteSeries && $agenda->children()->exists()) {
            $agenda->children()->delete();
            $agenda->delete();
        } else {
            $agenda->delete();
        }

        $this->showDetailModal = false;
        $this->selectedAgenda = null;

        session()->flash('status', 'Agenda berhasil dihapus.');
    }

    public function render(KalenderService $service)
    {
        $user = Auth::user();
        $canManageFlag = KalenderAgenda::canManageFinalFlag($user);

        // Ambil rentang default 6 bulan sekeliling hari ini
        $startRange = now()->subMonths(3)->startOfMonth()->toDateString();
        $endRange = now()->addMonths(6)->endOfMonth()->toDateString();

        $filters = [
            'kategori_id' => $this->filterKategori,
            'status' => $this->filterStatus,
            'is_final' => $this->filterFinal,
            'audiens' => $this->filterAudiens,
        ];

        $events = $service->getCalendarEvents($startRange, $endRange, $user, $filters);
        $kategoris = KalenderKategori::active()->get();

        // Query agenda terdekat tersortir tanggal
        $agendaListQuery = KalenderAgenda::with(['kategori', 'creator', 'validator'])
            ->visibleForUser($user)
            ->where('tgl_selesai', '>=', now()->toDateString())
            ->orderBy('tgl_mulai')
            ->orderBy('jam_mulai');

        if (! empty($this->filterKategori)) {
            $agendaListQuery->where('kategori_id', $this->filterKategori);
        }
        if (! empty($this->filterStatus)) {
            $agendaListQuery->where('status', $this->filterStatus);
        }
        if (isset($this->filterFinal) && $this->filterFinal !== '') {
            $agendaListQuery->where('is_final', (bool) $this->filterFinal);
        }
        if (! empty($this->filterAudiens)) {
            $agendaListQuery->where('audiens', $this->filterAudiens);
        }

        $usersList = User::whereHas('roles', function ($q) {
            $q->whereNotIn('name', ['Siswa', 'Orang Tua']);
        })->orderBy('name')->get(['id', 'name']);

        if ($usersList->isEmpty()) {
            $usersList = User::orderBy('name')->take(50)->get(['id', 'name']);
        }

        if ($user && ! $usersList->contains('id', $user->id)) {
            $usersList->prepend($user);
        }

        $upcomingAgendas = $agendaListQuery->limit(25)->get();
        $this->eventsJson = json_encode($events);

        return view('kalenderpendidikan::livewire.kalender-index', [
            'eventsJson' => $this->eventsJson,
            'kategoris' => $kategoris,
            'usersList' => $usersList,
            'canManageFlag' => $canManageFlag,
            'upcomingAgendas' => $upcomingAgendas,
        ]);
    }
}
