# Audit Menyeluruh Aplikasi SIRAGA

**What this repo does:** SIRAGA (Sistem Informasi Rujukan / Manajemen Sekolah) mengelola data akademik Kurikulum Merdeka, administrasi kesiswaan dan DAPODIK, presensi harian, penilaian blok 9 Minggu, pencetakan rapor (Wikrama & Pemerintah), beasiswa orang tua asuh (OTA), inventaris sarana prasarana, serta situs publik SMK Wikrama 1 Garut. Beban yang diasumsikan adalah 800–1.500 siswa aktif, 60–100 guru dan tendik, serta ratusan wali murid yang mengakses sistem bersamaan, dengan konkurensi puncak 30–100 request simultan saat pengisian nilai massal, presensi pagi, dan pembagian rapor semester.

---

## Must fix

1. **Endpoint AI Assistant Menjalankan Kueri SQL Bebas Tanpa Proteksi Akses & Membocorkan Data Finansial** (`app/Modules/AiAssistant/Controllers/ChatApiController.php:L52-98`, `app/Modules/AiAssistant/routes.php:L10`)
   - **What this is:** Fitur asisten cerdas yang menerjemahkan pertanyaan bahasa alami menjadi kueri SQL melalui Google Gemini lalu mengeksekusinya ke basis data via `DB::select()`.
   - **Problem:** Rute `/ai-assistant/chat` tidak memiliki filter peran sehingga dapat diakses siswa atau pengguna level rendah. Daftar blokir kueri hanya menyaring tabel sistem bawaan (`users`, `audits`), sehingga pengguna dapat meminta data rahasia seperti setoran donatur, saldo kas OTA, mutasi uang, atau identitas pribadi siswa secara bebas melalui kueri SQL yang dihasilkan AI.
   - **Fix:** Bungkus rute dengan middleware `role_or_permission:Super Admin|Admin|Kurikulum|Guru`, tambahkan tabel finansial (`kas_mutasis`, `beasiswa_ota_*`, `donaturs`) ke daftar blokir, dan simpan skema tabel dalam cache.
   - **If we skip it:** Pengguna non-staf dapat mengekstrak data keuangan sekolah dan data pribadi siswa, serta menghabiskan kuota Gemini API sekolah.

2. **27 Komponen Livewire Mengubah Data Tanpa Pengecekan Otorisasi Internal (Bypass IDOR)** (`app/Modules/Prestasi/Livewire/PrestasiGuruIndex.php:L238-270`, `app/Modules/Sarpras/Livewire/AsetIndex.php:L180`, `app/Modules/AdministrasiWeb/Livewire/ArticleForm.php`, dll.)
   - **What this is:** Komponen Livewire yang menyediakan aksi manipulasi data seperti `save()`, `delete()`, dan `update()` di berbagai modul (Prestasi, Sarpras, Web, OTA, Seni Budaya).
   - **Problem:** Meskipun rute awal dilindungi middleware, aksi AJAX Livewire dikirim ke endpoint `/livewire/update` tanpa verifikasi ulang hak akses di dalam method mutasi. Pengguna berstatus staf biasa yang memodifikasi payload JavaScript dapat memanggil method `delete($id)` atau `save()` pada data yang bukan kewenangannya.
   - **Fix:** Pasang trait `App\Core\Concerns\GuardsPermission` pada 27 komponen tersebut dan panggil `$this->guard('permission.name')` di baris pertama setiap method mutasi.
   - **If we skip it:** Terjadi celah Broken Access Control di mana pengguna dapat menghapus aset sekolah, berita web, atau prestasi guru lain secara langsung.

3. **Tabel Nilai Siswa Tidak Memiliki Kunci Unik Sehingga Memicu Duplikasi Nilai & Rata-rata Bias** (`app/Modules/Penilaian/Database/Migrations/2026_10_04_710000_create_penilaian_tables.php:L40-52`)
   - **What this is:** Tabel penyimpanan nilai akademik siswa per penugasan mengajar (`nilai_akhirs`, `nilai_sumatifs`, dan `nilai_formatifs`).
   - **Problem:** Skema basis data tidak memiliki *unique constraint* kombinasi `['siswa_id', 'penugasan_mengajar_id']`. Proses impor nilai massal atau klik tombol ganda akan memasukkan baris nilai ganda untuk siswa yang sama, mengacaukan perhitungan `AVG(nilai_akhir)` di dashboard dan menghasilkan data rapor yang tidak konsisten.
   - **Fix:** Buat migrasi baru yang membersihkan duplikat historis lalu menambahkan indeks unik `$table->unique(['siswa_id', 'penugasan_mengajar_id'])` pada tabel `nilai_akhirs` dan `nilai_sumatifs`.
   - **If we skip it:** Nilai rata-rata sekolah di dashboard menjadi bias dan cetak rapor dapat menampilkan data acak dari baris duplikat.

