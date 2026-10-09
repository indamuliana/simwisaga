<div>
 <div class="mb-4">
 <a href="{{ route('ekskul.index') }}" class="text-primary hover:underline">&larr; Kembali ke Dashboard Ekskul</a>
 </div>

 <h2 class="mb-2 text-2xl font-bold text-text-main">Nilai Ekstrakurikuler: {{ $ekskul->nama }}</h2>
 <p class="mb-6 text-text-secondary">Silakan tambahkan siswa anggota ekskul ini, lalu masukkan nilai dan deskripsinya.</p>

 <!-- Tambah Anggota -->
 <div class="card mb-6">
 <h3 class="text-lg font-bold mb-4">Tambah Anggota (Cari Siswa)</h3>
 <div class="flex flex-col gap-2 relative">
 <input type="text" wire:model.live.debounce.300ms="search_siswa" class="input max-w-md" placeholder="Ketik nama siswa (minimal 3 huruf)...">
 
 @if(count($searchResults) > 0)
 <div class="absolute top-full left-0 mt-1 w-full max-w-md bg-surface border border-border rounded shadow-lg z-50">
 <ul class="divide-y dark:divide-gray-700">
 @foreach($searchResults as $res)
 <li class="p-3 flex justify-between items-center hover:bg-surface-hover dark:hover:bg-gray-700">
 <div>
 <div class="font-medium">{{ $res->nama }}</div>
 <div class="text-xs text-text-secondary">{{ $res->rombel?->nama }}</div>
 </div>
 <button wire:click="addAnggota({{ $res->id }})" class="btn btn-primary text-xs py-1 px-2">Tambah</button>
 </li>
 @endforeach
 </ul>
 </div>
 @endif
 </div>
 </div>

 <!-- Tabel Nilai -->
 <div class="card mb-6 overflow-x-auto">
 <div class="flex justify-between items-center mb-4">
 <h3 class="text-lg font-bold">Daftar Anggota & Nilai</h3>
 <button wire:click="saveNilai" class="btn btn-primary">Simpan Nilai</button>
 </div>

 @if(session()->has('message'))
 <div class="mb-4 p-3 bg-green-100 text-success rounded">
 {{ session('message') }}
 </div>
 @endif

 <table class="w-full text-sm text-left border border-border">
 <thead class="bg-surface-hover">
 <tr>
 <th class="p-3 border-b dark:border-gray-600 w-48">Nama Siswa</th>
 <th class="p-3 border-b dark:border-gray-600 w-24">Kelas</th>
 <th class="p-3 border-b text-center dark:border-gray-600 w-24">Nilai Angka</th>
 <th class="p-3 border-b text-center dark:border-gray-600 w-24">Grade</th>
 <th class="p-3 border-b dark:border-gray-600">Deskripsi Capaian</th>
 <th class="p-3 border-b dark:border-gray-600 w-16 text-center">Aksi</th>
 </tr>
 </thead>
 <tbody class="divide-y dark:divide-gray-700">
 @forelse($anggotaData as $siswaId => $data)
 <tr class="hover:bg-surface-hover dark:hover:bg-gray-800/50">
 <td class="p-3 font-medium">{{ $data['siswa']->nama }}</td>
 <td class="p-3 text-text-secondary">{{ $data['siswa']->rombel?->nama }}</td>
 <td class="p-3 text-center">
 <input type="number" min="0" max="100" wire:model="anggotaData.{{ $siswaId }}.nilai_angka" class="w-16 text-center border border-border dark:border-gray-600 rounded px-2 py-1 bg-surface focus:outline-none focus:border-google-blue focus:ring-1 focus:ring-google-blue">
 </td>
 <td class="p-3 text-center font-bold text-primary">
 {{-- Tampilkan huruf jika sudah tersimpan --}}
 {{ App\Modules\Ekstrakurikuler\Models\EkstrakurikulerAnggota::where('ekstrakurikuler_id', $ekskul_id)->where('siswa_id', $siswaId)->where('semester_id', $semester_id)->first()?->nilai_huruf ?? '-' }}
 </td>
 <td class="p-3">
 <input type="text" wire:model="anggotaData.{{ $siswaId }}.deskripsi" class="w-full border border-border dark:border-gray-600 rounded px-2 py-1 bg-surface focus:outline-none focus:border-google-blue focus:ring-1 focus:ring-google-blue" placeholder="Misal: Sangat aktif dalam kegiatan...">
 </td>
 <td class="p-3 text-center">
 <button wire:click="removeAnggota({{ $siswaId }})" onclick="confirm('Keluarkan siswa dari ekskul ini?') || event.stopImmediatePropagation()" class="text-google-red hover:underline text-xs">Hapus</button>
 </td>
 </tr>
 @empty
 <tr>
 <td colspan="6" class="p-8 text-center text-text-secondary">Belum ada anggota. Silakan cari dan tambahkan siswa di atas.</td>
 </tr>
 @endforelse
 </tbody>
 </table>
 </div>
</div>
