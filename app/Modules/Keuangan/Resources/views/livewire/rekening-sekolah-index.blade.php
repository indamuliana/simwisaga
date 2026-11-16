<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-border pb-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-text-main flex items-center gap-2.5">
                <span class="p-2 rounded-xl bg-primary/10 text-primary">🏦</span>
                Rekening Sekolah &amp; Kas Tunai
            </h1>
            <p class="text-xs sm:text-sm text-text-secondary mt-1">
                Kelola rekening bank tujuan transfer orang tua serta kas fisik loket penerimaan bendahara.
            </p>
        </div>
    </div>

    @if (session('status'))
        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 text-xs font-bold">
            ✅ {{ session('status') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form Rekening -->
        <div class="bg-surface p-5 rounded-2xl border border-border shadow-xs space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-text-muted">
                {{ $rekeningId ? 'Edit Rekening / Kas' : 'Tambah Rekening / Kas' }}
            </h3>

            <div>
                <label class="block text-xs font-semibold text-text-secondary mb-1">Nama Kas / Bank</label>
                <input type="text" wire:model="namaBank" placeholder="Contoh: BSI Operasional, Kas Tunai Loket" class="w-full text-xs rounded-xl border-border bg-bg text-text-main py-2">
            </div>

            <div>
                <label class="flex items-center gap-2 text-xs font-semibold text-text-main cursor-pointer my-2">
                    <input type="checkbox" wire:model.live="isKasTunai" class="rounded border-border text-primary focus:ring-primary">
                    <span>Ini adalah Kas Fisik Tunai (Bukan Rekening Bank)</span>
                </label>
            </div>

            @if(! $isKasTunai)
                <div>
                    <label class="block text-xs font-semibold text-text-secondary mb-1">Nomor Rekening</label>
                    <input type="text" wire:model="nomorRekening" placeholder="Nomor rekening bank resmi" class="w-full text-xs rounded-xl border-border bg-bg text-text-main py-2 font-mono">
                </div>
            @endif

            <div>
                <label class="block text-xs font-semibold text-text-secondary mb-1">Atas Nama Rekening</label>
                <input type="text" wire:model="atasNama" placeholder="Contoh: SMK WIKRAMA 1 GARUT" class="w-full text-xs rounded-xl border-border bg-bg text-text-main py-2">
            </div>

            @if(! $rekeningId)
                <div>
                    <label class="block text-xs font-semibold text-text-secondary mb-1">Saldo Awal (Rp)</label>
                    <input type="number" wire:model="saldoAwal" placeholder="0" class="w-full text-xs rounded-xl border-border bg-bg text-text-main py-2 font-mono">
                </div>
            @endif

            <div>
                <label class="block text-xs font-semibold text-text-secondary mb-1">Keterangan (Opsional)</label>
                <textarea wire:model="keterangan" rows="2" class="w-full text-xs rounded-xl border-border bg-bg text-text-main"></textarea>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" wire:click="simpan" class="flex-1 py-2.5 rounded-xl bg-primary hover:bg-primary-hover text-white font-bold text-xs shadow-xs transition-colors">
                    Simpan Akun
                </button>
                @if($rekeningId)
                    <button type="button" wire:click="$set('rekeningId', null)" class="px-4 py-2.5 rounded-xl border border-border text-xs font-semibold text-text-main">
                        Batal
                    </button>
                @endif
            </div>
        </div>

        <!-- Tabel Rekening -->
        <div class="lg:col-span-2 bg-surface rounded-2xl border border-border shadow-xs overflow-hidden">
            <div class="p-4 border-b border-border">
                <h2 class="text-sm font-bold text-text-main">Daftar Akun Kas &amp; Rekening Bank Sekolah</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-bg text-text-muted uppercase text-[10px] tracking-wider border-b border-border">
                        <tr>
                            <th class="py-3 px-4 text-left">Nama Akun</th>
                            <th class="py-3 px-4 text-left">No. Rekening &amp; A.N</th>
                            <th class="py-3 px-4 text-right">Saldo Saat Ini</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach($rekenings as $r)
                            <tr class="hover:bg-surface-hover transition-colors">
                                <td class="py-3 px-4">
                                    <div class="font-bold text-text-main flex items-center gap-1.5">
                                        <span>{{ $r->is_kas_tunai ? '💵' : '🏦' }}</span>
                                        <span>{{ $r->nama_bank }}</span>
                                    </div>
                                    <div class="text-[10px] text-text-muted">{{ $r->is_kas_tunai ? 'Kas Tunai' : 'Rekening Bank' }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-mono font-bold text-text-main">{{ $r->nomor_rekening ?: '-' }}</div>
                                    <div class="text-[10px] text-text-muted">a.n {{ $r->atas_nama }}</div>
                                </td>
                                <td class="py-3 px-4 text-right font-black text-emerald-600">
                                    Rp {{ number_format($r->saldo_saat_ini, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <button type="button" wire:click="edit({{ $r->id }})" class="px-2.5 py-1 text-xs font-semibold rounded-lg border border-border bg-bg hover:bg-surface-hover text-text-main">
                                        Edit
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

