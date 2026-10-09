<div>
 <h2 class="mb-4 text-2xl font-bold">Rekapitulasi Absensi</h2>
 
 <div class="card mb-6">
 <form wire:submit="cetakPdf" class="flex flex-col md:flex-row gap-4 items-end">
 <div class="w-full md:w-1/3">
 <label class="block text-sm font-medium text-text-main">Pilih Bulan</label>
 <input type="month" wire:model.live="bulan" class="input mt-1 w-full" required>
 </div>
 
 <div class="w-full md:w-1/3">
 <label class="block text-sm font-medium text-text-main">Pilih Rombel</label>
 <select wire:model.live="rombelId" class="input mt-1 w-full" required>
 <option value="">-- Pilih Rombel --</option>
 @foreach($rombels as $rombel)
 <option value="{{ $rombel->id }}">{{ $rombel->nama }}</option>
 @endforeach
 </select>
 </div>
 
 <div class="w-full md:w-1/3">
 <button type="submit" class="btn btn-primary w-full h-[42px]" @if(!$rombelId || empty($rekap)) disabled @endif>
 Cetak PDF
 </button>
 </div>
 </form>
 </div>

 @if($rombelId && $bulan)
 <div class="card overflow-x-auto">
 @if(empty($rekap))
 <div class="text-center py-4 text-text-secondary">Belum ada data absensi untuk bulan dan rombel ini.</div>
 @else
 <table class="w-full text-sm text-left">
 <thead class="text-xs text-text-main uppercase bg-surface-hover text-text-secondary">
 <tr>
 <th class="px-4 py-2">No</th>
 <th class="px-4 py-2">NIS</th>
 <th class="px-4 py-2">Nama Siswa</th>
 <th class="px-4 py-2 text-center text-green-600">Hadir</th>
 <th class="px-4 py-2 text-center text-yellow-600">Sakit</th>
 <th class="px-4 py-2 text-center text-primary">Izin</th>
 <th class="px-4 py-2 text-center text-red-600">Alpa</th>
 </tr>
 </thead>
 <tbody>
 @foreach($rekap as $index => $row)
 <tr class="bg-surface border-b hover:bg-surface-hover border-border">
 <td class="px-4 py-2">{{ $index + 1 }}</td>
 <td class="px-4 py-2">{{ $row['nis'] }}</td>
 <td class="px-4 py-2 font-medium text-text-main">{{ $row['nama'] }}</td>
 <td class="px-4 py-2 text-center">{{ $row['hadir'] }}</td>
 <td class="px-4 py-2 text-center">{{ $row['sakit'] }}</td>
 <td class="px-4 py-2 text-center">{{ $row['izin'] }}</td>
 <td class="px-4 py-2 text-center font-bold {{ $row['alpa'] >= 3 ? 'text-red-600' : '' }}">
 {{ $row['alpa'] }}
 </td>
 </tr>
 @endforeach
 </tbody>
 </table>
 @endif
 </div>
 @endif
</div>
