<div class="space-y-6">
 <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
 <div>
 <h1 class="text-2xl font-bold tracking-tight text-text-main">Guru & Tenaga Kependidikan (GTK)</h1>
 <p class="text-sm text-text-secondary">Pengelolaan profil pendidik, staf kependidikan, serta akun pengguna sistem.</p>
 </div>
 <div class="flex items-center gap-2">
 @can('kepegawaian.pegawai.cetak')
 <button wire:click="cetakDaftar" class="btn btn-secondary">
 <span>&#128424;</span> Cetak PDF
 </button>
 @endcan
 @can('kepegawaian.pegawai.tambah')
 <button wire:click="create" class="btn btn-primary">
 + Tambah Pegawai
 </button>
 @endcan
 </div>
 </div>

 @if (session('status'))
 <div class="p-4 text-sm text-success bg-success-soft rounded-lg border border-success">
 {{ session('status') }}
 </div>
 @endif

 <!-- Search & Filter Card -->
 <div class="p-4 card flex flex-col sm:flex-row gap-4">
 <div class="flex-1">
 <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama, NIP, NUPTK, atau NIK..." class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 </div>
 <div class="sm:w-52">
 <select wire:model.live="filterGtk" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 <option value="">-- Semua Jenis GTK --</option>
 <option value="guru">Guru</option>
 <option value="tendik">Tenaga Kependidikan</option>
 </select>
 </div>
 <div class="sm:w-52">
 <select wire:model.live="filterStatus" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 <option value="">-- Semua Status --</option>
 <option value="pns">PNS</option>
 <option value="pppk">PPPK</option>
 <option value="gty">GTY (Tetap Yayasan)</option>
 <option value="gtt">GTT (Tidak Tetap)</option>
 <option value="honor">Honor Sekolah</option>
 </select>
 </div>
 </div>

 <!-- Table Card -->
 <div class="card overflow-hidden">
 <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800 text-left text-sm">
 <thead class="bg-surface-hover bg-surface/50 text-text-secondary font-medium">
 <tr>
 <x-table.sortable-th field="nama_lengkap" :sortField="$sortField" :sortDirection="$sortDirection" class="px-6 py-3">Nama Pegawai & Kontak</x-table.sortable-th>
 <x-table.sortable-th field="nip" :sortField="$sortField" :sortDirection="$sortDirection" class="px-6 py-3">NIP / NUPTK / NIK</x-table.sortable-th>
 <x-table.sortable-th field="jenis_gtk" :sortField="$sortField" :sortDirection="$sortDirection" class="px-6 py-3">Jenis & Status</x-table.sortable-th>
 <x-table.sortable-th field="user_id" :sortField="$sortField" :sortDirection="$sortDirection" class="px-6 py-3">Akun Login</x-table.sortable-th>
 <x-table.sortable-th field="is_active" :sortField="$sortField" :sortDirection="$sortDirection" class="px-6 py-3">Status</x-table.sortable-th>
 <th class="px-6 py-3 text-right">Aksi</th>
 </tr>
 </thead>
 <tbody class="divide-y divide-slate-200 dark:divide-slate-800 text-text-main">
 @forelse($pegawais as $p)
 <tr class="hover:bg-surface-hover/50 dark:hover:bg-slate-800/30 transition">
 <td class="px-6 py-4">
 <div class="font-bold text-text-main">{{ $p->nama_gelar }}</div>
 <div class="text-xs text-text-secondary">{{ $p->email ?? '-' }} | {{ $p->no_hp ?? '-' }}</div>
 </td>
 <td class="px-6 py-4 font-mono text-xs">
 <div><span class="text-slate-400">NIP:</span> {{ $p->nip ?? '-' }}</div>
 <div><span class="text-slate-400">NUPTK:</span> {{ $p->nuptk ?? '-' }}</div>
 <div><span class="text-slate-400">NIK:</span> {{ \App\Core\Support\DataMasker::maskNik($p->nik) }}</div>
 </td>
 <td class="px-6 py-4">
 <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $p->jenis_gtk === 'guru' ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300' : 'bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300' }}">
 {{ $p->jenis_gtk === 'guru' ? 'Guru' : 'Tendik' }}
 </span>
 <span class="text-xs text-text-secondary block mt-0.5 uppercase">{{ $p->status_kepegawaian }}</span>
 </td>
 <td class="px-6 py-4">
 @if($p->user)
 <div class="font-mono text-xs font-semibold text-text-main">{{ $p->user->username }}</div>
 <div class="text-xs text-primary dark:text-indigo-400">{{ $p->user->getRoleNames()->join(', ') }}</div>
 @else
 <span class="text-xs italic text-slate-400">Belum ada akun</span>
 @endif
 </td>
 <td class="px-6 py-4">
 @if($p->is_active)
 <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-success-soft text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400">Aktif</span>
 @else
 <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-text-secondary bg-surface">Nonaktif</span>
 @endif
 </td>
 <td class="px-6 py-4 text-right space-x-2">
 @can('kepegawaian.pegawai.edit')
 <button wire:click="edit({{ $p->id }})" class="text-xs font-medium text-primary hover:text-indigo-800 dark:text-indigo-400">Edit</button>
 @endcan
 @can('kepegawaian.pegawai.hapus')
 <button wire:click="delete({{ $p->id }})" wire:confirm="Hapus data pegawai {{ $p->nama_lengkap }} beserta akun loginnya?" class="text-xs font-medium text-rose-600 hover:text-rose-800 dark:text-rose-400">Hapus</button>
 @endcan
 </td>
 </tr>
 @empty
 <tr>
 <td colspan="6" class="px-6 py-8 text-center text-slate-400">Belum ada data guru atau tenaga kependidikan yang sesuai.</td>
 </tr>
 @endforelse
 </tbody>
 </table>

 <div class="px-6 py-4 border-t border-border">
 {{ $pegawais->links() }}
 </div>
 </div>

 <!-- Modal Form -->
 @if($showModal)
 <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
 <div class="w-full max-w-2xl bg-surface rounded-2xl shadow-xl border border-border overflow-hidden my-8">
 <div class="px-6 py-4 border-b border-border flex justify-between items-center">
 <h2 class="text-lg font-bold text-text-main">
 {{ $pegawaiId ? 'Edit Data Pegawai' : 'Tambah Pegawai Baru' }}
 </h2>
 <button wire:click="$set('showModal', false)" class="text-slate-400 hover:text-text-secondary dark:hover:text-slate-200">&times;</button>
 </div>
 <form wire:submit="save" class="p-6 space-y-4">
 <!-- Identitas Utama -->
 <div class="grid grid-cols-4 gap-3">
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Gelar Depan</label>
 <input type="text" wire:model="gelar_depan" placeholder="Drs. / Ir." class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 </div>
 <div class="col-span-2">
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Nama Lengkap *</label>
 <input type="text" wire:model="nama_lengkap" placeholder="Nama tanpa gelar" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 @error('nama_lengkap') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Gelar Belakang</label>
 <input type="text" wire:model="gelar_belakang" placeholder="S.Kom. / M.Pd." class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 </div>
 </div>

 <div class="grid grid-cols-3 gap-3">
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">NIP</label>
 <input type="text" wire:model="nip" placeholder="18 digit atau strip" class="w-full text-sm font-mono rounded-lg border-border bg-surface text-text-main">
 @error('nip') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">NUPTK</label>
 <input type="text" wire:model="nuptk" placeholder="16 digit" class="w-full text-sm font-mono rounded-lg border-border bg-surface text-text-main">
 @error('nuptk') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">NIK</label>
 <input type="text" wire:model="nik" placeholder="16 digit" class="w-full text-sm font-mono rounded-lg border-border bg-surface text-text-main">
 @error('nik') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
 </div>
 </div>

 <div class="grid grid-cols-3 gap-3">
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Jenis GTK</label>
 <select wire:model.live="jenis_gtk" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 <option value="guru">Guru (Pendidik)</option>
 <option value="tendik">Tenaga Kependidikan (Tendik)</option>
 </select>
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Status Kepegawaian</label>
 <select wire:model="status_kepegawaian" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 <option value="gty">GTY (Tetap Yayasan)</option>
 <option value="gtt">GTT (Tidak Tetap)</option>
 <option value="honor">Honor Sekolah</option>
 <option value="pns">PNS DPK</option>
 <option value="pppk">PPPK</option>
 </select>
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Jenis Kelamin</label>
 <select wire:model="jenis_kelamin" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 <option value="L">Laki-laki</option>
 <option value="P">Perempuan</option>
 </select>
 </div>
 </div>

 <div class="grid grid-cols-2 gap-3">
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">No. WhatsApp / HP</label>
 <input type="text" wire:model="no_hp" placeholder="08xxxxxxxxxx" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Email</label>
 <input type="email" wire:model="email" placeholder="nama@wikrama.sch.id" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 </div>
 </div>

 <!-- Akun Login Pengguna (Keputusan Grill-Me: Ditentukan Manual oleh Operator) -->
 <div class="pt-4 border-t border-border space-y-3">
 <div class="flex items-center">
 <label class="inline-flex items-center gap-2 cursor-pointer">
 <input type="checkbox" wire:model.live="buat_akun" class="rounded border-border text-primary focus:ring-indigo-500">
 <span class="text-sm font-bold text-text-main">Kelola Akun Login Sistem</span>
 </label>
 </div>

 @if($buat_akun)
 <div class="p-4 bg-indigo-50/50 bg-surface/60 rounded-xl border border-indigo-100 border-border space-y-3">
 <p class="text-xs text-indigo-700 dark:text-indigo-300">
 Username dan password ditentukan bebas oleh Operator saat mendaftarkan data pegawai.
 </p>
 <div class="grid grid-cols-3 gap-3">
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Username Login *</label>
 <input type="text" wire:model="username" placeholder="Misal: beni.iskandar" class="w-full text-sm rounded-lg border-border bg-surface text-text-main font-mono">
 @error('username') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">
 Password {{ $pegawaiId ? '(Kosongkan bila tidak diubah)' : '*' }}
 </label>
 <input type="password" wire:model="password" placeholder="Minimal 6 karakter" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 @error('password') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
 </div>
 <div>
 <label class="block text-xs font-semibold text-text-main text-text-secondary mb-1">Peran Hak Akses (Role) *</label>
 <select wire:model="role_name" class="w-full text-sm rounded-lg border-border bg-surface text-text-main">
 @foreach($roles as $r)
 <option value="{{ $r->name }}">{{ $r->name }}</option>
 @endforeach
 </select>
 </div>
 </div>
 </div>
 @endif
 </div>

 <div class="pt-4 flex justify-end space-x-3 border-t border-border">
 <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 text-sm text-text-secondary hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg">Batal</button>
 <button type="submit" class="btn btn-primary">Simpan Data Pegawai</button>
 </div>
 </form>
 </div>
 </div>
 @endif
</div>
