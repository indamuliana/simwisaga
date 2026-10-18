<div class="mx-auto max-w-5xl space-y-6 pb-12">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('web-admin.articles.index') }}" class="p-2 rounded-xl border border-border bg-surface hover:bg-surface-hover text-text-secondary transition-colors" title="Kembali ke Daftar">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-black text-text-main">
                    {{ $articleId ? 'Edit Artikel' : 'Tulis Artikel Baru' }}
                </h1>
                <p class="text-xs text-text-secondary mt-0.5">
                    {{ $articleId ? 'Perbarui konten dan informasi berita yang sudah tayang.' : 'Tulis berita dokumentasi, prestasi, atau pengumuman sekolah.' }}
                </p>
            </div>
        </div>
    </div>

    <form wire:submit="save" class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- ── Left Column: Konten Utama (8 cols) ── -->
        <div class="lg:col-span-8 space-y-5">
            <div class="card space-y-4">
                <div>
                    <label class="label">Judul Artikel *</label>
                    <input type="text" wire:model.live.debounce.400ms="judul" class="input font-bold text-base" placeholder="Contoh: Siswa PPLG Raih Medali Emas Olimpiade Coding Nasional">
                    @error('judul') <span class="text-xs text-red-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="label">Slug URL (Permalink) *</label>
                    <div class="flex items-center rounded-xl border border-border bg-surface-hover px-3 py-1 text-xs font-mono text-text-secondary">
                        <span class="opacity-60">/berita/</span>
                        <input type="text" wire:model="slug" class="bg-transparent border-0 p-0 text-xs font-mono text-text-main focus:ring-0 w-full ml-1 font-bold">
                    </div>
                    @error('slug') <span class="text-xs text-red-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="label">Ringkasan Berita (Excerpt / Sinopsis Singkat)</label>
                    <textarea wire:model="ringkasan" rows="3" class="input text-xs" placeholder="Ringkasan 1-2 kalimat yang menarik untuk preview kartu dan meta search engine..."></textarea>
                    @error('ringkasan') <span class="text-xs text-red-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="label">Konten Berita Lengkap *</label>
                    <textarea wire:model="konten" rows="14" class="input font-sans text-sm leading-relaxed" placeholder="Tulis isi berita secara lengkap di sini. Gunakan baris baru antar paragraf..."></textarea>
                    @error('konten') <span class="text-xs text-red-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- ── Right Column: Metadata & Pengaturan Terbit (4 cols) ── -->
        <div class="lg:col-span-4 space-y-5">
            
            <!-- Publikasi Card -->
            <div class="card space-y-4">
                <h3 class="text-sm font-bold text-text-main border-b border-border pb-2">Status &amp; Publikasi</h3>
                
                <div>
                    <label class="label">Status Penerbitan</label>
                    <select wire:model="status" class="input text-xs font-bold">
                        <option value="published">Tayang Langsung (Published)</option>
                        <option value="draft">Simpan sebagai Draf (Draft)</option>
                    </select>
                </div>

                <div>
                    <label class="label">Tanggal &amp; Waktu Terbit</label>
                    <input type="datetime-local" wire:model="published_at" class="input text-xs">
                </div>

                <div>
                    <label class="label">Kategori Berita</label>
                    <select wire:model="kategori_id" class="input text-xs">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->nama }}</option>
                        @endforeach
                    </select>
                    @error('kategori_id') <span class="text-xs text-red-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="label">Nama Penulis / Redaksi</label>
                    <input type="text" wire:model="penulis_nama" class="input text-xs" placeholder="Humas Wikrama">
                </div>
            </div>

            <!-- Gambar Sampul Card -->
            <div class="card space-y-4">
                <h3 class="text-sm font-bold text-text-main border-b border-border pb-2">Gambar Sampul</h3>
                
                <div class="space-y-3">
                    @if ($gambar_sampul_file)
                    <div class="rounded-xl overflow-hidden aspect-[16/10] bg-slate-900 border border-border relative">
                        <img src="{{ $gambar_sampul_file->temporaryUrl() }}" alt="Preview" class="w-full h-full object-cover">
                        <span class="absolute top-2 right-2 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-600 text-white shadow-xs">Foto Baru</span>
                    </div>
                    @elseif ($existing_gambar_sampul)
                    <div class="rounded-xl overflow-hidden aspect-[16/10] bg-slate-900 border border-border relative">
                        <img src="{{ str_starts_with($existing_gambar_sampul, 'http') ? $existing_gambar_sampul : asset($existing_gambar_sampul) }}" alt="Sampul Saat Ini" class="w-full h-full object-cover">
                        <span class="absolute top-2 right-2 px-2 py-0.5 rounded text-[10px] font-bold bg-slate-900/80 text-white shadow-xs">Sampul Aktif</span>
                    </div>
                    @else
                    <div class="rounded-xl overflow-hidden aspect-[16/10] bg-surface-hover border border-dashed border-border flex items-center justify-center text-text-muted text-xs">
                        Belum ada gambar sampul
                    </div>
                    @endif

                    <div>
                        <input type="file" wire:model="gambar_sampul_file" accept="image/*" class="text-xs text-text-secondary file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer w-full">
                        <p class="text-[10px] text-text-muted mt-1">Format: JPG, PNG, atau WebP (Maksimal 3 MB).</p>
                        @error('gambar_sampul_file') <span class="text-xs text-red-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card p-4 space-y-2">
                <button type="submit" class="btn btn-primary w-full flex items-center justify-center gap-2 text-xs py-2.5">
                    <svg wire:loading.remove wire:target="save" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <svg wire:loading wire:target="save" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span wire:loading.remove wire:target="save">{{ $articleId ? 'Simpan Perubahan' : 'Terbitkan Artikel' }}</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </button>

                <a href="{{ route('web-admin.articles.index') }}" class="btn btn-outline w-full text-center text-xs py-2">
                    Batal
                </a>
            </div>

        </div>
    </form>
</div>
