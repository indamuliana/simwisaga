<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-border pb-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="p-2 rounded-xl bg-brand-500/10 text-brand-600 dark:text-brand-400 font-bold text-lg">📖</span>
                <h1 class="text-2xl font-bold tracking-tight text-text-main">Pusat Panduan &amp; Dokumentasi SIRAGA</h1>
            </div>
            <p class="text-sm text-text-secondary mt-1">
                @if($isSiswaOrOrtu)
                Panduan resmi pemantauan akademik, absensi, rapor, dan informasi kelulusan untuk Siswa dan Orang Tua.
                @else
                Panduan teknis dan SOP operasional siklus akademik: pergantian semester, tahun ajaran baru, kenaikan kelas, dan kelulusan siswa.
                @endif
            </p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-surface border border-border text-text-secondary">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span>Versi Panduan: 2.5 (Update 2026)</span>
            </span>
        </div>
    </div>

    <!-- Knowledgebase Layout (Sidebar Kiri + Konten Kanan) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Sidebar Daftar Bab (Kiri - 4 Kolom di LG) -->
        <div class="lg:col-span-4 space-y-4">
            <div class="card p-4 bg-surface border border-border shadow-sm rounded-2xl space-y-3 sticky top-6">
                <span class="text-xs font-bold uppercase tracking-wider text-text-secondary block px-2">
                    Daftar Bab Panduan
                </span>

                <nav class="space-y-1">
                    @if(!$isSiswaOrOrtu)
                    <!-- Navigasi Staf / Guru / Admin -->
                    <button type="button" wire:click="setChapter('siklus-akademik')"
                        class="w-full text-left px-3 py-2.5 rounded-xl text-xs font-semibold transition flex items-center justify-between {{ $activeChapter === 'siklus-akademik' ? 'bg-brand-600 text-white shadow-sm' : 'text-text-main hover:bg-surface-hover' }}">
                        <div class="flex items-center gap-2.5">
                            <span>🏛️</span>
                            <span>1. Konsep Siklus Akademik</span>
                        </div>
                        <span class="text-[10px] {{ $activeChapter === 'siklus-akademik' ? 'text-white/80' : 'text-text-secondary' }}">Ikhtisar</span>
                    </button>

                    <button type="button" wire:click="setChapter('ganti-semester')"
                        class="w-full text-left px-3 py-2.5 rounded-xl text-xs font-semibold transition flex items-center justify-between {{ $activeChapter === 'ganti-semester' ? 'bg-brand-600 text-white shadow-sm' : 'text-text-main hover:bg-surface-hover' }}">
                        <div class="flex items-center gap-2.5">
                            <span>⚡</span>
                            <span>2. Pergantian Semester</span>
                        </div>
                        <span class="text-[10px] {{ $activeChapter === 'ganti-semester' ? 'text-white/80' : 'text-text-secondary' }}">Dampak &amp; Cara</span>
                    </button>

                    <button type="button" wire:click="setChapter('ganti-tahun-ajaran')"
                        class="w-full text-left px-3 py-2.5 rounded-xl text-xs font-semibold transition flex items-center justify-between {{ $activeChapter === 'ganti-tahun-ajaran' ? 'bg-brand-600 text-white shadow-sm' : 'text-text-main hover:bg-surface-hover' }}">
                        <div class="flex items-center gap-2.5">
                            <span>📅</span>
                            <span>3. Pergantian Tahun Ajaran</span>
                        </div>
                        <span class="text-[10px] {{ $activeChapter === 'ganti-tahun-ajaran' ? 'text-white/80' : 'text-text-secondary' }}">TA Baru</span>
                    </button>

                    <button type="button" wire:click="setChapter('kenaikan-kelas')"
                        class="w-full text-left px-3 py-2.5 rounded-xl text-xs font-semibold transition flex items-center justify-between {{ $activeChapter === 'kenaikan-kelas' ? 'bg-brand-600 text-white shadow-sm' : 'text-text-main hover:bg-surface-hover' }}">
                        <div class="flex items-center gap-2.5">
                            <span>📈</span>
                            <span>4. Kenaikan Kelas &amp; Siswa Tinggal</span>
                        </div>
                        <span class="text-[10px] {{ $activeChapter === 'kenaikan-kelas' ? 'text-white/80' : 'text-text-secondary' }}">Wizard</span>
                    </button>

                    <button type="button" wire:click="setChapter('kelulusan-siswa')"
                        class="w-full text-left px-3 py-2.5 rounded-xl text-xs font-semibold transition flex items-center justify-between {{ $activeChapter === 'kelulusan-siswa' ? 'bg-brand-600 text-white shadow-sm' : 'text-text-main hover:bg-surface-hover' }}">
                        <div class="flex items-center gap-2.5">
                            <span>🎓</span>
                            <span>5. Kelulusan Siswa Kelas XII</span>
                        </div>
                        <span class="text-[10px] {{ $activeChapter === 'kelulusan-siswa' ? 'text-white/80' : 'text-text-secondary' }}">Eksekusi</span>
                    </button>

                    <button type="button" wire:click="setChapter('pengelolaan-alumni')"
                        class="w-full text-left px-3 py-2.5 rounded-xl text-xs font-semibold transition flex items-center justify-between {{ $activeChapter === 'pengelolaan-alumni' ? 'bg-brand-600 text-white shadow-sm' : 'text-text-main hover:bg-surface-hover' }}">
                        <div class="flex items-center gap-2.5">
                            <span>💼</span>
                            <span>6. Pengelolaan Alumni &amp; BMW</span>
                        </div>
                        <span class="text-[10px] {{ $activeChapter === 'pengelolaan-alumni' ? 'text-white/80' : 'text-text-secondary' }}">Tracer</span>
                    </button>

                    <button type="button" wire:click="setChapter('faq-troubleshooting')"
                        class="w-full text-left px-3 py-2.5 rounded-xl text-xs font-semibold transition flex items-center justify-between {{ $activeChapter === 'faq-troubleshooting' ? 'bg-brand-600 text-white shadow-sm' : 'text-text-main hover:bg-surface-hover' }}">
                        <div class="flex items-center gap-2.5">
                            <span>❓</span>
                            <span>7. FAQ &amp; Troubleshooting</span>
                        </div>
                        <span class="text-[10px] {{ $activeChapter === 'faq-troubleshooting' ? 'text-white/80' : 'text-text-secondary' }}">Bantuan</span>
                    </button>
                    @else
                    <!-- Navigasi Siswa & Orang Tua -->
                    <button type="button" wire:click="setChapter('portal-siswa-ortu')"
                        class="w-full text-left px-3 py-2.5 rounded-xl text-xs font-semibold transition flex items-center justify-between {{ $activeChapter === 'portal-siswa-ortu' ? 'bg-brand-600 text-white shadow-sm' : 'text-text-main hover:bg-surface-hover' }}">
                        <div class="flex items-center gap-2.5">
                            <span>👤</span>
                            <span>1. Panduan Portal Akun Anda</span>
                        </div>
                    </button>

                    <button type="button" wire:click="setChapter('cek-kehadiran')"
                        class="w-full text-left px-3 py-2.5 rounded-xl text-xs font-semibold transition flex items-center justify-between {{ $activeChapter === 'cek-kehadiran' ? 'bg-brand-600 text-white shadow-sm' : 'text-text-main hover:bg-surface-hover' }}">
                        <div class="flex items-center gap-2.5">
                            <span>📋</span>
                            <span>2. Memeriksa Catatan Kehadiran</span>
                        </div>
                    </button>

                    <button type="button" wire:click="setChapter('unduh-rapor')"
                        class="w-full text-left px-3 py-2.5 rounded-xl text-xs font-semibold transition flex items-center justify-between {{ $activeChapter === 'unduh-rapor' ? 'bg-brand-600 text-white shadow-sm' : 'text-text-main hover:bg-surface-hover' }}">
                        <div class="flex items-center gap-2.5">
                            <span>📄</span>
                            <span>3. Melihat &amp; Cetak e-Rapor</span>
                        </div>
                    </button>

                    <button type="button" wire:click="setChapter('ketentuan-kenaikan-kelulusan')"
                        class="w-full text-left px-3 py-2.5 rounded-xl text-xs font-semibold transition flex items-center justify-between {{ $activeChapter === 'ketentuan-kenaikan-kelulusan' ? 'bg-brand-600 text-white shadow-sm' : 'text-text-main hover:bg-surface-hover' }}">
                        <div class="flex items-center gap-2.5">
                            <span>🎯</span>
                            <span>4. Kriteria Kenaikan &amp; Kelulusan</span>
                        </div>
                    </button>

                    <button type="button" wire:click="setChapter('tracer-study-alumni')"
                        class="w-full text-left px-3 py-2.5 rounded-xl text-xs font-semibold transition flex items-center justify-between {{ $activeChapter === 'tracer-study-alumni' ? 'bg-brand-600 text-white shadow-sm' : 'text-text-main hover:bg-surface-hover' }}">
                        <div class="flex items-center gap-2.5">
                            <span>🎓</span>
                            <span>5. Pemutakhiran Data Tamatan</span>
                        </div>
                    </button>
                    @endif
                </nav>

                <div class="p-3 rounded-xl bg-surface-hover border border-border text-[11px] text-text-secondary">
                    💡 <em>Tip: Klik bab di atas untuk membuka panduan langkah demi langkah beserta diagram alur.</em>
                </div>
            </div>
        </div>

        <!-- Kolom Konten Artikel (Kanan - 8 Kolom di LG) -->
        <div class="lg:col-span-8">
            <div class="card p-6 sm:p-8 bg-surface border border-border shadow-sm rounded-2xl space-y-6">

                @if(!$isSiswaOrOrtu)
                <!-- ========================================== -->
                <!-- BAB 1: KONSEP SIKLUS AKADEMIK SIRAGA       -->
                <!-- ========================================== -->
                @if($activeChapter === 'siklus-akademik')
                <div class="space-y-6">
                    <div class="border-b border-border pb-4">
                        <span class="text-xs font-bold uppercase tracking-wider text-brand-600 dark:text-brand-400">Bab 1</span>
                        <h2 class="text-2xl font-bold text-text-main mt-1">Konsep &amp; Arsitektur Siklus Akademik SIRAGA</h2>
                        <p class="text-sm text-text-secondary mt-1">Memahami relasi data antara Tahun Ajaran, Semester, Rombel, Siswa, dan Guru.</p>
                    </div>

                    <div class="prose dark:prose-invert max-w-none text-sm text-text-main space-y-4">
                        <p>
                            Sistem Informasi Rapor &amp; Akademik (SIRAGA) dibangun di atas struktur siklus berjenjang yang terbagi menjadi dua dimensi waktu: <strong>Tahun Ajaran</strong> dan <strong>Semester</strong>.
                        </p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 my-4">
                            <div class="p-4 rounded-xl border border-blue-200 dark:border-blue-900 bg-blue-50/50 dark:bg-blue-950/20 space-y-2">
                                <h4 class="font-bold text-blue-800 dark:text-blue-300 text-sm flex items-center gap-2">
                                    <span>📅</span> Dimensi Tahun Ajaran (Tahunan)
                                </h4>
                                <ul class="text-xs space-y-1 list-disc list-inside text-text-secondary">
                                    <li>Menaungi 2 Semester (Ganjil &amp; Genap).</li>
                                    <li>Rombongan Belajar (Rombel) terikat pada Tahun Ajaran.</li>
                                    <li>Kenaikan Kelas dan Kelulusan dieksekusi saat pergantian tahun ajaran.</li>
                                </ul>
                            </div>

                            <div class="p-4 rounded-xl border border-emerald-200 dark:border-emerald-900 bg-emerald-50/50 dark:bg-emerald-950/20 space-y-2">
                                <h4 class="font-bold text-emerald-800 dark:text-emerald-300 text-sm flex items-center gap-2">
                                    <span>⚡</span> Dimensi Semester (Semesteran)
                                </h4>
                                <ul class="text-xs space-y-1 list-disc list-inside text-text-secondary">
                                    <li>Jadwal &amp; Penugasan Mengajar Guru terikat pada Semester.</li>
                                    <li>Nilai Formatif, Sumatif, dan Rapor dicatat per Semester.</li>
                                    <li>Rekap Absensi Harian dihitung per Semester.</li>
                                </ul>
                            </div>
                        </div>

                        <div class="rounded-xl border border-border p-4 bg-surface-hover space-y-2">
                            <h4 class="font-bold text-text-main text-sm">Prinsip Integritas Historis Data:</h4>
                            <p class="text-xs text-text-secondary leading-relaxed">
                                Setiap mutasi kelas dan catatan nilai masa lalu diarsipkan secara permanen pada tabel <code>rombel_siswa</code> dan <code>riwayat_akademiks</code>. Perubahan tahun ajaran tidak menghapus rapor lama siswa.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- BAB 2: PERGANTIAN SEMESTER                 -->
                <!-- ========================================== -->
                @elseif($activeChapter === 'ganti-semester')
                <div class="space-y-6">
                    <div class="border-b border-border pb-4">
                        <span class="text-xs font-bold uppercase tracking-wider text-brand-600 dark:text-brand-400">Bab 2</span>
                        <h2 class="text-2xl font-bold text-text-main mt-1">Prosedur &amp; Dampak Pergantian Semester</h2>
                        <p class="text-sm text-text-secondary mt-1">Panduan lengkap langkah demi langkah pergantian dari Semester Ganjil ke Semester Genap.</p>
                    </div>

                    <div class="space-y-4 text-sm text-text-main">
                        <h3 class="font-bold text-base text-text-main">1. Jika Semester Berganti, Apa Dampaknya pada Sistem?</h3>
                        
                        <div class="overflow-x-auto rounded-xl border border-border">
                            <table class="min-w-full divide-y divide-border text-xs">
                                <thead class="bg-surface-hover text-text-secondary font-bold">
                                    <tr>
                                        <th class="px-4 py-2.5 text-left">Komponen Data</th>
                                        <th class="px-4 py-2.5 text-left">Status Perubahan</th>
                                        <th class="px-4 py-2.5 text-left">Penjelasan Teknis</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-border">
                                    <tr>
                                        <td class="px-4 py-2.5 font-semibold">Siswa &amp; Rombel</td>
                                        <td class="px-4 py-2.5 text-emerald-600 font-bold">TETAP (Tidak Berubah)</td>
                                        <td class="px-4 py-2.5 text-text-secondary">Siswa tetap berada di kelas/rombel yang sama (misal tetap di X PPLG 1).</td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-2.5 font-semibold">Nilai Rapor</td>
                                        <td class="px-4 py-2.5 text-blue-600 font-bold">MULAI BARU (Terpisah)</td>
                                        <td class="px-4 py-2.5 text-text-secondary">Nilai semester ganjil diarsipkan; penginputan nilai formatif/sumatif semester genap dimulai dari kosong.</td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-2.5 font-semibold">Absensi Harian</td>
                                        <td class="px-4 py-2.5 text-blue-600 font-bold">MULAI BARU (Terpisah)</td>
                                        <td class="px-4 py-2.5 text-text-secondary">Rekapitulasi kehadiran dihitung ulang dari awal semester aktif.</td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-2.5 font-semibold">Penugasan Guru</td>
                                        <td class="px-4 py-2.5 text-amber-600 font-bold">DISALIN OTOMATIS</td>
                                        <td class="px-4 py-2.5 text-text-secondary">Sistem menyediakan opsi menyalin jadwal guru mapel agar guru langsung bisa mengajar di semester baru.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <h3 class="font-bold text-base text-text-main mt-6">2. Langkah-Langkah Mengganti Semester:</h3>
                        <ol class="space-y-3 list-decimal list-inside text-xs leading-relaxed text-text-secondary">
                            <li>Masuk ke menu <strong>Master Akademik &gt; Tahun Ajaran &amp; Semester</strong>.</li>
                            <li>Temukan Tahun Ajaran yang sedang berjalan, lalu lihat kotak <strong>Semester Genap</strong> (atau semester tujuan).</li>
                            <li>Klik tombol <strong>"⚡ Aktifkan Semester"</strong>.</li>
                            <li>
                                Kotak dialog <strong>Transisi Semester Cerdas</strong> akan muncul:
                                <ul class="list-disc list-inside ml-6 mt-1 space-y-1 text-text-main font-medium">
                                    <li>Centang <em>"Kunci Penginputan Nilai Semester Sebelumnya"</em> (Rekomendasi agar nilai ganjil aman).</li>
                                    <li>Centang <em>"Kunci Absensi Semester Sebelumnya"</em>.</li>
                                    <li>Centang <em>"Salin Penugasan Mengajar Guru"</em> (Rekomendasi agar jadwal guru langsung tersedia).</li>
                                </ul>
                            </li>
                            <li>Klik tombol <strong>"Aktifkan Semester Sekarang"</strong>. Sistem akan mengeksekusi transisi secara aman.</li>
                        </ol>

                        <div class="rounded-xl border border-amber-500/20 bg-amber-500/5 p-4 text-xs text-amber-700 dark:text-amber-300">
                            <strong>Penting:</strong> Setelah berganti ke Semester Genap, pencetakan rapor akhir tahun pada semester genap akan otomatis memuat <strong>Keputusan Kenaikan Kelas / Kelulusan</strong> di bagian akhir rapor.
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- BAB 3: PERGANTIAN TAHUN AJARAN             -->
                <!-- ========================================== -->
                @elseif($activeChapter === 'ganti-tahun-ajaran')
                <div class="space-y-6">
                    <div class="border-b border-border pb-4">
                        <span class="text-xs font-bold uppercase tracking-wider text-brand-600 dark:text-brand-400">Bab 3</span>
                        <h2 class="text-2xl font-bold text-text-main mt-1">Prosedur &amp; Dampak Pergantian Tahun Ajaran Baru</h2>
                        <p class="text-sm text-text-secondary mt-1">SOP pergantian kalender tahun ajaran dari Tahun N ke Tahun N+1.</p>
                    </div>

                    <div class="space-y-4 text-sm text-text-main">
                        <h3 class="font-bold text-base text-text-main">1. Apa yang Terjadi pada Sistem saat Ganti Tahun Ajaran?</h3>
                        <p class="text-xs text-text-secondary leading-relaxed">
                            Ketika tahun ajaran berganti (contoh: dari 2024/2025 ke 2025/2026), seluruh siswa berada di akhir masa studi tingkatannya. Sekolah perlu menyiapkan rombel baru, memproses kenaikan kelas, dan meluluskan siswa kelas XII.
                        </p>

                        <h3 class="font-bold text-base text-text-main mt-4">2. Checklist Tahapan Membuka Tahun Ajaran Baru:</h3>
                        <div class="space-y-2 text-xs">
                            <div class="flex items-start gap-2 p-2.5 rounded-lg border border-border bg-surface-hover">
                                <span class="font-bold text-brand-600">Langkah 1:</span>
                                <span>Buat Tahun Ajaran Baru di menu <strong>Tahun Ajaran &amp; Semester</strong> (contoh: "2025/2026").</span>
                            </div>
                            <div class="flex items-start gap-2 p-2.5 rounded-lg border border-border bg-surface-hover">
                                <span class="font-bold text-brand-600">Langkah 2:</span>
                                <span>Masuk ke menu <strong>Rombongan Belajar (Rombel)</strong>, klik tombol <strong>"⚡ Salin Rombel dari TA Lain"</strong> untuk menduplikasi seluruh kelas X, XI, XII ke TA baru secara instan.</span>
                            </div>
                            <div class="flex items-start gap-2 p-2.5 rounded-lg border border-border bg-surface-hover">
                                <span class="font-bold text-brand-600">Langkah 3:</span>
                                <span>Proses <strong>Kelulusan Siswa Kelas XII</strong> di menu <em>Kesiswaan &gt; Kelulusan Siswa</em> agar kelas XII diarsipkan ke Modul Alumni.</span>
                            </div>
                            <div class="flex items-start gap-2 p-2.5 rounded-lg border border-border bg-surface-hover">
                                <span class="font-bold text-brand-600">Langkah 4:</span>
                                <span>Jalankan <strong>Wizard Kenaikan Kelas</strong> di menu <em>Kesiswaan &gt; Kenaikan Kelas</em> (Kelas X naik ke XI, XI naik ke XII).</span>
                            </div>
                            <div class="flex items-start gap-2 p-2.5 rounded-lg border border-border bg-surface-hover">
                                <span class="font-bold text-brand-600">Langkah 5:</span>
                                <span>Input siswa baru (Kelas X) melalui fitur <em>Import DAPODIK</em> atau <em>Tambah Siswa</em>, lalu alokasikan rombelnya di <em>Plotting Rombel</em>.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- BAB 4: KENAIKAN KELAS & TINGGAL KELAS     -->
                <!-- ========================================== -->
                <!-- ========================================== -->
                <!-- BAB 4: KENAIKAN KELAS & TINGGAL KELAS     -->
                <!-- ========================================== -->
                @elseif($activeChapter === 'kenaikan-kelas')
                <div class="space-y-8">
                    <!-- Header Bab -->
                    <div class="border-b border-border pb-4">
                        <span class="text-xs font-bold uppercase tracking-wider text-brand-600 dark:text-brand-400">Bab 4</span>
                        <h2 class="text-2xl font-bold text-text-main mt-1">Panduan Lengkap Kenaikan Kelas, Tinggal Kelas, &amp; Integritas Ledger</h2>
                        <p class="text-sm text-text-secondary mt-1">
                            Pedoman menyeluruh prosedur kenaikan kelas per rombel, penanganan siswa tinggal kelas/mutasi, serta jaminan keutuhan arsip nilai dan presensi lintas tahun ajaran.
                        </p>
                    </div>

                    <!-- 1. Konsep Dasar & Arsitektur Integritas Data -->
                    <div class="space-y-4 text-sm text-text-main">
                        <div class="flex items-center gap-2">
                            <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-brand-100 text-brand-700 dark:bg-brand-950 dark:text-brand-300 font-bold text-xs">1</span>
                            <h3 class="font-bold text-base text-text-main">Konsep Dasar &amp; Arsitektur Integritas Data (Ledger)</h3>
                        </div>
                        <p class="text-xs text-text-secondary leading-relaxed">
                            Proses kenaikan kelas di SIRAGA didesain dengan prinsip <strong>Immutability of Academic History</strong> (Data akademik masa lalu tidak pernah dihapus). Perpindahan siswa ke tingkat baru tidak merusak arsip rapor, absensi, maupun buku nilai pada tahun ajaran sebelumnya.
                        </p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 my-3">
                            <div class="p-4 rounded-xl border border-blue-200 dark:border-blue-900 bg-blue-50/50 dark:bg-blue-950/20 space-y-2">
                                <h4 class="font-bold text-blue-800 dark:text-blue-300 text-xs flex items-center gap-2">
                                    <span>📍</span> Pointer Kelas Aktif (siswas.rombel_id)
                                </h4>
                                <p class="text-[11px] text-text-secondary leading-relaxed">
                                    Menunjuk rombel yang sedang diduduki siswa saat ini. Pointer ini digunakan oleh sistem harian: presensi kelas hari ini, input tugas mengajar semester aktif, dan penentuan wali kelas saat ini.
                                </p>
                            </div>

                            <div class="p-4 rounded-xl border border-purple-200 dark:border-purple-900 bg-purple-50/50 dark:bg-purple-950/20 space-y-2">
                                <h4 class="font-bold text-purple-800 dark:text-purple-300 text-xs flex items-center gap-2">
                                    <span>📜</span> Ledger &amp; Riwayat Akademik (rombel_siswa &amp; riwayat_akademiks)
                                </h4>
                                <p class="text-[11px] text-text-secondary leading-relaxed">
                                    Merekam jejak keanggotaan rombel dan keputusan kenaikan setiap tahunnya. Berkat arsitektur <code>scopeInRombel</code>, saat Anda mencetak ulang buku leger atau rapor masa lalu, siswa lama akan tetap tampil lengkap.
                                </p>
                            </div>
                        </div>

                        <div class="p-4 rounded-xl bg-surface-hover border border-border text-xs space-y-2">
                            <strong class="text-text-main flex items-center gap-2">
                                <span>🛡️</span> Mengapa Arsitektur Ini Penting untuk Ledger Sekolah?
                            </strong>
                            <p class="text-text-secondary leading-relaxed">
                                Di banyak sistem konvensional, saat siswa dinaikkan dari kelas X ke XI, membuka kelas X di tahun lalu menjadi kosong karena ID kelasnya sudah berganti. Di SIRAGA, modul Penilaian, Rapor, dan Absensi otomatis membaca relasi historis sehingga leger nilai dan absensi tetap 100% utuh dan siap diekspor kapan saja.
                            </p>
                        </div>
                    </div>

                    <!-- 2. Prasyarat Wajib Sebelum Eksekusi -->
                    <div class="space-y-4 text-sm text-text-main">
                        <div class="flex items-center gap-2">
                            <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300 font-bold text-xs">2</span>
                            <h3 class="font-bold text-base text-text-main">Prasyarat Wajib Sebelum Memulai Kenaikan Kelas</h3>
                        </div>
                        <p class="text-xs text-text-secondary leading-relaxed">
                            Pastikan langkah-langkah persiapan berikut telah terpenuhi sebelum membuka wizard kenaikan kelas:
                        </p>

                        <div class="space-y-2.5 text-xs">
                            <div class="flex items-start gap-3 p-3.5 rounded-xl border border-border bg-surface">
                                <span class="flex-shrink-0 w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center font-bold text-xs">✓</span>
                                <div>
                                    <strong class="text-text-main block">1. Rapor Semester Genap Sudah Final &amp; Dicetak</strong>
                                    <span class="text-text-secondary">Seluruh nilai formatif, sumatif, kehadiran, dan catatan wali kelas semester genap telah dikunci dan diserahkan ke siswa/wali murid.</span>
                                </div>
                            </div>

                            <div class="flex items-start gap-3 p-3.5 rounded-xl border border-border bg-surface">
                                <span class="flex-shrink-0 w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center font-bold text-xs">✓</span>
                                <div>
                                    <strong class="text-text-main block">2. Rapat Pleno Dewan Guru Telah Selesai</strong>
                                    <span class="text-text-secondary">Wali kelas dan kepala sekolah telah mengantongi Berita Acara Rapat Kenaikan Kelas, termasuk daftar siswa yang dinyatakan mengulang (tinggal kelas) atau mutasi.</span>
                                </div>
                            </div>

                            <div class="flex items-start gap-3 p-3.5 rounded-xl border border-border bg-surface">
                                <span class="flex-shrink-0 w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center font-bold text-xs">✓</span>
                                <div>
                                    <strong class="text-text-main block">3. Tahun Ajaran Baru Sudah Dibuat</strong>
                                    <span class="text-text-secondary">Admin telah menambahkan Tahun Ajaran berikutnya pada menu <em>Akademik &gt; Tahun Ajaran</em> (misal: Tahun Ajaran 2025/2026).</span>
                                </div>
                            </div>

                            <div class="flex items-start gap-3 p-3.5 rounded-xl border border-border bg-surface">
                                <span class="flex-shrink-0 w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center font-bold text-xs">✓</span>
                                <div>
                                    <strong class="text-text-main block">4. Rombongan Belajar T.A. Baru Sudah Digenerate</strong>
                                    <span class="text-text-secondary">
                                        Rombel tingkat XI dan XII pada T.A. Baru harus sudah ada. Gunakan tombol praktis <strong>"📋 Duplikasi Rombel ke T.A. Baru"</strong> di menu <em>Akademik &gt; Rombongan Belajar</em> untuk menyalin struktur seluruh rombel dalam 1 kali klik.
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Alur Wizard Kenaikan Kelas Langkah demi Langkah -->
                    <div class="space-y-4 text-sm text-text-main">
                        <div class="flex items-center gap-2">
                            <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 font-bold text-xs">3</span>
                            <h3 class="font-bold text-base text-text-main">Langkah Operasional Wizard Kenaikan Kelas</h3>
                        </div>
                        <p class="text-xs text-text-secondary leading-relaxed">
                            Proses dilakukan <strong>per rombel asal</strong> secara teratur (misal: rombel X PPLG 1 diproses lebih dulu, dilanjutkan X PPLG 2, dst).
                        </p>

                        <div class="grid grid-cols-1 gap-3 text-xs">
                            <div class="p-4 rounded-xl border border-border bg-surface-hover/50 space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-brand-600 dark:text-brand-400">Langkah 1: Buka Menu &amp; Pilih Rombel Asal</span>
                                    <span class="text-[10px] px-2 py-0.5 rounded bg-surface border border-border font-mono">Kesiswaan &gt; Kenaikan Kelas</span>
                                </div>
                                <p class="text-text-secondary leading-relaxed">
                                    Pilih <strong>Tahun Ajaran Asal</strong> (misal <em>2024/2025</em>) dan <strong>Rombel Asal</strong> (misal <em>X PPLG 1</em>). Sistem akan memuat seluruh siswa aktif yang berada di rombel tersebut.
                                </p>
                            </div>

                            <div class="p-4 rounded-xl border border-border bg-surface-hover/50 space-y-1.5">
                                <span class="font-bold text-brand-600 dark:text-brand-400">Langkah 2: Sistem Otomatis Mendeteksi Rombel Tujuan Utama</span>
                                <p class="text-text-secondary leading-relaxed">
                                    Pilih <strong>Tahun Ajaran Tujuan</strong> (misal <em>2025/2026</em>). Sistem cerdas SIRAGA secara otomatis mencocokkan rombel asal ke tingkat berikutnya dengan jurusan &amp; nomor urut yang sama (contoh: <code>X PPLG 1</code> otomatis dipasangkan ke <code>XI PPLG 1</code> pada T.A. Baru).
                                </p>
                            </div>

                            <div class="p-4 rounded-xl border border-border bg-surface-hover/50 space-y-1.5">
                                <span class="font-bold text-brand-600 dark:text-brand-400">Langkah 3: Tinjau Ringkasan Kehadiran &amp; Keputusan</span>
                                <p class="text-text-secondary leading-relaxed">
                                    Pada tabel siswa, sistem menampilkan rekapitulasi kehadiran tahunan: <strong>S (Sakit)</strong>, <strong>I (Izin)</strong>, dan <strong>A (Alpa)</strong>. Angka alpa di atas ambang batas toleransi akan ditandai dengan warna merah untuk mempermudah pengecekan syarat kehadiran.
                                </p>
                            </div>

                            <div class="p-4 rounded-xl border border-border bg-surface-hover/50 space-y-1.5">
                                <span class="font-bold text-brand-600 dark:text-brand-400">Langkah 4: Sesuaikan Siswa Khusus (Tinggal / Mutasi)</span>
                                <p class="text-text-secondary leading-relaxed">
                                    Secara default, seluruh siswa tersetting <strong>Naik Kelas</strong>. Jika terdapat siswa yang tidak memenuhi kriteria kenaikan kelas, ubah keputusan pada baris siswa tersebut menjadi <em>Tinggal Kelas</em> atau <em>Pindah</em>.
                                </p>
                            </div>

                            <div class="p-4 rounded-xl border border-border bg-surface-hover/50 space-y-1.5">
                                <span class="font-bold text-brand-600 dark:text-brand-400">Langkah 5: Eksekusi Kenaikan Kelas</span>
                                <p class="text-text-secondary leading-relaxed">
                                    Klik tombol hijau <strong>"🚀 Eksekusi Kenaikan Kelas"</strong> di bagian bawah dan klik Konfirmasi pada dialog pengaman. Dalam hitungan detik, seluruh status siswa diperbarui secara atomik dalam database.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Penanganan Siswa Tinggal Kelas & Mutasi -->
                    <div class="space-y-4 text-sm text-text-main">
                        <div class="flex items-center gap-2">
                            <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300 font-bold text-xs">4</span>
                            <h3 class="font-bold text-base text-text-main">Penanganan Kasus: Siswa Tinggal Kelas &amp; Mutasi</h3>
                        </div>

                        <div class="space-y-3">
                            <!-- Tinggal Kelas -->
                            <div class="p-4 rounded-xl border border-amber-300 dark:border-amber-800 bg-amber-50/50 dark:bg-amber-950/20 space-y-2 text-xs">
                                <div class="flex items-center gap-2 font-bold text-amber-800 dark:text-amber-300">
                                    <span>⚠️</span> Kasus A: Siswa Tinggal Kelas (Mengulang)
                                </div>
                                <p class="text-text-secondary leading-relaxed">
                                    Jika ada siswa yang diputuskan mengulang pada tingkat yang sama berdasarkan hasil pleno dewan guru:
                                </p>
                                <ol class="list-decimal list-inside space-y-1 text-text-secondary pl-1">
                                    <li>Ubah dropdown kolom <strong>Keputusan</strong> pada baris siswa menjadi <strong>Tinggal Kelas</strong>.</li>
                                    <li>Sistem secara dinamis menyaring pilihan pada kolom <strong>Rombel Tujuan</strong> agar hanya menampilkan rombel setingkat pada T.A. Baru (misal tetap tingkat X, seperti <em>X PPLG 1 T.A. 2025/2026</em>).</li>
                                    <li>Tuliskan catatan pendukung pada kolom catatan (misal: <em>"Berdasarkan Rapat Pleno Dewan Guru tgl 20 Juni 2025, dinyatakan mengulang"</em>).</li>
                                    <li>Hasil di sistem: Siswa tetap aktif, ditempatkan di kelas X tahun baru, dan tercatat di riwayat mutasi dengan status <code>tinggal_kelas</code>.</li>
                                </ol>
                            </div>

                            <!-- Pindah Sekolah / DO -->
                            <div class="p-4 rounded-xl border border-rose-200 dark:border-rose-900 bg-rose-50/50 dark:bg-rose-950/20 space-y-2 text-xs">
                                <div class="flex items-center gap-2 font-bold text-rose-800 dark:text-rose-300">
                                    <span>🚪</span> Kasus B: Siswa Pindah Sekolah atau Dikeluarkan (DO)
                                </div>
                                <p class="text-text-secondary leading-relaxed">
                                    Jika siswa mengundurkan diri atau pindah sekolah di akhir tahun ajaran:
                                </p>
                                <ul class="list-disc list-inside space-y-1 text-text-secondary pl-1">
                                    <li>Pilih <strong>Pindah Sekolah</strong> atau <strong>Dikeluarkan</strong>.</li>
                                    <li>Sistem secara otomatis mengosongkan rombel tujuan dan mengubah status siswa menjadi <code>is_active = false</code>.</li>
                                    <li>Siswa tidak akan membebani kuota rombel baru maupun daftar hadir guru, tetapi seluruh arsip nilai masa lalu tetap tersimpan utuh di sistem.</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- 5. Dampak Menyeluruh ke Modul Lain -->
                    <div class="space-y-4 text-sm text-text-main">
                        <div class="flex items-center gap-2">
                            <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300 font-bold text-xs">5</span>
                            <h3 class="font-bold text-base text-text-main">Dampak Menyeluruh ke Modul Lain dalam SIRAGA</h3>
                        </div>
                        <p class="text-xs text-text-secondary leading-relaxed">
                            Kenaikan kelas mengikat erat sinkronisasi antar modul secara otomatis:
                        </p>

                        <div class="overflow-x-auto rounded-xl border border-border">
                            <table class="min-w-full divide-y divide-border text-xs">
                                <thead class="bg-surface-hover text-text-secondary font-bold">
                                    <tr>
                                        <th class="px-4 py-2.5 text-left w-44">Modul Aplikasi</th>
                                        <th class="px-4 py-2.5 text-left">Apa yang Terjadi Setelah Kenaikan Kelas?</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-border">
                                    <tr>
                                        <td class="px-4 py-2.5 font-semibold text-text-main flex items-center gap-1.5">
                                            <span>📊</span> Modul Penilaian
                                        </td>
                                        <td class="px-4 py-2.5 text-text-secondary">
                                            Guru mapel di Tahun Ajaran Baru langsung melihat daftar siswa baru di rombelnya. Buku nilai tahun lalu tetap dapat ditinjau oleh guru mapel lama dengan memilih filter tahun ajaran/rombel lama.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-2.5 font-semibold text-text-main flex items-center gap-1.5">
                                            <span>📄</span> Modul Rapor &amp; Ledger
                                        </td>
                                        <td class="px-4 py-2.5 text-text-secondary">
                                            Wali kelas dan bagian kurikulum dapat mencetak ulang rapor maupun leger nilai masa lalu kapan saja. Siswa yang sudah naik ke tingkat XI tetap muncul di leger kelas X tahun sebelumnya karena sistem mengenali histori rombel.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-2.5 font-semibold text-text-main flex items-center gap-1.5">
                                            <span>📅</span> Modul Absensi
                                        </td>
                                        <td class="px-4 py-2.5 text-text-secondary">
                                            Daftar hadir harian dan presensi per mata pelajaran langsung sinkron dengan rombel baru di tahun ajaran baru. Rekap absensi tahun lalu tetap tersimpan rapi sebagai arsip kehadiran.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-2.5 font-semibold text-text-main flex items-center gap-1.5">
                                            <span>🎓</span> Kelas XII (Tingkat Akhir)
                                        </td>
                                        <td class="px-4 py-2.5 text-text-secondary">
                                            Siswa kelas XII <strong>tidak diproses di menu Kenaikan Kelas</strong>. Kelas XII diproses khusus pada menu <strong>Kesiswaan &gt; Kelulusan Siswa</strong> untuk dialihkan ke Modul Alumni.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- 6. Idempotensi, Koreksi Kesalahan & FAQ -->
                    <div class="space-y-4 text-sm text-text-main">
                        <div class="flex items-center gap-2">
                            <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 font-bold text-xs">6</span>
                            <h3 class="font-bold text-base text-text-main">Idempotensi, Perbaikan Data &amp; Pertanyaan Umum (FAQ)</h3>
                        </div>

                        <div class="space-y-3 text-xs">
                            <div class="p-4 rounded-xl border border-border bg-surface space-y-2">
                                <strong class="text-text-main text-xs block">
                                    Q1: Apakah proses kenaikan kelas bisa diulang atau dikoreksi jika operator salah klik?
                                </strong>
                                <p class="text-text-secondary leading-relaxed">
                                    <strong>Ya, sangat aman.</strong> Sistem SIRAGA bersifat <em>idempoten</em>. Jika Anda keliru menandai siswa naik kelas padahal seharusnya tinggal kelas (atau sebaliknya):
                                </p>
                                <ol class="list-decimal list-inside space-y-1 text-text-secondary pl-1">
                                    <li>Buka kembali menu <em>Kesiswaan &gt; Kenaikan Kelas</em>.</li>
                                    <li>Pilih Rombel Asal yang sama dan T.A. Tujuan yang sama.</li>
                                    <li>Sistem akan mendeteksi status tersimpan sebelumnya (ditandai dengan badge hijau <code>✓ Tersimpan</code>).</li>
                                    <li>Koreksi status siswa yang keliru, lalu klik kembali tombol <strong>"Eksekusi Kenaikan Kelas"</strong>.</li>
                                    <li>Sistem akan memperbarui penempatan rombel dan memperbarui riwayat mutasi tanpa membuat duplikasi baris.</li>
                                </ol>
                            </div>

                            <div class="p-4 rounded-xl border border-border bg-surface space-y-2">
                                <strong class="text-text-main text-xs block">
                                    Q2: Mengapa Rombel Tujuan Utama atau dropdown Rombel Tujuan kosong?
                                </strong>
                                <p class="text-text-secondary leading-relaxed">
                                    Rombongan belajar untuk Tahun Ajaran Tujuan belum dibuat. Silakan buka menu <em>Akademik &gt; Rombongan Belajar</em>, lalu klik tombol <strong>"Duplikasi Rombel ke T.A. Baru"</strong> untuk menghasilkan rombel tingkat XI &amp; XII secara otomatis.
                                </p>
                            </div>

                            <div class="p-4 rounded-xl border border-border bg-surface space-y-2">
                                <strong class="text-text-main text-xs block">
                                    Q3: Bagaimana jika ada perombakan kelas (misal siswa di kelas X PPLG 1 disebar ke XI PPLG 1 dan XI PPLG 2)?
                                </strong>
                                <p class="text-text-secondary leading-relaxed">
                                    Sistem mendukung pembagian acak atau perombakan rombel. Pada tabel siswa, Anda dapat memilih <strong>Rombel Tujuan</strong> yang berbeda untuk masing-masing siswa secara perorangan sebelum menekan tombol eksekusi.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- ========================================== -->
                <!-- BAB 5: KELULUSAN SISWA KELAS XII           -->
                <!-- ========================================== -->
                @elseif($activeChapter === 'kelulusan-siswa')
                <div class="space-y-6">
                    <div class="border-b border-border pb-4">
                        <span class="text-xs font-bold uppercase tracking-wider text-brand-600 dark:text-brand-400">Bab 5</span>
                        <h2 class="text-2xl font-bold text-text-main mt-1">Panduan Kelulusan Siswa Tingkat Akhir (Kelas XII)</h2>
                        <p class="text-sm text-text-secondary mt-1">Prosedur kelulusan massal kelas XII dan pengalihan otomatis ke database Alumni.</p>
                    </div>

                    <div class="space-y-4 text-sm text-text-main">
                        <h3 class="font-bold text-base text-text-main">1. Apa yang Terjadi saat Kelas XII Dinyatakan Lulus?</h3>
                        <ul class="text-xs space-y-2 list-disc list-inside text-text-secondary">
                            <li>Status siswa pada data induk berubah dari <code>aktif</code> menjadi <code>lulus</code>.</li>
                            <li>Status keaktifan (<code>is_active</code>) disetel ke <code>false</code> sehingga siswa tidak lagi muncul di absensi kelas aktif atau penginputan nilai harian.</li>
                            <li><strong>Otomatis Terdaftar sebagai Alumni:</strong> Sistem secara otomatis membuatkan kartu profil di <strong>Modul Alumni</strong> lengkap dengan Tahun Lulus dan Nomor Ijazah.</li>
                            <li>Riwayat kelulusan dicatat permanen di <code>riwayat_akademiks</code>.</li>
                        </ul>

                        <h3 class="font-bold text-base text-text-main mt-6">2. Langkah Eksekusi Kelulusan:</h3>
                        <ol class="space-y-2 text-xs list-decimal list-inside text-text-secondary">
                            <li>Buka menu <strong>Kesiswaan &gt; Kelulusan Siswa</strong>.</li>
                            <li>Pilih Tahun Ajaran Kelulusan dan Rombel Kelas XII yang ingin diproses (atau pilih Semua Kelas XII).</li>
                            <li>Tinjau daftar siswa: secara default seluruh siswa berstatus <strong>Lulus</strong>. Jika ada siswa yang belum lulus/mengulang, ubah statusnya menjadi <em>Tidak Lulus / Mengulang</em>.</li>
                            <li>(Opsional) Anda dapat mengisikan Nomor Seri Ijazah secara manual per baris atau menggunakan fitur <strong>Format Prefix Nomor Ijazah</strong> di bagian atas.</li>
                            <li>Klik tombol <strong>"🎓 Eksekusi Kelulusan"</strong>.</li>
                        </ol>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- BAB 6: PENGELOLAAN ALUMNI & BMW            -->
                <!-- ========================================== -->
                @elseif($activeChapter === 'pengelolaan-alumni')
                <div class="space-y-6">
                    <div class="border-b border-border pb-4">
                        <span class="text-xs font-bold uppercase tracking-wider text-brand-600 dark:text-brand-400">Bab 6</span>
                        <h2 class="text-2xl font-bold text-text-main mt-1">Pengelolaan Data Alumni &amp; Tracer Study (BMW)</h2>
                        <p class="text-sm text-text-secondary mt-1">Standar pelacakan tamatan SMK: Bekerja, Melanjutkan Kuliah, dan Wirausaha.</p>
                    </div>

                    <div class="space-y-4 text-sm text-text-main">
                        <p class="text-xs text-text-secondary leading-relaxed">
                            Modul Alumni didesain khusus bagi Koordinator BKK (Bursa Kerja Khusus), Koordinator Alumni, dan Bagian Kesiswaan untuk memantau serapan lulusan SMK Wikrama 1 Garut di dunia kerja dan perguruan tinggi.
                        </p>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 my-2">
                            <div class="p-3 rounded-xl border border-blue-200 dark:border-blue-900 bg-blue-50/50 dark:bg-blue-950/20 text-xs">
                                <strong class="text-blue-700 dark:text-blue-300 block mb-1">💼 Bekerja (B)</strong>
                                <span class="text-text-secondary">Tamatan yang diterima bekerja di industri, kantor, pabrik, atau instansi pemerintahan.</span>
                            </div>
                            <div class="p-3 rounded-xl border border-purple-200 dark:border-purple-900 bg-purple-50/50 dark:bg-purple-950/20 text-xs">
                                <strong class="text-purple-700 dark:text-purple-300 block mb-1">🎓 Melanjutkan (M)</strong>
                                <span class="text-text-secondary">Tamatan yang menempuh pendidikan tinggi (Diploma, Sarjana, atau Akademi).</span>
                            </div>
                            <div class="p-3 rounded-xl border border-emerald-200 dark:border-emerald-900 bg-emerald-50/50 dark:bg-emerald-950/20 text-xs">
                                <strong class="text-emerald-700 dark:text-emerald-300 block mb-1">🚀 Wirausaha (W)</strong>
                                <span class="text-text-secondary">Tamatan yang merintis usaha mandiri, startup, perdagangan, atau jasa.</span>
                            </div>
                        </div>

                        <h3 class="font-bold text-base text-text-main mt-4">Fitur Utama Modul Alumni:</h3>
                        <ul class="text-xs space-y-2 list-disc list-inside text-text-secondary">
                            <li><strong>Pencarian &amp; Filter Cepat:</strong> Filter berdasarkan angkatan (tahun lulus) dan status karir.</li>
                            <li><strong>Pembaruan Data Tracer:</strong> Klik tombol <em>Edit Tracer</em> untuk mencatat nama perusahaan, program studi kampus, no HP WhatsApp terbaru, dan LinkedIn.</li>
                            <li><strong>Export Excel Resmi:</strong> Tombol <strong>"📥 Export Excel (.xlsx)"</strong> menghasilkan spreadsheet berformat rapi dengan kop dan tabel lengkap yang siap diserahkan ke Dinas Pendidikan atau Pengawas Sekolah.</li>
                        </ul>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- BAB 7: FAQ & TROUBLESHOOTING               -->
                <!-- ========================================== -->
                @elseif($activeChapter === 'faq-troubleshooting')
                <div class="space-y-6">
                    <div class="border-b border-border pb-4">
                        <span class="text-xs font-bold uppercase tracking-wider text-brand-600 dark:text-brand-400">Bab 7</span>
                        <h2 class="text-2xl font-bold text-text-main mt-1">Tanya Jawab (FAQ) &amp; Penyelesaian Kendala</h2>
                        <p class="text-sm text-text-secondary mt-1">Jawaban atas pertanyaan yang sering diajukan seputar siklus akademik.</p>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div class="p-4 rounded-xl border border-border bg-surface-hover space-y-1.5">
                            <h4 class="font-bold text-text-main text-sm">Q: Bagaimana jika guru masih perlu memperbaiki nilai semester yang sudah dikunci?</h4>
                            <p class="text-text-secondary leading-relaxed">
                                A: Admin atau bagian Kurikulum dapat membuka kembali kunci nilai di menu <em>Tahun Ajaran &amp; Semester</em> dengan mengklik tombol <strong>(Buka)</strong> pada status nilai semester terkait. Setelah selesai diedit, pastikan untuk menguncinya kembali.
                            </p>
                        </div>

                        <div class="p-4 rounded-xl border border-border bg-surface-hover space-y-1.5">
                            <h4 class="font-bold text-text-main text-sm">Q: Apakah siswa yang tinggal kelas nilainya hilang?</h4>
                            <p class="text-text-secondary leading-relaxed">
                                A: Tidak. Seluruh nilai rapor tahun sebelumnya tetap tersimpan utuh di database. Pada tahun ajaran baru, siswa tersebut akan memulai lembar penilaian baru di tingkat yang sama.
                            </p>
                        </div>

                        <div class="p-4 rounded-xl border border-border bg-surface-hover space-y-1.5">
                            <h4 class="font-bold text-text-main text-sm">Q: Apakah nomor ijazah alumni harus diisi saat kelulusan?</h4>
                            <p class="text-text-secondary leading-relaxed">
                                A: Tidak wajib saat itu juga. Jika blangko ijazah belum terbit dari kementerian, Anda dapat mengosongkannya terlebih dahulu, lalu memperbaruinya kemudian melalui menu <em>Pengelolaan Alumni</em>.
                            </p>
                        </div>
                    </div>
                </div>
                @endif

                @else
                <!-- ========================================== -->
                <!-- PANDUAN KHUSUS SISWA & ORANG TUA          -->
                <!-- ========================================== -->
                @if($activeChapter === 'portal-siswa-ortu')
                <div class="space-y-6">
                    <div class="border-b border-border pb-4">
                        <span class="text-xs font-bold uppercase tracking-wider text-brand-600 dark:text-brand-400">Panduan Siswa &amp; Orang Tua</span>
                        <h2 class="text-2xl font-bold text-text-main mt-1">Selamat Datang di Portal SIRAGA</h2>
                        <p class="text-sm text-text-secondary mt-1">Panduan dasar pemanfaatan sistem informasi akademik bagi peserta didik dan wali murid.</p>
                    </div>

                    <div class="prose dark:prose-invert max-w-none text-sm text-text-main space-y-4">
                        <p class="text-xs text-text-secondary leading-relaxed">
                            Melalui portal ini, Anda dapat memantau kehadiran harian di sekolah secara transparan, melihat capaian nilai mata pelajaran, serta mengunduh dokumen laporan hasil belajar (e-Rapor).
                        </p>
                    </div>
                </div>

                @elseif($activeChapter === 'cek-kehadiran')
                <div class="space-y-6">
                    <div class="border-b border-border pb-4">
                        <span class="text-xs font-bold uppercase tracking-wider text-brand-600 dark:text-brand-400">Panduan Kehadiran</span>
                        <h2 class="text-2xl font-bold text-text-main mt-1">Cara Memeriksa Catatan Kehadiran Siswa</h2>
                        <p class="text-sm text-text-secondary mt-1">Pantau rekapitulasi kehadiran (Hadir, Sakit, Izin, dan Alpa).</p>
                    </div>

                    <div class="space-y-3 text-xs text-text-secondary">
                        <p>1. Buka menu <strong>Absensi Siswa</strong> di bilah navigasi samping.</p>
                        <p>2. Anda akan melihat rekapitulasi total kehadiran bulan berjalan dan semester berjalan.</p>
                        <p>3. Jika terdapat catatan Alpa (tanpa keterangan) melebihi 3 hari, sistem akan menampilkan notifikasi peringatan dini agar segera dikonfirmasikan ke Pembimbing Rayon atau Wali Kelas.</p>
                    </div>
                </div>

                @elseif($activeChapter === 'unduh-rapor')
                <div class="space-y-6">
                    <div class="border-b border-border pb-4">
                        <span class="text-xs font-bold uppercase tracking-wider text-brand-600 dark:text-brand-400">Panduan e-Rapor</span>
                        <h2 class="text-2xl font-bold text-text-main mt-1">Cara Melihat &amp; Mengunduh e-Rapor</h2>
                        <p class="text-sm text-text-secondary mt-1">Unduh lembar hasil belajar resmi dalam format PDF.</p>
                    </div>

                    <div class="space-y-3 text-xs text-text-secondary">
                        <p>1. Masuk ke menu <strong>Rapor &gt; Cetak Rapor</strong>.</p>
                        <p>2. Pastikan periode semester yang dipilih sesuai dengan semester yang ingin dilihat.</p>
                        <p>3. Klik tombol <strong>"Unduh e-Rapor PDF"</strong> untuk menyimpan berkas rapor resmi.</p>
                        <p>4. Pada rapor semester genap, Anda dapat melihat keterangan resmi status <strong>Kenaikan Kelas</strong> atau <strong>Kelulusan</strong>.</p>
                    </div>
                </div>

                @elseif($activeChapter === 'ketentuan-kenaikan-kelulusan')
                <div class="space-y-6">
                    <div class="border-b border-border pb-4">
                        <span class="text-xs font-bold uppercase tracking-wider text-brand-600 dark:text-brand-400">Informasi Akademik</span>
                        <h2 class="text-2xl font-bold text-text-main mt-1">Kriteria Kenaikan Kelas &amp; Kelulusan</h2>
                        <p class="text-sm text-text-secondary mt-1">Syarat dan ketentuan akademis di SMK Wikrama 1 Garut.</p>
                    </div>

                    <div class="space-y-3 text-xs text-text-secondary leading-relaxed">
                        <p><strong>Kriteria Kenaikan Kelas:</strong></p>
                        <ul class="list-disc list-inside space-y-1">
                            <li>Menyelesaikan seluruh program pembelajaran pada semester 1 dan semester 2.</li>
                            <li>Memperoleh capaian kompetensi minimal pada seluruh mata pelajaran kejuruan dan umum.</li>
                            <li>Kehadiran efektif minimal 85% dari total hari efektif belajar.</li>
                            <li>Memiliki nilai sikap dan kepribadian minimal kategori 'Baik'.</li>
                        </ul>

                        <p class="mt-4"><strong>Kriteria Kelulusan (Kelas XII):</strong></p>
                        <ul class="list-disc list-inside space-y-1">
                            <li>Menyelesaikan seluruh program pembelajaran dari semester 1 hingga semester 6.</li>
                            <li>Lulus Uji Kompetensi Keahlian (UKK) kejuruan.</li>
                            <li>Telah menempuh dan menyelesaikan Praktik Kerja Lapangan (PKL).</li>
                        </ul>
                    </div>
                </div>

                @elseif($activeChapter === 'tracer-study-alumni')
                <div class="space-y-6">
                    <div class="border-b border-border pb-4">
                        <span class="text-xs font-bold uppercase tracking-wider text-brand-600 dark:text-brand-400">Portal Tamatan</span>
                        <h2 class="text-2xl font-bold text-text-main mt-1">Pemutakhiran Data Tamatan / Alumni</h2>
                        <p class="text-sm text-text-secondary mt-1">Bagi siswa kelas XII yang telah dinyatakan lulus.</p>
                    </div>

                    <div class="space-y-3 text-xs text-text-secondary leading-relaxed">
                        <p>Setelah dinyatakan Lulus, data Anda akan tersimpan di pangkalan data Alumni SMK Wikrama 1 Garut.</p>
                        <p>Pastikan Anda selalu memperbarui informasi kontak (No WhatsApp dan Email aktif) serta status pekerjaan/kuliah/wirausaha Anda ke BKK (Bursa Kerja Khusus) sekolah agar mendapatkan info lowongan kerja dan penyaluran karir.</p>
                    </div>
                </div>
                @endif

                @endif

            </div>
        </div>

    </div>
</div>
