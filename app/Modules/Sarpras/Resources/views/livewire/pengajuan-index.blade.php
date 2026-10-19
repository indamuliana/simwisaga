<div class="space-y-6">
    @include('sarpras::partials.sub-nav', ['active' => 'pengajuan'])

    @if(session()->has('message'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-sm flex items-center justify-between">
            <span>{{ session('message') }}</span>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
        </div>
    @endif

    {{-- Filter & Action Bar --}}
    <div class="bg-surface border border-border rounded-2xl p-4 shadow-xs flex flex-col sm:flex-row gap-3 items-stretch sm:items-center justify-between">
        <div class="flex-1 flex gap-2">
            <div class="relative flex-1">
                <input type="text" 
                       wire:model.live.debounce.300ms="search" 
                       placeholder="Cari nomor pengajuan atau nama barang..." 
                       class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-border bg-bg text-text-main focus:ring-2 focus:ring-primary">
                <svg class="w-4 h-4 absolute left-3 top-2.5 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </div>

            <select wire:model.live="filterStatus" class="text-xs rounded-xl border border-border bg-bg text-text-main py-2 px-3">
                <option value="">Semua Status</option>
                <option value="Diajukan">Diajukan (Menunggu Review)</option>
                <option value="Review Sarpras">Review Sarpras</option>
                <option value="Disetujui Kepsek">Disetujui Kepsek</option>
                <option value="Realisasi Bendahara">Realisasi Bendahara</option>
                <option value="Selesai">Selesai</option>
                <option value="Ditolak">Ditolak</option>
            </select>
        </div>

        @can('sarpras.pengajuan.tambah')
        <button type="button" wire:click="create" class="px-3.5 py-2 rounded-xl bg-primary text-white hover:bg-primary-hover text-xs font-semibold flex items-center gap-1.5 transition shadow-xs">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            Ajukan Pembelian Baru
        </button>
        @endcan
    </div>

    {{-- Tabel Pengajuan --}}
    <div class="bg-surface border border-border rounded-2xl shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-surface-hover/60 border-b border-border text-text-muted font-medium uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-4">No. Pengajuan</th>
                        <th class="py-3 px-4">Nama Barang & Spek</th>
                        <th class="py-3 px-4">Pemohon & Ruangan</th>
                        <th class="py-3 px-4 text-center">Jumlah</th>
                        <th class="py-3 px-4 text-right">Total Estimasi (Rp)</th>
                        <th class="py-3 px-4 text-center">Urgensi</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Aksi / Alur</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($pengajuans as $p)
                        <tr class="hover:bg-surface-hover/50 transition">
                            <td class="py-3 px-4 font-mono font-bold text-primary">
                                {{ $p->nomor_pengajuan }}
                                <div class="text-[10px] text-text-muted font-normal">{{ $p->created_at?->format('d/m/Y H:i') }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-text-main">{{ $p->nama_barang }}</div>
                                @if($p->spesifikasi)
                                    <div class="text-[11px] text-text-muted line-clamp-1">{{ $p->spesifikasi }}</div>
                                @endif
                                @if($p->catatan_sarpras)
                                    <div class="text-[10px] text-purple-600 dark:text-purple-400 mt-0.5">Sarpras: {{ $p->catatan_sarpras }}</div>
                                @endif
                                @if($p->catatan_kepsek)
                                    <div class="text-[10px] text-blue-600 dark:text-blue-400 mt-0.5">Kepsek: {{ $p->catatan_kepsek }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-semibold text-text-main">{{ $p->user?->name }}</div>
                                <div class="text-[11px] text-text-secondary">{{ $p->ruangan?->nama_ruangan ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-text-main">{{ $p->jumlah }}</td>
                            <td class="py-3 px-4 text-right font-bold text-text-main">
                                {{ number_format($p->total_estimasi, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @php
                                    $urgClass = match($p->urgensi) {
                                        'Mendesak' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300 font-bold',
                                        'Tinggi' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
                                        'Sedang' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
                                        default => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'
                                    };
                                @endphp
                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $urgClass }}">
                                    {{ $p->urgensi }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                @php
                                    $stClass = match($p->status) {
                                        'Diajukan' => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
                                        'Review Sarpras' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300',
                                        'Disetujui Kepsek' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
                                        'Realisasi Bendahara' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
                                        'Selesai' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300 font-bold',
                                        'Ditolak' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300',
                                        default => 'bg-gray-100 text-gray-700'
                                    };
                                @endphp
                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $stClass }}">
                                    {{ $p->status }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    {{-- Tahap 1: Review oleh Sarpras --}}
                                    @if($p->status === 'Diajukan' && auth()->user()->can('sarpras.pengajuan.review'))
                                        <button type="button" wire:click="openAction({{ $p->id }}, 'review')" class="px-2 py-1 rounded-md bg-purple-600 text-white font-semibold text-[10px] hover:bg-purple-700 transition" title="Review & Teruskan">
                                            Review
                                        </button>
                                        <button type="button" wire:click="openAction({{ $p->id }}, 'tolak')" class="px-2 py-1 rounded-md bg-rose-600 text-white font-semibold text-[10px] hover:bg-rose-700 transition" title="Tolak">
                                            Tolak
                                        </button>
                                    @endif

                                    {{-- Tahap 2: Approval Kepala Sekolah --}}
                                    @if($p->status === 'Review Sarpras' && auth()->user()->can('sarpras.pengajuan.approve'))
                                        <button type="button" wire:click="openAction({{ $p->id }}, 'approve_kepsek')" class="px-2 py-1 rounded-md bg-blue-600 text-white font-semibold text-[10px] hover:bg-blue-700 transition" title="Setujui Anggaran">
                                            Setujui
                                        </button>
                                        <button type="button" wire:click="openAction({{ $p->id }}, 'tolak')" class="px-2 py-1 rounded-md bg-rose-600 text-white font-semibold text-[10px] hover:bg-rose-700 transition" title="Tolak">
                                            Tolak
                                        </button>
                                    @endif

                                    {{-- Tahap 3: Realisasi Bendahara --}}
                                    @if($p->status === 'Disetujui Kepsek' && auth()->user()->can('sarpras.pengajuan.realisasi'))
                                        <button type="button" wire:click="openAction({{ $p->id }}, 'realisasi_bendahara')" class="px-2 py-1 rounded-md bg-amber-600 text-white font-semibold text-[10px] hover:bg-amber-700 transition" title="Proses Realisasi">
                                            Realisasi
                                        </button>
                                    @endif

                                    {{-- Tahap 4: Konfirmasi Barang Tiba / Selesai --}}
                                    @if($p->status === 'Realisasi Bendahara' && auth()->user()->can('sarpras.pengajuan.review'))
                                        <button type="button" wire:click="openAction({{ $p->id }}, 'selesai')" class="px-2 py-1 rounded-md bg-emerald-600 text-white font-semibold text-[10px] hover:bg-emerald-700 transition" title="Barang Tiba di Sekolah">
                                            Selesai
                                        </button>
                                    @endif

                                    {{-- Hapus oleh pemohon jika masih Diajukan --}}
                                    @if($p->status === 'Diajukan' && $p->user_id === auth()->id())
                                        <button type="button" 
                                                wire:confirm="Batalkan pengajuan ini?"
                                                wire:click="delete({{ $p->id }})" 
                                                class="p-1 rounded-lg text-rose-500 hover:bg-surface-hover" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-text-muted">Belum ada pengajuan pembelian.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pengajuans->hasPages())
            <div class="p-4 border-t border-border">
                {{ $pengajuans->links() }}
            </div>
        @endif
    </div>

    {{-- Modal Ajukan Pembelian Baru --}}
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-surface border border-border rounded-2xl w-full max-w-lg p-6 shadow-xl space-y-4">
                <div class="flex items-center justify-between border-b border-border pb-3">
                    <h3 class="text-base font-bold text-text-main">
                        Form Pengajuan Pembelian Sarpras
                    </h3>
                    <button type="button" wire:click="$set('showCreateModal', false)" class="text-text-muted hover:text-text-main text-lg">&times;</button>
                </div>

                <form wire:submit="submitPengajuan" class="space-y-3 text-xs">
                    <div>
                        <label class="block font-medium text-text-main mb-1">Nama Barang / Aset yang Dibutuhkan <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model="nama_barang" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3" placeholder="Contoh: Kabel HDMI 15 meter, Mesin Las Inverter">
                        @error('nama_barang') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-medium text-text-main mb-1">Kategori Barang</label>
                            <select wire:model="kategori_id" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3">
                                <option value="">Pilih Kategori</option>
                                @foreach($kategoris as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-medium text-text-main mb-1">Peruntukan Ruangan</label>
                            <select wire:model="ruangan_id" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3">
                                <option value="">Pilih Ruangan</option>
                                @foreach($ruangans as $r)
                                    <option value="{{ $r->id }}">{{ $r->nama_ruangan }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block font-medium text-text-main mb-1">Jumlah Unit <span class="text-rose-500">*</span></label>
                            <input type="number" wire:model="jumlah" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3" min="1">
                        </div>
                        <div class="col-span-2">
                            <label class="block font-medium text-text-main mb-1">Estimasi Harga Satuan (Rp) <span class="text-rose-500">*</span></label>
                            <input type="number" wire:model="estimasi_harga_satuan" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3" min="0">
                        </div>
                    </div>

                    <div>
                        <label class="block font-medium text-text-main mb-1">Tingkat Urgensi</label>
                        <select wire:model="urgensi" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3">
                            <option value="Rendah">Rendah (Dapat menunggu pengadaan tahunan)</option>
                            <option value="Sedang">Sedang (Dibutuhkan dalam 1-2 bulan)</option>
                            <option value="Tinggi">Tinggi (Dibutuhkan segera untuk pembelajaran)</option>
                            <option value="Mendesak">Mendesak (Darurat / menghambat aktivitas sekolah)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-medium text-text-main mb-1">Spesifikasi & Rincian Kebutuhan</label>
                        <textarea wire:model="spesifikasi" rows="3" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3" placeholder="Tuliskan merk yang direkomendasikan, link contoh barang, atau urgensi pemakaian..."></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-border">
                        <button type="button" wire:click="$set('showCreateModal', false)" class="px-4 py-2 rounded-xl border border-border text-text-secondary hover:bg-surface-hover">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-primary text-white font-semibold hover:bg-primary-hover shadow-xs">Kirim Pengajuan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal Aksi Approval / Catatan --}}
    @if($showActionModal && $activePengajuan)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-surface border border-border rounded-2xl w-full max-w-md p-6 shadow-xl space-y-4">
                <div class="flex items-center justify-between border-b border-border pb-3">
                    <h3 class="text-base font-bold text-text-main">
                        Konfirmasi Aksi: {{ ucfirst(str_replace('_', ' ', $actionType)) }}
                    </h3>
                    <button type="button" wire:click="$set('showActionModal', false)" class="text-text-muted hover:text-text-main text-lg">&times;</button>
                </div>

                <div class="p-3 bg-surface-hover rounded-xl text-xs space-y-1">
                    <div>No: <strong class="font-mono">{{ $activePengajuan->nomor_pengajuan }}</strong></div>
                    <div>Barang: <strong>{{ $activePengajuan->nama_barang }}</strong> ({{ $activePengajuan->jumlah }} unit)</div>
                    <div>Total Anggaran: <strong class="text-primary">Rp {{ number_format($activePengajuan->total_estimasi, 0, ',', '.') }}</strong></div>
                </div>

                <div class="text-xs">
                    <label class="block font-medium text-text-main mb-1">Catatan / Keterangan Aksi:</label>
                    <textarea wire:model="actionCatatan" rows="3" class="w-full rounded-xl border border-border bg-bg text-text-main py-2 px-3" placeholder="Berikan catatan persetujuan atau alasan..."></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-border">
                    <button type="button" wire:click="$set('showActionModal', false)" class="px-4 py-2 rounded-xl border border-border text-text-secondary hover:bg-surface-hover text-xs">Batal</button>
                    <button type="button" wire:click="processAction" class="px-4 py-2 rounded-xl {{ $actionType === 'tolak' ? 'bg-rose-600 hover:bg-rose-700' : 'bg-primary hover:bg-primary-hover' }} text-white font-semibold text-xs shadow-xs">
                        Proses Aksi
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
