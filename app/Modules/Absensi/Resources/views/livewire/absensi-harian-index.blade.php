<div>
 <h2 class="mb-4 text-2xl font-bold">Absensi Harian</h2>
 <div class="mb-6 flex flex-col md:flex-row md:items-end gap-4 card">
 <div>
 <label class="block text-sm font-medium text-text-main">Tanggal</label>
 <input type="date" wire:model.live="tanggal" class="input mt-1 w-full">
 </div>
 <div class="w-full md:w-1/3">
 <label class="block text-sm font-medium text-text-main">Rombel</label>
 <select wire:model.live="rombel_id" class="input mt-1 w-full">
 <option value="">-- Pilih Rombel --</option>
 @foreach($rombels as $rombel)
 <option value="{{ $rombel->id }}">{{ $rombel->nama }}</option>
 @endforeach
 </select>
 </div>
 </div>

 @if(session()->has('message'))
 <div class="mb-4 p-4 bg-green-100 text-success rounded-md font-medium">
 {{ session('message') }}
 </div>
 @endif

 @if(count($siswas) > 0)
 <div class="mb-4">
 <button wire:click="tandaiSemuaHadir" class="btn btn-primary">
 Tandai Semua Hadir
 </button>
 </div>

 <div class="bg-surface shadow rounded-lg overflow-x-auto card">
 <table class="w-full text-sm text-left">
 <thead class="text-xs text-text-main uppercase bg-surface-hover text-text-secondary">
 <tr>
 <th class="px-6 py-3 w-1/4">Siswa</th>
 <th class="px-6 py-3 w-1/2">Kehadiran</th>
 <th class="px-6 py-3 w-1/4">Keterangan</th>
 </tr>
 </thead>
 <tbody class="divide-y divide-gray-200">
 @foreach($siswas as $siswa)
 <tr>
 <td class="px-6 py-4 font-medium text-text-main">
 {{ $siswa->nama }}
 <div class="text-xs text-text-secondary">{{ $siswa->nipd }}</div>
 </td>
 <td class="px-6 py-4">
 <div class="flex flex-wrap gap-3">
 @foreach($statuses as $status)
 <label class="inline-flex items-center cursor-pointer">
 <input type="radio" wire:model="absensiData.{{ $siswa->id }}.status" value="{{ $status->value }}" class="text-primary focus:ring-indigo-500">
 <span class="ml-1 text-sm">{{ $status->label() }}</span>
 </label>
 @endforeach
 </div>
 </td>
 <td class="px-6 py-4">
 <input type="text" wire:model="absensiData.{{ $siswa->id }}.keterangan" class="input w-full" placeholder="Keterangan (opsional)">
 </td>
 </tr>
 @endforeach
 </tbody>
 </table>
 </div>

 <div class="mt-6">
 <button wire:click="save" class="btn btn-primary w-full md:w-auto">
 Simpan Absensi Harian
 </button>
 </div>
 @elseif($rombel_id && $tanggal)
 <div class="text-text-secondary text-sm py-4 card">Tidak ada data siswa di rombel ini.</div>
 @endif
</div>
