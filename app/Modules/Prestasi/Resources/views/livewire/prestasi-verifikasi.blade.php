<div class="space-y-6">
    @include('prestasi::partials.sub-nav', ['active' => 'verifikasi'])

    @if (session()->has('message'))
        <div class="p-4 rounded-xl bg-teal-500/10 border border-teal-500/20 text-teal-700 dark:text-teal-300 text-sm flex items-center justify-between">
            <span class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('message') }}
            </span>
            <button type="button" @click="$el.parentElement.remove()" class="text-teal-700 hover:opacity-75">&times;</button>
        </div>
    @endif

    <!-- Status Tabs -->
    <div class="bg-surface rounded-2xl border border-border p-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="flex items-center gap-2">
            <button wire:click="$set('tabStatus', 'pending')" 
                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $tabStatus === 'pending' ? 'bg-amber-500 text-white shadow-xs' : 'bg-surface-hover text-text-secondary hover:text-text-main' }}">
                <span>⏳ Menunggu Verifikasi</span>
                @if($counts['pending'] > 0)
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $tabStatus === 'pending' ? 'bg-white text-amber-600' : 'bg-amber-500 text-white' }}">
                        {{ $counts['pending'] }}
                    </span>
                @endif
            </button>

            <button wire:click="$set('tabStatus', 'approved')" 
                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $tabStatus === 'approved' ? 'bg-teal-600 text-white shadow-xs' : 'bg-surface-hover text-text-secondary hover:text-text-main' }}">
                <span>✓ Telah Disetujui</span>
                <span class="text-[10px] opacity-80">({{ $counts['approved'] }})</span>
            </button>

            <button wire:click="$set('tabStatus', 'rejected')" 
                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $tabStatus === 'rejected' ? 'bg-rose-600 text-white shadow-xs' : 'bg-surface-hover text-text-secondary hover:text-text-main' }}">
                <span>✕ Ditolak</span>
                <span class="text-[10px] opacity-80">({{ $counts['rejected'] }})</span>
            </button>
        </div>

        <div class="relative w-full md:w-72">
            <input type="text" wire:model.live.debounce.300ms="search" 
                   placeholder="Cari nama siswa / event..." 
                   class="w-full text-xs rounded-xl border border-border bg-surface pl-9 pr-3 py-1.5 focus:ring-1 focus:ring-primary focus:border-primary">
            <svg class="w-4 h-4 text-text-muted absolute left-3 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
    </div>

    <!-- Table of Submissions -->
    <div class="bg-surface rounded-2xl border border-border overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-surface-hover/70 text-text-secondary uppercase text-[10px] font-bold tracking-wider border-b border-border">
                    <tr>
                        <th class="p-3 w-12 text-center">No</th>
                        <th class="p-3">Siswa Pemohon</th>
                        <th class="p-3">Prestasi & Capaian</th>
                        <th class="p-3">Penyelenggara & Tingkat</th>
                        <th class="p-3">Tanggal Kegiatan</th>
                        <th class="p-3 text-center">Bukti</th>
                        <th class="p-3 text-center">Status</th>
                        <th class="p-3 text-center">Aksi Verifikasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($items as $idx => $item)
                        <tr class="hover:bg-surface-hover/50 transition">
                            <td class="p-3 text-center text-text-muted">
                                {{ $items->firstItem() + $idx }}
                            </td>
                            <td class="p-3">
                                @if($item->tipe_peserta === 'kelompok')
                                    <strong class="text-text-main block">{{ $item->nama_tim ?: 'Tim Siswa' }}</strong>
                                    <span class="text-[11px] text-text-secondary block">{{ $item->daftar_siswa_nama }}</span>
                                @else
                                    @php $s = $item->siswas->first(); @endphp
                                    <strong class="text-text-main block">{{ $s?->nama ?? '-' }}</strong>
                                    <span class="text-[11px] text-text-secondary block">
                                        {{ $s?->rombel?->nama ?? '-' }} &bull; Rayon {{ $s?->rayon?->nama ?? '-' }}
                                    </span>
                                @endif
                            </td>
                            <td class="p-3">
                                <strong class="text-text-main block text-xs">{{ $item->nama_prestasi }}</strong>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-teal-50 text-teal-700 dark:bg-teal-950 dark:text-teal-300 inline-block mt-0.5">
                                    {{ $item->peringkat }}
                                </span>
                            </td>
                            <td class="p-3">
                                <span class="text-xs text-text-main block font-semibold">{{ $item->penyelenggara_nama }}</span>
                                <span class="text-[11px] text-text-muted block">Tingkat {{ $item->tingkat_label }}</span>
                            </td>
                            <td class="p-3">
                                <span class="text-xs text-text-main block font-semibold">
                                    {{ $item->tanggal_perolehan ? $item->tanggal_perolehan->format('d/m/Y') : '-' }}
                                </span>
                                <span class="text-[11px] text-text-muted block">Diajukan: {{ $item->created_at->diffForHumans() }}</span>
                            </td>
                            <td class="p-3 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    @if($item->sertifikat_file_path)
                                        <a href="{{ Storage::url($item->sertifikat_file_path) }}" target="_blank" 
                                           class="p-1 rounded text-teal-600 hover:bg-teal-50" title="Buka Berkas Sertifikat">
                                            📄
                                        </a>
                                    @endif
                                    @if($item->foto_file_path)
                                        <a href="{{ Storage::url($item->foto_file_path) }}" target="_blank" 
                                           class="p-1 rounded text-blue-600 hover:bg-blue-50" title="Buka Foto Kegiatan">
                                            🖼️
                                        </a>
                                    @endif
                                </div>
                            </td>
                            <td class="p-3 text-center">
                                @if($item->status_verifikasi === 'pending')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 dark:bg-amber-950 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                        Menunggu
                                    </span>
                                @elseif($item->status_verifikasi === 'approved')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-teal-50 text-teal-700 dark:bg-teal-950 dark:text-teal-300 border border-teal-200 dark:border-teal-800">
                                        Disetujui
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 dark:bg-rose-950 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
                                        Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="p-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button wire:click="openDetail({{ $item->id }})" 
                                            class="btn btn-xs btn-outline border-border flex items-center gap-1 text-[11px]">
                                        <span>Periksa</span> &rarr;
                                    </button>

                                    @if($item->status_verifikasi === 'pending')
                                        <button wire:click="approve({{ $item->id }})" 
                                                wire:confirm="Setujui pengajuan prestasi ini untuk masuk ke Rapor siswa?"
                                                class="p-1 rounded bg-teal-500 text-white hover:bg-teal-600" title="Setujui Langsung">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                        <button wire:click="openReject({{ $item->id }})" 
                                                class="p-1 rounded bg-rose-500 text-white hover:bg-rose-600" title="Tolak">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-text-muted text-xs">
                                Tidak ada pengajuan prestasi dalam status ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-border">
            {{ $items->links() }}
        </div>
    </div>

    <!-- MODAL DETAIL & VERIFIKASI -->
    @if($showDetailModal && $selectedPrestasi)
    <div class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 overflow-hidden">
        <div class="bg-surface rounded-2xl border border-border w-full max-w-2xl shadow-2xl flex flex-col max-h-[88vh] overflow-hidden">
            <!-- Header (Fixed / Sticky) -->
            <div class="px-6 py-3.5 border-b border-border flex items-center justify-between bg-surface-hover/50 flex-none">
                <h3 class="font-bold text-text-main text-base flex items-center gap-2">
                    <span>🔍</span> Tinjauan Pengajuan Prestasi Siswa
                </h3>
                <button type="button" wire:click="$set('showDetailModal', false)" 
                        class="w-8 h-8 rounded-lg flex items-center justify-center text-text-muted hover:text-text-main hover:bg-surface-hover text-lg font-bold transition">
                    &times;
                </button>
            </div>

            <!-- Body (Scrollable) -->
            <div class="p-6 space-y-4 overflow-y-auto flex-1">
                <div class="p-4 rounded-xl bg-surface-hover/60 border border-border space-y-3 text-xs">
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="text-text-muted block text-[11px]">Nama Prestasi:</span>
                            <h4 class="text-base font-bold text-text-main">{{ $selectedPrestasi->nama_prestasi }}</h4>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-teal-50 text-teal-700 dark:bg-teal-950 dark:text-teal-300 border border-teal-200">
                            {{ $selectedPrestasi->peringkat }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-4 pt-2 border-t border-border/60">
                        <div>
                            <span class="text-text-muted block text-[11px]">Siswa Penerima:</span>
                            <strong class="text-text-main block">{{ $selectedPrestasi->daftar_siswa_nama }}</strong>
                        </div>
                        <div>
                            <span class="text-text-muted block text-[11px]">Penyelenggara:</span>
                            <strong class="text-text-main block">{{ $selectedPrestasi->penyelenggara_nama }}</strong>
                            <span class="text-text-secondary text-[11px]">Tingkat {{ $selectedPrestasi->tingkat_label }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-text-muted block text-[11px]">Guru Pembimbing:</span>
                            <strong class="text-text-main block">{{ $selectedPrestasi->pembimbing_nama }}</strong>
                        </div>
                        <div>
                            <span class="text-text-muted block text-[11px]">Tanggal Perolehan:</span>
                            <strong class="text-text-main block">{{ $selectedPrestasi->tanggal_perolehan ? $selectedPrestasi->tanggal_perolehan->format('d F Y') : '-' }}</strong>
                        </div>
                    </div>

                    @if($selectedPrestasi->catatan_verifikasi)
                        <div class="p-3 rounded-lg bg-surface border border-border">
                            <span class="text-text-muted block text-[11px] font-semibold">Catatan Verifikasi:</span>
                            <p class="text-text-main mt-0.5">{{ $selectedPrestasi->catatan_verifikasi }}</p>
                        </div>
                    @endif
                </div>

                <!-- Dokumen Bukti & Foto -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-border">
                    <div class="p-3 rounded-xl bg-surface border border-border text-center">
                        <span class="text-xs font-bold text-text-main block mb-2">Berkas Scan Sertifikat (PDF)</span>
                        @if($selectedPrestasi->sertifikat_file_path)
                            <a href="{{ Storage::url($selectedPrestasi->sertifikat_file_path) }}" target="_blank" 
                               class="btn btn-xs btn-primary">
                                Buka Dokumen Sertifikat &rarr;
                            </a>
                        @else
                            <span class="text-xs text-text-muted">Tidak ada berkas sertifikat</span>
                        @endif
                    </div>

                    <div class="p-3 rounded-xl bg-surface border border-border text-center">
                        <span class="text-xs font-bold text-text-main block mb-2">Foto Dokumentasi Kegiatan / Piala</span>
                        @if($selectedPrestasi->foto_file_path)
                            <a href="{{ Storage::url($selectedPrestasi->foto_file_path) }}" target="_blank">
                                <img src="{{ Storage::url($selectedPrestasi->foto_file_path) }}" alt="Foto" class="h-28 w-auto mx-auto rounded-lg object-cover hover:opacity-90">
                            </a>
                        @else
                            <span class="text-xs text-text-muted">Tidak ada foto dokumentasi</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Footer (Fixed / Sticky) -->
            <div class="px-6 py-3.5 border-t border-border bg-surface-hover/30 flex justify-between items-center flex-none">
                <button type="button" wire:click="$set('showDetailModal', false)" class="btn btn-sm btn-outline border-border text-xs px-4">
                    Tutup
                </button>

                @if($selectedPrestasi->status_verifikasi === 'pending')
                    <div class="flex items-center gap-2">
                        <button type="button" wire:click="openReject({{ $selectedPrestasi->id }})" class="btn btn-sm btn-outline border-rose-300 text-rose-600 text-xs px-3">
                            Tolak Pengajuan
                        </button>
                        <button type="button" wire:click="approve({{ $selectedPrestasi->id }})" class="btn btn-sm btn-primary text-xs px-4">
                            ✓ Setujui & Masukkan ke Rapor
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- MODAL INPUT ALASAN PENOLAKAN -->
    @if($showRejectModal)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-surface rounded-2xl border border-border w-full max-w-md shadow-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-border flex items-center justify-between bg-surface-hover/50">
                <h3 class="font-bold text-rose-600 text-sm">Alasan Penolakan Pengajuan</h3>
                <button type="button" wire:click="$set('showRejectModal', false)" class="text-text-muted hover:text-text-main text-lg">&times;</button>
            </div>
            <form wire:submit="submitReject" class="p-6 space-y-4">
                <div>
                    <label class="text-xs font-semibold text-text-main block mb-1">Catatan Revisi / Alasan Ditolak *</label>
                    <textarea wire:model="catatanVerifikasi" rows="3" placeholder="Misal: Berkas sertifikat belum terbaca jelas, mohon unggah scan PDF asli..." 
                              class="w-full text-xs rounded-xl border border-border bg-surface px-3 py-2"></textarea>
                    @error('catatanVerifikasi') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" wire:click="$set('showRejectModal', false)" class="btn btn-sm btn-outline border-border text-xs">Batal</button>
                    <button type="submit" class="btn btn-sm bg-rose-600 hover:bg-rose-700 text-white text-xs">Kirim Penolakan</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