4. **Race Condition Penyaluran Kas OTA Tanpa Kunci Baris Mengakibatkan Saldo Menjadi Negatif** (`app/Modules/BeasiswaOta/Services/OtaKasService.php:L215-260`)
   - **What this is:** Layanan `catatAlokasiRutin` yang memvalidasi sisa saldo kas kelompok donatur sebelum mencairkan beasiswa 6 bulanan siswa.
   - **Problem:** Pengecekan saldo dilakukan tanpa *pessimistic row locking* (`lockForUpdate()`). Jika dua operator memproses penyaluran untuk siswa berbeda dalam kelompok yang sama di waktu bersamaan, keduanya lolos validasi saldo lama dan menulis transaksi keluar, sehingga kas menjadi minus.
   - **Fix:** Kunci baris kelompok di awal transaksi menggunakan `Kelompok::where('id', $id)->lockForUpdate()->first()` sebelum menghitung saldo.
   - **If we skip it:** Saldo kas beasiswa dapat minus saat penyaluran massal di awal semester dan pembukuan keuangan menjadi selisih.

5. **Pencetakan Label QR Sarpras Memanggil API Eksternal Secara Sinkron Sehingga Gagal Saat Offline** (`app/Modules/Sarpras/Services/QrCodeService.php:L15-28`)
   - **What this is:** Layanan pembuatan kode QR untuk label inventaris aset barang dan berkas berita acara sarana prasarana.
   - **Problem:** Layanan mengarahkan tautan ke server pihak ketiga `https://api.qrserver.com/v1/create-qr-code/`. Saat mencetak 50 label aset melalui DomPDF, server melakukan 50 panggilan HTTP keluar yang memblokir proses; jika internet putus atau API luar lambat, proses cetak langsung gagal dengan error *connection timeout*.
   - **Fix:** Ganti pemanggilan URL eksternal dengan generator SVG QR Code lokal berbasis PHP murni yang bekerja mandiri tanpa koneksi internet.
   - **If we skip it:** Fitur cetak label inventaris tidak dapat digunakan pada jaringan lokal tanpa internet dan membocorkan kode aset sekolah ke luar.

6. **Kalkulasi Hari Efektif Presensi Menghitung Hari Libur Nasional & Sekolah Sebagai Hari Masuk** (`app/Modules/Absensi/Services/RekapAbsensiService.php:L22-42`, `L124`)
   - **What this is:** Fungsi `hitungHariEfektif` yang menentukan total hari kegiatan belajar mengajar untuk menghitung persentase kehadiran siswa.
   - **Problem:** Fungsi hanya mengecek hari kerja Senin–Jumat (`!$current->isWeekend()`) dan mengabaikan tabel `KalenderLibur`. Pada bulan yang memiliki 10 hari libur Idul Fitri atau cuti bersama, siswa yang selalu hadir tercatat hanya masuk 12 dari 22 hari, sehingga persentase kehadirannya anjlok menjadi 54.5% pada rekap resmi.
   - **Fix:** Ambil data tanggal dari `KalenderLibur` bertipe `is_kbm_libur = true` dan kurangkan dari total hari kerja dalam perulangan `hitungHariEfektif`.
   - **If we skip it:** Rekapitulasi absensi bulanan, laporan wali kelas, dan persentase kehadiran siswa di rapor menjadi salah hitung pada bulan yang memiliki hari libur.

7. **Status Siswa dan Flag Aktif Tidak Tersinkronisasi Sehingga Siswa Non-Aktif Muncul di Rapor** (`app/Modules/Kesiswaan/Models/Siswa.php:L69-70`, `app/Modules/Kesiswaan/Livewire/SiswaEdit.php:L240-241`)
   - **What this is:** Dua kolom status pada model siswa: `status` (enum: 'aktif', 'lulus', 'pindah', 'dikeluarkan') dan `is_active` (boolean).
   - **Problem:** Kedua kolom tidak terhubung otomatis di antarmuka edit. Jika staf mengubah `status` menjadi 'pindah' tetapi membiarkan `is_active` tetap bernilai `true`, modul presensi akan menyembunyikan siswa tersebut (`where status = aktif`), tetapi modul cetak rapor massal tetap memuatnya (`where is_active = true`).
   - **Fix:** Tambahkan event listener `saving` pada model `Siswa` agar nilai `is_active` otomatis mengikuti (`is_active = ($status === 'aktif')`).
   - **If we skip it:** Siswa yang sudah keluar atau pindah sekolah tetap dicetak rapornya dengan halaman kosong saat pencetakan satu rombel.

