<div class="space-y-6">
    @include('beasiswaota::partials.sub-nav', ['active' => 'penerima'])

    <!-- Alert Notifikasi -->
    @if(session()->has('message'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-xl text-emerald-800 dark:text-emerald-200 text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('message') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
        </div>
    @endif
    @if(session()->has('error'))
        <div class="p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-xl text-rose-800 dark:text-rose-200 text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">&times;</button>
        </div>
    @endif

    <div class="card border border-border rounded-2xl p-6 bg-surface shadow-xs">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-5">
            <div class="w-full sm:w-80">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama atau NISN siswa..." class="input input-bordered w-full text-sm">
            </div>
            <button wire:click="openCreate" class="btn btn-primary text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Siswa Penerima & Komponen
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-border bg-surface-hover/50 text-xs font-semibold text-text-secondary uppercase">
                        <th class="py-3 px-4">Nama Siswa & Rombel</th>
                        <th class="py-3 px-4">Kelompok Donatur Pendamping</th>
                        <th class="py-3 px-4">Rincian Komponen Pembiayaan</th>
                        <th class="py-3 px-4 text-right">Target Kebutuhan / Semester</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($penerimas as $p)
                        @php
                            $totalKebutuhan = (float) $p->komponenBiayas->sum('total_target_semester');
                        @endphp
                        <tr class="hover:bg-surface-hover/30 transition">
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-text-main">{{ $p->siswa->nama }}</div>
                                <div class="text-xs text-text-secondary">
                                    {{ $p->siswa->rombel?->nama ?? 'Tanpa Rombel' }} &bull; NISN: {{ $p->siswa->nisn ?? '-' }}
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($p->kelompok)
                                    <div class="font-semibold text-text-main flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                        {{ $p->kelompok->nama_kelompok }}
                                    </div>
                                    <div class="text-xs text-text-secondary">Target Kelompok: Rp {{ number_format($p->kelompok->target_nominal, 0, ',', '.') }}</div>
                                @else
                                    <span class="text-xs text-amber-600 bg-amber-50 px-2 py-0.5 rounded font-medium">
                                        Belum Ditautkan Kelompok
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex flex-wrap gap-1.5 max-w-md">
                                    @foreach($p->komponenBiayas as $kmp)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-surface-hover border border-border rounded-lg text-xs">
                                            <strong>{{ $kmp->nama_komponen }}:</strong>
                                            @if($kmp->nominal_per_bulan)
                                                <span>Rp {{ number_format($kmp->nominal_per_bulan, 0, ',', '.') }}/bln</span>
                                            @else
                                                <span>Rp {{ number_format($kmp->total_target_semester, 0, ',', '.') }}</span>
                                            @endif
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-right font-black text-text-main">
                                Rp {{ number_format($totalKebutuhan, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($p->status === 'aktif')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300">
                                        {{ ucfirst($p->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button wire:click="edit({{ $p->id }})" title="Edit Siswa & Komponen" class="p-1.5 rounded-lg text-text-secondary hover:text-amber-600 hover:bg-surface-hover">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button wire:confirm="Yakin ingin menghapus siswa penerima beasiswa ini?" wire:click="delete({{ $p->id }})" title="Hapus Siswa" class="p-1.5 rounded-lg text-text-secondary hover:text-rose-600 hover:bg-surface-hover">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-text-secondary">
                                Belum ada data siswa penerima beasiswa. Klik "Tambah Siswa Penerima & Komponen" di atas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $penerimas->links() }}</div>
    </div>

    <!-- MODAL: FORM SISWA PENERIMA & KOMPONEN BIAYA -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
            <div class="bg-surface border border-border rounded-2xl max-w-3xl w-full p-6 shadow-xl space-y-5 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center border-b border-border pb-3">
                    <div>
                        <h3 class="text-lg font-bold text-text-main">
                            {{ $penerimaId ? 'Edit Penerima Beasiswa & Komponen' : 'Pilih Siswa Penerima & Atur Komponen' }}
                        </h3>
                        <p class="text-xs text-text-secondary">Setiap siswa dapat memiliki rincian komponen pembiayaan yang berbeda-beda.</p>
                    </div>
                    <button wire:click="$set('showModal', false)" class="text-text-secondary hover:text-text-main text-xl">&times;</button>
                </div>

                <!-- Alert Error di Dalam Modal -->
                @if(session()->has('modal_error'))
                    <div class="p-3 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-xl text-rose-800 dark:text-rose-200 text-xs flex items-center gap-2">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ session('modal_error') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="p-3 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-xl text-rose-700 dark:text-rose-300 text-xs">
                        <div class="font-bold mb-1">Mohon periksa inputan Anda:</div>
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="space-y-4">
                    <!-- Cari & Pilih Siswa -->
                    <div class="relative">
                        <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Cari & Pilih Siswa (DAPODIK) <span class="text-rose-500">*</span></label>
                        
                        @if($siswaId)
                            <div class="p-2.5 bg-primary/5 border border-primary/20 rounded-xl flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-primary/20 text-primary">
                                        Siswa Terpilih
                                    </span>
                                    <span class="font-bold text-text-main text-sm">{{ $searchSiswaQuery }}</span>
                                </div>
                                <button type="button" wire:click="resetSelectedSiswa" class="btn btn-ghost btn-xs text-text-secondary hover:text-rose-600">
                                    Ganti Siswa
                                </button>
                            </div>
                        @else
                            <input type="text" wire:model.live.debounce.250ms="searchSiswaQuery" 
                                   placeholder="Ketik minimal 2 huruf nama siswa atau NISN..." 
                                   class="input input-bordered w-full text-sm">
                            
                            @if(!empty($siswaOptions))
                                <div class="absolute z-20 left-0 right-0 mt-1 bg-surface border border-border rounded-xl shadow-lg max-h-48 overflow-y-auto divide-y divide-border">
                                    @foreach($siswaOptions as $sOpt)
                                        <div wire:click="selectSiswa({{ $sOpt['id'] }}, '{{ addslashes($sOpt['nama']) }} ({{ $sOpt['rombel']['nama'] ?? 'Tanpa Rombel' }})')" 
                                             class="p-2.5 hover:bg-surface-hover cursor-pointer text-xs flex justify-between items-center">
                                            <div>
                                                <span class="font-bold text-text-main">{{ $sOpt['nama'] }}</span>
                                                <span class="text-text-secondary ml-1">({{ $sOpt['rombel']['nama'] ?? 'Tanpa Rombel' }})</span>
                                            </div>
                                            <span class="text-[11px] text-text-secondary">NISN: {{ $sOpt['nisn'] ?? '-' }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @endif
                        @error('siswaId') <span class="text-xs text-rose-500 block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Pilih Kelompok Donatur Pendamping -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">
                                Kelompok Donatur Pendamping (1 Kelompok = 1 Siswa)
                            </label>
                            <select wire:model.live="kelompokId" class="select select-bordered w-full text-sm">
                                <option value="">-- Belum Ditautkan Kelompok --</option>
                                @foreach($availableKelompoks as $klp)
                                    <option value="{{ $klp->id }}">
                                        {{ $klp->nama_kelompok }} (Target: Rp {{ number_format($klp->target_nominal, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('kelompokId') <span class="text-xs text-rose-500 block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Status Beasiswa</label>
                            <select wire:model="status" class="select select-bordered w-full text-sm">
                                <option value="aktif">Aktif Menerima</option>
                                <option value="selesai">Selesai / Lulus</option>
                                <option value="dibatalkan">Dibatalkan</option>
                            </select>
                        </div>
                    </div>

                    <!-- Setting Komponen Pembiayaan Siswa -->
                    <div class="border border-border rounded-xl p-4 bg-surface-hover/30 space-y-3">
                        <div class="flex justify-between items-center border-b border-border pb-2">
                            <div>
                                <h4 class="font-bold text-sm text-text-main">Setting Komponen Pembiayaan Siswa Ini</h4>
                                <p class="text-[11px] text-text-secondary">Rincian kebutuhan pembiayaan yang akan disalurkan per 6 bulan (1 semester).</p>
                            </div>
                            <button type="button" wire:click="addKomponenRow" class="btn btn-xs btn-outline">
                                + Tambah Komponen
                            </button>
                        </div>

                        <div class="space-y-3">
                            @foreach($komponens as $idx => $kmp)
                                <div class="p-3 bg-surface rounded-xl border border-border text-xs space-y-2"
                                     wire:key="komponen-row-{{ $idx }}-{{ $kmp['id'] ?? 'new' }}">
                                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 items-start">
                                        <div class="sm:col-span-3">
                                            <label class="block text-[10px] font-semibold text-text-secondary mb-0.5">Jenis Komponen</label>
                                            <select wire:model.live="komponens.{{ $idx }}.jenis_komponen" class="select select-bordered select-xs w-full">
                                                <option value="spp">SPP Bulanan</option>
                                                <option value="asrama">Asrama Bulanan</option>
                                                <option value="dsp">Cicilan DSP</option>
                                                <option value="lainnya">Lainnya</option>
                                            </select>
                                        </div>

                                        <div class="sm:col-span-3">
                                            <label class="block text-[10px] font-semibold text-text-secondary mb-0.5">Nama Komponen</label>
                                            <input type="text" wire:model.blur="komponens.{{ $idx }}.nama_komponen" class="input input-bordered input-xs w-full" placeholder="Misal: SPP Bulanan">
                                            @error('komponens.' . $idx . '.nama_komponen')
                                                <span class="text-[10px] text-rose-500 block mt-0.5">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        @if(in_array($kmp['jenis_komponen'] ?? '', ['spp', 'asrama']))
                                            <div class="sm:col-span-2">
                                                <label class="block text-[10px] font-semibold text-text-secondary mb-0.5">Tarif / Bulan</label>
                                                <input type="text" inputmode="numeric" 
                                                       wire:model.live.debounce.300ms="komponens.{{ $idx }}.nominal_per_bulan" 
                                                       class="input input-bordered input-xs w-full font-medium" 
                                                       placeholder="200000">
                                                @if(!empty($kmp['nominal_per_bulan']))
                                                    <span class="text-[10px] text-primary font-medium block mt-0.5">
                                                        Rp {{ number_format((float) \App\Modules\BeasiswaOta\Livewire\PenerimaBeasiswaIndex::cleanNominal($kmp['nominal_per_bulan']), 0, ',', '.') }}/bln
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <div class="sm:col-span-2">
                                                <label class="block text-[10px] font-semibold text-text-secondary mb-0.5">Keterangan Termin</label>
                                                <input type="text" wire:model.blur="komponens.{{ $idx }}.keterangan" 
                                                       class="input input-bordered input-xs w-full" 
                                                       placeholder="Misal: Cicilan ke-1">
                                            </div>
                                        @endif

                                        <div class="sm:col-span-3">
                                            <label class="block text-[10px] font-semibold text-text-secondary mb-0.5">
                                                Total Target Semester (6 Bln)
                                            </label>
                                            <input type="text" inputmode="numeric" 
                                                   wire:model.live.debounce.300ms="komponens.{{ $idx }}.total_target_semester" 
                                                   class="input input-bordered input-xs w-full font-bold text-text-main" 
                                                   placeholder="0">
                                            @if(!empty($kmp['total_target_semester']))
                                                <span class="text-[10px] text-text-secondary font-medium block mt-0.5">
                                                    = Rp {{ number_format((float) \App\Modules\BeasiswaOta\Livewire\PenerimaBeasiswaIndex::cleanNominal($kmp['total_target_semester']), 0, ',', '.') }}
                                                </span>
                                            @endif
                                            @error('komponens.' . $idx . '.total_target_semester')
                                                <span class="text-[10px] text-rose-500 block mt-0.5">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="sm:col-span-1 text-center pt-3.5">
                                            <button type="button" wire:click="removeKomponenRow({{ $idx }})" 
                                                    class="btn btn-ghost btn-xs text-rose-500 hover:text-rose-700 hover:bg-rose-50 dark:hover:bg-rose-950/30"
                                                    title="Hapus baris ini">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Grand Total & Kelompok Comparison -->
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 pt-3 border-t border-border/80">
                            <div>
                                <span class="text-xs text-text-secondary font-medium">Total Target Kebutuhan Siswa per Semester (6 Bulan):</span>
                                <div class="text-base font-bold text-primary">
                                    Rp {{ number_format($this->grandTotal, 0, ',', '.') }}
                                </div>
                            </div>

                            @if($kelompokId)
                                @php
                                    $selectedKlp = $availableKelompoks->firstWhere('id', $kelompokId);
                                    $targetKlp = (float) ($selectedKlp?->target_nominal ?? 0);
                                    $selisih = $targetKlp - $this->grandTotal;
                                @endphp
                                <div class="text-left sm:text-right">
                                    <div class="text-[11px] text-text-secondary">
                                        Target Iuran Kelompok: <span class="font-bold text-text-main">Rp {{ number_format($targetKlp, 0, ',', '.') }}</span>
                                    </div>
                                    @if(abs($selisih) < 1)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300">
                                            ✓ Pas 100% Sesuai Target Kelompok
                                        </span>
                                    @elseif($selisih > 0)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-950/50 dark:text-blue-300" title="Kelebihan dana akan dialihkan ke Kas Insidental saat penyaluran">
                                            Surplus +Rp {{ number_format($selisih, 0, ',', '.') }} (Masuk Kas Insidental)
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-950/50 dark:text-amber-300">
                                            Kebutuhan Melebihi Kelompok (-Rp {{ number_format(abs($selisih), 0, ',', '.') }})
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Catatan Tambahan</label>
                        <textarea wire:model="keterangan" rows="2" class="textarea textarea-bordered w-full text-sm" placeholder="Keterangan kondisi ekonomi atau catatan beasiswa siswa..."></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-border">
                    <button type="button" wire:click="$set('showModal', false)" class="btn btn-ghost text-sm">Batal</button>
                    <button type="button" wire:click="save" class="btn btn-primary text-sm flex items-center gap-1.5" wire:loading.attr="disabled">
                        <span wire:loading wire:target="save" class="loading loading-spinner loading-xs"></span>
                        Simpan Data Siswa & Komponen
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
