<div class="space-y-6">
    {{-- Header Halaman --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-text-main">Import & Export Data Siswa DAPODIK</h1>
            <p class="text-sm text-text-secondary">Kelola data siswa resmi format standar DAPODIK (66-67 kolom dengan 2 baris header).</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <button type="button" wire:click="exportDapodik" class="btn btn-primary inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>Export Data Siswa (.xlsx)</span>
            </button>

            <button type="button" wire:click="downloadTemplate" class="btn btn-secondary inline-flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                <span>Download Template (.xlsx)</span>
            </button>

            <!-- @can('kesiswaan.siswa.hapus')
            <button type="button" 
                    wire:click="resetAllSiswa" 
                    wire:confirm="PERINGATAN KRUSIAL: Apakah Anda yakin ingin MENGHAPUS SELURUH DATA SISWA beserta SELURUH AKUN PENGGUNA SISWA DAN ORANG TUA? Tindakan ini akan mengosongkan database siswa dan TIDAK DAPAT DIBATALKAN!" 
                    class="btn inline-flex items-center gap-1.5 bg-rose-600 hover:bg-rose-700 text-white shadow-xs"
                    title="Hapus seluruh data siswa dan akun siswa & orang tua">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                <span>Reset Data Siswa</span>
            </button>
            @endcan -->

            <a href="{{ route('kesiswaan.siswa') }}" class="btn btn-secondary inline-flex items-center gap-1.5 text-text-secondary hover:text-text-main">
                &larr; Kembali
            </a>
        </div>
    </div>

    {{-- Banner Aksi Cepat: Export & Template --}}
    <div class="card p-5 bg-surface-raised border border-border">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-start sm:items-center gap-3.5">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-text-main">Ekspor Database & Unduh Template</h3>
                    <p class="text-xs text-text-secondary">Ekspor seluruh data siswa aktif ke file Excel format DAPODIK atau unduh template kosong siap pakai.</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                <button type="button" wire:click="exportDapodik" class="btn btn-primary text-xs inline-flex items-center gap-2 px-4 py-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Export Data Siswa (.xlsx)</span>
                </button>
                <button type="button" wire:click="downloadTemplate" class="btn btn-secondary text-xs inline-flex items-center gap-2 px-4 py-2">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    <span>Download Template (.xlsx)</span>
                </button>
            </div>
        </div>
    </div>

    @if (session('status'))
        <div class="p-4 text-sm text-success bg-success-soft rounded-lg border border-success">
            {{ session('status') }}
        </div>
    @endif

    @if (session('error'))
        <div class="p-4 text-sm text-rose-800 bg-rose-50 rounded-lg dark:bg-rose-950/50 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
            {{ session('error') }}
        </div>
    @endif

    <!-- Upload Card & Instructions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 card p-6 space-y-5">
            <h3 class="text-base font-bold text-text-main">Pilih File Excel (.xlsx / .xls)</h3>

            <form wire:submit="processImport" class="space-y-4">
                <div class="border-2 border-dashed border-border rounded-xl p-8 text-center hover:border-indigo-500 transition">
                    <input type="file" wire:model="file" accept=".xlsx,.xls" id="fileInput" class="hidden">
                    <label for="fileInput" class="cursor-pointer space-y-2 block">
                        <div class="text-3xl text-primary dark:text-indigo-400">&#128196;</div>
                        <span class="text-sm font-semibold text-text-main text-text-secondary block">
                            {{ $file ? $file->getClientOriginalName() : 'Klik di sini untuk memilih file DAPODIK' }}
                        </span>
                        <span class="text-xs text-slate-400 block">Maksimal ukuran file: 20 MB</span>
                    </label>
                </div>
                @error('file') <span class="text-xs text-rose-500 block">{{ $message }}</span> @enderror

                <div class="flex items-center justify-between pt-2">
                    <button type="button" wire:click="importSample" wire:confirm="Impor 19 siswa dari file bawaan DATABASE_SISWA_SAMPLE.xlsx?" class="text-xs font-semibold text-primary hover:text-indigo-800 dark:text-indigo-400">
                        &#9654; Uji Coba Langsung dengan Sample (19 Siswa)
                    </button>
                    <button type="submit" wire:loading.attr="disabled" class="btn btn-primary px-5">
                        <span wire:loading.remove>Mulai Import Data</span>
                        <span wire:loading>Memproses Data...</span>
                    </button>
                </div>
            </form>

            @if($resultSummary)
                <div class="pt-6 border-t border-border space-y-4">
                    <h4 class="text-sm font-bold text-text-main">Laporan Hasil Import</h4>
                    <div class="grid grid-cols-3 gap-3 text-center">
                        <div class="p-3 bg-surface-hover bg-surface/60 rounded-lg">
                            <span class="text-xs text-slate-400 block">Total Baris</span>
                            <span class="text-lg font-bold text-text-main">{{ $resultSummary['total_baris'] }}</span>
                        </div>
                        <div class="p-3 bg-success-soft dark:bg-emerald-950/40 rounded-lg">
                            <span class="text-xs text-emerald-600 dark:text-emerald-400 block">Siswa Baru</span>
                            <span class="text-lg font-bold text-emerald-700 text-success">{{ $resultSummary['imported'] }}</span>
                        </div>
                        <div class="p-3 bg-blue-50 dark:bg-blue-950/40 rounded-lg">
                            <span class="text-xs text-primary dark:text-blue-400 block">Diperbarui</span>
                            <span class="text-lg font-bold text-blue-700 dark:text-blue-300">{{ $resultSummary['updated'] }}</span>
                        </div>
                    </div>

                    @if(!empty($resultSummary['errors']))
                        <div class="p-4 bg-rose-50 dark:bg-rose-950/40 rounded-lg border border-rose-200 dark:border-rose-900 text-xs text-rose-800 dark:text-rose-300 space-y-1 max-h-64 overflow-y-auto">
                            <span class="font-bold block">Peringatan / Baris yang Gagal ({{ count($resultSummary['errors']) }} baris):</span>
                            @foreach($resultSummary['errors'] as $err)
                                <div>Baris {{ $err['baris'] }} ({{ $err['nama'] }}): <span class="text-rose-600 dark:text-rose-400 font-mono">{{ $err['pesan'] }}</span></div>
                            @endforeach
                        </div>
                    @endif

                    <div class="flex flex-wrap justify-end gap-3 pt-2">
                        <a href="{{ route('kesiswaan.plotting-rombel') }}" class="btn btn-primary text-xs px-4 py-2">
                            Lanjut ke Plotting Rombel &rarr;
                        </a>
                        <a href="{{ route('kesiswaan.plotting-rayon') }}" class="btn btn-secondary text-xs px-4 py-2">
                            Lanjut ke Plotting Rayon &rarr;
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Info Card -->
        <div class="card p-6 space-y-4">
            <h3 class="text-base font-bold text-text-main">Panduan Format DAPODIK</h3>
            <ul class="text-xs text-text-secondary space-y-2.5 list-disc pl-4">
                <li>Struktur file memiliki <b>2 baris header</b> (Data Ayah, Ibu, dan Wali bertingkat).</li>
                <li>
                    Kolom <b>Rombel Saat Ini</b>: <span class="text-indigo-600 dark:text-indigo-400 font-medium">Boleh dikosongkan</span>. Jika dikosongkan, data siswa tetap berhasil diimpor dan rombel dapat diisi nanti melalui modul plotting rombel. Rombel siswa lama yang sudah ada tidak akan terhapus.
                </li>
                <li>Jika kolom <b>Rombel Saat Ini</b> diisi (misal: <code>TJKT XI</code> atau <code>PPLG X</code>), sistem otomatis memetakannya ke rombel resmi nomor 1 (contoh: <code>XI TJKT 1</code>, <code>X PPLG 1</code>).</li>
                <li>Alias jurusan lama (<code>RPL &rarr; PPLG</code>, <code>TKJ &rarr; TJKT</code>, <code>BDP &rarr; PMN</code>) dikenali otomatis.</li>
                <li>Setiap siswa otomatis dibuatkan akun login (<b>Username: NISN / NIS</b>) dan akun Orang Tua (<b>Username: ortu_[NISN]</b>).</li>
                <li>Jika tidak memiliki file dari Dapodik, klik tombol <b>Download Template (.xlsx)</b> di atas untuk mendapatkan berkas siap isi lengkap dengan 1 baris contoh panduan.</li>
            </ul>
        </div>
    </div>

    {{-- Zona Bahaya: Reset Seluruh Data Siswa & Akun --}}
    @can('kesiswaan.siswa.hapus')
    <div class="card p-5 border border-rose-200 dark:border-rose-900/60 bg-rose-50/50 dark:bg-rose-950/20 rounded-xl">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-start gap-3.5">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-rose-100 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-rose-800 dark:text-rose-300">Zona Bahaya: Reset Seluruh Data Siswa & Akun</h4>
                    <p class="text-xs text-rose-700/85 dark:text-rose-400">Menghapus seluruh rekaman siswa, riwayat kelas & rayon, data orang tua, serta <strong>seluruh akun login Siswa dan Orang Tua</strong> dari database. Akun Admin, Guru, dan Master Data tetap aman.</p>
                </div>
            </div>
            <button type="button" 
                    wire:click="resetAllSiswa" 
                    wire:confirm="PERINGATAN KRUSIAL: Apakah Anda yakin ingin MENGHAPUS SELURUH DATA SISWA beserta SELURUH AKUN PENGGUNA SISWA DAN ORANG TUA? Tindakan ini akan mengosongkan database siswa dan TIDAK DAPAT DIBATALKAN!" 
                    class="btn inline-flex items-center justify-center gap-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold px-4 py-2.5 rounded-lg shrink-0 shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                <span>Reset Seluruh Data Siswa</span>
            </button>
        </div>
    </div>
    @endcan
</div>
