<div class="mx-auto max-w-6xl space-y-6 pb-12">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-text-main">Manajemen Artikel &amp; Berita Sekolah</h1>
            <p class="text-sm text-text-secondary mt-1">
                Publikasikan dokumentasi kegiatan, prestasi siswa, dan siaran pers resmi di website sekolah.
            </p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('web-admin.articles.create') }}" class="btn btn-sm btn-primary flex items-center gap-1.5 text-xs px-4 py-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Tulis Artikel Baru</span>
            </a>
        </div>
    </div>

    <!-- Flash Message -->
    @if (session('message'))
    <div class="rounded-xl border border-emerald-500/20 bg-emerald-50 dark:bg-emerald-950/30 p-4 text-sm font-semibold text-emerald-700 dark:text-emerald-300 flex items-center gap-2">
        <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <span>{{ session('message') }}</span>
    </div>
    @endif

    <!-- Filter & Search Bar -->
    <div class="card p-4 flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
            <div class="relative w-full sm:w-72">
                <input type="text" 
                       wire:model.live.debounce.300ms="search" 
                       placeholder="Cari judul, ringkasan, penulis..." 
                       class="input text-xs pl-8 py-2 w-full">
                <svg class="w-4 h-4 text-text-secondary absolute left-2.5 top-2.5 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <select wire:model.live="filterKategori" class="input text-xs py-2 w-auto">
                <option value="">Semua Kategori</option>
                @foreach ($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->nama }}</option>
                @endforeach
            </select>

            <select wire:model.live="filterStatus" class="input text-xs py-2 w-auto">
                <option value="">Semua Status</option>
                <option value="published">Tayang (Published)</option>
                <option value="draft">Draf (Draft)</option>
            </select>
        </div>

        <span class="text-xs text-text-secondary font-medium">
            Total: <strong>{{ $articles->total() }}</strong> artikel
        </span>
    </div>

    <!-- Table of Articles -->
    <div class="card overflow-hidden p-0 border border-border">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-surface-hover border-b border-border text-text-secondary font-bold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="py-3.5 px-4 w-16">Sampul</th>
                        <th class="py-3.5 px-4">Judul &amp; Ringkasan</th>
                        <th class="py-3.5 px-4 w-36">Kategori &amp; Penulis</th>
                        <th class="py-3.5 px-4 w-28 text-center">Status</th>
                        <th class="py-3.5 px-4 w-28 text-center">Statistik</th>
                        <th class="py-3.5 px-4 w-32 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($articles as $art)
                    <tr class="hover:bg-surface-hover/50 transition-colors">
                        <td class="py-3 px-4">
                            <div class="w-14 h-11 rounded-lg overflow-hidden bg-slate-900 shrink-0 border border-border">
                                <img src="{{ $art->gambarSampulUrl() }}" alt="{{ $art->judul }}" class="w-full h-full object-cover">
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <a href="{{ route('web-admin.articles.edit', $art->id) }}" class="font-bold text-text-main hover:text-primary transition-colors text-sm line-clamp-1">
                                {{ $art->judul }}
                            </a>
                            <p class="text-text-secondary text-[11px] line-clamp-1 mt-0.5">
                                {{ $art->ringkasan ?: strip_tags($art->konten) }}
                            </p>
                            <span class="text-[10px] font-mono text-text-muted mt-1 block">
                                Slug: /berita/{{ $art->slug }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            @if ($art->category)
                            <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                {{ $art->category->nama }}
                            </span>
                            @else
                            <span class="text-text-muted text-[10px]">-</span>
                            @endif
                            <div class="text-[10px] text-text-secondary mt-1">
                                Oleh: <strong>{{ $art->penulis_nama ?: ($art->author?->name ?? 'Admin') }}</strong>
                            </div>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <button type="button" 
                                    wire:click="toggleStatus({{ $art->id }})" 
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold transition-all cursor-pointer {{ $art->status === 'published' ? 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-border' }}"
                                    title="Klik untuk beralih status">
                                <span class="w-1.5 h-1.5 rounded-full {{ $art->status === 'published' ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400' }}"></span>
                                <span>{{ $art->status === 'published' ? 'Tayang' : 'Draf' }}</span>
                            </button>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <div class="font-bold font-mono text-text-main text-xs">{{ number_format($art->view_count) }}</div>
                            <span class="text-[10px] text-text-muted">kali dibaca</span>
                        </td>
                        <td class="py-3 px-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ url('/berita/' . $art->slug) }}" target="_blank" class="p-1.5 rounded-lg text-text-secondary hover:text-primary hover:bg-surface-hover transition-colors" title="Lihat Halaman Publik">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('web-admin.articles.edit', $art->id) }}" class="p-1.5 rounded-lg text-text-secondary hover:text-blue-600 hover:bg-surface-hover transition-colors" title="Edit Artikel">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <button type="button" 
                                        wire:click="deleteArticle({{ $art->id }})" 
                                        wire:confirm="Yakin ingin menghapus artikel '{{ $art->judul }}'?" 
                                        class="p-1.5 rounded-lg text-text-secondary hover:text-red-600 hover:bg-surface-hover transition-colors" 
                                        title="Hapus Artikel">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-text-muted">
                            <p class="text-sm font-semibold">Belum ada artikel atau berita yang ditemukan.</p>
                            <a href="{{ route('web-admin.articles.create') }}" class="btn btn-sm btn-primary inline-flex items-center gap-1.5 mt-3 text-xs">
                                + Tulis Artikel Pertama
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($articles->hasPages())
        <div class="p-4 border-t border-border">
            {{ $articles->links() }}
        </div>
        @endif
    </div>
</div>
