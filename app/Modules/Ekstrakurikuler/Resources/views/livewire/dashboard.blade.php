<div>
 <h2 class="mb-4 text-2xl font-bold">Dashboard Ekstrakurikuler</h2>
 <p class="mb-6 text-text-secondary">Pilih ekstrakurikuler binaan Anda untuk mengelola anggota dan menginput nilai akhir semester.</p>

 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
 @forelse($ekskuls as $ekskul)
 <div class="card flex flex-col justify-between">
 <div>
 <h3 class="text-lg font-bold text-text-main">{{ $ekskul->nama }}</h3>
 @if(auth()->user()->isSuperAdmin())
 <p class="text-xs text-text-secondary mt-2">Pembina: {{ $ekskul->pembina->nama_lengkap }}</p>
 @endif
 </div>
 
 <div class="mt-6 flex flex-col gap-2">
 <a href="{{ route('ekskul.input', $ekskul->id) }}" class="btn btn-primary w-full text-center">
 Kelola Nilai & Anggota
 </a>
 </div>
 </div>
 @empty
 <div class="col-span-full card p-8 text-center text-text-secondary">
 Anda belum memiliki tugas binaan ekstrakurikuler.
 </div>
 @endforelse
 </div>
</div>