---

## Should fix

8. **Middleware `web` dan `auth` Didaftarkan Berulang Dua Kali pada 7 Modul** (`app/Core/Providers/ModuleServiceProvider.php:L36-38`, `app/Modules/Rapor/routes.php:L14`, dll.)
   - **What this is:** Pendaftaran rute modular di mana `ModuleServiceProvider` memuat rute dari file `routes.php` tiap modul.
   - **Problem:** `ModuleServiceProvider` sudah membungkus seluruh rute modul dengan middleware `['web', 'auth', 'active', 'password.fresh']`. Namun 7 modul (`Rapor`, `Penilaian`, `Absensi`, `BeasiswaOta`, `Ekstrakurikuler`, `Prestasi`, `SeniBudaya`) kembali membungkus rutenya dengan `Route::middleware(['web', 'auth'])`, menyebabkan session dan verifikasi token dieksekusi dobel di setiap request.
   - **Fix:** Hapus pembungkus `Route::middleware(['web', 'auth'])` pada file `routes.php` modul-modul tersebut.
   - **If we skip it:** Siklus hidup request HTTP menjalankan middleware dobel yang memboroskan memori dan waktu eksekusi CPU server.

9. **Pengecekan Super Admin `Gate::before` Didaftarkan Ganda di Dua Service Provider Berbeda** (`app/Providers/AppServiceProvider.php:L23-25`, `app/Core/Providers/ModuleServiceProvider.php:L61`)
   - **What this is:** Bypass hak akses global untuk pengguna dengan peran Super Admin.
   - **Problem:** `AppServiceProvider` dan `ModuleServiceProvider` sama-sama mendaftarkan closure `Gate::before` yang memeriksa `isSuperAdmin()`, sehingga setiap pengecekan `@can` di Blade atau middleware menjalankan evaluasi peran dua kali berturut-turut.
   - **Fix:** Hapus deklarasi `Gate::before` dari `AppServiceProvider.php` dan pusatkan sepenuhnya di `ModuleServiceProvider.php`.
   - **If we skip it:** Ratusan pengecekan hak akses di bilah navigasi dan tabel daftar menjalankan kueri duplikat pada setiap tampilan halaman.

10. **Pemeriksaan Skema Tabel Runtime Dijalankan pada Setiap Render Widget Dashboard** (`app/Core/Livewire/ExecutiveSummaryWidget.php:L65-66`)
    - **What this is:** Widget ringkasan eksekutif pada dashboard utama yang menampilkan grafik rata-rata nilai mata pelajaran.
    - **Problem:** Kode memanggil `DB::getSchemaBuilder()->hasColumn('mapels', 'nama_mapel')` di setiap render widget untuk menentukan nama kolom. Kueri ke tabel sistem `information_schema` ini lambat dan tidak perlu karena kolom `nama` pada tabel `mapels` sudah baku sejak migrasi awal.
    - **Fix:** Hapus pemanggilan `hasColumn()` dan langsung gunakan kolom `'mapels.nama'`.
    - **If we skip it:** Halaman dashboard utama membuang waktu 15–30ms per request hanya untuk memeriksa kolom yang tidak pernah berubah.

11. **Fungsi Preload Cohort Rapor Mengabaikan Model Nilai Sehingga Memicu Kueri N+1 Saat Cetak Massal** (`app/Modules/Rapor/Services/RaporScoreService.php:L48-103`, `L280-298`)
    - **What this is:** Fitur optimasi `preloadCohort()` untuk mempercepat cetak rapor 1 kelas dalam format PDF.
    - **Problem:** `preloadCohort()` hanya memuat catatan, absensi, ekskul, dan prestasi. Di dalam method `getWikramaAcademicRecords()`, kueri untuk `NilaiAkhir`, `NilaiFormatif`, `NilaiHarian`, dan `NilaiSumatif` tetap dijalankan per siswa secara mandiri; mencetak 36 siswa memicu sekitar 180 kueri SQL.
    - **Fix:** Perluas `preloadCohort()` agar memuat keempat tabel nilai secara batch menggunakan `whereIn('siswa_id', $siswaIds)` dan mengelompokkannya per ID siswa.
    - **If we skip it:** Pencetakan rapor massal satu angkatan berjalan lambat dan berisiko mengalami *maximum execution time exceeded*.

