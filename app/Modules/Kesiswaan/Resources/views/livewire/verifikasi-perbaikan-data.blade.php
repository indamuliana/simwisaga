<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-surface p-6 rounded-2xl border border-border shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-primary uppercase tracking-wider mb-1">
                <span>👥 Kesiswaan</span>
                <span>&bull;</span>
                <span>Layanan Siswa</span>
            </div>
            <h1 class="text-2xl font-black text-text-main tracking-tight">Verifikasi Perbaikan Data Siswa</h1>
            <p class="text-xs text-text-muted mt-1">
                Tinjau permohonan perubahan data pribadi dan orang tua yang diajukan oleh siswa. Menyetujui akan langsung memperbarui database siswa secara otomatis.
            </p>
        </div>

        <div class="flex items-center gap-2 text-xs">
            <span class="text-text-muted">Status:</span>
            <select wire:model.live="filterStatus" class="rounded-xl border border-border bg-surface px-3 py-1.5 text-xs text-text-main font-semibold focus:border-primary focus:outline-hidden">
                <option value="menunggu">Menunggu Verifikasi</option>
                <option value="disetujui">Telah Disetujui</option>
                <option value="ditolak">Ditolak</option>
                <option value="semua">Semua Riwayat</option>
            </select>
        </div>
    </div>

    @if (session('status'))
        <div class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 p-4 text-xs font-medium text-emerald-800 dark:text-emerald-300 flex items-center gap-2 shadow-2xs">
            <span>✅</span>
            <span>{{ session('status') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div class="rounded-xl border border-rose-500/30 bg-rose-500/10 p-4 text-xs font-medium text-rose-800 dark:text-rose-300 flex items-center gap-2 shadow-2xs">
            <span>❌</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Table Pengajuan -->
    <div class="card p-6 space-y-4">
        @if ($pengajuans->isEmpty())
            <div class="p-8 text-center rounded-xl border border-dashed border-border text-xs text-text-muted">
                Tidak ada pengajuan perbaikan data siswa pada status ini.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-surface-raised text-text-muted uppercase text-[10px] tracking-wider border-b border-border">
                        <tr>
                            <th class="p-3">Siswa</th>
                            <th class="p-3">Kategori & Field</th>
                            <th class="p-3">Data Lama</th>
                            <th class="p-3">Usulan Baru</th>
                            <th class="p-3">Alasan & Berkas</th>
                            <th class="p-3">Status</th>
                            <th class="p-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach ($pengajuans as $p)
                            @php
                                $firstChange = collect($p->rincian_perubahan)->first() ?? [];
                                $fieldName = $firstChange['field'] ?? array_key_first($p->rincian_perubahan ?? []);
                                $lama = $firstChange['lama'] ?? '-';
                                $baru = $firstChange['baru'] ?? '-';
                            @endphp
                            <tr class="hover:bg-surface-hover/50 transition-colors">
                                <td class="p-3 whitespace-nowrap">
                                    <div class="font-bold text-text-main">{{ $p->siswa?->nama ?? 'Siswa Tidak Ditemukan' }}</div>
                                    <div class="text-[10px] text-text-muted font-mono">
                                        NISN: {{ $p->siswa?->nisn ?: '-' }} &bull; {{ $p->siswa?->rombel?->nama ?: 'Tanpa Rombel' }}
                                    </div>
                                    <div class="text-[10px] text-text-muted">{{ $p->created_at->format('d/m/Y H:i') }}</div>
                                </td>
                                <td class="p-3">
                                    <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-semibold bg-surface-raised border border-border text-text-secondary uppercase">
                                        {{ $p->kategori }}
                                    </span>
                                    <div class="font-bold text-text-main mt-0.5">{{ ucwords(str_replace('_', ' ', $fieldName)) }}</div>
                                </td>
                                <td class="p-3 text-rose-500 font-mono line-through">{{ $lama }}</td>
                                <td class="p-3 text-emerald-600 font-mono font-bold">{{ $baru }}</td>
                                <td class="p-3 max-w-[200px]">
                                    <div class="truncate text-text-secondary" title="{{ $p->alasan }}">{{ $p->alasan }}</div>
                                    @if ($p->lampiran)
                                        <a href="{{ asset('storage/' . $p->lampiran) }}" target="_blank" class="inline-flex items-center gap-1 text-[10px] text-primary font-bold hover:underline mt-1">
                                            <span>📎 Lihat Berkas</span>
                                        </a>
                                    @endif
                                </td>
                                <td class="p-3 whitespace-nowrap">
                                    @if ($p->status === 'menunggu')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-600 border border-amber-500/20">
                                            ⏳ Menunggu
                                        </span>
                                    @elseif ($p->status === 'disetujui')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-600 border border-emerald-500/20">
                                            ✅ Disetujui
                                        </span>
                                        @if ($p->diprosesOleh)
                                            <div class="text-[10px] text-text-muted mt-0.5">oleh {{ $p->diprosesOleh->name }}</div>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-600 border border-rose-500/20">
                                            ❌ Ditolak
                                        </span>
                                        @if ($p->catatan_admin)
                                            <div class="text-[10px] text-text-muted mt-0.5 max-w-[120px] truncate" title="{{ $p->catatan_admin }}">{{ $p->catatan_admin }}</div>
                                        @endif
                                    @endif
                                </td>
                                <td class="p-3 text-right whitespace-nowrap">
                                    @if ($p->status === 'menunggu')
                                        <div class="inline-flex items-center gap-1.5">
                                            <button type="button" wire:click="approve({{ $p->id }})" wire:confirm="Apakah Anda yakin ingin menyetujui pengajuan ini? Data siswa akan otomatis diperbarui." class="px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] shadow-2xs transition-all flex items-center gap-1">
                                                <span>✓</span>
                                                <span>Setujui</span>
                                            </button>
                                            <button type="button" wire:click="openRejectModal({{ $p->id }})" class="px-2.5 py-1.5 rounded-lg bg-surface-raised hover:bg-rose-500/10 text-rose-600 border border-border font-bold text-[11px] shadow-2xs transition-all flex items-center gap-1">
                                                <span>✕</span>
                                                <span>Tolak</span>
                                            </button>
                                        </div>
                                    @else
                                        <span class="text-text-muted text-[10px]">Telah Selesai</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $pengajuans->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Alasan Tolak -->
    @if ($showRejectModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
            <div class="bg-surface rounded-2xl border border-border shadow-xl max-w-md w-full p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-border pb-3">
                    <h3 class="text-sm font-bold text-text-main flex items-center gap-1.5">
                        <span class="text-rose-600">✕</span>
                        <span>Tolak Pengajuan Perbaikan Data</span>
                    </h3>
                    <button type="button" wire:click="closeRejectModal" class="text-text-muted hover:text-text-main text-lg font-bold">&times;</button>
                </div>

                <form wire:submit.prevent="reject" class="space-y-4 text-xs">
                    <div>
                        <label class="block font-medium text-text-secondary mb-1">Alasan Penolakan (akan dibaca oleh siswa):</label>
                        <textarea wire:model="catatanTolak" rows="3" placeholder="Contoh: Berkas foto KK/Akta tidak terbaca dengan jelas, silakan ajukan ulang..." class="w-full rounded-xl border border-border bg-surface px-3 py-2 text-xs text-text-main focus:border-primary focus:outline-hidden"></textarea>
                        @error('catatanTolak') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-border">
                        <button type="button" wire:click="closeRejectModal" class="px-4 py-2 rounded-xl border border-border hover:bg-surface-hover text-text-secondary font-bold text-xs">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-xs" wire:loading.attr="disabled">
                            <span wire:loading.remove>Tolak Pengajuan</span>
                            <span wire:loading>Memproses...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
