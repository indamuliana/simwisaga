<div>
 <h2 class="mb-4 text-2xl font-bold">Dashboard Pengolahan Nilai</h2>
 <p class="mb-6 text-text-secondary">Pilih kelas yang Anda ajar untuk mulai mengisi Tujuan Pembelajaran (TP) dan Nilai Akademik.</p>

 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
 @forelse($penugasan as $tugas)
 <div class="card flex flex-col justify-between">
 <div>
 <h3 class="text-lg font-bold text-text-main">{{ $tugas->mapel->nama }}</h3>
 <p class="text-sm font-medium text-primary mt-1">{{ $tugas->rombel->nama }}</p>
 
 @if(auth()->user()->isSuperAdmin())
 <p class="text-xs text-text-secondary mt-2">Guru: {{ $tugas->pegawai->nama_lengkap }}</p>
 @endif
 </div>
 
 <div class="mt-6 flex flex-col gap-2">
 <a href="{{ route('penilaian.input', $tugas->id) }}" class="btn btn-primary w-full text-center">
 Input Nilai
 </a>
 </div>
 </div>
 @empty
 <div class="col-span-full card p-8 text-center text-text-secondary">
 Anda belum memiliki jadwal/penugasan mengajar di semester ini.
 </div>
 @endforelse
 </div>
</div>
