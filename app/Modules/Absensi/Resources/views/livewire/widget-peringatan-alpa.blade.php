<div>
 @if(count($siswaPeringatan) > 0)
 <div class="card border-l-4 border-red-500">
 <h3 class="text-lg font-bold text-red-700 mb-2 flex items-center">
 <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
 Peringatan Akumulasi Alpa
 </h3>
 <p class="text-sm text-text-secondary mb-4">
 Siswa berikut telah mencapai batas akumulasi ketidakhadiran (Alpa) dan membutuhkan tindak lanjut (Surat Peringatan).
 </p>

 <div class="overflow-x-auto">
 <table class="w-full text-sm text-left">
 <thead class="text-xs text-text-main uppercase bg-surface-hover text-text-secondary">
 <tr>
 <th class="px-4 py-2">Siswa</th>
 <th class="px-4 py-2">Rombel</th>
 <th class="px-4 py-2 text-center">Total Alpa</th>
 <th class="px-4 py-2 text-center">Rekomendasi Tindakan</th>
 </tr>
 </thead>
 <tbody>
 @foreach($siswaPeringatan as $siswa)
 <tr class="bg-surface border-b border-border">
 <td class="px-4 py-2">
 <div class="font-medium text-text-main">{{ $siswa['nama'] }}</div>
 <div class="text-xs text-text-secondary">{{ $siswa['nis'] }}</div>
 </td>
 <td class="px-4 py-2">{{ $siswa['rombel'] }}</td>
 <td class="px-4 py-2 text-center font-bold text-red-600">{{ $siswa['total_alpa'] }} Hari</td>
 <td class="px-4 py-2 text-center">
 <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $siswa['bg_color'] }}">
 {{ $siswa['sp_level'] }}
 </span>
 </td>
 </tr>
 @endforeach
 </tbody>
 </table>
 </div>
 </div>
 @endif
</div>
