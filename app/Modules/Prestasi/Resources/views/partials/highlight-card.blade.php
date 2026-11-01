@props(['item', 'isGrid' => false])

<div wire:click="previewItem('{{ $item['type'] }}', {{ $item['id'] }})"
     class="{{ $isGrid ? 'w-full' : 'w-[280px] sm:w-[320px] shrink-0' }} rounded-3xl bg-white border border-slate-200/90 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 cursor-pointer overflow-hidden flex flex-col justify-between group relative">
    
    <!-- Top Image / Documentation Banner -->
    <div class="relative h-44 w-full bg-slate-900 overflow-hidden">
        @if(!empty($item['photo_url']))
            <img src="{{ $item['photo_url'] }}" alt="{{ $item['title'] }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-900/20 to-transparent"></div>
        @else
            <!-- Fallback gradient banner with trophy -->
            <div class="w-full h-full flex flex-col items-center justify-center p-4 text-center"
                 style="background: linear-gradient(135deg, #001733 0%, #00305F 100%);">
                <span class="text-4xl drop-shadow-md mb-1">🏆</span>
                <span class="text-[10px] font-bold text-amber-300 uppercase tracking-widest">
                    {{ $item['rank'] }}
                </span>
            </div>
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent"></div>
        @endif

        <!-- Floating Badges on Image -->
        <div class="absolute top-3 left-3 right-3 flex items-center justify-between gap-1.5 z-10">
            <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider backdrop-blur-md shadow-sm {{ $item['recipient_badge_color'] }}">
                {{ $item['recipient_badge'] }}
            </span>
            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold text-white bg-slate-900/70 border border-white/20 backdrop-blur-md">
                {{ $item['level'] }}
            </span>
        </div>

        <!-- Rank Pill at bottom of image -->
        <div class="absolute bottom-2.5 left-3 right-3 z-10 flex items-center justify-between">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-400 text-amber-950 text-xs font-black shadow-md">
                <span>⭐</span>
                <span>{{ $item['rank'] }}</span>
            </span>
            <span class="text-[10px] font-semibold text-blue-100/90 bg-black/40 px-2 py-0.5 rounded-md backdrop-blur-xs">
                {{ $item['date_formatted'] }}
            </span>
        </div>
    </div>

    <!-- Body Information -->
    <div class="p-4 sm:p-5 flex-1 flex flex-col justify-between gap-3">
        <div class="space-y-1.5">
            <h4 class="text-sm font-extrabold text-slate-800 line-clamp-2 leading-snug group-hover:text-[#00519E] transition-colors"
                title="{{ $item['title'] }}">
                {{ $item['title'] }}
            </h4>
            
            <div class="flex items-center gap-1.5 text-xs font-semibold text-slate-600">
                <span class="text-amber-500">👤</span>
                <span class="line-clamp-1" title="{{ $item['recipient'] }}">{{ $item['recipient'] }}</span>
            </div>

            <p class="text-[11px] text-slate-400 line-clamp-1">
                {{ $item['organizer'] }}
            </p>
        </div>

        <!-- Footer / CTA inside card -->
        <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-[#00519E] group-hover:text-amber-600 transition-colors">
            <span class="text-[11px]">Lihat Detail & Berkas</span>
            <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
        </div>
    </div>
</div>
