<div class="mx-auto max-w-4xl space-y-5">
 <div>
 <h1 class="text-xl font-bold">Identitas Sekolah</h1>
 <p class="text-sm text-text-secondary">Data ini dipakai di kop surat, landing page, dan seluruh dokumen PDF.</p>
 </div>

 @if (session('status'))
 <div class="rounded-lg border border-success bg-success-soft px-4 py-3 text-sm text-success">{{ session('status') }}</div>
 @endif

 <form wire:submit="save" class="space-y-5">
 {{-- Data sekolah --}}
 <div class="card space-y-4">
 <h2 class="font-semibold">Data Sekolah</h2>
 <div class="grid gap-4 sm:grid-cols-2">
 <div class="sm:col-span-2">
 <label class="label">Nama sekolah *</label>
 <input type="text" wire:model="nama" class="input">
 @error('nama') <p class="error">{{ $message }}</p> @enderror
 </div>
 <div><label class="label">NPSN</label><input type="text" wire:model="npsn" class="input">@error('npsn') <p class="error">{{ $message }}</p> @enderror</div>
 <div><label class="label">NSS</label><input type="text" wire:model="nss" class="input">@error('nss') <p class="error">{{ $message }}</p> @enderror</div>
 <div class="sm:col-span-2"><label class="label">Nama yayasan / instansi induk</label><input type="text" wire:model="nama_yayasan" class="input">@error('nama_yayasan') <p class="error">{{ $message }}</p> @enderror</div>
 <div class="sm:col-span-2"><label class="label">Alamat</label><textarea wire:model="alamat" rows="2" class="input"></textarea>@error('alamat') <p class="error">{{ $message }}</p> @enderror</div>
 <div><label class="label">Kelurahan/Desa</label><input type="text" wire:model="kelurahan" class="input"></div>
 <div><label class="label">Kecamatan</label><input type="text" wire:model="kecamatan" class="input"></div>
 <div><label class="label">Kabupaten/Kota</label><input type="text" wire:model="kabupaten" class="input"></div>
 <div><label class="label">Provinsi</label><input type="text" wire:model="provinsi" class="input"></div>
 <div><label class="label">Kode pos</label><input type="text" wire:model="kode_pos" class="input"></div>
 <div><label class="label">Telepon</label><input type="text" wire:model="telepon" class="input"></div>
 <div><label class="label">Email</label><input type="email" wire:model="email" class="input">@error('email') <p class="error">{{ $message }}</p> @enderror</div>
 <div><label class="label">Website</label><input type="text" wire:model="website" class="input"></div>
 </div>
 </div>

 {{-- Kepala sekolah --}}
 <div class="card space-y-4">
 <h2 class="font-semibold">Kepala Sekolah</h2>
 <div class="grid gap-4 sm:grid-cols-2">
 <div><label class="label">Nama (beserta gelar)</label><input type="text" wire:model="kepala_sekolah_nama" class="input"></div>
 <div><label class="label">NIP</label><input type="text" wire:model="kepala_sekolah_nip" class="input"></div>
 </div>
 <p class="text-xs text-text-secondary">Nanti akan terhubung otomatis ke data pegawai.</p>
 </div>

 {{-- Gambar --}}
 <div class="card space-y-5">
 <h2 class="font-semibold">Logo dan Kop Surat</h2>
 <p class="text-xs text-text-secondary">Format PNG, JPG, atau WebP. Logo maksimal 2 MB. Kop surat maksimal 4 MB dengan lebar minimal 800 piksel.</p>

 @foreach ([
 ['logo_sekolah', 'Logo sekolah', $school->logoSekolahUrl(), 'h-20'],
 ['logo_aplikasi', 'Logo aplikasi', $school->logoAplikasiUrl(), 'h-20'],
 ] as [$field, $label, $url, $h])
 <div class="flex flex-wrap items-center gap-4">
 <div class="flex h-24 w-24 items-center justify-center overflow-hidden rounded-lg border border-dashed border-border bg-surface-hover dark:border-gray-600 bg-surface">
 @if ($this->{$field})
 <img src="{{ $this->{$field}->temporaryUrl() }}" class="max-h-full max-w-full object-contain" alt="">
 @elseif ($url)
 <img src="{{ $url }}" class="max-h-full max-w-full object-contain" alt="{{ $label }}">
 @else
 <span class="text-xs text-gray-400">Belum ada</span>
 @endif
 </div>
 <div class="min-w-0 flex-1">
 <label class="label">{{ $label }}</label>
 <input type="file" wire:model="{{ $field }}" accept="image/png,image/jpeg,image/webp" class="block w-full text-sm">
 <div wire:loading wire:target="{{ $field }}" class="text-xs text-text-secondary">Mengunggah...</div>
 @error($field) <p class="error">{{ $message }}</p> @enderror
 @if ($url && ! $this->{$field})
 <button type="button" wire:click="removeFile('{{ $field }}')" wire:confirm="Hapus {{ strtolower($label) }}?" class="mt-1 text-xs text-red-600 hover:underline">Hapus gambar</button>
 @endif
 </div>
 </div>
 @endforeach

 <div>
 <label class="label">Kop surat (gambar siap pakai, lebar A4)</label>
 @if ($kop_surat)
 <img src="{{ $kop_surat->temporaryUrl() }}" class="mb-2 w-full rounded border border-border" alt="Pratinjau kop">
 @elseif ($school->kopSuratUrl())
 <img src="{{ $school->kopSuratUrl() }}" class="mb-2 w-full rounded border border-border" alt="Kop surat">
 @endif
 <input type="file" wire:model="kop_surat" accept="image/png,image/jpeg,image/webp" class="block w-full text-sm">
 <div wire:loading wire:target="kop_surat" class="text-xs text-text-secondary">Mengunggah...</div>
 @error('kop_surat') <p class="error">{{ $message }}</p> @enderror
 @if ($school->kopSuratUrl() && ! $kop_surat)
 <button type="button" wire:click="removeFile('kop_surat')" wire:confirm="Hapus kop surat?" class="mt-1 text-xs text-red-600 hover:underline">Hapus gambar</button>
 @endif
 </div>
 </div>

 {{-- Aplikasi --}}
 <div class="card space-y-4">
 <h2 class="font-semibold">Identitas Aplikasi</h2>
 <div class="grid gap-4 sm:grid-cols-2">
 <div><label class="label">Nama aplikasi *</label><input type="text" wire:model="nama_aplikasi" class="input">@error('nama_aplikasi') <p class="error">{{ $message }}</p> @enderror</div>
 <div><label class="label">Kepanjangan</label><input type="text" wire:model="kepanjangan_aplikasi" class="input"></div>
 </div>
 </div>

 @can('sistem.sekolah.edit')
 <div class="flex justify-end">
 <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="save">Simpan perubahan</button>
 </div>
 @endcan
 </form>
</div>
