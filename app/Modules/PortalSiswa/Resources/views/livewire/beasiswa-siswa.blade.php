<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-surface p-6 rounded-2xl border border-border shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-primary uppercase tracking-wider mb-1">
                <span>🎓 Portal Siswa</span>
                <span>&bull;</span>
                <span>Bantuan & Donatur OTA</span>
            </div>
            <h1 class="text-2xl font-black text-text-main tracking-tight">Beasiswa & Bantuan Pendidikan</h1>
            <p class="text-xs text-text-muted mt-1">
                Informasi penerimaan beasiswa, riwayat penyaluran dana pendidikan, donatur pembina Orang Tua Asuh (OTA), dan pengajuan bantuan.
            </p>
        </div>

        <div>
            <button type="button" wire:click="openFormModal" class="px-4 py-2.5 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs font-bold shadow-xs transition-all flex items-center gap-2">
                <span>✍️</span>
                <span>Ajukan Beasiswa Baru</span>
            </button>
        </div>
    </div>

    @if (session('success_beasiswa'))
        <div class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 p-4 text-xs font-medium text-emerald-800 dark:text-emerald-300 flex items-center gap-2 shadow-2xs">
            <span>✅</span>
            <span>{{ session('success_beasiswa') }}</span>
        </div>
    @endif

    <!-- KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="card p-5 bg-gradient-to-br from-primary/10 via-surface to-surface border border-primary/20">
            <span class="text-[10px] uppercase font-bold text-primary block">Total Beasiswa Diterima</span>
            <span class="text-2xl font-black text-primary font-mono mt-1 block">
                Rp {{ number_format($totalNominalDiterima, 0, ',', '.') }}
            </span>
            <span class="text-[10px] text-text-muted">Akumulasi seluruh penyaluran</span>
        </div>
        <div class="card p-5 border-l-4 {{ $siswa?->otaPenerimas->isNotEmpty() ? 'border-l-emerald-500' : 'border-l-border' }}">
            <span class="text-[10px] uppercase font-bold {{ $siswa?->otaPenerimas->isNotEmpty() ? 'text-emerald-600' : 'text-text-muted' }} block">Status Beasiswa OTA</span>
            <span class="text-xl font-black text-text-main mt-1 block">
                {{ $siswa?->otaPenerimas->isNotEmpty() ? 'Penerima Aktif' : 'Belum Terdaftar' }}
            </span>
            <span class="text-[10px] text-text-muted">
                {{ $siswa?->otaPenerimas->isNotEmpty() ? ($siswa->otaPenerimas->first()->kelompok?->nama_kelompok ?? ($siswa->otaPenerimas->first()->kelompok?->nama ?? 'Program OTA')) : 'Program Orang Tua Asuh' }}
            </span>
        </div>
        <div class="card p-5 border-l-4 border-l-blue-500">
            <span class="text-[10px] uppercase font-bold text-blue-600 block">Riwayat Penyaluran</span>
            <span class="text-2xl font-black text-blue-600 font-mono mt-1 block">{{ $riwayatAlokasi->count() }}</span>
            <span class="text-[10px] text-text-muted">Transaksi Pencairan Dana</span>
        </div>
        <div class="card p-5 border-l-4 border-l-amber-500">
            <span class="text-[10px] uppercase font-bold text-amber-600 block">Afirmasi Pemerintah</span>
            <div class="flex items-center gap-1.5 mt-1">
                @if ($siswa?->penerima_kip)
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-600 border border-amber-500/20">KIP</span>
                @endif
                @if ($siswa?->layak_pip)
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-500/10 text-blue-600 border border-blue-500/20">PIP</span>
                @endif
                @if (!$siswa?->penerima_kip && !$siswa?->layak_pip)
                    <span class="text-xs font-semibold text-text-muted">Mandiri / Reguler</span>
                @endif
            </div>
            <span class="text-[10px] text-text-muted mt-1 block">Bantuan Sosial Nasional</span>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="border-b border-border bg-surface p-2 rounded-2xl border shadow-xs">
        <nav class="flex flex-wrap items-center gap-1.5">
            <button type="button" wire:click="$set('activeTab', 'saya')" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $activeTab === 'saya' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
                <span>🤝 Beasiswa & Donatur OTA</span>
            </button>
            <button type="button" wire:click="$set('activeTab', 'pengajuan')" class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 {{ $activeTab === 'pengajuan' ? 'bg-primary text-white shadow-xs' : 'text-text-secondary hover:text-text-main hover:bg-surface-hover' }}">
                <span>📬 Riwayat Pengajuan Mandiri</span>
                @if ($riwayatPengajuan->isNotEmpty())
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $activeTab === 'pengajuan' ? 'bg-white/20 text-white' : 'bg-surface-raised text-text-muted border border-border' }}">
                        {{ $riwayatPengajuan->count() }}
                    </span>
                @endif
            </button>
        </nav>
    </div>

    <!-- Tab 1: Beasiswa Saya, OTA & Riwayat Penyaluran -->
    @if ($activeTab === 'saya')
        <div class="space-y-6">
            <!-- 1. Program Beasiswa Orang Tua Asuh (OTA) & Donatur Pembina -->
            <div class="card p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-border pb-3">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">🤝</span>
                        <div>
                            <h2 class="text-sm font-bold text-text-main">Program Beasiswa Orang Tua Asuh (OTA)</h2>
                            <p class="text-xs text-text-muted">Informasi kelompok dan para donatur pembina yang mendanai pembiayaan pendidikan Anda</p>
                        </div>
                    </div>
                </div>

                @if (!$siswa || $siswa->otaPenerimas->isEmpty())
                    <div class="p-8 text-center rounded-xl border border-dashed border-border text-xs text-text-muted space-y-2">
                        <span class="text-3xl block">🤝</span>
                        <p class="font-medium text-text-main">Anda saat ini belum terdaftar sebagai penerima manfaat Program Beasiswa OTA.</p>
                        <p class="text-[11px] text-text-muted">Jika membutuhkan bantuan biaya pendidikan, Anda dapat mengajukan permohonan melalui tombol di atas.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        @foreach ($siswa->otaPenerimas as $ota)
                            @php
                                $kelompok = $ota->kelompok;
                                $donaturs = $kelompok?->donaturs ?? collect();
                            @endphp
                            <div class="rounded-2xl border border-primary/30 bg-gradient-to-br from-primary/5 via-surface to-surface p-5 space-y-4 shadow-xs">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-primary text-white uppercase tracking-wider">
                                            Penerima Aktif OTA
                                        </span>
                                        <h3 class="text-base font-extrabold text-text-main mt-2">
                                            {{ $kelompok?->nama_kelompok ?? ($kelompok?->nama ?? 'Kelompok Donatur OTA') }}
                                        </h3>
                                        <p class="text-xs text-text-secondary mt-0.5">
                                            Kode Kelompok: <span class="font-mono font-bold text-text-main">{{ $kelompok?->kode_kelompok ?: ($ota->batch_kode ?: '-') }}</span>
                                        </p>
                                    </div>
                                    @if ($kelompok?->koordinator_nama)
                                        <div class="text-right">
                                            <span class="text-[10px] text-text-muted uppercase font-bold block">Koordinator</span>
                                            <span class="font-bold text-text-main text-xs block">{{ $kelompok->koordinator_nama }}</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Daftar Donatur Pembina -->
                                <div class="pt-3 border-t border-border/80">
                                    <div class="flex items-center gap-1.5 text-xs font-bold text-text-main mb-2">
                                        <span>👥</span>
                                        <span>Pemberi Donatur Pembina:</span>
                                    </div>

                                    @if ($donaturs->isEmpty())
                                        <div class="p-3 rounded-xl bg-surface border border-border text-xs text-text-muted">
                                            Informasi donatur kelompok pembina sedang dalam proses pemutakhiran.
                                        </div>
                                    @else
                                        <div class="space-y-1.5">
                                            @foreach ($donaturs as $donatur)
                                                <div class="flex items-center justify-between p-2.5 rounded-xl bg-surface border border-border text-xs">
                                                    <div class="flex items-center gap-2">
                                                        <div class="w-7 h-7 rounded-lg bg-primary-soft text-primary flex items-center justify-center font-bold text-xs">
                                                            {{ strtoupper(substr($donatur->nama, 0, 1)) }}
                                                        </div>
                                                        <div>
                                                            <div class="font-bold text-text-main">{{ $donatur->nama }}</div>
                                                            @if ($donatur->instansi)
                                                                <div class="text-[10px] text-text-muted">{{ $donatur->instansi }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-surface-raised text-text-secondary border border-border">
                                                        {{ ucfirst($donatur->tipe ?: 'Donatur Rutin') }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <!-- Komponen Biaya yang Ditanggung -->
                                @if ($ota->komponenBiayas && $ota->komponenBiayas->isNotEmpty())
                                    <div class="pt-3 border-t border-border/80">
                                        <span class="text-[11px] font-bold text-text-main block mb-1.5">Komponen Bantuan Biaya Sekolah:</span>
                                        <div class="flex flex-wrap gap-1.5">
                                            @foreach ($ota->komponenBiayas as $kb)
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-surface-raised border border-border text-text-secondary">
                                                    <span>{{ $kb->nama_komponen ?: ucfirst($kb->jenis_komponen ?? 'SPP') }}</span>
                                                    @if ($kb->nominal_per_bulan || $kb->total_target_semester)
                                                        <strong class="ml-1.5 text-primary font-mono">
                                                            Rp {{ number_format($kb->nominal_per_bulan ?: $kb->total_target_semester, 0, ',', '.') }}
                                                        </strong>
                                                    @endif
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- 2. Tabel Riwayat Penyaluran Beasiswa (Alokasi Dana) -->
            <div class="card p-6 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-border pb-3 gap-2">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">📜</span>
                        <div>
                            <h3 class="text-sm font-bold text-text-main">Riwayat Penyaluran & Pencairan Beasiswa</h3>
                            <p class="text-xs text-text-muted">Daftar alokasi dana bantuan pendidikan yang telah dicairkan beserta rincian pembiayaannya</p>
                        </div>
                    </div>
                    @if ($riwayatAlokasi->isNotEmpty())
                        <span class="text-xs font-bold text-primary font-mono bg-primary-soft px-3 py-1 rounded-lg">
                            Total Penyaluran: Rp {{ number_format($totalNominalDiterima, 0, ',', '.') }}
                        </span>
                    @endif
                </div>

                @if ($riwayatAlokasi->isEmpty())
                    <div class="p-8 text-center rounded-xl border border-dashed border-border text-xs text-text-muted">
                        Belum ada riwayat penyaluran dana beasiswa yang tercatat.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-surface-raised text-text-muted uppercase text-[10px] tracking-wider border-b border-border">
                                <tr>
                                    <th class="p-3 w-10 text-center">No</th>
                                    <th class="p-3">Tanggal & No. Kwitansi</th>
                                    <th class="p-3">Sumber / Donatur OTA</th>
                                    <th class="p-3">Semester & Tahun Ajaran</th>
                                    <th class="p-3">Rincian Komponen Biaya</th>
                                    <th class="p-3 text-right font-bold text-primary">Nominal Cair</th>
                                    <th class="p-3">Catatan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                @foreach ($riwayatAlokasi as $idx => $alo)
                                    @php
                                        $kelompokAlo = $alo->kelompok;
                                        $donatursAlo = $kelompokAlo?->donaturs ?? collect();
                                    @endphp
                                    <tr class="hover:bg-surface-hover/50 transition-colors">
                                        <td class="p-3 text-center text-text-muted font-mono">{{ $idx + 1 }}</td>
                                        <td class="p-3">
                                            <div class="font-bold text-text-main">
                                                {{ \Carbon\Carbon::parse($alo->tanggal_alokasi)->translatedFormat('d F Y') }}
                                            </div>
                                            <div class="font-mono text-[11px] text-primary font-bold mt-0.5">
                                                {{ $alo->nomor_kwitansi }}
                                            </div>
                                        </td>
                                        <td class="p-3">
                                            <div class="font-semibold text-text-main">
                                                {{ $kelompokAlo?->nama_kelompok ?? ($kelompokAlo?->nama ?? 'Donatur OTA') }}
                                            </div>
                                            @if ($donatursAlo->isNotEmpty())
                                                <div class="text-[10px] text-text-muted mt-0.5">
                                                    Pemberi: {{ $donatursAlo->pluck('nama')->join(', ') }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="p-3 text-text-secondary">
                                            <div>{{ $alo->tahunAjaran?->nama ?? 'T.A. ' . $alo->tahun_ajaran_id }}</div>
                                            <div class="text-[10px] text-text-muted">{{ $alo->semester?->nama ?? 'Semester ' . $alo->semester_id }}</div>
                                        </td>
                                        <td class="p-3">
                                            @if ($alo->details->isEmpty())
                                                <span class="text-text-muted">-</span>
                                            @else
                                                <div class="space-y-1">
                                                    @foreach ($alo->details as $det)
                                                        <div class="flex items-center justify-between gap-3 text-[11px]">
                                                            <span class="text-text-secondary">{{ $det->nama_komponen }}</span>
                                                            <span class="font-mono font-bold text-text-main">Rp {{ number_format($det->nominal, 0, ',', '.') }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>
                                        <td class="p-3 text-right font-mono font-black text-sm text-primary whitespace-nowrap">
                                            Rp {{ number_format($alo->total_nominal, 0, ',', '.') }}
                                        </td>
                                        <td class="p-3 text-text-muted text-xs leading-relaxed max-w-[180px]">
                                            {{ $alo->catatan ?: '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- 3. Bantuan Sosial Pemerintah & Kartu Afirmasi -->
            <div class="card p-6 space-y-4">
                <div class="flex items-center gap-2 border-b border-border pb-3">
                    <span class="text-xl">🇮🇩</span>
                    <div>
                        <h2 class="text-sm font-bold text-text-main">Bantuan Pendidikan Pemerintah & Rekening Siswa</h2>
                        <p class="text-xs text-text-muted">Data kepemilikan program afirmasi nasional (KIP, PIP, KPS/KKS) dan rekening penyaluran siswa</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 text-xs">
                    <div class="p-4 rounded-xl border border-border bg-surface-raised space-y-1">
                        <span class="text-text-muted text-[10px] uppercase font-bold block">Kartu Indonesia Pintar (KIP)</span>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="font-bold text-text-main text-sm">
                                {{ $siswa?->penerima_kip ? 'Ya, Memiliki' : 'Tidak' }}
                            </span>
                        </div>
                        @if ($siswa?->nomor_kip)
                            <div class="text-[11px] font-mono text-primary font-bold">No: {{ $siswa->nomor_kip }}</div>
                        @endif
                    </div>

                    <div class="p-4 rounded-xl border border-border bg-surface-raised space-y-1">
                        <span class="text-text-muted text-[10px] uppercase font-bold block">Program Indonesia Pintar (PIP)</span>
                        <div class="font-bold text-text-main text-sm mt-1">
                            {{ $siswa?->layak_pip ? 'Layak / Terdaftar' : 'Belum Terdaftar' }}
                        </div>
                        @if ($siswa?->alasan_layak_pip)
                            <div class="text-[11px] text-text-muted truncate" title="{{ $siswa->alasan_layak_pip }}">{{ $siswa->alasan_layak_pip }}</div>
                        @endif
                    </div>

                    <div class="p-4 rounded-xl border border-border bg-surface-raised space-y-1">
                        <span class="text-text-muted text-[10px] uppercase font-bold block">KPS / KKS (Perlindungan Sosial)</span>
                        <div class="font-bold text-text-main text-sm mt-1">
                            {{ $siswa?->penerima_kps ? 'Penerima KPS/KKS' : 'Tidak' }}
                        </div>
                        @if ($siswa?->nomor_kps)
                            <div class="text-[11px] font-mono text-primary font-bold">No: {{ $siswa->nomor_kps }}</div>
                        @endif
                    </div>

                    <div class="p-4 rounded-xl border border-border bg-surface-raised space-y-1">
                        <span class="text-text-muted text-[10px] uppercase font-bold block">Rekening Bank Siswa</span>
                        <div class="font-bold text-text-main text-sm mt-1">
                            {{ $siswa?->nama_bank ?: 'Belum Ada' }}
                        </div>
                        @if ($siswa?->nomor_rekening_bank)
                            <div class="text-[11px] font-mono text-text-main font-bold">No: {{ $siswa->nomor_rekening_bank }}</div>
                            <div class="text-[10px] text-text-muted truncate">a.n {{ $siswa->rekening_atas_nama ?: $siswa->nama }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Tab 2: Riwayat Pengajuan Beasiswa Mandiri -->
    @if ($activeTab === 'pengajuan')
        <div class="card p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-border pb-4">
                <div>
                    <h3 class="text-sm font-bold text-text-main">Riwayat Permohonan Beasiswa Mandiri</h3>
                    <p class="text-xs text-text-muted">Pantau status seleksi dan verifikasi berkas permohonan beasiswa Anda.</p>
                </div>
                <button type="button" wire:click="openFormModal" class="px-3 py-1.5 rounded-lg bg-primary hover:bg-primary-hover text-white text-xs font-bold transition-all flex items-center gap-1.5">
                    <span>➕ Ajukan Baru</span>
                </button>
            </div>

            @if ($riwayatPengajuan->isEmpty())
                <div class="p-8 text-center rounded-xl border border-dashed border-border text-xs text-text-muted">
                    Belum ada riwayat permohonan beasiswa yang diajukan.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-surface-raised text-text-muted uppercase text-[10px] tracking-wider border-b border-border">
                            <tr>
                                <th class="p-3">Tanggal</th>
                                <th class="p-3">Jenis Beasiswa</th>
                                <th class="p-3">Tanggungan / Gaji Ortu</th>
                                <th class="p-3">Alasan Permohonan</th>
                                <th class="p-3">Status</th>
                                <th class="p-3">Catatan Admin</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach ($riwayatPengajuan as $p)
                                <tr class="hover:bg-surface-hover/50 transition-colors">
                                    <td class="p-3 whitespace-nowrap text-text-secondary">
                                        {{ $p->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="p-3">
                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-primary-soft text-primary uppercase">
                                            {{ str_replace('_', ' ', $p->jenis_beasiswa) }}
                                        </span>
                                        @if ($p->nominal_pengajuan)
                                            <div class="text-[11px] font-mono text-text-muted mt-0.5">
                                                Diusulkan: Rp {{ number_format($p->nominal_pengajuan, 0, ',', '.') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="p-3 text-text-secondary">
                                        <div>Gaji: Rp {{ $p->penghasilan_orang_tua ? number_format($p->penghasilan_orang_tua, 0, ',', '.') : '-' }}</div>
                                        <div class="text-[10px] text-text-muted">Tanggungan: {{ $p->jumlah_tanggungan ? $p->jumlah_tanggungan . ' orang' : '-' }}</div>
                                    </td>
                                    <td class="p-3 max-w-[220px] truncate text-text-secondary" title="{{ $p->alasan }}">
                                        {{ $p->alasan }}
                                    </td>
                                    <td class="p-3 whitespace-nowrap">
                                        @if ($p->status === 'menunggu')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-600 border border-amber-500/20">
                                                ⏳ Menunggu Seleksi
                                            </span>
                                        @elseif ($p->status === 'disetujui')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-600 border border-emerald-500/20">
                                                ✅ Disetujui
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-600 border border-rose-500/20">
                                                ❌ Ditolak
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-text-muted">
                                        {{ $p->catatan_admin ?: '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif

    <!-- Modal Form Ajukan Beasiswa -->
    @if ($showFormModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
            <div class="bg-surface rounded-2xl border border-border shadow-xl max-w-lg w-full p-6 space-y-4 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-border pb-3">
                    <h3 class="text-sm font-bold text-text-main flex items-center gap-2">
                        <span>✍️ Formulir Pengajuan Permohonan Beasiswa</span>
                    </h3>
                    <button type="button" wire:click="closeFormModal" class="text-text-muted hover:text-text-main text-lg font-bold">&times;</button>
                </div>

                <form wire:submit.prevent="submitPengajuan" class="space-y-4 text-xs">
                    <div>
                        <label class="block font-medium text-text-secondary mb-1">Jenis Beasiswa yang Diajukan</label>
                        <select wire:model="jenisBeasiswa" class="w-full rounded-xl border border-border bg-surface px-3 py-2 text-xs text-text-main focus:border-primary focus:outline-hidden">
                            <option value="ota">Beasiswa Orang Tua Asuh (OTA)</option>
                            <option value="afirmasi_ekonomi">Beasiswa Afirmasi / Kurang Mampu</option>
                            <option value="prestasi">Beasiswa Prestasi Akademik/Non-Akademik</option>
                            <option value="lainnya">Bantuan Lainnya</option>
                        </select>
                        @error('jenisBeasiswa') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium text-text-secondary mb-1">Perkiraan Penghasilan Ortu (Rp/bulan)</label>
                            <input type="number" wire:model="penghasilanOrangTua" placeholder="Contoh: 1500000" class="w-full rounded-xl border border-border bg-surface px-3 py-2 text-xs text-text-main focus:border-primary focus:outline-hidden">
                            @error('penghasilanOrangTua') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-medium text-text-secondary mb-1">Jumlah Tanggungan Keluarga</label>
                            <input type="number" wire:model="jumlahTanggungan" placeholder="Contoh: 3" class="w-full rounded-xl border border-border bg-surface px-3 py-2 text-xs text-text-main focus:border-primary focus:outline-hidden">
                            @error('jumlahTanggungan') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block font-medium text-text-secondary mb-1">Nominal Bantuan yang Diharapkan (Opsional)</label>
                        <input type="number" wire:model="nominalPengajuan" placeholder="Contoh: 500000" class="w-full rounded-xl border border-border bg-surface px-3 py-2 text-xs text-text-main focus:border-primary focus:outline-hidden">
                        @error('nominalPengajuan') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-medium text-text-secondary mb-1">Alasan / Penjelasan Kondisi Ekonomi</label>
                        <textarea wire:model="alasan" rows="3" placeholder="Jelaskan mengapa Anda memerlukan bantuan beasiswa ini..." class="w-full rounded-xl border border-border bg-surface px-3 py-2 text-xs text-text-main focus:border-primary focus:outline-hidden"></textarea>
                        @error('alasan') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-medium text-text-secondary mb-1">Upload Dokumen Pendukung (SKTM / Slip Gaji / Foto Rumah, Maks: 3MB)</label>
                        <input type="file" wire:model="lampiranDokumen" accept=".jpg,.jpeg,.png,.pdf" class="w-full text-xs text-text-secondary file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-soft file:text-primary hover:file:bg-primary/20">
                        @error('lampiranDokumen') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-border">
                        <button type="button" wire:click="closeFormModal" class="px-4 py-2 rounded-xl border border-border hover:bg-surface-hover text-text-secondary font-bold text-xs">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-primary hover:bg-primary-hover text-white font-bold text-xs shadow-xs" wire:loading.attr="disabled">
                            <span wire:loading.remove>Kirim Permohonan</span>
                            <span wire:loading>Mengirim...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
