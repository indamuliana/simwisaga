<div class="card p-5 bg-surface border border-border rounded-xl shadow-xs">
    <div class="flex items-center justify-between pb-3 border-b border-border">
        <h3 class="text-base font-bold text-text-main flex items-center gap-2">
            <svg class="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            Agenda Kegiatan Terdekat
        </h3>
        @auth
        <a href="{{ route('kalender.index') }}" class="text-xs font-semibold text-primary hover:underline flex items-center gap-1">
            Lihat Kalender Penuh &rarr;
        </a>
        @endauth
    </div>

    <div class="mt-4 space-y-3">
        @forelse($events as $event)
        <div class="flex items-start gap-3 p-3 rounded-lg border border-border bg-surface-hover/30 hover:bg-surface-hover/70 transition-colors">
            {{-- Tanggal Box --}}
            <div class="w-12 h-12 flex-shrink-0 rounded-lg flex flex-col items-center justify-center text-white shadow-xs" style="background-color: {{ $event->display_warna }}">
                <span class="text-[10px] uppercase font-bold leading-none">{{ $event->tgl_mulai->translatedFormat('M') }}</span>
                <span class="text-base font-extrabold leading-none mt-0.5">{{ $event->tgl_mulai->format('d') }}</span>
            </div>

            {{-- Info Kegiatan --}}
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full text-white" style="background-color: {{ $event->display_warna }}">
                        {{ $event->kategori?->nama ?? 'Kegiatan' }}
                    </span>
                    @if($event->is_final)
                    <span class="text-[10px] font-semibold text-emerald-600 bg-emerald-50 dark:bg-emerald-950 px-1.5 py-0.5 rounded border border-emerald-300">
                        FINAL
                    </span>
                    @endif
                </div>

                <h4 class="text-sm font-semibold text-text-main truncate mt-1">
                    {{ $event->judul }}
                </h4>

                <div class="flex items-center gap-3 text-xs text-text-muted mt-1 flex-wrap">
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        @if($event->is_all_day)
                            Sepanjang Hari
                        @else
                            {{ substr($event->jam_mulai, 0, 5) }} &ndash; {{ substr($event->jam_selesai, 0, 5) }} WIB
                        @endif
                    </span>

                    @if($event->lokasi)
                    <span class="flex items-center gap-1 truncate">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /></svg>
                        {{ $event->lokasi }}
                    </span>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="py-6 text-center text-text-muted text-xs">
            Tidak ada agenda kegiatan terdekat dalam waktu dekat.
        </div>
        @endforelse
    </div>
</div>
