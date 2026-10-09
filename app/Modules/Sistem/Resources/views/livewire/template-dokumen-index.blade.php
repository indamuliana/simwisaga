<div class="space-y-6">
 <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
 <div>
 <h1 class="text-2xl font-bold tracking-tight text-text-main">Template Dokumen & Penanda Tangan</h1>
 <p class="text-sm text-text-secondary">Atur kop surat, orientasi kertas, format titimangsa, dan susunan pejabat penanda tangan (1–3 kolom) untuk setiap jenis dokumen.</p>
 </div>
 </div>

 @if (session('status'))
 <div class="p-4 text-sm text-success bg-success-soft rounded-lg border border-success">
 {{ session('status') }}
 </div>
 @endif

 <!-- Cards Grid -->
 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
 @foreach($templates as $tpl)
 <div class="card p-6 flex flex-col justify-between">
 <div class="space-y-3">
 <div class="flex items-center justify-between">
 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
 {{ strtoupper($tpl->orientasi) }}
 </span>
 <span class="text-xs text-slate-400 font-mono">{{ $tpl->kode }}</span>
 </div>
 <h3 class="text-lg font-bold text-text-main">{{ $tpl->nama }}</h3>
 <p class="text-xs text-text-secondary">{{ $tpl->deskripsi }}</p>

 <div class="pt-3 border-t border-slate-100 border-border/80 space-y-1.5 text-xs text-text-secondary">
 <div class="flex justify-between">
 <span class="text-slate-400">Titimangsa:</span>
 <span class="font-medium">{{ $tpl->titimangsa_default }}</span>
 </div>
 <div class="flex justify-between">
 <span class="text-slate-400">Kop Surat:</span>
 <span class="font-medium {{ $tpl->tampilkan_kop ? 'text-emerald-600' : 'text-slate-400' }}">
 {{ $tpl->tampilkan_kop ? 'Ditampilkan' : 'Tidak' }}
 </span>
 </div>
 <div class="pt-2">
 <span class="text-slate-400 block mb-1">Susunan Penanda Tangan ({{ $tpl->signers->count() }} kolom):</span>
 <ul class="space-y-1 pl-2 border-l-2 border-indigo-200 dark:border-indigo-900">
 @foreach($tpl->signers as $s)
 <li class="text-[11px] text-text-main text-text-secondary">
 <span class="font-semibold">Kolom {{ $s->kolom_ke }}:</span> {{ $s->judul_jabatan }} ({{ ucfirst(str_replace('_', ' ', $s->sumber)) }})
 </li>
 @endforeach
 </ul>
 </div>
 </div>
 </div>

 <div class="pt-4 mt-4 border-t border-slate-100 border-border flex justify-between items-center gap-2">
 <button wire:click="testCetak({{ $tpl->id }})" class="text-xs font-semibold text-text-secondary hover:text-primary flex items-center gap-1 transition">
 <span>&#128424;</span> Uji Cetak PDF
 </button>
 @can('sistem.template.edit')
 <button wire:click="edit({{ $tpl->id }})" class="btn btn-primary">
 Atur Template
 </button>
 @endcan
 </div>
 </div>
 @endforeach
 </div>

 <!-- Modal Form -->
 @if($showModal)
 <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
 <div class="w-full max-w-2xl bg-surface rounded-2xl shadow-xl border border-border overflow-hidden my-8">
 <div class="px-6 py-4 border-b border-border flex justify-between items-center">
 <h2 class="text-lg font-bold text-text-main">
 Atur Template Dokumen
 </h2>
 <button wire:click="$set('showModal', false)" class="text-slate-400 hover:text-text-secondary dark:hover:text-slate-200">&times;</button>
 </div>
 <form wire:submit="save" class="p-6 space-y-4">
 <div class="grid grid-cols-2 gap-4">
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Nama Dokumen</label>
 <input type="text" wire:model="nama" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 @error('nama') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Orientasi Kertas</label>
 <select wire:model="orientasi" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 <option value="portrait">Portrait (Tegak)</option>
 <option value="landscape">Landscape (Mendatar)</option>
 </select>
 </div>
 </div>

 <div class="grid grid-cols-2 gap-4">
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Format Titimangsa</label>
 <input type="text" wire:model="titimangsa_default" placeholder="Garut, {tanggal}" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 <span class="text-[11px] text-slate-400">Gunakan <code class="font-mono text-primary dark:text-indigo-400">{tanggal}</code> untuk tanggal saat cetak</span>
 </div>
 <div class="flex items-center pt-5">
 <label class="inline-flex items-center gap-2 cursor-pointer">
 <input type="checkbox" wire:model="tampilkan_kop" class="rounded border-border text-primary focus:ring-indigo-500">
 <span class="text-sm font-medium text-text-main text-text-secondary">Tampilkan Kop Surat</span>
 </label>
 </div>
 </div>

 <!-- Signers List -->
 <div class="pt-4 border-t border-border space-y-3">
 <div class="flex items-center justify-between">
 <h4 class="text-sm font-bold text-text-main">Daftar Penanda Tangan (Maksimal 3 Kolom)</h4>
 @if(count($signers) < 3)
 <button type="button" wire:click="addSigner" class="text-xs font-semibold text-primary hover:text-indigo-700 dark:text-indigo-400">
 + Tambah Penanda Tangan
 </button>
 @endif
 </div>

 @foreach($signers as $idx => $s)
 <div class="p-3 bg-surface-hover bg-surface/50 rounded-xl border border-border space-y-2">
 <div class="flex items-center justify-between gap-3">
 <span class="text-xs font-bold text-primary dark:text-indigo-400">Penanda Tangan {{ $idx + 1 }}</span>
 <button type="button" wire:click="removeSigner({{ $idx }})" class="text-xs text-rose-500 hover:text-rose-700">Hapus</button>
 </div>
 <div class="grid grid-cols-3 gap-3">
 <div>
 <label class="block text-[11px] text-text-secondary mb-0.5">Kolom Ke</label>
 <select wire:model="signers.{{ $idx }}.kolom_ke" class="w-full text-xs rounded-lg border-border bg-surface text-text-main">
 <option value="1">Kolom 1 (Kiri)</option>
 <option value="2">Kolom 2 (Tengah)</option>
 <option value="3">Kolom 3 (Kanan)</option>
 </select>
 </div>
 <div>
 <label class="block text-[11px] text-text-secondary mb-0.5">Judul Jabatan</label>
 <input type="text" wire:model="signers.{{ $idx }}.judul_jabatan" placeholder="Misal: Mengetahui, Kepala Sekolah" class="w-full text-xs rounded-lg border-border bg-surface text-text-main">
 </div>
 <div>
 <label class="block text-[11px] text-text-secondary mb-0.5">Sumber Pejabat</label>
 <select wire:model.live="signers.{{ $idx }}.sumber" class="w-full text-xs rounded-lg border-border bg-surface text-text-main">
 <option value="kepala_sekolah">Otomatis Kepala Sekolah</option>
 <option value="wali_kelas">Otomatis Wali Kelas</option>
 <option value="pembimbing_rayon">Otomatis Pembimbing Rayon</option>
 <option value="kesiswaan">Wakasek Kesiswaan</option>
 <option value="kurikulum">Wakasek Kurikulum</option>
 <option value="orang_tua">Orang Tua / Wali Siswa</option>
 <option value="custom">Pejabat Khusus (Ketik Manual)</option>
 </select>
 </div>
 </div>

 @if($s['sumber'] === 'custom')
 <div class="grid grid-cols-2 gap-3 pt-1">
 <div>
 <input type="text" wire:model="signers.{{ $idx }}.nama_pejabat" placeholder="Nama Lengkap Pejabat" class="w-full text-xs rounded-lg border-border bg-surface text-text-main">
 </div>
 <div>
 <input type="text" wire:model="signers.{{ $idx }}.nip_pejabat" placeholder="NIP / NUPTK" class="w-full text-xs rounded-lg border-border bg-surface text-text-main">
 </div>
 </div>
 @endif
 </div>
 @endforeach
 </div>

 <div class="pt-4 flex justify-end space-x-3 border-t border-border">
 <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 text-sm text-text-secondary hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg">Batal</button>
 <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
 </div>
 </form>
 </div>
 </div>
 @endif
</div>
