@props([
    'field',
    'sortField' => null,
    'sortDirection' => null
])

<th {{ $attributes->merge(['class' => 'cursor-pointer hover:bg-surface-hover select-none']) }} 
    wire:click="sortBy('{{ $field }}')">
    <div class="flex items-center gap-2">
        <span>{{ $slot }}</span>
        <span class="flex flex-col text-[10px] text-text-muted/40">
            <!-- Panah Atas (ASC) -->
            <svg class="w-3 h-3 {{ $sortField === $field && $sortDirection === 'asc' ? 'text-primary' : '' }}" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            <!-- Panah Bawah (DESC) -->
            <svg class="w-3 h-3 -mt-1 {{ $sortField === $field && $sortDirection === 'desc' ? 'text-primary' : '' }}" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </span>
    </div>
</th>
