<div class="space-y-6">
    @include('beasiswaota::partials.sub-nav', ['active' => 'penyaluran'])

    <!-- Alert Notifikasi -->
    @if(session()->has('message'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-xl text-emerald-800 dark:text-emerald-200 text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('message') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
        </div>
    @endif

    <!-- Navigasi Tab Penyaluran -->
    <div class="flex items-center justify-between border-b border-border pb-2">
        <div class="flex flex-wrap gap-2">
            <button wire:click="$set('activeTab', 'rutin')" 
                    class="px-4 py-2 font-semibold text-sm rounded-xl transition {{ $activeTab === 'rutin' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
                Alokasi 6 Bulanan (Kelompok Asuh)
            </button>
            <button wire:click="$set('activeTab', 'insidental')" 
                    class="px-4 py-2 font-semibold text-sm rounded-xl transition {{ $activeTab === 'insidental' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
                Penyaluran Insidental Fleksibel
            </button>
            <button wire:click="$set('activeTab', 'riwayat')" 
                    class="px-4 py-2 font-semibold text-sm rounded-xl transition {{ $activeTab === 'riwayat' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
                Riwayat & Kwitansi Penyaluran
            </button>
        </div>

        <div class="text-xs text-text-secondary">
            Saldo Kas Insidental Tersedia: <strong class="text-amber-600 dark:text-amber-400">Rp {{ number_format($saldoInsidental, 0, ',', '.') }}</strong>
        </div>
    </div>

    <!-- TAB 1: ALOKASI 6 BULANAN KELOMPOK -->
    @if($activeTab === 'rutin')
        <div class="card border border-border rounded-2xl p-6 bg-surface shadow-xs space-y-6">
            <div>
                <h3 class="text-lg font-bold text-text-main">Alokasi Beasiswa Semesteran (6 Bulan Sekali)</h3>
                <p class="text-xs text-text-secondary mt-0.5">
                    Menyalurkan dana kas kelompok donatur ke komponen pembiayaan siswa binaan secara spesifik per bulan kalender. Jika terdapat kelebihan dana, otomatis dialihkan ke kas insidental.
                </p>
            </div>

            @if(session()->has('error_rutin'))
                <div class="p-3 bg-rose-50 text-rose-800 text-xs rounded-xl border border-rose-200">
                    {{ session('error_rutin') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Pilih Kelompok Donatur</label>
                    <select wire:model.live="rutinKelompokId" class="select select-bordered w-full text-sm">
                        <option value="">-- Pilih Kelompok Donatur --</option>
                        @foreach($kelompoks as $klp)
                            <option value="{{ $klp->id }}">
                                {{ $klp->nama_kelompok }} ({{ $klp->kode_kelompok }}) - Siswa: {{ $klp->siswaPenerima?->siswa->nama ?? 'Belum ada' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Periode Semester</label>
                    <select wire:model.live="rutinSemesterId" class="select select-bordered w-full text-sm">
                        @foreach($semesters as $sem)
                            <option value="{{ $sem->id }}">{{ $sem->label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Tanggal Penyaluran</label>
                    <input type="date" wire:model="rutinTanggal" class="input input-bordered w-full text-sm">
                </div>
            </div>

            <!-- Box Status Kas Kelompok Terpilih -->
            @if($selectedKelompok && $selectedKelompok->siswaPenerima)
                @php
                    $siswaAsuh = $selectedKelompok->siswaPenerima->siswa;
                @endphp
                <div class="p-4 bg-surface-hover/50 rounded-xl border border-border flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <div class="text-xs text-text-secondary">Siswa Penerima Manfaat Asuh:</div>
                        <div class="text-base font-bold text-text-main">{{ $siswaAsuh->nama }}</div>
                        <div class="text-xs text-text-secondary">{{ $siswaAsuh->rombel?->nama ?? '-' }} &bull; NISN: {{ $siswaAsuh->nisn ?? '-' }}</div>
                    </div>

                    <div class="flex gap-4">
                        <div class="text-right">
                            <div class="text-[11px] text-text-secondary">Target Semester:</div>
                            <div class="font-bold text-text-main">Rp {{ number_format($selectedKelompok->target_nominal, 0, ',', '.') }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-[11px] text-text-secondary">Saldo Kas Kelompok:</div>
                            <div class="text-lg font-black text-emerald-600 dark:text-emerald-400">
                                Rp {{ number_format($saldoKelompok, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabel Checklist Alokasi Komponen Bulan ke Bulan -->
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <h4 class="font-bold text-sm text-text-main">
                            Rincian Pembiayaan Siswa (Centang Komponen yang Dibayarkan):
                        </h4>
                        <span class="text-xs text-text-secondary">
                            Status lunas per bulan akan tercantum pada kwitansi penyaluran.
                        </span>
                    </div>

                    <div class="border border-border rounded-xl overflow-hidden">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-surface-hover text-text-secondary uppercase">
                                <tr>
                                    <th style="width: 40px;" class="py-2.5 px-3 text-center">Pilih</th>
                                    <th class="py-2.5 px-3">Komponen Biaya</th>
                                    <th class="py-2.5 px-3">Periode / Bulan</th>
                                    <th class="py-2.5 px-3 text-right">Nominal (Rp)</th>
                                    <th class="py-2.5 px-3">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                @forelse($rutinDetails as $index => $item)
                                    <tr class="hover:bg-surface-hover/30 transition {{ !empty($item['selected']) ? 'bg-primary/5' : 'opacity-60' }}">
                                        <td class="py-2.5 px-3 text-center">
                                            <input type="checkbox" wire:click="toggleRutinItem({{ $index }})" {{ !empty($item['selected']) ? 'checked' : '' }} class="checkbox checkbox-primary checkbox-xs">
                                        </td>
                                        <td class="py-2.5 px-3 font-semibold text-text-main">
                                            {{ $item['nama_komponen'] }}
                                            <span class="text-[10px] text-text-secondary uppercase">({{ $item['jenis_komponen'] }})</span>
                                        </td>
                                        <td class="py-2.5 px-3">
                                            @if($item['bulan'])
                                                <span class="px-2 py-0.5 rounded bg-surface border border-border font-medium">
                                                    Bulan {{ $item['bulan'] }}
                                                </span>
                                            @else
                                                <span class="text-text-secondary">1 Periode Semester</span>
                                            @endif
                                        </td>
                                        <td class="py-2.5 px-3 text-right font-bold text-text-main">
                                            Rp {{ number_format($item['nominal'], 0, ',', '.') }}
                                        </td>
                                        <td class="py-2.5 px-3 text-text-secondary">
                                            {{ $item['keterangan'] }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-4 text-center text-text-secondary">Siswa ini belum memiliki konfigurasi komponen pembiayaan. Atur di menu Siswa & Komponen.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Ringkasan Alokasi & Penanganan Surplus -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-surface-hover/40 rounded-xl border border-border">
                        <div>
                            <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Catatan Kwitansi</label>
                            <textarea wire:model="rutinCatatan" rows="2" class="textarea textarea-bordered w-full text-xs" placeholder="Catatan tambahan pada bukti kwitansi..."></textarea>
                        </div>

                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between">
                                <span class="text-text-secondary">Total Alokasi ke Siswa:</span>
                                <strong class="text-text-main text-sm">Rp {{ number_format($rutinTotalAlokasi, 0, ',', '.') }}</strong>
                            </div>

                            @if($rutinSurplus > 0)
                                <div class="flex justify-between p-2 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 rounded-lg text-amber-900 dark:text-amber-200">
                                    <span>Surplus Kelompok (Dipindahkan ke Kas Insidental):</span>
                                    <strong class="font-bold">+Rp {{ number_format($rutinSurplus, 0, ',', '.') }}</strong>
                                </div>
                            @endif

                            <div class="flex justify-between border-t border-border pt-2 font-bold">
                                <span>Total Kas Kelompok Terpotong:</span>
                                <span class="text-primary text-sm">Rp {{ number_format($rutinTotalAlokasi + $rutinSurplus, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-3">
                        <button wire:click="saveAlokasiRutin" class="btn btn-primary text-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Eksekusi Penyaluran 6 Bulanan & Terbitkan Kwitansi
                        </button>
                    </div>
                </div>
            @elseif($rutinKelompokId)
                <div class="p-6 text-center text-text-secondary bg-surface-hover/30 rounded-xl">
                    Kelompok ini belum memiliki siswa penerima beasiswa asuh aktif. Silakan hubungkan siswa di menu Siswa & Komponen.
                </div>
            @endif
        </div>
    @endif

    <!-- TAB 2: PENYALURAN INSIDENTAL FLEKSIBEL -->
    @if($activeTab === 'insidental')
        <div class="card border border-border rounded-2xl p-6 bg-surface shadow-xs space-y-6">
            <div>
                <h3 class="text-lg font-bold text-text-main">Penyaluran Fleksibel dari Kas Insidental</h3>
                <p class="text-xs text-text-secondary mt-0.5">
                    Digunakan untuk menyalurkan dana bantuan kepada siswa mana saja (baik siswa penerima beasiswa OTA yang defisit maupun siswa umum non-beasiswa) yang membutuhkan subsidi biaya sekolah.
                </p>
            </div>

            @if(session()->has('error_insidental'))
                <div class="p-3 bg-rose-50 text-rose-800 text-xs rounded-xl border border-rose-200">
                    {{ session('error_insidental') }}
                </div>
            @endif

            <div class="p-4 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 rounded-xl flex justify-between items-center">
                <div>
                    <div class="text-xs text-amber-800 dark:text-amber-200 font-semibold uppercase">Saldo Kas Insidental Siap Salur</div>
                    <div class="text-xs text-amber-700/80 dark:text-amber-300">Sumber dari donatur insidental & surplus kelompok OTA</div>
                </div>
                <div class="text-2xl font-black text-amber-600 dark:text-amber-400">
                    Rp {{ number_format($saldoInsidental, 0, ',', '.') }}
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Cari Siswa Bebas -->
                <div class="relative">
                    <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Cari & Pilih Siswa (Beasiswa / Non-Beasiswa)</label>
                    <input type="text" wire:model.live.debounce.250ms="insidentalSearchSiswa" 
                           placeholder="Ketik minimal 2 huruf nama siswa..." 
                           class="input input-bordered w-full text-sm">
                    
                    @if(!empty($insidentalSiswaOptions))
                        <div class="absolute z-20 left-0 right-0 mt-1 bg-surface border border-border rounded-xl shadow-lg max-h-48 overflow-y-auto divide-y divide-border">
                            @foreach($insidentalSiswaOptions as $opt)
                                <div wire:click="selectInsidentalSiswa({{ $opt['id'] }}, '{{ addslashes($opt['nama']) }}')" 
                                     class="p-2.5 hover:bg-surface-hover cursor-pointer text-xs flex justify-between items-center">
                                    <div>
                                        <span class="font-bold text-text-main">{{ $opt['nama'] }}</span>
                                        <span class="text-text-secondary ml-1">({{ $opt['rombel']['nama'] ?? 'Tanpa Rombel' }})</span>
                                    </div>
                                    <span class="text-[11px] text-text-secondary">NISN: {{ $opt['nisn'] ?? '-' }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    @error('insidentalSiswaId') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Tanggal Penyaluran</label>
                    <input type="date" wire:model="insidentalTanggal" class="input input-bordered w-full text-sm">
                </div>
            </div>

            <!-- Form Rincian Bantuan -->
            <div class="border border-border rounded-xl p-4 bg-surface-hover/30 space-y-3">
                <div class="flex justify-between items-center border-b border-border pb-2">
                    <h4 class="font-bold text-sm text-text-main">Rincian Pos Pembiayaan yang Dibantu</h4>
                    <button wire:click="addInsidentalRow" class="btn btn-xs btn-outline">+ Tambah Baris Bantuan</button>
                </div>

                <div class="space-y-2.5">
                    @foreach($insidentalDetails as $idx => $row)
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 items-center bg-surface p-2.5 rounded-lg border border-border text-xs">
                            <div class="sm:col-span-3">
                                <label class="block text-[10px] text-text-secondary mb-0.5">Jenis Komponen</label>
                                <select wire:model="insidentalDetails.{{ $idx }}.jenis_komponen" class="select select-bordered select-xs w-full">
                                    <option value="spp">SPP Bulanan</option>
                                    <option value="asrama">Asrama</option>
                                    <option value="dsp">Cicilan DSP</option>
                                    <option value="lainnya">Lainnya / Bantuan Khusus</option>
                                </select>
                            </div>

                            <div class="sm:col-span-4">
                                <label class="block text-[10px] text-text-secondary mb-0.5">Nama Keperluan Bantuan</label>
                                <input type="text" wire:model="insidentalDetails.{{ $idx }}.nama_komponen" class="input input-bordered input-xs w-full" placeholder="Misal: Bantuan Tunggakan SPP">
                            </div>

                            <div class="sm:col-span-4">
                                <label class="block text-[10px] text-text-secondary mb-0.5">Nominal Bantuan (Rp)</label>
                                <input type="number" wire:model="insidentalDetails.{{ $idx }}.nominal" wire:keyup="calculateInsidentalTotal" class="input input-bordered input-xs w-full font-bold" placeholder="0">
                            </div>

                            <div class="sm:col-span-1 text-center pt-3">
                                <button wire:click="removeInsidentalRow({{ $idx }})" class="text-rose-500 hover:text-rose-700 text-sm font-bold">&times;</button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-between items-center pt-2 font-bold text-xs">
                    <span class="text-text-secondary">Total Bantuan yang Disalurkan:</span>
                    <span class="text-amber-600 text-sm">Rp {{ number_format($insidentalTotal, 0, ',', '.') }}</span>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-text-secondary uppercase mb-1">Catatan / Alasan Bantuan</label>
                <textarea wire:model="insidentalCatatan" rows="2" class="textarea textarea-bordered w-full text-xs" placeholder="Keterangan kondisi bantuan insidental..."></textarea>
            </div>

            <div class="flex justify-end pt-2">
                <button wire:click="saveAlokasiInsidental" class="btn btn-primary text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Salurkan Dana Insidental & Cetak Kwitansi
                </button>
            </div>
        </div>
    @endif

    <!-- TAB 3: RIWAYAT PENYALURAN -->
    @if($activeTab === 'riwayat')
        <div class="card border border-border rounded-2xl p-6 bg-surface shadow-xs space-y-4">
            <h3 class="text-lg font-bold text-text-main">Riwayat Penyaluran Beasiswa OTA & Kwitansi</h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-border bg-surface-hover/50 text-xs font-semibold text-text-secondary uppercase">
                            <th class="py-3 px-4">No. Kwitansi & Tanggal</th>
                            <th class="py-3 px-4">Siswa Penerima</th>
                            <th class="py-3 px-4">Sumber Pembiayaan</th>
                            <th class="py-3 px-4">Rincian Komponen Terbayar</th>
                            <th class="py-3 px-4 text-right">Total Disalurkan</th>
                            <th class="py-3 px-4 text-center">Cetak Kwitansi</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse($alokasis as $a)
                            <tr class="hover:bg-surface-hover/30 transition">
                                <td class="py-3.5 px-4">
                                    <div class="font-mono text-xs font-bold text-text-main">{{ $a->nomor_kwitansi }}</div>
                                    <div class="text-xs text-text-secondary">{{ $a->tanggal_alokasi->translatedFormat('d F Y') }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-text-main">{{ $a->siswa->nama }}</div>
                                    <div class="text-xs text-text-secondary">{{ $a->siswa->rombel?->nama ?? '-' }} &bull; NISN: {{ $a->siswa->nisn ?? '-' }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($a->tipe_alokasi === 'rutin_semester')
                                        <span class="inline-block px-2 py-0.5 rounded text-xs font-semibold bg-emerald-50 text-emerald-700">
                                            {{ $a->kelompok?->nama_kelompok ?? 'Kelompok Asuh' }}
                                        </span>
                                    @else
                                        <span class="inline-block px-2 py-0.5 rounded text-xs font-semibold bg-amber-50 text-amber-700">
                                            Kas Insidental Fleksibel
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="flex flex-wrap gap-1 max-w-xs">
                                        @foreach($a->details as $dtl)
                                            <span class="inline-block px-1.5 py-0.5 bg-surface-hover border border-border rounded text-[11px]">
                                                {{ $dtl->nama_komponen }} {{ $dtl->bulan ? '('.$dtl->bulan.')' : '' }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-right font-black text-text-main">
                                    Rp {{ number_format($a->total_nominal, 0, ',', '.') }}
                                    @if($a->surplus_dialihkan > 0)
                                        <div class="text-[10px] text-amber-600 font-normal">
                                            Surplus +Rp {{ number_format($a->surplus_dialihkan, 0, ',', '.') }} ke Insidental
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <a href="{{ route('beasiswa-ota.cetak.kwitansi', $a->id) }}" target="_blank" 
                                       class="btn btn-xs btn-outline border-rose-200 text-rose-700 hover:bg-rose-50 flex items-center gap-1 mx-auto">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                        Cetak Kwitansi
                                    </a>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <button wire:confirm="Batalkan penyaluran ini? Saldo kas akan dikembalikan." 
                                            wire:click="deleteAlokasi({{ $a->id }})" 
                                            class="text-rose-500 hover:text-rose-700 p-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-text-secondary">Belum ada riwayat penyaluran beasiswa.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $alokasis->links() }}</div>
        </div>
    @endif
</div>
