<div class="organigram-branch flex flex-col items-center">
    {{-- Node Card --}}
    <div class="organigram-card bg-surface hover:bg-surface-hover/80 border border-border rounded-xl p-3.5 shadow-xs hover:shadow-md hover:border-primary/50 transition-all text-center min-w-[200px] max-w-[240px] z-10 relative">
        <div class="flex items-center justify-between gap-1 mb-1.5">
            @if(!empty($node['kode']))
                <span class="px-1.5 py-0.5 rounded text-[9px] font-mono font-bold bg-surface-hover text-text-muted border border-border">
                    {{ $node['kode'] }}
                </span>
            @else
                <span></span>
            @endif
            @if(!empty($node['bobot_jam']) && $node['bobot_jam'] > 0)
                <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-900">
                    ⚡ {{ number_format($node['bobot_jam'], 1) }}J
                </span>
            @endif
        </div>

        <h4 class="text-xs font-bold text-text-main line-clamp-2" title="{{ $node['nama'] }}">
            {{ $node['nama'] }}
        </h4>

        {{-- Pejabat Info --}}
        <div class="mt-2.5 pt-2 border-t border-border space-y-1.5">
            @forelse($node['pejabats'] as $p)
                <div class="flex items-center gap-2 text-left">
                    <div class="w-6 h-6 rounded-full {{ $p['is_internal'] ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/60 dark:text-blue-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/60 dark:text-amber-300' }} flex items-center justify-center font-bold text-[10px] shrink-0">
                        {{ substr($p['nama'], 0, 1) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="text-[11px] font-semibold text-text-main truncate" title="{{ $p['nama'] }}">
                            {{ $p['nama'] }}
                        </div>
                        @if(!empty($p['nip']))
                            <div class="text-[9px] text-text-muted truncate">
                                {{ $p['nip'] }}
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-[10px] text-text-muted italic">
                    (Belum ada pejabat)
                </div>
            @endforelse
        </div>
    </div>

    {{-- Children Container with Tree Lines --}}
    @if(!empty($node['children']))
        {{-- Vertical Stem Down from Card --}}
        <div class="w-0.5 h-6 bg-border"></div>

        {{-- Horizontal Distributor Bus & Children --}}
        <div class="flex items-start justify-center gap-6 relative">
            @php $childCount = count($node['children']); @endphp
            @foreach($node['children'] as $index => $child)
                <div class="flex flex-col items-center relative">
                    {{-- Horizontal Connector Bar --}}
                    @if($childCount > 1)
                        <div class="absolute top-0 h-0.5 bg-border
                            {{ $index === 0 ? 'left-1/2 right-0' : ($index === $childCount - 1 ? 'left-0 right-1/2' : 'left-0 right-0') }}">
                        </div>
                    @endif

                    {{-- Vertical Stem Down to Child --}}
                    <div class="w-0.5 h-6 bg-border relative z-0"></div>

                    {{-- Recursive Call for Sub-Branch --}}
                    @include('kepegawaian::livewire.partials.organigram-node', ['node' => $child])
                </div>
            @endforeach
        </div>
    @endif
</div>
