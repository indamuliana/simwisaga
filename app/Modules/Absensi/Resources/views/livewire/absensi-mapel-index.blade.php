<div>
 <h2 class="mb-4 text-2xl font-bold">Absensi Mata Pelajaran</h2>
 <div class="mb-6 flex flex-col md:flex-row md:items-end gap-4 card">
 <div>
 <label class="block text-sm font-medium text-text-main">Tanggal</label>
 <input type="date" wire:model.live="tanggal" class="input mt-1 w-full">
 </div>
 <div class="w-full md:w-1/2">
 <label class="block text-sm font-medium text-text-main">Penugasan Mengajar</label>
 <select wire:model.live="penugasan_id" class="input mt-1 w-full">
 <option value="">-- Pilih Penugasan --</option>
 @foreach($penugasans as $penugasan)
 <option value="{{ $penugasan->id }}">{{ $penugasan->mapel->nama }} - {{ $penugasan->rombel->nama }}</option>
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
 <div class="bg-surface shadow rounded-lg overflow-x-auto card">
 <table class="w-full text-sm text-left">
 <thead class="text-xs text-text-main uppercase bg-surface-hover text-text-secondary">
 <tr>
 <th class="px-6 py-3 w-1/3">Siswa</th>
 <th class="px-6 py-3 w-1/3">Status Harian (Master)</th>
 <th class="px-6 py-3 w-1/3">Kehadiran Mapel</th>
 </tr>
 </thead>
 <tbody class="divide-y divide-gray-200">
 @foreach($siswas as $siswa)
 @php
 $harianStatus = $absensiHarianStatus[$siswa->id] ?? null;
 $isHadirHarian = ($harianStatus === \App\Enums\StatusAbsensi::HADIR->value || $harianStatus === null);
 @endphp
 <tr>
 <td class="px-6 py-4 font-medium text-text-main">
 {{ $siswa->nama }}
 <div class="text-xs text-text-secondary">{{ $siswa->nipd }}</div>
 </td>
 <td class="px-6 py-4">
 @if($harianStatus)
 @php $enum = \App\Enums\StatusAbsensi::tryFrom($harianStatus); @endphp
 <span class="px-2 py-1 rounded-full text-xs font-medium {{ $enum?->color() ?? 'bg-gray-100 text-text-main' }}">
 {{ $enum?->label() ?? $harianStatus }}
 </span>
 @else
 <span class="text-gray-400 italic">Belum ada absen harian</span>
 @endif
 </td>
 <td class="px-6 py-4">
 @if($isHadirHarian)
 <div class="flex gap-4">
 <label class="inline-flex items-center cursor-pointer">
 <input type="radio" wire:model="absensiMapelData.{{ $siswa->id }}.status" value="{{ \App\Enums\StatusAbsensi::HADIR->value }}" class="text-primary focus:ring-indigo-500">
 <span class="ml-1 text-sm">Hadir</span>
 </label>
 <label class="inline-flex items-center cursor-pointer">
 <input type="radio" wire:model="absensiMapelData.{{ $siswa->id }}.status" value="{{ \App\Enums\StatusAbsensi::ALPA->value }}" class="text-red-600 focus:ring-red-500">
 <span class="ml-1 text-sm font-semibold text-red-600">Alpa (Bolos)</span>
 </label>
 </div>
 @else
 <span class="text-gray-400 italic font-medium">Terkunci (Mengikuti Harian)</span>
 @endif
 </td>
 </tr>
 @endforeach
 </tbody>
 </table>
 </div>

 <div class="mt-6">
 <button wire:click="save" class="btn btn-primary w-full md:w-auto">
 Simpan Absensi Mapel
 </button>
 </div>
 @elseif($penugasan_id && $tanggal)
 <div class="text-text-secondary text-sm py-4 card">Tidak ada data siswa untuk penugasan ini.</div>
 @endif
</div>