12. **State Livewire Menyimpan Seluruh Graf Model Eloquent pada Modal Preview Rapor** (`app/Modules/Rapor/Livewire/RaporWikramaIndex.php:L39`, `L234-249`)
    - **What this is:** Modal pratinjau lembar rapor siswa pada halaman evaluasi Rapor Wikrama.
    - **Problem:** Properti publik `$previewData` menampung array berisi objek `Siswa`, `Semester`, `School`, dan koleksi nilai bersarang. Livewire 3 menyerialisasi seluruh pohon objek Eloquent tersebut ke dalam format JSON berukuran ratusan kilobyte setiap kali modal dibuka atau ditutup.
    - **Fix:** Hanya simpan `$previewSiswaId` (integer) pada properti publik, lalu ubah data pratinjau menjadi *computed property* yang dihitung saat modal aktif.
    - **If we skip it:** Beban payload jaringan membengkak ratusan KB per interaksi klik dan browser pengguna menjadi lambat merespons.

13. **Pemindaian Direktori Manifest 18 Modul Dilakukan dari Disk pada Setiap Siklus Request HTTP** (`app/Core/Modules/ModuleRegistry.php:L37-53`)
    - **What this is:** Registry modul yang membaca file `module.php` dari direktori `app/Modules/*` untuk menyusun menu sidebar dan hak akses.
    - **Problem:** Method `all()` memeriksa disk dan memanggil `require` pada 18 file manifest di setiap request tanpa adanya mekanisme cache pada level produksi.
    - **Fix:** Bungkus hasil pemindaian manifest dalam `Cache::rememberForever('siraga_modules_manifest', ...)` pada lingkungan selain `local`.
    - **If we skip it:** Terjadi pembacaan I/O disk yang tidak perlu pada setiap request pengguna.

14. **Accessor Profil Akun Pengguna Menjalankan Rangkaian Kueri Berulang Tanpa Memoization** (`app/Models/User.php:L72-113`)
    - **What this is:** Accessor `$user->linked_profile` yang menampilkan nama lengkap, identitas (NIP/NISN), dan peran pengguna pada header navbar atas.
    - **Problem:** Jika pengguna bukan pegawai, accessor memeriksa relasi `pegawai` (hasil null), lalu kueri `siswa` (hasil null), lalu kueri tabel `roles` dan tabel `siswas` via username. Kueri ini tidak dimemoize dan dipanggil berulang kali di komponen layout.
    - **Fix:** Simpan hasil evaluasi accessor pada variabel properti internal `$this->cachedProfile` setelah panggilan pertama.
    - **If we skip it:** Menghasilkan 3-4 kueri basis data tambahan pada setiap halaman yang dibuka oleh pengguna terautentikasi.

15. **Arsitektur Penilaian Mapel Pilihan Mengkloning Total Modul Penilaian Reguler Tanpa Perlu** (`app/Modules/Penilaian/Models/NilaiMapelPilihan*`, `MapelPilihanScoreService`)
    - **What this is:** Struktur data khusus untuk mata pelajaran pilihan pada fase F Kurikulum Merdeka.
    - **Problem:** Sistem membuat 5 tabel terpisah (`nilai_mapil_*`), 4 model kembar, dan kalkulator nilai terpisah (`MapelPilihanScoreService`), alih-alih memanfaatkan skema penugasan mengajar yang sudah ada. Hal ini menambah beban pemeliharaan sekitar 800 baris kode duplikat.
    - **Fix:** Hubungkan logika kalkulasi skor pada `MapelPilihanScoreService` langsung ke `PenilaianScoreCalculator` agar rumus pembobotan 9M tetap bersumber dari satu titik acuan.
    - **If we skip it:** Setiap kali aturan atau pembobotan nilai 9M diubah oleh kurikulum, perubahan harus diketik ulang dua kali di dua modul berbeda.

---

## Nice to have

16. **Berkas Sampah Terminal Menumpuk di Direktori Utama Proyek** (`C:\xampp\htdocs\SIRAGA\bootstrap()`, `'`, `{`, `test_output.html`, `powershell.bat`)
    - **What this is:** File-file residu yang tidak sengaja terbentuk akibat kesalahan pengetikan pengalihan output terminal (*shell redirection*) saat pengembangan.
    - **Problem:** File bernama `'`, `{`, `bootstrap()`, dan `powershell.bat` berada di root direktori. Khususnya `powershell.bat` dapat mengintersepsi pemanggilan perintah PowerShell standar dari CLI.
    - **Fix:** Hapus file-file sampah tersebut dari direktori proyek dan pastikan git status bersih.
    - **If we skip it:** Direktori proyek tidak rapi dan perintah terminal berisiko terganggu oleh skrip batch lokal.

