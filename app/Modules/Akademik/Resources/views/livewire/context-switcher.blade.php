<div class="flex items-center gap-2">
 @if ($current)
    <div class="hidden items-center gap-1.5 sm:flex">
        <span class="inline-flex items-center gap-1.5 rounded-lg bg-primary-soft px-2.5 py-1 text-xs font-semibold text-primary border border-primary/20 shadow-xs">
            <svg class="h-3.5 w-3.5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span>T.A. {{ $current->tahunAjaran?->nama }} - {{ ucfirst($current->semester) }}</span>
            @if ($current->isLockedForNilai() || $current->isLockedForAbsensi())
                <span title="Periode ini dikunci untuk penginputan" class="text-amber-500">&#128274;</span>
            @endif
        </span>
    </div>

    <select wire:model.live="selectedSemesterId"
            wire:change="switchSemester"
            class="max-w-[130px] sm:max-w-[200px] md:max-w-xs truncate rounded-lg border border-border bg-surface px-2.5 py-1 text-xs text-text-main shadow-xs focus:border-primary focus:outline-none">
        @foreach ($semesters as $sem)
            <option value="{{ $sem->id }}" @selected($sem->id == $current->id)>
                {{ $sem->tahunAjaran?->nama }} ({{ ucfirst($sem->semester) }}) {{ $sem->is_active ? '★ Aktif' : '' }}
            </option>
        @endforeach
    </select>
    @else
    <span class="text-xs text-text-muted">T.A. belum diatur</span>
    @endif
</div>
