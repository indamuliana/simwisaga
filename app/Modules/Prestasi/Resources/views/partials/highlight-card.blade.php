@props(['item'])

<div wire:click="previewItem('{{ $item['type'] }}', {{ $item['id'] }})"
     class="w-full sm:w-[260px] max-w-[280px] rounded-2xl bg-white border border-slate-200/90 shadow-xs hover:shadow-lg hover:-translate-y-1 transition-all duration-300 cursor-pointer overflow-hidden flex flex-col justify-between group relative shrink-0">
    
    <!-- Top Image / Documentation Banner (Compact) -->
    <div class="relative h-28 sm:h-32 w-full bg-slate-900 overflow-hidden">
        @if(!empty($item['photo_url']))
            <img src="{{ $item['photo_url'] }}" alt="{{ $item['title'] }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-900/20 to-transparent"></div>
        @else
            <!-- Fallback gradient banner with trophy -->
            <div class="w-full h-full flex flex-col items-center justify-center p-3 text-center"
                 style="background: linear-gradient(135deg, #001733 0%, #00305F 100%);">
                <span class="text-3xl drop-shadow-xs mb-0.5">🏆</span>
                <span class="text-[9px] font-black text-amber-300 uppercase tracking-widest">
                    {{ $item['rank'] }}
                </span>
            </div>
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent"></div>
        @endif

        <!-- Floating Badges on Image -->
        <div class="absolute top-2 left-2 right-2 flex items-center justify-between gap-1 z-10">
            <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider backdrop-blur-md shadow-xs {{ $item['recipient_badge_color'] }}">
                {{ $item['recipient_badge'] }}
            </span>
            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold text-white bg-slate-900/75 border border-white/20 backdrop-blur-md">
                {{ $item['level'] }}
            </span>
        </div>

        <!-- Rank Pill at bottom of image -->
        <div class="absolute bottom-2 left-2 right-2 z-10 flex items-center justify-between">
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-400 text-amber-950 text-[10px] font-black shadow-xs">
                <span>⭐</span>
                <span>{{ $item['rank'] }}</span>
            </span>
            <span class="text-[9px] font-medium text-blue-100/90 bg-black/50 px-1.5 py-0.5 rounded backdrop-blur-xs">
                {{ $item['date_formatted'] }}
            </span>
        </div>
    </div>

    <!-- Body Information (Compact & Focused) -->
    <div class="p-3 sm:p-3.5 flex-1 flex flex-col justify-between gap-2.5">
        <div class="space-y-1">
            <!-- Judul Prestasi -->
            <h4 class="text-xs sm:text-[13px] font-bold text-slate-800 line-clamp-2 leading-snug group-hover:text-[#00519E] transition-colors"
                title="{{ $item['title'] }}">
                {{ $item['title'] }}
            </h4>
            
            <!-- Nama Peraih / Siswa (Bold & Clear) -->
            <div class="flex items-center gap-1.5 text-[11px] font-bold text-slate-700 pt-0.5">
                <span class="text-amber-500 text-xs">🎓</span>
                <span class="line-clamp-1 text-slate-900" title="{{ $item['recipient'] }}">
                    {{ $item['recipient'] }}
                </span>
            </div>

            <!-- Penyelenggara -->
            <p class="text-[10px] text-slate-400 line-clamp-1" title="{{ $item['organizer'] }}">
                {{ $item['organizer'] }}
            </p>
        </div>

        <!-- Footer / CTA -->
        <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[10px] font-bold text-[#00519E] group-hover:text-amber-600 transition-colors">
            <span>Detail &amp; Bukti</span>
            <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
        </div>
    </div>
</div>
