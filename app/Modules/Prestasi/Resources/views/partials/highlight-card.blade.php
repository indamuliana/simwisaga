@props(['item'])

<article wire:click="previewItem('{{ $item['type'] }}', {{ $item['id'] }})"
         class="hallmark-highlight-card group"
         tabindex="0"
         role="button"
         aria-label="{{ $item['title'] }}, {{ $item['rank'] }} diraih oleh {{ $item['recipient'] }}">
    
    <!-- Top Visual / Documentation Frame -->
    <div class="hallmark-card-media">
        @if(!empty($item['photo_url']))
            <img src="{{ $item['photo_url'] }}" alt="{{ $item['title'] }}" class="hallmark-card-img" loading="lazy">
            <div class="hallmark-card-overlay"></div>
        @else
            <!-- Editorial Emblem Fallback -->
            <div class="hallmark-card-fallback">
                <svg class="hallmark-trophy-emblem" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.504-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.004-4.5H8.496m8.008 0a6 6 0 00-4.004-5.659 6 6 0 00-4.004 5.66M16.5 10.875A3.375 3.375 0 0019.875 7.5v-1.5A2.25 2.25 0 0017.625 3.75h-1.125m-9 7.125A3.375 3.375 0 014.125 7.5v-1.5A2.25 2.25 0 016.375 3.75h1.125"/>
                </svg>
                <span class="hallmark-card-fallback-rank">{{ $item['rank'] }}</span>
            </div>
            <div class="hallmark-card-overlay"></div>
        @endif

        <!-- Floating Badges: Category & Level -->
        <div class="hallmark-media-badges">
            <span class="hallmark-pill hallmark-pill-category">
                {{ $item['recipient_badge'] }}
            </span>
            <span class="hallmark-pill hallmark-pill-level">
                {{ $item['level'] }}
            </span>
        </div>

        <!-- Rank Banner at Bottom of Media -->
        <div class="hallmark-media-bottom">
            <span class="hallmark-rank-tag">
                <svg class="w-3 h-3 text-amber-950" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                <span>{{ $item['rank'] }}</span>
            </span>
            <span class="hallmark-date-tag">
                {{ $item['date_formatted'] }}
            </span>
        </div>
    </div>

    <!-- Card Content -->
    <div class="hallmark-card-body">
        <div class="space-y-1">
            <h4 class="hallmark-card-title" title="{{ $item['title'] }}">
                {{ $item['title'] }}
            </h4>
            
            <!-- Recipient Name (Bold & Clear) -->
            <div class="hallmark-card-recipient">
                <svg class="w-3.5 h-3.5 text-amber-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147L12 6.377l7.74 3.77m-15.48 0L12 13.917l7.74-3.77m-15.48 0v4.706c0 2.234 3.466 4.047 7.74 4.047s7.74-1.813 7.74-4.047v-4.706M21 9v6"/>
                </svg>
                <span class="truncate" title="{{ $item['recipient'] }}">
                    {{ $item['recipient'] }}
                </span>
            </div>

            <!-- Organizer -->
            <p class="hallmark-card-organizer truncate" title="{{ $item['organizer'] }}">
                {{ $item['organizer'] }}
            </p>
        </div>

        <!-- Footer Action -->
        <div class="hallmark-card-action">
            <span>Detail &amp; Bukti</span>
            <svg class="w-3.5 h-3.5 hallmark-arrow" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
            </svg>
        </div>
    </div>
</article>
