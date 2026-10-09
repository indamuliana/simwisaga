<div class="space-y-6">
 <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
 <div>
 <h1 class="text-2xl font-bold tracking-tight text-text-main">Import Data Siswa DAPODIK</h1>
 <p class="text-sm text-text-secondary">Unggah format resmi file Excel DAPODIK 66 kolom dengan 2 baris header.</p>
 </div>
 <a href="{{ route('kesiswaan.siswa') }}" class="inline-flex items-center text-sm font-semibold text-text-secondary hover:text-primary transition">
 &larr; Kembali ke Data Siswa
 </a>
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

 <div class="flex justify-end gap-3 pt-2">
 <a href="{{ route('kesiswaan.plotting-rayon') }}" class="px-4 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition">
 Lanjut ke Plotting Rayon &rarr;
 </a>
 </div>
 </div>
 @endif
 </div>

 <!-- Info Card -->
 <div class="card p-6 space-y-4">
 <h3 class="text-base font-bold text-text-main">Panduan Format DAPODIK</h3>
 <ul class="text-xs text-text-secondary space-y-2 list-disc pl-4">
 <li>Struktur file memiliki <b>2 baris header</b> (Data Ayah, Ibu, dan Wali bertingkat).</li>
 <li>Kolom <b>AQ (Rombel Saat Ini)</b> seperti <code>TJKT XI</code> atau <code>PPLG X</code> otomatis dipetakan ke rombel resmi nomor 1 (contoh: <code>XI TJKT 1</code>, <code>X PPLG 1</code>).</li>
 <li>Alias jurusan lama (<code>RPL &rarr; PPLG</code>, <code>TKJ &rarr; TJKT</code>, <code>BDP &rarr; PMN</code>) dikenali otomatis.</li>
 <li>Setiap siswa otomatis dibuatkan akun login (<b>Username: NISN</b>) dan akun Orang Tua (<b>Username: ortu_NISN</b>).</li>
 <li>Jika file memiliki kolom tambahan <b>Rayon</b>, sistem akan langsung mencatatnya; jika belum ada, gunakan fitur <b>Plotting Rayon Massal</b>.</li>
 </ul>
 </div>
 </div>
</div>
