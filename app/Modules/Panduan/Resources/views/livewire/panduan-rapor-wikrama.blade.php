<div class="space-y-6">
    <!-- Header Utama Panduan Pengguna -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-border pb-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="p-2 rounded-xl bg-brand-500/10 text-brand-600 dark:text-brand-400 font-bold text-xl">📖</span>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-text-main">Panduan Rapor Wikrama (9 Minggu)</h1>
                    <p class="text-xs text-brand-600 dark:text-brand-400 font-medium">Buku Panduan Pengguna untuk Guru, Pembimbing Rayon, dan Wali Kelas</p>
                </div>
            </div>
            <p class="text-sm text-text-secondary mt-1">
                Panduan praktis pengisian nilai, pemahaman format rapor, rumus perhitungan, cara remedial, hingga prosedur cetak Rapor 9 Minggu di SMK Wikrama.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('rapor.wikrama') }}" target="_blank"
                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold bg-blue-600 text-white shadow-sm hover:bg-blue-700 transition">
                <span>Buka Menu Rapor</span>
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
            </a>
            <span class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold bg-surface border border-border text-text-secondary">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span>Panduan Praktis Pengguna</span>
            </span>
        </div>
    </div>

    <!-- Layout Knowledgebase (Sidebar Navigasi Kiri + Konten Kanan) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Sidebar Daftar Isi (Kiri - 4 Kolom di LG) -->
        <div class="lg:col-span-4 space-y-4">
            <div class="card p-4 bg-surface border border-border shadow-sm rounded-2xl space-y-3 sticky top-6">
                <div class="flex items-center justify-between px-2 pb-1 border-b border-border/60">
                    <span class="text-xs font-bold uppercase tracking-wider text-text-secondary">
                        Daftar Bab Panduan
                    </span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-brand-500/10 text-brand-600 font-bold">
                        8 Bab Mudah
                    </span>
                </div>

                <nav class="space-y-1">
                    <button type="button" wire:click="setSection('konsep')"
                        class="w-full text-left px-3 py-2.5 rounded-xl text-xs font-semibold transition flex items-center justify-between {{ $activeSection === 'konsep' ? 'bg-blue-600 text-white shadow-sm' : 'text-text-main hover:bg-surface-hover' }}">
                        <div class="flex items-center gap-2.5">
                            <span>💡</span>
                            <span>1. Konsep &amp; Siklus 9 Minggu</span>
                        </div>
                        <span class="text-[10px] {{ $activeSection === 'konsep' ? 'text-white/80' : 'text-text-secondary' }}">Tujuan</span>
                    </button>

                    <button type="button" wire:click="setSection('struktur')"
                        class="w-full text-left px-3 py-2.5 rounded-xl text-xs font-semibold transition flex items-center justify-between {{ $activeSection === 'struktur' ? 'bg-blue-600 text-white shadow-sm' : 'text-text-main hover:bg-surface-hover' }}">
                        <div class="flex items-center gap-2.5">
                            <span>📑</span>
                            <span>2. Struktur &amp; Anatomi PDF</span>
                        </div>
                        <span class="text-[10px] {{ $activeSection === 'struktur' ? 'text-white/80' : 'text-text-secondary' }}">Isi Lembar</span>
                    </button>

                    <button type="button" wire:click="setSection('perhitungan')"
                        class="w-full text-left px-3 py-2.5 rounded-xl text-xs font-semibold transition flex items-center justify-between {{ $activeSection === 'perhitungan' ? 'bg-blue-600 text-white shadow-sm' : 'text-text-main hover:bg-surface-hover' }}">
                        <div class="flex items-center gap-2.5">
                            <span>🔢</span>
                            <span>3. Rumus &amp; Perhitungan Nilai Rapor</span>
                        </div>
                        <span class="text-[10px] {{ $activeSection === 'perhitungan' ? 'text-white/80' : 'text-text-secondary' }}">Hitung Nilai</span>
                    </button>

                    <button type="button" wire:click="setSection('aliran-data')"
                        class="w-full text-left px-3 py-2.5 rounded-xl text-xs font-semibold transition flex items-center justify-between {{ $activeSection === 'aliran-data' ? 'bg-blue-600 text-white shadow-sm' : 'text-text-main hover:bg-surface-hover' }}">
                        <div class="flex items-center gap-2.5">
                            <span>🔄</span>
                            <span>4. Aliran Data &amp; Integrasi Multi-Sumber</span>
                        </div>
                        <span class="text-[10px] {{ $activeSection === 'aliran-data' ? 'text-white/80' : 'text-text-secondary' }}">Sumber Nilai</span>
                    </button>

                    <button type="button" wire:click="setSection('lokasi-pengaturan')"
                        class="w-full text-left px-3 py-2.5 rounded-xl text-xs font-semibold transition flex items-center justify-between {{ $activeSection === 'lokasi-pengaturan' ? 'bg-blue-600 text-white shadow-sm' : 'text-text-main hover:bg-surface-hover' }}">
                        <div class="flex items-center gap-2.5">
                            <span>⚙️</span>
                            <span>5. Lokasi Pengaturan &amp; Peta URL</span>
                        </div>
                        <span class="text-[10px] {{ $activeSection === 'lokasi-pengaturan' ? 'text-white/80' : 'text-text-secondary' }}">Peta Menu</span>
                    </button>

                    <button type="button" wire:click="setSection('troubleshooting')"
                        class="w-full text-left px-3 py-2.5 rounded-xl text-xs font-semibold transition flex items-center justify-between {{ $activeSection === 'troubleshooting' ? 'bg-blue-600 text-white shadow-sm' : 'text-text-main hover:bg-surface-hover' }}">
                        <div class="flex items-center gap-2.5">
                            <span>❓</span>
                            <span>6. FAQ &amp; Pemecahan Masalah</span>
                        </div>
                        <span class="text-[10px] {{ $activeSection === 'troubleshooting' ? 'text-white/80' : 'text-text-secondary' }}">Tanya Jawab</span>
                    </button>

                    <button type="button" wire:click="setSection('sop')"
                        class="w-full text-left px-3 py-2.5 rounded-xl text-xs font-semibold transition flex items-center justify-between {{ $activeSection === 'sop' ? 'bg-blue-600 text-white shadow-sm' : 'text-text-main hover:bg-surface-hover' }}">
                        <div class="flex items-center gap-2.5">
                            <span>👥</span>
                            <span>7. SOP &amp; Alur Kerja 6 Peran</span>
                        </div>
                        <span class="text-[10px] {{ $activeSection === 'sop' ? 'text-white/80' : 'text-text-secondary' }}">Langkah Kerja</span>
                    </button>

                    <button type="button" wire:click="setSection('glosarium')"
                        class="w-full text-left px-3 py-2.5 rounded-xl text-xs font-semibold transition flex items-center justify-between {{ $activeSection === 'glosarium' ? 'bg-blue-600 text-white shadow-sm' : 'text-text-main hover:bg-surface-hover' }}">
                        <div class="flex items-center gap-2.5">
                            <span>📖</span>
                            <span>8. Glosarium Kurikulum Merdeka</span>
                        </div>
                        <span class="text-[10px] {{ $activeSection === 'glosarium' ? 'text-white/80' : 'text-text-secondary' }}">Kamus Singkat</span>
                    </button>
                </nav>

                <!-- Kartu Ringkasan Cepat Pengguna -->
                <div class="p-3.5 rounded-xl bg-surface-hover border border-border/70 space-y-2 mt-4 text-xs">
                    <div class="font-bold text-text-main flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-brand-500"></span>
                        <span>Rumus Cepat Nilai Rapor:</span>
                    </div>
                    <div class="text-[11px] font-mono bg-surface p-2 rounded-lg border border-border text-brand-600 dark:text-brand-400 font-bold text-center">
                        NA = (50% TP) + (30% STS) + (20% NH)
                    </div>
                    <p class="text-[11px] text-text-secondary leading-relaxed">
                        Jika ada nilai di bawah <strong>75 (KKM)</strong>, nilai rapor otomatis bertanda strip (–) agar siswa segera remedial.
                    </p>
                </div>
            </div>
        </div>

        <!-- Panel Konten Panduan Pengguna (Kanan - 8 Kolom di LG) -->
        <div class="lg:col-span-8 space-y-6">

            <!-- ========================================== -->
            <!-- 1. KONSEP & SIKLUS 9 MINGGU -->
            <!-- ========================================== -->
            @if($activeSection === 'konsep')
            <div class="card p-6 bg-surface border border-border shadow-sm rounded-2xl space-y-6">
                <div class="flex items-center gap-3 border-b border-border pb-4">
                    <span class="p-2.5 rounded-2xl bg-brand-500/10 text-brand-600 text-2xl">💡</span>
                    <div>
                        <h2 class="text-xl font-bold text-text-main">1. Konsep &amp; Filosofi Rapor Wikrama</h2>
                        <p class="text-xs text-text-secondary">Mengapa ada Rapor 9 Minggu dan bagaimana sistem ini membantu perkembangan belajar siswa</p>
                    </div>
                </div>

                <div class="space-y-5 text-sm text-text-secondary">
                    <!-- Kartu Pengantar Mudah Dipahami -->
                    <div class="p-4 rounded-xl border border-brand-500/20 bg-brand-500/5 space-y-2">
                        <h3 class="text-sm font-bold text-brand-700 dark:text-brand-300 flex items-center gap-2">
                            <span>🎯</span>
                            <span>Apa Itu Rapor 9 Minggu?</span>
                        </h3>
                        <p class="text-xs text-text-secondary leading-relaxed">
                            <strong>Rapor Wikrama</strong> adalah laporan kemajuan belajar siswa yang dibagikan setiap <strong>9 minggu (tengah semester)</strong>. Berbeda dengan Rapor Pemerintah yang hanya dibagikan di akhir semester (18 minggu), Rapor Wikrama hadir sebagai <em>alat pantau dini</em> agar siswa, guru, dan orang tua tahu capaian belajar siswa lebih cepat tanpa harus menunggu akhir tahun.
                        </p>
                    </div>

                    <!-- 3 Manfaat Utama -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div class="p-3.5 rounded-xl border border-border bg-surface-hover space-y-1">
                            <div class="text-xs font-bold text-text-main flex items-center gap-1.5">
                                <span>🔍</span>
                                <span>Tahu Kelemahan Lebih Awal</span>
                            </div>
                            <p class="text-[11px] text-text-secondary leading-relaxed">
                                Jika ada materi (TP) yang belum tuntas, siswa langsung tahu di pekan ke-9 dan dapat segera memperbaikinya lewat bimbingan remedial.
                            </p>
                        </div>
                        <div class="p-3.5 rounded-xl border border-border bg-surface-hover space-y-1">
                            <div class="text-xs font-bold text-text-main flex items-center gap-1.5">
                                <span>🤝</span>
                                <span>Orang Tua Terinformasi</span>
                            </div>
                            <p class="text-[11px] text-text-secondary leading-relaxed">
                                Orang tua menerima laporan 4 kali dalam setahun, sehingga pendampingan belajar di rumah menjadi lebih tepat sasaran.
                            </p>
                        </div>
                        <div class="p-3.5 rounded-xl border border-border bg-surface-hover space-y-1">
                            <div class="text-xs font-bold text-text-main flex items-center gap-1.5">
                                <span>🌟</span>
                                <span>Penilaian Menyeluruh</span>
                            </div>
                            <p class="text-[11px] text-text-secondary leading-relaxed">
                                Tidak hanya nilai ujian teori, tetapi juga menilai keaktifan harian (adab), seni budaya, ekstrakurikuler, dan kehadiran.
                            </p>
                        </div>
                    </div>

                    <!-- Alur Waktu 9 Pekan yang Mudah Dimengerti -->
                    <div class="space-y-3 pt-2">
                        <h3 class="text-sm font-bold text-text-main flex items-center gap-2">
                            <span>⏱️</span>
                            <span>Jadwal Kegiatan Belajar Selama 9 Pekan</span>
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-2 text-xs">
                            <div class="p-3 rounded-xl border border-border bg-surface space-y-1">
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-blue-500/10 text-blue-600">Pekan 1 – 2</span>
                                <div class="font-bold text-text-main text-[11px]">Mulai Belajar &amp; Pengenalan Materi</div>
                                <p class="text-[10px] text-text-secondary leading-snug">Guru menyiapkan materi dan tujuan pembelajaran (TP) yang akan dipelajari siswa.</p>
                            </div>
                            <div class="p-3 rounded-xl border border-border bg-surface space-y-1">
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-600">Pekan 3 – 7</span>
                                <div class="font-bold text-text-main text-[11px]">Praktik &amp; Tugas Harian</div>
                                <p class="text-[10px] text-text-secondary leading-snug">Pembelajaran aktif di kelas/lab, tugas harian, penilaian adab, dan kegiatan ekskul.</p>
                            </div>
                            <div class="p-3 rounded-xl border border-border bg-surface space-y-1">
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-amber-500/10 text-amber-600">Pekan 8</span>
                                <div class="font-bold text-text-main text-[11px]">Ujian Tengah Semester (STS)</div>
                                <p class="text-[10px] text-text-secondary leading-snug">Pelaksanaan ujian sumatif tengah semester untuk mengukur daya serap materi siswa.</p>
                            </div>
                            <div class="p-3 rounded-xl border border-border bg-surface space-y-1">
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-purple-500/10 text-purple-600">Pekan 9</span>
                                <div class="font-bold text-text-main text-[11px]">Remedial &amp; Pembagian Rapor</div>
                                <p class="text-[10px] text-text-secondary leading-snug">Remedial bagi yang belum tuntas, penulisan catatan wali kelas, dan pembagian rapor.</p>
                            </div>
                        </div>
                    </div>

                    <!-- 4 Periode Rapor dalam 1 Tahun -->
                    <div class="space-y-3 pt-2">
                        <h3 class="text-sm font-bold text-text-main flex items-center gap-2">
                            <span>📅</span>
                            <span>Kapan Saja Rapor Wikrama Dibagikan?</span>
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            <div class="p-3.5 rounded-xl border border-border bg-surface space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-text-main">Rapor 9M1 (Tengah Ganjil)</span>
                                    <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-brand-500/10 text-brand-600 font-bold">Pekan 1-9 Ganjil</span>
                                </div>
                                <p class="text-[11px] text-text-secondary leading-relaxed">
                                    Laporan perkembangan pertama di awal tahun ajaran baru. Memantau adaptasi dan pemahaman awal materi siswa.
                                </p>
                            </div>

                            <div class="p-3.5 rounded-xl border border-border bg-surface space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-text-main">Rapor 9M2 (Akhir Ganjil)</span>
                                    <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-brand-500/10 text-brand-600 font-bold">Pekan 10-18 Ganjil</span>
                                </div>
                                <p class="text-[11px] text-text-secondary leading-relaxed">
                                    Laporan penutupan semester ganjil yang menjadi dasar pencetakan Rapor Semester Resmi Pemerintah.
                                </p>
                            </div>

                            <div class="p-3.5 rounded-xl border border-border bg-surface space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-text-main">Rapor 9M3 (Tengah Genap)</span>
                                    <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-brand-500/10 text-brand-600 font-bold">Pekan 1-9 Genap</span>
                                </div>
                                <p class="text-[11px] text-text-secondary leading-relaxed">
                                    Laporan pemantauan persiapan kenaikan kelas atau kelulusan di semester genap.
                                </p>
                            </div>

                            <div class="p-3.5 rounded-xl border border-border bg-surface space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-text-main">Rapor 9M4 (Akhir Genap)</span>
                                    <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-brand-500/10 text-brand-600 font-bold">Pekan 10-18 Genap</span>
                                </div>
                                <p class="text-[11px] text-text-secondary leading-relaxed">
                                    Laporan penentu kenaikan kelas fase selanjutnya atau kelulusan siswa tingkat akhir.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- 3 Status Hasil Belajar Siswa -->
                    <div class="space-y-3 pt-2">
                        <h3 class="text-sm font-bold text-text-main flex items-center gap-2">
                            <span>🚦</span>
                            <span>3 Arti Status Nilai Siswa di Rapor</span>
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="p-3.5 rounded-xl border border-emerald-500/30 bg-emerald-50/50 dark:bg-emerald-950/20 space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-emerald-700 dark:text-emerald-400">1. KOMPETEN</span>
                                    <span>🟢</span>
                                </div>
                                <p class="text-[11px] text-text-secondary leading-snug">
                                    Semua nilai tugas, materi, dan ujian sudah mencapai minimal <strong>75</strong>. Nilai angka dan predikat muncul lengkap.
                                </p>
                            </div>

                            <div class="p-3.5 rounded-xl border border-rose-500/30 bg-rose-50/50 dark:bg-rose-950/20 space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-rose-700 dark:text-rose-400">2. BELUM KOMPETEN</span>
                                    <span>🔴</span>
                                </div>
                                <p class="text-[11px] text-text-secondary leading-snug">
                                    Ada nilai materi/ujian di bawah <strong>75</strong>. Nilai muncul tanda strip (–). Siswa <strong>wajib segera remedial</strong> ke guru mapel.
                                </p>
                            </div>

                            <div class="p-3.5 rounded-xl border border-amber-500/30 bg-amber-50/50 dark:bg-amber-950/20 space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-amber-700 dark:text-amber-400">3. BELUM LENGKAP</span>
                                    <span>🟡</span>
                                </div>
                                <p class="text-[11px] text-text-secondary leading-snug">
                                    Ada nilai tugas atau ujian yang belum diinput guru ke sistem. Nilai muncul tanda strip (–) sampai guru melengkapi nilai.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- ========================================== -->
            <!-- 2. STRUKTUR & ANATOMI DOKUMEN PDF -->
            <!-- ========================================== -->
            @if($activeSection === 'struktur')
            <div class="card p-6 bg-surface border border-border shadow-sm rounded-2xl space-y-6">
                <div class="flex items-center gap-3 border-b border-border pb-4">
                    <span class="p-2.5 rounded-2xl bg-brand-500/10 text-brand-600 text-2xl">📑</span>
                    <div>
                        <h2 class="text-xl font-bold text-text-main">2. Struktur &amp; Anatomi Dokumen PDF</h2>
                        <p class="text-xs text-text-secondary">Penjelasan isi lembar cetak rapor dari kop surat, identitas siswa, hingga tabel nilai A s/d D</p>
                    </div>
                </div>

                <div class="space-y-5 text-sm text-text-secondary">
                    <p class="leading-relaxed">
                        Saat dicetak atau diunduh, Rapor Wikrama berupa lembaran PDF resmi siap cetak ukuran <strong>A4 Portrait</strong> dengan huruf baku <em>Times New Roman</em> yang rapi. Lembar rapor terbagi menjadi 5 bagian utama:
                    </p>

                    <!-- Bagian Kop & Identitas -->
                    <div class="p-4 rounded-xl border border-border bg-surface-hover space-y-3">
                        <div class="font-bold text-text-main text-xs flex items-center justify-between">
                            <span class="flex items-center gap-2">
                                <span>🏫</span>
                                <span>Bagian Atas: Kop Surat Sekolah &amp; Identitas Siswa</span>
                            </span>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-brand-500/10 text-brand-600 font-bold">Identitas Resmi</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                            <div class="space-y-1">
                                <div><strong>Kop Surat:</strong> Memuat logo Yayasan Prawitama dan logo SMK Wikrama Garut yang proporsional dan jelas.</div>
                                <div><strong>Judul:</strong> <code>LAPORAN HASIL BELAJAR PESERTA DIDIK (RAPOR 9 MINGGU)</code>.</div>
                            </div>
                            <div class="space-y-1 bg-surface p-2.5 rounded-lg border border-border text-[11px]">
                                <div>&bull; <strong>Nama Siswa:</strong> Nama lengkap siswa</div>
                                <div>&bull; <strong>Rombel &amp; Rayon:</strong> Kelas kejuruan &amp; kelompok bimbingan</div>
                                <div>&bull; <strong>NISN / NIS:</strong> Nomor induk siswa nasional &amp; sekolah</div>
                                <div>&bull; <strong>Jurusan / Konsentrasi:</strong> Keahlian siswa (misal: PPLG, TJKT)</div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel A: Akademik -->
                    <div class="p-4 rounded-xl border border-border bg-surface space-y-3">
                        <div class="font-bold text-text-main text-xs flex items-center justify-between border-b border-border pb-2">
                            <span class="flex items-center gap-2">
                                <span>📊</span>
                                <span>A. Nilai Akademik (Tabel Nilai Pelajaran &amp; Materi TP)</span>
                            </span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-600">Paling Utama</span>
                        </div>
                        <p class="text-xs text-text-secondary leading-relaxed">
                            Menampilkan seluruh mata pelajaran yang dipelajari. Di bawah nama setiap mata pelajaran, terdapat rincian materi atau <strong>Tujuan Pembelajaran (TP)</strong> yang telah dipelajari siswa:
                        </p>

                        <!-- Tabel Contoh Sederhana -->
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs border border-border rounded-lg overflow-hidden font-sans">
                                <thead class="bg-surface-hover border-b border-border text-text-main font-bold">
                                    <tr>
                                        <th class="p-2 border-r border-border text-center w-8">No</th>
                                        <th class="p-2 border-r border-border text-left">Mata Pelajaran &amp; Materi (TP)</th>
                                        <th class="p-2 border-r border-border text-center w-12">KKM</th>
                                        <th class="p-2 border-r border-border text-center w-12">NH</th>
                                        <th class="p-2 border-r border-border text-center w-14">STS</th>
                                        <th class="p-2 border-r border-border text-center w-14">NA</th>
                                        <th class="p-2 border-r border-border text-center w-24">Predikat</th>
                                        <th class="p-2 text-left">Kalimat Capaian</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-border text-[11px]">
                                    <tr class="bg-surface">
                                        <td class="p-2 text-center border-r border-border font-bold">1</td>
                                        <td class="p-2 border-r border-border font-bold text-text-main">Bahasa Indonesia</td>
                                        <td class="p-2 text-center border-r border-border font-mono">75</td>
                                        <td class="p-2 text-center border-r border-border font-mono font-bold">90</td>
                                        <td class="p-2 text-center border-r border-border font-mono font-bold">88</td>
                                        <td class="p-2 text-center border-r border-border font-mono font-bold text-brand-600">89</td>
                                        <td class="p-2 text-center border-r border-border font-bold text-blue-600">Cakap</td>
                                        <td class="p-2 text-[10px] leading-snug">Menunjukkan penguasaan yang baik dalam menyusun teks laporan hasil observasi...</td>
                                    </tr>
                                    <tr class="bg-surface text-text-secondary text-[10px]">
                                        <td class="p-1 border-r border-border"></td>
                                        <td class="p-1 pl-4 border-r border-border">&bull; TP 1: Menyusun teks laporan observasi</td>
                                        <td class="p-1 border-r border-border"></td>
                                        <td class="p-1 text-center border-r border-border font-mono">90</td>
                                        <td class="p-1 border-r border-border"></td>
                                        <td class="p-1 border-r border-border"></td>
                                        <td class="p-1 text-center border-r border-border font-semibold text-emerald-600">Tuntas</td>
                                        <td class="p-1"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-[11px] text-text-secondary flex flex-wrap gap-3 pt-1">
                            <span><strong>NH:</strong> Nilai Harian (Keaktifan/Tugas)</span>
                            <span><strong>STS:</strong> Ujian Tengah Semester</span>
                            <span><strong>NA:</strong> Nilai Akhir Rapor</span>
                        </div>
                    </div>

                    <!-- Tabel B, C, D Ringkas -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 rounded-xl border border-border bg-surface space-y-2">
                            <div class="font-bold text-text-main text-xs flex items-center gap-1.5 border-b border-border pb-1.5">
                                <span>⚽</span>
                                <span>B. Kegiatan Ekstrakurikuler</span>
                            </div>
                            <p class="text-xs text-text-secondary leading-relaxed">
                                Berisi nama ekskul yang diikuti (Pramuka/Peminatan), nilai predikat huruf (Sangat Baik / Baik / Cukup / Kurang), dan catatan perkembangan dari pelatih ekskul.
                            </p>
                        </div>

                        <div class="p-4 rounded-xl border border-border bg-surface space-y-2">
                            <div class="font-bold text-text-main text-xs flex items-center gap-1.5 border-b border-border pb-1.5">
                                <span>📅</span>
                                <span>C. Ketidakhadiran (Presensi)</span>
                            </div>
                            <p class="text-xs text-text-secondary leading-relaxed">
                                Menampilkan rekap jumlah hari <strong>Sakit</strong>, <strong>Izin</strong>, dan <strong>Alpa</strong> selama 9 pekan, lengkap dengan tanggal ketidakhadirannya agar transparan bagi orang tua.
                            </p>
                        </div>
                    </div>

                    <!-- Tabel D & Lembar Tanda Tangan -->
                    <div class="p-4 rounded-xl border border-border bg-surface space-y-2">
                        <div class="font-bold text-text-main text-xs flex items-center gap-1.5 border-b border-border pb-1.5">
                            <span>✍️</span>
                            <span>D. Catatan Pembimbing Rayon &amp; Tanda Tangan</span>
                        </div>
                        <p class="text-xs text-text-secondary leading-relaxed">
                            Berisi pesan personal dari Wali Kelas/Pembimbing Rayon tentang motivasi belajar dan sikap siswa, serta kolom tanda tangan <strong>Orang Tua</strong>, <strong>Pembimbing Rayon</strong>, dan <strong>Kepala Sekolah</strong>.
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- ========================================== -->
            <!-- 3. RUMUS & PERHITUNGAN NILAI -->
            <!-- ========================================== -->
            @if($activeSection === 'perhitungan')
            <div class="card p-6 bg-surface border border-border shadow-sm rounded-2xl space-y-6">
                <div class="flex items-center gap-3 border-b border-border pb-4">
                    <span class="p-2.5 rounded-2xl bg-brand-500/10 text-brand-600 text-2xl">🔢</span>
                    <div>
                        <h2 class="text-xl font-bold text-text-main">3. Rumus &amp; Perhitungan Nilai Rapor</h2>
                        <p class="text-xs text-text-secondary">Bagaimana sistem menghitung Nilai Akhir (NA), predikat, dan menyusun kalimat narasi otomatis</p>
                    </div>
                </div>

                <div class="space-y-5 text-sm text-text-secondary">
                    <!-- Kotak Rumus Ramah Pengguna -->
                    <div class="p-5 rounded-2xl border border-brand-500/20 bg-brand-500/5 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-brand-700 dark:text-brand-300 text-sm">Rumus Nilai Akhir (NA) Rapor 9 Minggu</span>
                            <span class="text-xs px-2.5 py-0.5 rounded-full bg-surface border border-border text-brand-600 font-bold">Standar SIWARAGA (PenilaianScoreCalculator)</span>
                        </div>
                        
                        <div class="p-4 bg-surface rounded-xl border border-border text-center shadow-sm">
                            <div class="font-mono text-base font-bold text-text-main">
                                Nilai Akhir = (50% &times; Rata TP) + (30% &times; Nilai STS) + (20% &times; Nilai Harian)
                            </div>
                            <p class="text-xs text-text-secondary mt-1.5">
                                Nilai akhir dihitung otomatis oleh sistem SIWARAGA berdasarkan pembobotan resmi sekolah.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs pt-1">
                            <div class="p-2.5 bg-surface rounded-lg border border-border text-center">
                                <span class="font-bold text-text-main">50% Tugas &amp; Praktik (TP)</span>
                                <div class="text-[11px] text-text-secondary mt-0.5">Rata-rata seluruh materi yang dipelajari selama 9 pekan.</div>
                            </div>
                            <div class="p-2.5 bg-surface rounded-lg border border-border text-center">
                                <span class="font-bold text-text-main">30% Ujian Tengah Semester (STS)</span>
                                <div class="text-[11px] text-text-secondary mt-0.5">Skor tes ujian sumatif pekan ke-8.</div>
                            </div>
                            <div class="p-2.5 bg-surface rounded-lg border border-border text-center">
                                <span class="font-bold text-text-main">20% Nilai Harian (NH)</span>
                                <div class="text-[11px] text-text-secondary mt-0.5">Penilaian sikap, adab belajar, dan keaktifan siswa.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Kotak Perhatian: Mengapa Nilai Strip? -->
                    <div class="p-4 rounded-xl border border-rose-500/30 bg-rose-50/50 dark:bg-rose-950/20 space-y-2">
                        <div class="font-bold text-rose-700 dark:text-rose-400 text-xs flex items-center gap-2">
                            <span class="text-base">🛑</span>
                            <span>Penting: Mengapa Nilai Siswa Menjadi Tanda Strip (–)?</span>
                        </div>
                        <p class="text-xs text-text-secondary leading-relaxed">
                            Di SMK Wikrama berlaku <strong>Prinsip Ketuntasan Belajar</strong>:
                        </p>
                        <ul class="text-xs text-text-secondary space-y-1 list-disc list-inside">
                            <li>Batas nilai tuntas minimal (KKM) adalah <strong>75</strong>.</li>
                            <li>Jika ada <strong>satu saja materi (TP) atau nilai ujian di bawah 75</strong>, Nilai Akhir sengaja <strong>dikunci menjadi tanda strip (–)</strong> dengan status <em>Belum Kompeten</em>.</li>
                            <li><strong>Tujuannya:</strong> Memastikan tidak ada materi penting yang terlewat. Siswa wajib segera mengikuti <strong>remedial</strong> dengan guru mapel sebelum pekan ke-9 berakhir.</li>
                            <li>Begitu guru mengupdate nilai remedial menjadi &ge; 75, Nilai Akhir angka akan otomatis muncul kembali.</li>
                        </ul>
                    </div>

                    <!-- Contoh Perhitungan Kasus Siswa -->
                    <div class="p-4 rounded-xl border border-border bg-surface space-y-2">
                        <div class="font-bold text-text-main text-xs uppercase tracking-wider">
                            Contoh Kasus Nyata:
                        </div>
                        <div class="text-xs space-y-1.5 leading-relaxed">
                            <p>Siswa bernama <strong>Ahmad</strong> pada mata pelajaran Informatika memperoleh data nilai:</p>
                            <div class="p-3 bg-surface-hover rounded-lg border border-border font-mono text-[11px] space-y-1">
                                <div>&bull; Tugas TP 1 = 80, Tugas TP 2 = 90 &rarr; <strong>Rata-rata TP = 85</strong></div>
                                <div>&bull; Nilai Ujian STS = <strong>90</strong></div>
                                <div>&bull; Nilai Harian (NH) = <strong>80</strong></div>
                                <div class="pt-1 text-brand-600 font-bold border-t border-border mt-1">
                                    Hitungan NA = (85 &times; 50%) + (90 &times; 30%) + (80 &times; 20%) = 42.5 + 27 + 16 = <strong>85.5 (Dibulatkan menjadi 86)</strong>
                                </div>
                            </div>
                            <p class="text-[11px] text-text-secondary">
                                Karena semua nilai di atas 75, status Ahmad adalah <strong>Kompeten</strong> dan mendapat predikat <strong>Cakap (B)</strong>.
                            </p>
                        </div>
                    </div>

                    <!-- Tabel Predikat Nilai -->
                    <div class="space-y-2">
                        <span class="font-bold text-text-main text-xs uppercase tracking-wider">
                            Tabel Arti Predikat Kurikulum Merdeka
                        </span>
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs border border-border rounded-xl overflow-hidden font-sans">
                                <thead class="bg-surface-hover text-text-main font-bold">
                                    <tr>
                                        <th class="p-2.5 border-r border-border text-center">Rentang Nilai</th>
                                        <th class="p-2.5 border-r border-border text-center">Predikat</th>
                                        <th class="p-2.5 text-left">Arti &amp; Kalimat di Rapor</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-border text-[11px]">
                                    <tr class="bg-surface">
                                        <td class="p-2 text-center border-r border-border font-mono font-bold">94 – 100</td>
                                        <td class="p-2 text-center border-r border-border font-bold text-emerald-600">Mahir (A)</td>
                                        <td class="p-2">Sangat menguasai materi pembelajaran dengan istimewa.</td>
                                    </tr>
                                    <tr class="bg-surface">
                                        <td class="p-2 text-center border-r border-border font-mono font-bold">88 – 93</td>
                                        <td class="p-2 text-center border-r border-border font-bold text-blue-600">Cakap (B)</td>
                                        <td class="p-2">Menguasai materi pembelajaran dengan baik dan tuntas.</td>
                                    </tr>
                                    <tr class="bg-surface">
                                        <td class="p-2 text-center border-r border-border font-mono font-bold">81 – 87</td>
                                        <td class="p-2 text-center border-r border-border font-bold text-cyan-600">Layak (C)</td>
                                        <td class="p-2">Cukup menguasai materi standar yang diajarkan.</td>
                                    </tr>
                                    <tr class="bg-surface">
                                        <td class="p-2 text-center border-r border-border font-mono font-bold">75 – 80</td>
                                        <td class="p-2 text-center border-r border-border font-bold text-amber-600">Mulai Berkembang</td>
                                        <td class="p-2">Tuntas standar minimal, namun perlu latihan lebih teratur.</td>
                                    </tr>
                                    <tr class="bg-rose-50/40 dark:bg-rose-950/20 text-rose-700 dark:text-rose-400">
                                        <td class="p-2 text-center border-r border-border font-mono font-bold">&lt; 75</td>
                                        <td class="p-2 text-center border-r border-border font-bold">Belum Kompeten</td>
                                        <td class="p-2">Belum tuntas. Wajib menghubungi guru untuk remedial.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- ========================================== -->
            <!-- 4. ALIRAN DATA & INTEGRASI MULTI-SUMBER -->
            <!-- ========================================== -->
            @if($activeSection === 'aliran-data')
            <div class="card p-6 bg-surface border border-border shadow-sm rounded-2xl space-y-6">
                <div class="flex items-center gap-3 border-b border-border pb-4">
                    <span class="p-2.5 rounded-2xl bg-brand-500/10 text-brand-600 text-2xl">🔄</span>
                    <div>
                        <h2 class="text-xl font-bold text-text-main">4. Aliran Data &amp; Integrasi Multi-Sumber</h2>
                        <p class="text-xs text-text-secondary">Dari mana saja data nilai rapor dikumpulkan oleh sistem SIWARAGA secara otomatis</p>
                    </div>
                </div>

                <div class="space-y-5 text-sm text-text-secondary">
                    <p class="leading-relaxed">
                        Anda tidak perlu menghitung atau memindahkan nilai secara manual. Sistem SIWARAGA bekerja otomatis menghimpun data dari <strong>6 sumber pengisian guru dan staf sekolah</strong>:
                    </p>

                    <!-- 6 Sumber Data Non-Teknis -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div class="p-3.5 rounded-xl border border-border bg-surface space-y-1.5">
                            <div class="font-bold text-text-main text-xs flex items-center gap-2">
                                <span class="p-1 rounded bg-blue-500/10 text-blue-600">1</span>
                                <span>Guru Mata Pelajaran Reguler</span>
                            </div>
                            <p class="text-[11px] text-text-secondary leading-relaxed">
                                Guru mengisi nilai tugas/praktik (TP), ujian tengah semester (STS), dan nilai harian di menu <strong>Penilaian</strong>.
                            </p>
                        </div>

                        <div class="p-3.5 rounded-xl border border-border bg-surface space-y-1.5">
                            <div class="font-bold text-text-main text-xs flex items-center gap-2">
                                <span class="p-1 rounded bg-purple-500/10 text-purple-600">2</span>
                                <span>Guru Seni Budaya</span>
                            </div>
                            <p class="text-[11px] text-text-secondary leading-relaxed">
                                Siswa memilih 1 dari 4 cabang seni (Musik, Rupa, Tari, Teater). Di rapor, judul pelajaran otomatis mengikuti cabang seni yang diambil siswa.
                            </p>
                        </div>

                        <div class="p-3.5 rounded-xl border border-border bg-surface space-y-1.5">
                            <div class="font-bold text-text-main text-xs flex items-center gap-2">
                                <span class="p-1 rounded bg-emerald-500/10 text-emerald-600">3</span>
                                <span>Guru Mata Pelajaran Pilihan (Mapil)</span>
                            </div>
                            <p class="text-[11px] text-text-secondary leading-relaxed">
                                Khusus siswa kelas XI dan XII, nilai mapel pilihan yang diambil otomatis masuk ke kelompok C pada lembar nilai rapor.
                            </p>
                        </div>

                        <div class="p-3.5 rounded-xl border border-border bg-surface space-y-1.5">
                            <div class="font-bold text-text-main text-xs flex items-center gap-2">
                                <span class="p-1 rounded bg-amber-500/10 text-amber-600">4</span>
                                <span>Pembina Ekstrakurikuler</span>
                            </div>
                            <p class="text-[11px] text-text-secondary leading-relaxed">
                                Pembina mengisi keaktifan dan catatan perkembangan siswa di menu <strong>Ekstrakurikuler</strong> untuk periode bersangkutan.
                            </p>
                        </div>

                        <div class="p-3.5 rounded-xl border border-border bg-surface space-y-1.5">
                            <div class="font-bold text-text-main text-xs flex items-center gap-2">
                                <span class="p-1 rounded bg-rose-500/10 text-rose-600">5</span>
                                <span>Absensi Harian Guru Piket &amp; Wali Kelas</span>
                            </div>
                            <p class="text-[11px] text-text-secondary leading-relaxed">
                                Rekap Sakit, Izin, dan Alpa otomatis disaring sesuai rentang tanggal 9 pekan tersebut, lengkap dengan rincian tanggalnya.
                            </p>
                        </div>

                        <div class="p-3.5 rounded-xl border border-border bg-surface space-y-1.5">
                            <div class="font-bold text-text-main text-xs flex items-center gap-2">
                                <span class="p-1 rounded bg-teal-500/10 text-teal-600">6</span>
                                <span>Pembimbing Rayon / Wali Kelas</span>
                            </div>
                            <p class="text-[11px] text-text-secondary leading-relaxed">
                                Menuliskan catatan pembinaan karakter dan motivasi belajar personal untuk setiap siswa asuhannya.
                            </p>
                        </div>
                    </div>

                    <!-- Kotak Keamanan Riwayat Nilai -->
                    <div class="p-4 rounded-xl border border-emerald-500/30 bg-emerald-50/50 dark:bg-emerald-950/20 space-y-1.5">
                        <div class="font-bold text-emerald-800 dark:text-emerald-300 text-xs flex items-center gap-2">
                            <span>🔒</span>
                            <span>Jaminan Keamanan: Rapor yang Sudah Dibagikan Tidak Akan Tertimpa</span>
                        </div>
                        <p class="text-xs text-text-secondary leading-relaxed">
                            Anda tidak perlu khawatir nilai rapor lama rusak saat memasuki semester baru. Ketika Anda mencetak rapor periode <strong>9M1</strong>, sistem hanya memanggil arsip resmi saat periode 9M1 ditutup. Nilai semester genap tidak akan merusak riwayat rapor ganjil yang sudah diterima orang tua.
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- ========================================== -->
            <!-- 5. LOKASI PENGATURAN & PETA URL -->
            <!-- ========================================== -->
            @if($activeSection === 'lokasi-pengaturan')
            <div class="card p-6 bg-surface border border-border shadow-sm rounded-2xl space-y-6">
                <div class="flex items-center gap-3 border-b border-border pb-4">
                    <span class="p-2.5 rounded-2xl bg-brand-500/10 text-brand-600 text-2xl">⚙️</span>
                    <div>
                        <h2 class="text-xl font-bold text-text-main">5. Lokasi Pengaturan &amp; Peta URL</h2>
                        <p class="text-xs text-text-secondary">Peta menu dan tombol akses langsung menuju halaman kerja pengisian nilai</p>
                    </div>
                </div>

                <div class="space-y-4 text-sm text-text-secondary">
                    <p class="leading-relaxed">
                        Klik tombol <strong>"Buka Halaman"</strong> di bawah ini untuk langsung menuju menu yang sesuai dengan tugas Anda:
                    </p>

                    <div class="space-y-3">
                        <!-- Menu 1: Guru Mapel -->
                        <div class="p-4 rounded-xl border border-border bg-surface space-y-2 hover:border-brand-500/40 transition">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                <div class="font-bold text-text-main text-xs flex items-center gap-2">
                                    <span class="text-base">📝</span>
                                    <span>Menu Input Nilai Mapel (Guru Mata Pelajaran)</span>
                                </div>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-600">Guru Mapel</span>
                            </div>
                            <p class="text-xs text-text-secondary leading-relaxed">
                                Tempat membuat Tujuan Pembelajaran (TP), menginput nilai formatif/tugas, mengisi nilai ujian STS, memeriksa siswa yang butuh remedial, dan mengubah kalimat narasi capaian.
                            </p>
                            <div class="pt-1 flex items-center justify-between text-xs font-mono">
                                <span class="text-text-secondary">Menu: Penilaian &rarr; Input Nilai</span>
                                <a href="{{ url('/penilaian') }}" target="_blank" class="px-3 py-1 rounded-lg bg-emerald-600 text-white font-semibold hover:bg-emerald-700 transition">
                                    Buka Halaman Penilaian &rarr;
                                </a>
                            </div>
                        </div>

                        <!-- Menu 2: Wali Kelas -->
                        <div class="p-4 rounded-xl border border-border bg-surface space-y-2 hover:border-brand-500/40 transition">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                <div class="font-bold text-text-main text-xs flex items-center gap-2">
                                    <span class="text-base">💬</span>
                                    <span>Menu Catatan Pembimbing Rayon / Wali Kelas</span>
                                </div>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-teal-500/10 text-teal-600">Wali Kelas / Rayon</span>
                            </div>
                            <p class="text-xs text-text-secondary leading-relaxed">
                                Tempat menuliskan catatan pembinaan karakter, saran belajar, dan motivasi untuk setiap siswa asuhan pada periode rapor yang dipilih (Tabel D).
                            </p>
                            <div class="pt-1 flex items-center justify-between text-xs font-mono">
                                <span class="text-text-secondary">Menu: Rapor &rarr; Catatan Wali Kelas</span>
                                <a href="{{ url('/rapor/catatan-wali-kelas') }}" target="_blank" class="px-3 py-1 rounded-lg bg-teal-600 text-white font-semibold hover:bg-teal-700 transition">
                                    Buka Catatan Wali &rarr;
                                </a>
                            </div>
                        </div>

                        <!-- Menu 3: Cetak Rapor Wikrama -->
                        <div class="p-4 rounded-xl border border-brand-500/30 bg-brand-500/5 space-y-2">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                <div class="font-bold text-brand-700 dark:text-brand-300 text-xs flex items-center gap-2">
                                    <span class="text-base">🖨️</span>
                                    <span>Menu Pratinjau &amp; Cetak Rapor Wikrama</span>
                                </div>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-brand-500/20 text-brand-700 dark:text-brand-300">Wali Kelas / Staf TU</span>
                            </div>
                            <p class="text-xs text-text-secondary leading-relaxed">
                                Tempat memeriksa kelengkapan nilai satu kelas, melihat pratinjau lembar rapor siswa (modal preview), dan mencetak PDF (perorangan atau massal 1 rombel).
                            </p>
                            <div class="pt-1 flex items-center justify-between text-xs font-mono">
                                <code class="text-blue-600 font-bold">/rapor/wikrama</code>
                                <a href="{{ url('/rapor/wikrama') }}" target="_blank" class="px-3 py-1 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 transition">
                                    Buka Rapor Wikrama &rarr;
                                </a>
                            </div>
                        </div>

                        <!-- Menu 4: Ekskul -->
                        <div class="p-4 rounded-xl border border-border bg-surface space-y-2 hover:border-brand-500/40 transition">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                <div class="font-bold text-text-main text-xs flex items-center gap-2">
                                    <span class="text-base">⚽</span>
                                    <span>Menu Ekstrakurikuler</span>
                                </div>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-600">Pembina Ekskul</span>
                            </div>
                            <p class="text-xs text-text-secondary leading-relaxed">
                                Mengelola anggota ekskul, mengisi daftar hadir latihan, serta memberi nilai predikat dan catatan evaluasi berkala per 9 minggu.
                            </p>
                            <div class="pt-1 flex items-center justify-between text-xs font-mono">
                                <span class="text-text-secondary">Menu: Ekstrakurikuler</span>
                                <a href="{{ url('/ekskul') }}" target="_blank" class="px-3 py-1 rounded-lg bg-amber-600 text-white font-semibold hover:bg-amber-700 transition">
                                    Buka Ekstrakurikuler &rarr;
                                </a>
                            </div>
                        </div>

                        <!-- Menu 5: Seni Budaya -->
                        <div class="p-4 rounded-xl border border-border bg-surface space-y-2 hover:border-brand-500/40 transition">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                <div class="font-bold text-text-main text-xs flex items-center gap-2">
                                    <span class="text-base">🎨</span>
                                    <span>Menu Seni Budaya</span>
                                </div>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-purple-500/10 text-purple-600">Guru Seni Budaya</span>
                            </div>
                            <p class="text-xs text-text-secondary leading-relaxed">
                                Mendaftarkan siswa ke cabang seni (Musik, Rupa, Tari, Teater) dan mencatat kehadiran serta evaluasi karya seni.
                            </p>
                            <div class="pt-1 flex items-center justify-between text-xs font-mono">
                                <span class="text-text-secondary">Menu: Seni Budaya</span>
                                <a href="{{ url('/seni-budaya') }}" target="_blank" class="px-3 py-1 rounded-lg bg-purple-600 text-white font-semibold hover:bg-purple-700 transition">
                                    Buka Seni Budaya &rarr;
                                </a>
                            </div>
                        </div>

                        <!-- Menu 6: Pengaturan Kurikulum -->
                        <div class="p-4 rounded-xl border border-border bg-surface space-y-2 hover:border-brand-500/40 transition">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                <div class="font-bold text-text-main text-xs flex items-center gap-2">
                                    <span class="text-base">⚖️</span>
                                    <span>Pengaturan Bobot Nilai &amp; Predikat</span>
                                </div>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-500/10 text-blue-600">Admin / Kurikulum</span>
                            </div>
                            <p class="text-xs text-text-secondary leading-relaxed">
                                Tempat tim kurikulum mengubah persentase bobot TP, STS, NH (total 100%) dan ambang batas nilai predikat sekolah.
                            </p>
                            <div class="pt-1 flex items-center justify-between text-xs font-mono">
                                <code class="text-brand-600">/penilaian/pengaturan</code>
                                <a href="{{ url('/penilaian/pengaturan') }}" target="_blank" class="px-3 py-1 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 transition">
                                    Buka Pengaturan &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- ========================================== -->
            <!-- 6. FAQ & PEMECAHAN MASALAH -->
            <!-- ========================================== -->
            @if($activeSection === 'troubleshooting')
            <div class="card p-6 bg-surface border border-border shadow-sm rounded-2xl space-y-6">
                <div class="flex items-center gap-3 border-b border-border pb-4">
                    <span class="p-2.5 rounded-2xl bg-brand-500/10 text-brand-600 text-2xl">❓</span>
                    <div>
                        <h2 class="text-xl font-bold text-text-main">6. FAQ &amp; Pemecahan Masalah</h2>
                        <p class="text-xs text-text-secondary">Jawaban atas kendala yang paling sering ditanyakan oleh guru, wali kelas, dan staf sekolah</p>
                    </div>
                </div>

                <div class="space-y-4 text-sm text-text-secondary">
                    <!-- Q1 -->
                    <div class="p-4 rounded-xl border border-border bg-surface space-y-2">
                        <div class="font-bold text-text-main text-xs flex items-center gap-2">
                            <span class="p-1 rounded bg-amber-500/10 text-amber-600 font-bold">Tanya</span>
                            <span>Nilai rapor siswa saya muncul tanda strip (–), bagaimana cara memperbaikinya?</span>
                        </div>
                        <div class="text-xs text-text-secondary pl-6 space-y-1.5 leading-relaxed">
                            <p>Tanda strip (–) muncul karena salah satu dari 2 hal:</p>
                            <ol class="list-decimal list-inside space-y-1">
                                <li><strong>Belum Lengkap:</strong> Ada nilai TP atau nilai ujian STS yang belum Anda isi di menu Penilaian. Silakan buka menu input nilai dan lengkapi kolom yang masih kosong.</li>
                                <li><strong>Belum Kompeten:</strong> Ada salah satu nilai yang masih di bawah KKM 75 (berwarna merah di tab Rekap). Siswa harus mengikuti remedial. Setelah siswa tuntas remedial, ubah nilainya menjadi &ge; 75 lalu klik Simpan. Nilai akhir akan langsung muncul.</li>
                            </ol>
                        </div>
                    </div>

                    <!-- Q2 -->
                    <div class="p-4 rounded-xl border border-border bg-surface space-y-2">
                        <div class="font-bold text-text-main text-xs flex items-center gap-2">
                            <span class="p-1 rounded bg-amber-500/10 text-amber-600 font-bold">Tanya</span>
                            <span>Saya ingin mengubah kalimat capaian siswa agar lebih sesuai catatan saya, apakah bisa?</span>
                        </div>
                        <div class="text-xs text-text-secondary pl-6 space-y-1.5 leading-relaxed">
                            <p><strong>Bisa sekali!</strong> Gunakan fitur ubah deskripsi manual:</p>
                            <ol class="list-decimal list-inside space-y-1">
                                <li>Buka halaman input nilai mapel Anda &rarr; klik tab <strong>Rekap Nilai</strong>.</li>
                                <li>Cari nama siswa &rarr; klik tombol <strong>Edit Narasi (Ikon Pena)</strong> di sebelah kanan.</li>
                                <li>Ketikkan kalimat catatan yang ingin Anda tampilkan di rapor &rarr; klik <strong>Simpan Narasi</strong>.</li>
                                <li>Sistem akan langsung menampilkan kalimat Anda di lembar cetak rapor.</li>
                            </ol>
                        </div>
                    </div>

                    <!-- Q3 -->
                    <div class="p-4 rounded-xl border border-border bg-surface space-y-2">
                        <div class="font-bold text-text-main text-xs flex items-center gap-2">
                            <span class="p-1 rounded bg-amber-500/10 text-amber-600 font-bold">Tanya</span>
                            <span>Mengapa nilai TP pada mata pelajaran Seni Budaya kosong atau strip?</span>
                        </div>
                        <div class="text-xs text-text-secondary pl-6 space-y-1 leading-relaxed">
                            <p>
                                Hal ini normal. Pada mata pelajaran Seni Budaya, penilaian dihitung dari kehadiran pertemuan dan asesmen hasil karya seni (bukan angka tes harian). Kolom angka TP sengaja dikosongkan, namun status ketercapaian kompetensi tetap dicantumkan sebagai <strong>Tuntas</strong>.
                            </p>
                        </div>
                    </div>

                    <!-- Q4 -->
                    <div class="p-4 rounded-xl border border-border bg-surface space-y-2">
                        <div class="font-bold text-text-main text-xs flex items-center gap-2">
                            <span class="p-1 rounded bg-amber-500/10 text-amber-600 font-bold">Tanya</span>
                            <span>Catatan Wali Kelas tidak muncul saat lembar rapor siswa dicetak, kenapa?</span>
                        </div>
                        <div class="text-xs text-text-secondary pl-6 space-y-1 leading-relaxed">
                            <p>
                                Pastikan saat Anda mengisi di menu <code>/rapor/catatan-wali-kelas</code>, pilihan periode rapor di bagian atas sudah tepat (misalnya: <strong>9M1</strong> untuk rapor tengah ganjil). Jika Anda menginput di periode lain (misalnya 9M2), catatan tidak akan muncul saat mencetak rapor 9M1.
                            </p>
                        </div>
                    </div>

                    <!-- Q5 -->
                    <div class="p-4 rounded-xl border border-border bg-surface space-y-2">
                        <div class="font-bold text-text-main text-xs flex items-center gap-2">
                            <span class="p-1 rounded bg-amber-500/10 text-amber-600 font-bold">Tanya</span>
                            <span>Bagaimana cara mencetak rapor untuk satu kelas sekaligus tanpa klik satu per satu?</span>
                        </div>
                        <div class="text-xs text-text-secondary pl-6 space-y-1 leading-relaxed">
                            <p>
                                Buka menu <code>/rapor/wikrama</code> &rarr; pilih <strong>Rombel (Kelas)</strong> Anda dan pilih <strong>Periode 9 Minggu</strong> &rarr; klik tombol hijau <strong>Cetak PDF Massal</strong> di kanan atas. Sistem akan menggabungkan seluruh rapor siswa satu kelas menjadi 1 file PDF yang langsung siap dicetak ke printer.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- ========================================== -->
            <!-- 7. SOP & ALUR KERJA 6 PERAN -->
            <!-- ========================================== -->
            @if($activeSection === 'sop')
            <div class="card p-6 bg-surface border border-border shadow-sm rounded-2xl space-y-6">
                <div class="flex items-center gap-3 border-b border-border pb-4">
                    <span class="p-2.5 rounded-2xl bg-brand-500/10 text-brand-600 text-2xl">👥</span>
                    <div>
                        <h2 class="text-xl font-bold text-text-main">7. SOP &amp; Alur Kerja 6 Peran Pengguna</h2>
                        <p class="text-xs text-text-secondary">Panduan langkah praktis yang harus dilakukan sesuai tugas masing-masing di sekolah</p>
                    </div>
                </div>

                <div class="space-y-5 text-sm text-text-secondary">
                    <!-- Panduan Praktis Guru Mapel -->
                    <div class="p-4 rounded-xl border border-emerald-500/30 bg-surface space-y-3">
                        <div class="font-bold text-text-main text-xs flex items-center gap-2 border-b border-border pb-2">
                            <span class="p-1 rounded bg-emerald-500/10 text-emerald-600 text-base">👨‍🏫</span>
                            <div>
                                <div class="font-bold text-text-main">Panduan Langkah untuk Guru Mata Pelajaran</div>
                                <div class="text-[11px] text-text-secondary font-normal">Lakukan 5 langkah ini setiap periode 9 minggu:</div>
                            </div>
                        </div>
                        <div class="space-y-2 text-xs">
                            <div class="flex items-start gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-[10px] shrink-0 mt-0.5">1</span>
                                <div><strong>Buat Tujuan Pembelajaran (TP):</strong> Di awal semester, masukkan materi apa saja yang akan dipelajari di tab <em>Tujuan Pembelajaran</em>.</div>
                            </div>
                            <div class="flex items-start gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-[10px] shrink-0 mt-0.5">2</span>
                                <div><strong>Isi Nilai Tugas/Formatif:</strong> Masukkan nilai tugas siswa per-TP di tab <em>Nilai Formatif</em> secara berkala (atau via upload file Excel).</div>
                            </div>
                            <div class="flex items-start gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-[10px] shrink-0 mt-0.5">3</span>
                                <div><strong>Isi Nilai Ujian (STS) &amp; Nilai Harian (NH):</strong> Di pekan ke-8, masukkan nilai ujian STS dan pastikan nilai harian (adab) sudah terisi.</div>
                            </div>
                            <div class="flex items-start gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-[10px] shrink-0 mt-0.5">4</span>
                                <div><strong>Cek Tab Rekap Nilai &amp; Lakukan Remedial:</strong> Periksa apakah ada siswa yang berwarna merah (&lt;75). Lakukan bimbingan remedial dan perbarui nilainya menjadi tuntas.</div>
                            </div>
                            <div class="flex items-start gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-[10px] shrink-0 mt-0.5">5</span>
                                <div><strong>Kunci &amp; Selesaikan Nilai:</strong> Pastikan tidak ada lagi nilai strip sebelum jadwal pembagian rapor.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Panduan Praktis Wali Kelas / Pembimbing Rayon -->
                    <div class="p-4 rounded-xl border border-teal-500/30 bg-surface space-y-3">
                        <div class="font-bold text-text-main text-xs flex items-center gap-2 border-b border-border pb-2">
                            <span class="p-1 rounded bg-teal-500/10 text-teal-600 text-base">🧑‍🏫</span>
                            <div>
                                <div class="font-bold text-text-main">Panduan Langkah untuk Pembimbing Rayon / Wali Kelas</div>
                                <div class="text-[11px] text-text-secondary font-normal">Lakukan 4 langkah ini menjelang pembagian rapor:</div>
                            </div>
                        </div>
                        <div class="space-y-2 text-xs">
                            <div class="flex items-start gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-teal-600 text-white flex items-center justify-center font-bold text-[10px] shrink-0 mt-0.5">1</span>
                                <div><strong>Periksa Absensi Siswa:</strong> Pastikan data Sakit, Izin, dan Alpa siswa di kelas/rayon Anda sudah tercatat benar di menu <em>Absensi</em>.</div>
                            </div>
                            <div class="flex items-start gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-teal-600 text-white flex items-center justify-center font-bold text-[10px] shrink-0 mt-0.5">2</span>
                                <div><strong>Tulis Catatan Wali Kelas:</strong> Buka menu <em>Catatan Wali Kelas</em>, pilih periode rapor yang aktif, dan tuliskan pesan motivasi untuk setiap siswa.</div>
                            </div>
                            <div class="flex items-start gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-teal-600 text-white flex items-center justify-center font-bold text-[10px] shrink-0 mt-0.5">3</span>
                                <div><strong>Pantau Kelengkapan Nilai:</strong> Buka menu <em>Rapor Wikrama</em>, gunakan tombol pratinjau (Preview) untuk melihat lembar rapor tiap siswa. Jika ada nilai mapel yang masih strip, ingatkan guru mapel bersangkutan.</div>
                            </div>
                            <div class="flex items-start gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-teal-600 text-white flex items-center justify-center font-bold text-[10px] shrink-0 mt-0.5">4</span>
                                <div><strong>Cetak Dokumen Rapor:</strong> Klik tombol <em>Cetak PDF Massal</em> untuk mencetak rapor satu kelas sekaligus. Rapor siap ditandatangani dan dibagikan ke orang tua.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Pembagian Tanggung Jawab Singkat -->
                    <div class="p-4 rounded-xl border border-border bg-surface-hover space-y-2">
                        <div class="font-bold text-text-main text-xs uppercase tracking-wider flex items-center gap-1.5">
                            <span>📋</span>
                            <span>Ringkasan Tugas Berdasarkan Peran (Matriks RACI)</span>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-xs">
                            <div class="p-2.5 bg-surface rounded-lg border border-border">
                                <span class="font-bold text-text-main">Waka Kurikulum</span>
                                <div class="text-[11px] text-text-secondary mt-0.5">Menentukan jadwal 9M dan mengatur bobot nilai sekolah.</div>
                            </div>
                            <div class="p-2.5 bg-surface rounded-lg border border-border">
                                <span class="font-bold text-text-main">Guru Mapel</span>
                                <div class="text-[11px] text-text-secondary mt-0.5">Mengisi nilai TP, ujian STS, dan mengawal remedial.</div>
                            </div>
                            <div class="p-2.5 bg-surface rounded-lg border border-border">
                                <span class="font-bold text-text-main">Pembina Ekskul</span>
                                <div class="text-[11px] text-text-secondary mt-0.5">Mengisi nilai keaktifan dan catatan ekskul.</div>
                            </div>
                            <div class="p-2.5 bg-surface rounded-lg border border-border">
                                <span class="font-bold text-text-main">Guru Seni Budaya</span>
                                <div class="text-[11px] text-text-secondary mt-0.5">Mengatur cabang seni siswa dan evaluasi karya.</div>
                            </div>
                            <div class="p-2.5 bg-surface rounded-lg border border-border">
                                <span class="font-bold text-text-main">Wali Kelas / Rayon</span>
                                <div class="text-[11px] text-text-secondary mt-0.5">Menulis catatan karakter dan mencetak rapor.</div>
                            </div>
                            <div class="p-2.5 bg-surface rounded-lg border border-border">
                                <span class="font-bold text-text-main">Kepala Sekolah</span>
                                <div class="text-[11px] text-text-secondary mt-0.5">Mengesahkan dokumen rapor hasil belajar siswa.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- ========================================== -->
            <!-- 8. GLOSARIUM KURIKULUM MERDEKA -->
            <!-- ========================================== -->
            @if($activeSection === 'glosarium')
            <div class="card p-6 bg-surface border border-border shadow-sm rounded-2xl space-y-6">
                <div class="flex items-center gap-3 border-b border-border pb-4">
                    <span class="p-2.5 rounded-2xl bg-brand-500/10 text-brand-600 text-2xl">📖</span>
                    <div>
                        <h2 class="text-xl font-bold text-text-main">8. Glosarium Kurikulum Merdeka &amp; SIWARAGA</h2>
                        <p class="text-xs text-text-secondary">Penjelasan singkat arti istilah dan singkatan yang sering ditemui di lingkungan sekolah</p>
                    </div>
                </div>

                <div class="space-y-4 text-sm text-text-secondary">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                        <div class="p-3.5 rounded-xl border border-border bg-surface space-y-1">
                            <div class="font-bold text-text-main flex items-center justify-between">
                                <span>CP (Capaian Pembelajaran)</span>
                                <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-brand-500/10 text-brand-600 font-bold">Tingkat Kurikulum</span>
                            </div>
                            <p class="text-[11px] text-text-secondary leading-relaxed">
                                Target kemampuan yang wajib dicapai siswa pada akhir suatu jenjang fase belajar.
                            </p>
                        </div>

                        <div class="p-3.5 rounded-xl border border-border bg-surface space-y-1">
                            <div class="font-bold text-text-main flex items-center justify-between">
                                <span>TP (Tujuan Pembelajaran)</span>
                                <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-600 font-bold">Materi/Bab</span>
                            </div>
                            <p class="text-[11px] text-text-secondary leading-relaxed">
                                Rincian materi atau keterampilan spesifik yang dipelajari dan diujikan guru dalam setiap bab pelajaran.
                            </p>
                        </div>

                        <div class="p-3.5 rounded-xl border border-border bg-surface space-y-1">
                            <div class="font-bold text-text-main flex items-center justify-between">
                                <span>STS (Sumatif Tengah Semester)</span>
                                <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-amber-500/10 text-amber-600 font-bold">Ujian Pekan 8</span>
                            </div>
                            <p class="text-[11px] text-text-secondary leading-relaxed">
                                Ujian evaluasi tengah semester yang mengukur pemahaman materi selama 9 pekan pertama.
                            </p>
                        </div>

                        <div class="p-3.5 rounded-xl border border-border bg-surface space-y-1">
                            <div class="font-bold text-text-main flex items-center justify-between">
                                <span>SAS / SAT (Sumatif Akhir)</span>
                                <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-purple-500/10 text-purple-600 font-bold">Ujian Akhir</span>
                            </div>
                            <p class="text-[11px] text-text-secondary leading-relaxed">
                                Ujian akhir semester (SAS di semester ganjil, SAT di semester genap) untuk menentukan kenaikan kelas atau kelulusan.
                            </p>
                        </div>

                        <div class="p-3.5 rounded-xl border border-border bg-surface space-y-1">
                            <div class="font-bold text-text-main flex items-center justify-between">
                                <span>KKM (Kriteria Ketuntasan Minimal)</span>
                                <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-rose-500/10 text-rose-600 font-bold">Standar 75</span>
                            </div>
                            <p class="text-[11px] text-text-secondary leading-relaxed">
                                Standar nilai terendah yang harus dicapai siswa agar dinyatakan kompeten (tuntas). Di SMK Wikrama batasnya adalah 75.
                            </p>
                        </div>

                        <div class="p-3.5 rounded-xl border border-border bg-surface space-y-1">
                            <div class="font-bold text-text-main flex items-center justify-between">
                                <span>Rombel &amp; Rayon</span>
                                <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-text-secondary/10 text-text-main font-bold">Kelas Siswa</span>
                            </div>
                            <p class="text-[11px] text-text-secondary leading-relaxed">
                                <strong>Rombel:</strong> Kelas belajar jurusan (misal: PPLG X-1).<br>
                                <strong>Rayon:</strong> Kelompok bimbingan karakter antar-jurusan yang dibina Pembimbing Rayon.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
