<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-border pb-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-text-main flex items-center gap-2.5">
                <span class="p-2 rounded-xl bg-primary/10 text-primary">🧭</span>
                Pantauan Pembayaran Siswa Binaan
            </h1>
            <p class="text-xs sm:text-sm text-text-secondary mt-1">
                Akses khusus Guru Pembimbing Rayon &amp; Wali Kelas untuk memantau status administrasi siswa binaannya dan koordinasi dengan orang tua.
            </p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-surface p-4 rounded-2xl border border-border shadow-xs flex flex-col sm:flex-row items-center gap-3 justify-between">
        <div class="w-full sm:w-72">
            @if($pembimbingRayon && ! $isStaffKeuangan)
                <div class="px-3.5 py-2 rounded-xl bg-primary/10 border border-primary/20 text-primary text-xs font-bold flex items-center gap-2">
                    <span>🧭</span>
                    <span>Rayon Binaan: {{ $pembimbingRayon->nama }}</span>
                </div>
            @else
                <select wire:model.live="selectedRayonId" class="w-full text-xs rounded-xl border-border bg-bg text-text-main py-2">
                    @foreach($rayons as $ray)
                        <option value="{{ $ray->id }}">Rayon: {{ $ray->nama }}</option>
                    @endforeach
                </select>
            @endif
        </div>
        <div class="w-full sm:w-72">
            <input type="text"
                   wire:model.live.debounce.300ms="search"
                   placeholder="Cari nama anak binaan..."
                   class="w-full text-xs rounded-xl border-border bg-bg text-text-main py-2">
        </div>
    </div>

    <!-- Table Pantauan Binaan -->
    <div class="bg-surface rounded-2xl border border-border shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-bg text-text-muted uppercase text-[10px] tracking-wider border-b border-border">
                    <tr>
                        <th class="py-3 px-4 text-left">Nama Siswa</th>
                        <th class="py-3 px-4 text-left">Kelas / Rombel</th>
                        <th class="py-3 px-4 text-left">Nama Orang Tua</th>
                        <th class="py-3 px-4 text-right">Sisa Tunggakan</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">WhatsApp Ortu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($siswas as $s)
                        @php
                            $ortu = $s->orangTuas->firstWhere('hubungan', 'ayah')
                                ?? $s->orangTuas->firstWhere('hubungan', 'ibu')
                                ?? $s->orangTuas->firstWhere('hubungan', 'wali');
                            $hasTunggakan = (float) $s->total_tunggakan > 0;
                        @endphp
                        <tr class="hover:bg-surface-hover transition-colors">
                            <td class="py-3 px-4 font-bold text-text-main">
                                {{ $s->nama }}
                                <div class="text-[10px] text-text-muted font-normal">NISN: {{ $s->nisn ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4 font-medium text-text-secondary">{{ $s->rombel?->nama ?? '-' }}</td>
                            <td class="py-3 px-4">
                                <div class="font-medium text-text-main">{{ $ortu?->nama ?? 'Belum terisi' }}</div>
                                <div class="text-[10px] text-text-muted font-mono">{{ $ortu?->no_hp ?: '-' }}</div>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <span class="font-black {{ $hasTunggakan ? 'text-rose-600' : 'text-emerald-600' }}">
                                    Rp {{ number_format((float) $s->total_tunggakan, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($hasTunggakan)
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-600">Ada Tunggakan</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-600">Lunas</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <button type="button"
                                        wire:click="prepareWa({{ $s->id }})"
                                        class="px-3 py-1 text-xs font-semibold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white flex items-center gap-1.5 mx-auto transition-colors">
                                    <span>💬</span>
                                    <span>Hubungi</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-xs text-text-muted">
                                Tidak ada data siswa di rayon ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-border">
            {{ $siswas->links() }}
        </div>
    </div>

    <!-- Modal WhatsApp Preview -->
    @if($modalWaOpen && !empty($waData))
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-xs">
            <div class="bg-surface rounded-2xl border border-border shadow-2xl max-w-lg w-full p-5 space-y-4">
                <div class="flex items-center justify-between border-b border-border pb-3">
                    <h3 class="text-sm font-bold text-text-main flex items-center gap-2">
                        <span class="text-emerald-600 text-lg">💬</span>
                        Hubungi Orang Tua Siswa Binaan
                    </h3>
                    <button type="button" wire:click="$set('modalWaOpen', false)" class="text-text-muted hover:text-text-main text-lg">&times;</button>
                </div>

                <div class="text-xs space-y-2">
                    <div>
                        <span class="text-text-muted">Orang Tua / Wali:</span>
                        <span class="font-bold text-text-main">{{ $waData['nama_ortu'] }}</span>
                        <span class="font-mono text-primary">({{ $waData['no_hp'] ?: 'Tanpa No HP' }})</span>
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-text-muted uppercase mb-1">Pesan Rekomendasi:</label>
                    <div class="p-3 bg-bg border border-border rounded-xl font-mono text-[11px] leading-relaxed whitespace-pre-line text-text-main max-h-48 overflow-y-auto">
                        {{ $waData['pesan'] }}
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <button type="button" wire:click="$set('modalWaOpen', false)" class="px-4 py-2 rounded-xl border border-border text-xs font-semibold text-text-main">
                        Tutup
                    </button>
                    @if($waData['wa_link'])
                        <a href="{{ $waData['wa_link'] }}" target="_blank" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition-all flex items-center gap-2">
                            <span>📲 Buka WhatsApp</span>
                        </a>
                    @else
                        <span class="text-xs text-rose-600 font-bold">Nomor HP tidak valid</span>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>