17. **File Backup dan Fitur Preview Landing Page Usang Tertinggal di Konfigurasi Inti** (`config/landing_v1_backup.php`, `resources/views/welcome_v1_backup.blade.php`, `routes/web.php:L9-16`)
    - **What this is:** Mekanisme darurat untuk menampilkan kembali landing page versi lama melalui parameter URL `?landing_version=classic`.
    - **Problem:** File `config/landing_v1_backup.php` otomatis dimuat ke dalam cache konfigurasi sistem (`config:cache`), dan rute beranda menjalankan logika percabangan session yang sudah tidak digunakan.
    - **Fix:** Hapus file backup konfigurasi dan view tersebut, lalu kembalikan rute `/` agar merender tampilan `welcome` secara langsung.
    - **If we skip it:** Kode produksi menyimpan artefak usang yang membingungkan pengembang baru dan membebani file konfigurasi.

18. **Pengurutan Interval Predikat Nilai Dilakukan Berulang di Dalam Loop Siswa** (`app/Modules/Penilaian/Services/PenilaianScoreCalculator.php:L41-45`)
    - **What this is:** Fungsi `resolvePredikat()` yang menentukan label kompetensi siswa (misal: "Kompeten", "Sangat Baik").
    - **Problem:** Di setiap kalkulasi satu siswa pada satu mata pelajaran, array interval predikat diurutkan ulang dengan `usort()`. Pada satu kelas dengan 15 mapel, pengurutan array statis yang sama terjadi 540 kali.
    - **Fix:** Lakukan pengurutan interval satu kali saja di konstruktor kelas atau simpan hasil urutan secara statis.
    - **If we skip it:** Pemborosan mikro-siklus CPU saat memproses ekspor nilai ribuan siswa.

19. **Kunci API Gemini Ditulis Langsung pada File `.env` Aktif** (`.env:L67`)
    - **What this is:** Konfigurasi kredensial `GEMINI_API_KEY` untuk modul kecerdasan buatan.
    - **Problem:** Nilai kunci API langsung tertulis pada file `.env`. Walaupun sudah diabaikan di `.gitignore`, template `.env.example` harus dipastikan bersih dan kunci di `.env` perlu dijaga agar tidak bocor lewat arsip backup lokal.
    - **Fix:** Pastikan `.env.example` hanya berisi placeholder kosong `GEMINI_API_KEY=""` dan dokumentasikan prosedur rotasi kunci jika pernah terekspos.
    - **If we skip it:** Kredensial API berisiko terekspos jika folder aplikasi dibackup secara manual dalam format zip.

20. **Perintah CLI `inject:dummy` Mengacu pada Nama Kolom yang Salah** (`app/Console/Commands/InjectDummy.php:L34`)
    - **What this is:** Perintah artisan `inject:dummy` untuk mengisi data guru contoh saat pengujian sprint.
    - **Problem:** Perintah mengisi kolom `'force_password_change' => false`, sedangkan nama kolom resmi pada tabel `users` adalah `must_change_password`.
    - **Fix:** Ganti nama kolom menjadi `'must_change_password' => false`.
    - **If we skip it:** Eksekusi perintah menghasilkan warning atau gagal mengubah status password awal pengguna pengujian.

---

## Ringkasan Akhir Audit

- **Verdict:** Aplikasi SIRAGA memiliki arsitektur modular yang solid dan cakupan fitur yang sangat matang untuk standar SMK. Prioritas utama yang harus segera diselesaikan adalah pengamanan endpoint AI & otorisasi Livewire (Phase 1), pencegahan race condition kas OTA, serta penambahan indeks unik pada tabel penilaian sebelum sistem digunakan pada puncak periode pembagian rapor.
- **Lean:** Potensi pembersihan: -4 file sampah, -2 file konfigurasi/view usang, dan sekitar -950 baris kode duplikat/redundansi yang dapat dihemat.
- **Not checked:** Tampilan visual responsif pada setiap perangkat mobile di 80+ file Blade views, dan beban stres konkurensi di atas 500 koneksi simultan pada server produksi sesungguhnya.
