<div>
 <h2 class="mb-2 text-2xl font-bold">Manajemen Rapor Siswa</h2>
 
 @if(!$rombel_id)
 <div class="card p-8 text-center text-text-secondary">
 Anda belum ditugaskan sebagai Wali Kelas pada semester ini.
 </div>
 @else
 <p class="mb-6 text-text-secondary">Kelas Binaan: <strong>{{ $rombel->nama }}</strong></p>

 <div class="card overflow-x-auto">
 <div class="flex justify-between items-center mb-4">
 <h3 class="text-lg font-bold">Catatan Wali Kelas & Cetak Rapor</h3>
 <button wire:click="saveCatatan" class="btn btn-primary">Simpan Catatan</button>
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
 <th class="p-3 border-b dark:border-gray-600">Catatan Wali Kelas (Apresiasi/Nasihat)</th>
 <th class="p-3 border-b dark:border-gray-600 w-32 text-center">Cetak Rapor</th>
 </tr>
 </thead>
 <tbody class="divide-y dark:divide-gray-700">
 @foreach($siswas as $siswa)
 <tr class="hover:bg-surface-hover dark:hover:bg-gray-800/50">
 <td class="p-3 font-medium align-top">{{ $siswa->nama }}</td>
 <td class="p-3">
 <textarea wire:model.defer="catatans.{{ $siswa->id }}" class="w-full border border-border dark:border-gray-600 rounded p-2 bg-surface focus:outline-none focus:border-google-blue focus:ring-1 focus:ring-google-blue" rows="2" placeholder="Tuliskan pesan atau motivasi untuk siswa..."></textarea>
 </td>
 <td class="p-3 text-center align-top">
 <button wire:click="openPrintModal({{ $siswa->id }})" class="btn btn-secondary text-xs">Menu Cetak</button>
 </td>
 </tr>
 @endforeach
 </tbody>
 </table>
 </div>
 @endif

 <!-- Print Modal -->
 @if($showPrintModal)
 <div class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-black bg-opacity-50">
 <div class="bg-surface rounded-xl shadow-xl max-w-md w-full m-4">
 <div class="px-6 py-4 border-b border-border flex justify-between items-center">
 <h3 class="text-lg font-bold">Cetak Rapor: {{ $this->siswaPrint->nama }}</h3>
 <button wire:click="closePrintModal" class="text-text-secondary hover:text-text-main">&times;</button>
 </div>
 <div class="p-6">
 <div class="mb-4">
 <label class="label">Tanggal Cetak (Titi Mangsa)</label>
 <input type="date" wire:model="printTanggal" class="input">
 </div>
 
 <div class="flex flex-col gap-3 mt-6">
 <a href="{{ route('rapor.cetak.cover', ['siswa_id' => $printSiswaId]) }}" target="_blank" class="btn border border-google-blue text-primary hover:bg-blue-50 dark:hover:bg-blue-900/30 w-full justify-center">
 1. Cetak Cover Depan
 </a>
 <a href="{{ route('rapor.cetak.identitas', ['siswa_id' => $printSiswaId]) }}" target="_blank" class="btn border border-google-blue text-primary hover:bg-blue-50 dark:hover:bg-blue-900/30 w-full justify-center">
 2. Cetak Identitas Siswa
 </a>
 <a href="{{ route('rapor.cetak.nilai', ['siswa_id' => $printSiswaId, 'tanggal' => $printTanggal]) }}" target="_blank" class="btn btn-primary w-full justify-center">
 3. Cetak Rapor Semester (Nilai)
 </a>
 </div>
 </div>
 </div>
 </div>
 @endif
</div>
