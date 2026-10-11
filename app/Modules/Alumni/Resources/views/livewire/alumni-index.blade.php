<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-text-main">Pengelolaan Data Alumni &amp; Tracer Study</h1>
            <p class="text-sm text-text-secondary">Pangkalan data lulusan SMK Wikrama 1 Garut dan penelusuran tamatan standar BMW (Bekerja, Melanjutkan, Wirausaha).</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" wire:click="exportExcel" class="btn btn-primary text-xs flex items-center gap-1.5">
                <span>📥 Export Excel (.xlsx)</span>
            </button>
            <a href="{{ route('kesiswaan.kelulusan') }}" class="btn btn-secondary text-xs">
                + Proses Kelulusan Siswa
            </a>
        </div>
    </div>

    @if (session('status'))
    <div class="p-4 text-sm text-success bg-success-soft rounded-xl border border-success">
        {{ session('status') }}
    </div>
    @endif

    <!-- Statistik Serapan Lulusan (BMW) -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="card p-4 bg-surface border border-border rounded-xl">
            <span class="text-xs font-semibold text-text-secondary uppercase">Total Tamatan</span>
            <div class="text-2xl font-bold text-text-main mt-1">{{ $totalAlumni }}</div>
        </div>
        <div class="card p-4 bg-blue-50/50 dark:bg-blue-950/20 border border-blue-200 dark:border-blue-800 rounded-xl">
            <span class="text-xs font-semibold text-blue-700 dark:text-blue-400 uppercase">Bekerja</span>
            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">{{ $totalBekerja }}</div>
        </div>
        <div class="card p-4 bg-purple-50/50 dark:bg-purple-950/20 border border-purple-200 dark:border-purple-800 rounded-xl">
            <span class="text-xs font-semibold text-purple-700 dark:text-purple-400 uppercase">Melanjutkan Kuliah</span>
            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400 mt-1">{{ $totalKuliah }}</div>
        </div>
        <div class="card p-4 bg-emerald-50/50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-800 rounded-xl">
            <span class="text-xs font-semibold text-emerald-700 dark:text-emerald-400 uppercase">Wirausaha</span>
            <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">{{ $totalWirausaha }}</div>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="card p-4 bg-surface border border-border rounded-2xl grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Cari Alumni</label>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Nama, NISN, No. Ijazah, Instansi..." class="input w-full text-xs">
        </div>
        <div>
            <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Angkatan / Tahun Lulus</label>
            <select wire:model.live="filterTahunLulus" class="input w-full text-xs">
                <option value="">-- Semua Angkatan --</option>
                @foreach($daftarTahunLulus as $thn)
                <option value="{{ $thn }}">Lulus Tahun {{ $thn }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Status Tracer (BMW)</label>
            <select wire:model.live="filterStatusKarir" class="input w-full text-xs">
                <option value="">-- Semua Status Karir --</option>
                <option value="bekerja">Bekerja</option>
                <option value="kuliah">Melanjutkan Kuliah</option>
                <option value="wirausaha">Wirausaha</option>
                <option value="mencari_kerja">Mencari Kerja</option>
                <option value="belum_memutuskan">Belum Memutuskan</option>
            </select>
        </div>
    </div>

    <!-- Tabel Data Alumni -->
    <div class="card p-0 bg-surface border border-border shadow-sm rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-border text-left text-xs">
                <thead class="bg-surface-hover/80 text-text-secondary font-semibold uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-center w-12">No</th>
                        <th class="px-4 py-3">Nama Lengkap &amp; NISN</th>
                        <th class="px-4 py-3">Tahun Lulus</th>
                        <th class="px-4 py-3">Status Karir (BMW)</th>
                        <th class="px-4 py-3">Instansi / Kampus / Usaha</th>
                        <th class="px-4 py-3">Kontak Terkini</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border text-text-main">
                    @forelse($alumnis as $index => $a)
                    <tr class="hover:bg-surface-hover/40 transition">
                        <td class="px-4 py-3 text-center text-text-secondary font-medium">
                            {{ $alumnis->firstItem() + $index }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-bold text-text-main block">{{ $a->siswa?->nama }}</span>
                            <span class="text-[11px] text-text-secondary font-mono">{{ $a->siswa?->nisn ?: $a->siswa?->nipd }}</span>
                        </td>
                        <td class="px-4 py-3 font-semibold">
                            <span class="px-2 py-0.5 rounded bg-surface-hover border border-border text-[11px]">
                                {{ $a->tahun_lulus }}
                            </span>
                            @if($a->nomor_ijazah)
                            <span class="text-[10px] text-text-secondary block mt-0.5">Ijazah: {{ $a->nomor_ijazah }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold
                                {{ $a->status_karir === 'bekerja' ? 'bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300' : '' }}
                                {{ $a->status_karir === 'kuliah' ? 'bg-purple-100 text-purple-800 dark:bg-purple-950/60 dark:text-purple-300' : '' }}
                                {{ $a->status_karir === 'wirausaha' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300' : '' }}
                                {{ in_array($a->status_karir, ['mencari_kerja', 'belum_memutuskan']) ? 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' : '' }}
                            ">
                                {{ $a->status_karir_badge }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if($a->nama_instansi)
                            <div class="font-medium text-text-main">{{ $a->nama_instansi }}</div>
                            <div class="text-[11px] text-text-secondary">{{ $a->posisi_jurusan }}</div>
                            @else
                            <span class="text-text-secondary italic text-[11px]">Belum diisi</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-text-secondary">
                            <div>{{ $a->no_hp_terkini ?: '-' }}</div>
                            <div class="text-[11px] text-text-secondary">{{ $a->email_terkini ?: '-' }}</div>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button type="button" wire:click="editTracer({{ $a->id }})" class="btn btn-secondary text-xs py-1 px-2.5">
                                Edit Tracer
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-text-secondary">
                            Belum ada data alumni yang cocok dengan kriteria pencarian.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-border">
            {{ $alumnis->links() }}
        </div>
    </div>

    <!-- Modal Edit Tracer Study -->
    @if ($showEditModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="w-full max-w-lg bg-surface rounded-2xl shadow-xl border border-border overflow-hidden">
            <div class="px-6 py-4 border-b border-border flex justify-between items-center">
                <h2 class="text-lg font-bold text-text-main">Update Tracer Study Alumni</h2>
                <button wire:click="$set('showEditModal', false)" class="text-slate-400 hover:text-text-secondary">&times;</button>
            </div>
            <form wire:submit="saveTracer" class="p-6 space-y-4 text-xs">
                <div>
                    <label class="label">Status Karir (BMW) *</label>
                    <select wire:model="status_karir" class="input w-full font-semibold">
                        <option value="bekerja">Bekerja</option>
                        <option value="kuliah">Melanjutkan Kuliah</option>
                        <option value="wirausaha">Wirausaha</option>
                        <option value="mencari_kerja">Mencari Kerja</option>
                        <option value="belum_memutuskan">Belum Memutuskan</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="label">Nama Instansi / Kampus / Usaha</label>
                        <input type="text" wire:model="nama_instansi" placeholder="PT ABC / Universitas XYZ / Kedai Kopi" class="input w-full">
                    </div>
                    <div>
                        <label class="label">Posisi / Program Studi</label>
                        <input type="text" wire:model="posisi_jurusan" placeholder="Web Developer / S1 Informatika" class="input w-full">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="label">Nomor Seri Ijazah</label>
                        <input type="text" wire:model="nomor_ijazah" placeholder="DN-02/M-SMK/..." class="input w-full font-mono">
                    </div>
                    <div>
                        <label class="label">Estimasi Pendapatan</label>
                        <select wire:model="pendapatan_range" class="input w-full">
                            <option value="">-- Pilih Range --</option>
                            <option value="< 2 Juta">&lt; 2 Juta</option>
                            <option value="2 - 5 Juta">2 - 5 Juta</option>
                            <option value="5 - 10 Juta">5 - 10 Juta</option>
                            <option value="> 10 Juta">&gt; 10 Juta</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="label">No. HP / WhatsApp Terkini</label>
                        <input type="text" wire:model="no_hp_terkini" class="input w-full">
                    </div>
                    <div>
                        <label class="label">Email Terkini</label>
                        <input type="email" wire:model="email_terkini" class="input w-full">
                    </div>
                </div>

                <div>
                    <label class="label">LinkedIn / Profil Portofolio</label>
                    <input type="text" wire:model="linkedin_url" placeholder="https://linkedin.com/in/..." class="input w-full">
                </div>

                <div>
                    <label class="label">Catatan Tambahan</label>
                    <textarea wire:model="catatan" rows="2" class="input w-full"></textarea>
                </div>

                <div class="flex justify-end gap-2 border-t border-border pt-4">
                    <button type="button" wire:click="$set('showEditModal', false)" class="btn btn-secondary text-xs">Batal</button>
                    <button type="submit" class="btn btn-primary text-xs">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
