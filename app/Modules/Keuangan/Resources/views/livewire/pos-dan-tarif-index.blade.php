<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-border pb-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-text-main flex items-center gap-2.5">
                <span class="p-2 rounded-xl bg-primary/10 text-primary">⚙️</span>
                Pos Pembayaran &amp; Skema Tarif
            </h1>
            <p class="text-xs sm:text-sm text-text-secondary mt-1">
                Atur jenis pos biaya, tarif standar per angkatan (X, XI, XII), dispensasi/beasiswa perorangan siswa, serta terbitkan tagihan SPP massal.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button"
                    wire:click="sinkronkanTagihan"
                    wire:confirm="Sinkronkan seluruh tagihan yang belum dibayar agar otomatis mengikuti tarif Program & Tingkat terbaru?"
                    class="px-3.5 py-2 rounded-xl text-xs font-bold border border-border bg-surface hover:bg-surface-hover text-text-main shadow-xs flex items-center gap-2 transition-all">
                <span>🔄</span>
                <span>Sinkronkan Tagihan Siswa</span>
            </button>
            <button type="button"
                    wire:click="generateSppMassal"
                    wire:confirm="Apakah Anda yakin ingin menghasilkan tagihan seluruh pos (SPP 12 bulan Juli-Juni & DSP cicilan) untuk seluruh siswa aktif sesuai tarif angkatan dan programnya?"
                    class="px-4 py-2 rounded-xl text-xs font-bold bg-primary hover:bg-primary-hover text-white shadow-xs flex items-center gap-2 transition-all">
                <span>⚡</span>
                <span>Terbitkan Tagihan Massal (SPP &amp; DSP)</span>
            </button>
        </div>
    </div>

    @if (session('status'))
        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 text-xs font-bold">
            ✅ {{ session('status') }}
        </div>
    @endif

    @if (session('error'))
        <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-600 text-xs font-bold">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    <!-- Navigation Tabs -->
    <div class="flex items-center gap-2 border-b border-border pb-2">
        <button type="button"
                wire:click="$set('activeTab', 'tarif_standar')"
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $activeTab === 'tarif_standar' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:bg-surface-hover' }}">
            Tarif Standar Angkatan
        </button>
        <button type="button"
                wire:click="$set('activeTab', 'tarif_kustom')"
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $activeTab === 'tarif_kustom' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:bg-surface-hover' }}">
            Tarif Khusus / Beasiswa Siswa
        </button>
        <button type="button"
                wire:click="$set('activeTab', 'pos')"
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $activeTab === 'pos' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:bg-surface-hover' }}">
            Master Pos Biaya
        </button>
    </div>

    <!-- TAB 1: TARIF STANDAR ANGKATAN -->
    @if($activeTab === 'tarif_standar')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Form Input Tarif -->
            <div class="bg-surface p-5 rounded-2xl border border-border shadow-xs space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-text-muted">
                        {{ $tarifId ? 'Edit Tarif Standar' : 'Setel Tarif Standar' }}
                    </h3>
                    @if($tarifId)
                        <button type="button" wire:click="batalEditTarif" class="text-xs text-rose-600 font-bold hover:underline">
                            Batal Edit
                        </button>
                    @endif
                </div>

                <div>
                    <label class="block text-xs font-semibold text-text-secondary mb-1">Pos Biaya</label>
                    <select wire:model="tarifPosId" class="w-full text-xs rounded-xl border-border bg-bg text-text-main py-2">
                        @foreach($posBiayas as $pb)
                            <option value="{{ $pb->id }}">{{ $pb->nama }} ({{ $pb->kode }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-text-secondary mb-1">Tahun Ajaran</label>
                    <select wire:model="tarifTahunAjaranId" class="w-full text-xs rounded-xl border-border bg-bg text-text-main py-2">
                        @foreach($tahunAjarans as $ta)
                            <option value="{{ $ta->id }}">{{ $ta->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-text-secondary mb-1">Tingkat / Angkatan</label>
                    <select wire:model="tarifTingkat" class="w-full text-xs rounded-xl border-border bg-bg text-text-main py-2">
                        <option value="X">Kelas X (Tingkat 10)</option>
                        <option value="XI">Kelas XI (Tingkat 11)</option>
                        <option value="XII">Kelas XII (Tingkat 12)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-text-secondary mb-1">Program (Peminatan / Kelas)</label>
                    <select wire:model="tarifProgramId" class="w-full text-xs rounded-xl border-border bg-bg text-text-main py-2">
                        <option value="">Semua Program (Reguler &amp; Unggulan)</option>
                        @foreach($programs as $prog)
                            <option value="{{ $prog->id }}">Program {{ $prog->nama }}</option>
                        @endforeach
                    </select>
                    <span class="text-[10px] text-text-muted mt-1 block">Pilih jika tarif Tingkat X Unggulan dan Reguler berbeda nominalnya.</span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-text-secondary mb-1">Nominal Biaya (Rp)</label>
                    <input type="number" wire:model="tarifNominal" class="w-full text-xs rounded-xl border-border bg-bg text-text-main py-2 font-mono font-bold" placeholder="Contoh: 350000">
                </div>

                <div class="flex items-center gap-2">
                    <button type="button"
                            wire:click="simpanTarif"
                            class="flex-1 py-2.5 rounded-xl bg-primary hover:bg-primary-hover text-white font-bold text-xs shadow-xs transition-colors">
                        {{ $tarifId ? 'Perbarui Tarif Standar' : 'Simpan Tarif Standar' }}
                    </button>
                    @if($tarifId)
                        <button type="button" wire:click="batalEditTarif" class="px-4 py-2.5 rounded-xl border border-border text-xs font-semibold text-text-main hover:bg-surface-hover">
                            Batal
                        </button>
                    @endif
                </div>
            </div>

            <!-- Tabel Daftar Tarif -->
            <div class="lg:col-span-2 bg-surface rounded-2xl border border-border shadow-xs overflow-hidden">
                <div class="p-4 border-b border-border">
                    <h2 class="text-sm font-bold text-text-main">Daftar Tarif Standar Berjalan</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead class="bg-bg text-text-muted uppercase text-[10px] tracking-wider border-b border-border">
                            <tr>
                                <th class="py-3 px-4 text-left">Pos Biaya</th>
                                <th class="py-3 px-4 text-left">Tahun Ajaran</th>
                                <th class="py-3 px-4 text-center">Tingkat</th>
                                <th class="py-3 px-4 text-center">Program</th>
                                <th class="py-3 px-4 text-right">Nominal Standar</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @forelse($tarifs as $trf)
                                <tr class="hover:bg-surface-hover transition-colors">
                                    <td class="py-3 px-4 font-bold text-text-main">
                                        {{ $trf->posBiaya?->nama }}
                                        <span class="text-[10px] text-text-muted">({{ $trf->posBiaya?->kode }})</span>
                                    </td>
                                    <td class="py-3 px-4 text-text-secondary">{{ $trf->tahunAjaran?->nama }}</td>
                                    <td class="py-3 px-4 text-center font-bold text-primary">Kelas {{ $trf->tingkat }}</td>
                                    <td class="py-3 px-4 text-center">
                                        @if($trf->program)
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $trf->program->kode === 'UNG' ? 'bg-purple-500/10 text-purple-600' : 'bg-emerald-500/10 text-emerald-600' }}">
                                                {{ $trf->program->nama }}
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-bg text-text-muted border border-border">
                                                Semua Program
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-right font-black text-text-main">Rp {{ number_format($trf->nominal, 0, ',', '.') }}</td>
                                    <td class="py-3 px-4 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button type="button"
                                                    wire:click="editTarif({{ $trf->id }})"
                                                    class="px-2.5 py-1 text-xs font-semibold rounded-lg border border-border bg-bg hover:bg-surface-hover text-text-main transition-colors">
                                                Edit
                                            </button>
                                            <button type="button"
                                                    wire:click="hapusTarif({{ $trf->id }})"
                                                    wire:confirm="Yakin ingin menghapus tarif standar ini?"
                                                    class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-rose-500/10 hover:bg-rose-500 text-rose-600 hover:text-white transition-colors">
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-6 text-center text-xs text-text-muted">Belum ada tarif yang dikonfigurasi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- TAB 2: TARIF KUSTOM / BEASISWA SISWA -->
    @if($activeTab === 'tarif_kustom')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Form Kustom -->
            <div class="bg-surface p-5 rounded-2xl border border-border shadow-xs space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-text-muted">
                        {{ $kustomId ? 'Edit Tarif Khusus Siswa' : 'Tambah Tarif Kustom Siswa' }}
                    </h3>
                    @if($kustomId)
                        <button type="button" wire:click="batalEditKustom" class="text-xs text-rose-600 font-bold hover:underline">
                            Batal Edit
                        </button>
                    @endif
                </div>

                <div>
                    <label class="block text-xs font-semibold text-text-secondary mb-1">Cari Siswa</label>
                    <input type="text" wire:model.live.debounce.300ms="siswaSearch" placeholder="Ketik nama siswa..." class="w-full text-xs rounded-xl border-border bg-bg text-text-main py-2">
                    @if(count($siswaOptions) > 0)
                        <div class="mt-1 divide-y divide-border border border-border rounded-xl bg-bg max-h-40 overflow-y-auto">
                            @foreach($siswaOptions as $so)
                                <button type="button" wire:click="$set('kustomSiswaId', {{ $so->id }})" class="w-full p-2 text-left hover:bg-primary-soft text-xs flex justify-between">
                                    <span class="font-bold">{{ $so->nama }}</span>
                                    <span class="text-text-muted text-[10px]">{{ $so->rombel?->nama }}</span>
                                </button>
                            @endforeach
                        </div>
                    @endif
                    @if($kustomSiswaId)
                        <div class="mt-1 text-xs text-emerald-600 font-bold flex justify-between items-center bg-emerald-500/10 p-2 rounded-lg">
                            <span>Siswa ID #{{ $kustomSiswaId }} Terpilih</span>
                            <button type="button" wire:click="$set('kustomSiswaId', null)" class="text-rose-600 text-xs">Batal</button>
                        </div>
                    @endif
                </div>

                <div>
                    <label class="block text-xs font-semibold text-text-secondary mb-1">Pos Biaya</label>
                    <select wire:model="kustomPosId" class="w-full text-xs rounded-xl border-border bg-bg text-text-main py-2">
                        @foreach($posBiayas as $pb)
                            <option value="{{ $pb->id }}">{{ $pb->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-text-secondary mb-1">Tipe Penyesuaian</label>
                    <select wire:model="kustomJenis" class="w-full text-xs rounded-xl border-border bg-bg text-text-main py-2">
                        <option value="nominal_tetap">Nominal Khusus (Tetap)</option>
                        <option value="gratis">Gratis 100% (Bebas Biaya)</option>
                    </select>
                </div>

                @if($kustomJenis !== 'gratis')
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary mb-1">Nominal Baru (Rp)</label>
                        <input type="number" wire:model="kustomNominal" class="w-full text-xs rounded-xl border-border bg-bg text-text-main py-2 font-mono font-bold" placeholder="Contoh: 150000">
                    </div>
                @endif

                <div>
                    <label class="block text-xs font-semibold text-text-secondary mb-1">Alasan / Keterangan</label>
                    <input type="text" wire:model="kustomAlasan" placeholder="Contoh: Penerima Beasiswa OTA / Tahfidz" class="w-full text-xs rounded-xl border-border bg-bg text-text-main py-2">
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" wire:click="simpanKustom" class="flex-1 py-2.5 rounded-xl bg-primary hover:bg-primary-hover text-white font-bold text-xs shadow-xs transition-colors">
                        {{ $kustomId ? 'Perbarui Tarif Siswa' : 'Simpan Tarif Siswa' }}
                    </button>
                    @if($kustomId)
                        <button type="button" wire:click="batalEditKustom" class="px-4 py-2.5 rounded-xl border border-border text-xs font-semibold text-text-main hover:bg-surface-hover">
                            Batal
                        </button>
                    @endif
                </div>
            </div>

            <!-- Tabel Kustom -->
            <div class="lg:col-span-2 bg-surface rounded-2xl border border-border shadow-xs overflow-hidden">
                <div class="p-4 border-b border-border">
                    <h2 class="text-sm font-bold text-text-main">Daftar Siswa dengan Tarif Khusus / Beasiswa</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead class="bg-bg text-text-muted uppercase text-[10px] tracking-wider border-b border-border">
                            <tr>
                                <th class="py-3 px-4 text-left">Nama Siswa / Rombel</th>
                                <th class="py-3 px-4 text-left">Pos Biaya</th>
                                <th class="py-3 px-4 text-right">Tarif Khusus</th>
                                <th class="py-3 px-4 text-left">Alasan / Beasiswa</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @forelse($kustoms as $ks)
                                <tr class="hover:bg-surface-hover transition-colors">
                                    <td class="py-3 px-4">
                                        <div class="font-bold text-text-main">{{ $ks->siswa?->nama }}</div>
                                        <div class="text-[10px] text-text-muted">{{ $ks->siswa?->rombel?->nama ?? '-' }}</div>
                                    </td>
                                    <td class="py-3 px-4 font-medium text-text-secondary">{{ $ks->posBiaya?->nama }}</td>
                                    <td class="py-3 px-4 text-right font-black text-emerald-600">
                                        {{ $ks->jenis_potongan === 'gratis' ? 'GRATIS' : 'Rp ' . number_format($ks->nominal_kustom, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 px-4 text-text-secondary">{{ $ks->alasan ?: '-' }}</td>
                                    <td class="py-3 px-4 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button type="button"
                                                    wire:click="editKustom({{ $ks->id }})"
                                                    class="px-2.5 py-1 text-xs font-semibold rounded-lg border border-border bg-bg hover:bg-surface-hover text-text-main transition-colors">
                                                Edit
                                            </button>
                                            <button type="button"
                                                    wire:click="hapusKustom({{ $ks->id }})"
                                                    wire:confirm="Yakin ingin menghapus tarif khusus siswa ini?"
                                                    class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-rose-500/10 hover:bg-rose-500 text-rose-600 hover:text-white transition-colors">
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-xs text-text-muted">Belum ada siswa dengan tarif khusus.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-border">
                    {{ $kustoms->links() }}
                </div>
            </div>
        </div>
    @endif

    <!-- TAB 3: MASTER POS BIAYA -->
    @if($activeTab === 'pos')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-surface p-5 rounded-2xl border border-border shadow-xs space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-text-muted">
                        {{ $posId ? 'Edit Pos Biaya' : 'Tambah Pos Biaya' }}
                    </h3>
                    @if($posId)
                        <button type="button" wire:click="batalEditPos" class="text-xs text-rose-600 font-bold hover:underline">
                            Batal Edit
                        </button>
                    @endif
                </div>

                <div>
                    <label class="block text-xs font-semibold text-text-secondary mb-1">Kode Pos (Unik)</label>
                    <input type="text" wire:model="posKode" placeholder="Contoh: SPP, DSP, TOUR" class="w-full text-xs rounded-xl border-border bg-bg text-text-main py-2 uppercase font-mono">
                    @error('posKode') <span class="text-rose-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-secondary mb-1">Nama Pos Biaya</label>
                    <input type="text" wire:model="posNama" placeholder="Contoh: SPP Bulanan" class="w-full text-xs rounded-xl border-border bg-bg text-text-main py-2">
                    @error('posNama') <span class="text-rose-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-secondary mb-1">Tipe Pembayaran</label>
                    <select wire:model="posTipe" class="w-full text-xs rounded-xl border-border bg-bg text-text-main py-2">
                        <option value="bulanan">Rutin Bulanan (12 Bulan / TA)</option>
                        <option value="bebas">Bebas / Plafon Target (Dicicil, cth: DSP)</option>
                        <option value="insidental">Kegiatan Insidental (Sewaktu-waktu)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-secondary mb-1">Keterangan / Deskripsi</label>
                    <textarea wire:model="posDeskripsi" rows="2" class="w-full text-xs rounded-xl border-border bg-bg text-text-main" placeholder="Deskripsi peruntukan pos biaya"></textarea>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" wire:click="simpanPos" class="flex-1 py-2.5 rounded-xl bg-primary hover:bg-primary-hover text-white font-bold text-xs shadow-xs transition-colors">
                        {{ $posId ? 'Perbarui Pos Biaya' : 'Simpan Pos Biaya' }}
                    </button>
                    @if($posId)
                        <button type="button" wire:click="batalEditPos" class="px-4 py-2.5 rounded-xl border border-border text-xs font-semibold text-text-main hover:bg-surface-hover">
                            Batal
                        </button>
                    @endif
                </div>
            </div>

            <div class="lg:col-span-2 bg-surface rounded-2xl border border-border shadow-xs overflow-hidden">
                <div class="p-4 border-b border-border">
                    <h2 class="text-sm font-bold text-text-main">Master Pos Biaya</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead class="bg-bg text-text-muted uppercase text-[10px] tracking-wider border-b border-border">
                            <tr>
                                <th class="py-3 px-4 text-left">Kode</th>
                                <th class="py-3 px-4 text-left">Nama Pos</th>
                                <th class="py-3 px-4 text-left">Tipe</th>
                                <th class="py-3 px-4 text-left">Deskripsi</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach($posBiayas as $pb)
                                <tr class="hover:bg-surface-hover transition-colors">
                                    <td class="py-3 px-4 font-mono font-bold text-primary">{{ $pb->kode }}</td>
                                    <td class="py-3 px-4 font-bold text-text-main">{{ $pb->nama }}</td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-primary-soft text-primary">{{ $pb->badge_tipe }}</span>
                                    </td>
                                    <td class="py-3 px-4 text-text-secondary">{{ $pb->deskripsi ?: '-' }}</td>
                                    <td class="py-3 px-4 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button type="button"
                                                    wire:click="editPos({{ $pb->id }})"
                                                    class="px-2.5 py-1 text-xs font-semibold rounded-lg border border-border bg-bg hover:bg-surface-hover text-text-main transition-colors">
                                                Edit
                                            </button>
                                            <button type="button"
                                                    wire:click="hapusPos({{ $pb->id }})"
                                                    wire:confirm="Yakin ingin menghapus pos pembayaran '{{ $pb->nama }}'?"
                                                    class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-rose-500/10 hover:bg-rose-500 text-rose-600 hover:text-white transition-colors">
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>

