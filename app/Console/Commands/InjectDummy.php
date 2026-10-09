<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kepegawaian\Models\PenugasanMengajar;
use App\Modules\Akademik\Models\Mapel;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Penilaian\Models\TujuanPembelajaran;
use App\Modules\Penilaian\Models\NilaiFormatif;
use App\Modules\Penilaian\Models\NilaiSumatif;
use App\Modules\Penilaian\Models\NilaiAkhir;
use Illuminate\Support\Facades\Hash;

class InjectDummy extends Command
{
    protected $signature = 'inject:dummy';
    protected $description = 'Inject dummy data for Sprint S7 testing';

    public function handle()
    {
        $this->info('Starting Dummy Data Injection...');

        // 1. Create a Dummy Teacher User
        $user = User::firstOrCreate(
            ['username' => 'guru_dummy'],
            [
                'name' => 'Budi Santoso, S.Kom',
                'password' => Hash::make('password'),
                'is_active' => true,
                'force_password_change' => false,
            ]
        );
        $user->assignRole('Guru');

        $pegawai = Pegawai::firstOrCreate(
            ['nip' => '198001012005011001'],
            [
                'user_id' => $user->id,
                'nama_lengkap' => 'Budi Santoso',
                'gelar_depan' => '',
                'gelar_belakang' => 'S.Kom',
                'jk' => 'L',
                'status_pegawai' => 'PNS',
            ]
        );

        // 2. Find/Create Mapel
        $mapelWeb = Mapel::firstOrCreate(['kode' => 'PWEB'], ['nama' => 'Pemrograman Web & Perangkat Bergerak', 'kelompok' => 'kejuruan']);
        $mapelBasisData = Mapel::firstOrCreate(['kode' => 'BD'], ['nama' => 'Basis Data', 'kelompok' => 'kejuruan']);

        // 3. Get existing Rombels or create
        $rombel1 = Rombel::first();
        if (!$rombel1) {
            $this->error('No Rombel found! Run master seeders first.');
            return;
        }

        $semester = \App\Modules\Akademik\Models\Semester::where('is_active', true)->first();
        if (!$semester) {
            $this->error('No active Semester found!');
            return;
        }

        // 4. Create Penugasan Mengajar
        $penugasan1 = PenugasanMengajar::firstOrCreate([
            'pegawai_id' => $pegawai->id,
            'rombel_id' => $rombel1->id,
            'mapel_id' => $mapelWeb->id,
            'semester_id' => $semester->id,
        ], ['kkm_custom' => 75]);

        $penugasan2 = PenugasanMengajar::firstOrCreate([
            'pegawai_id' => $pegawai->id,
            'rombel_id' => $rombel1->id,
            'mapel_id' => $mapelBasisData->id,
            'semester_id' => $semester->id,
        ], ['kkm_custom' => 75]);

        // 5. Create Tujuan Pembelajaran
        $tpWeb1 = TujuanPembelajaran::firstOrCreate(['penugasan_mengajar_id' => $penugasan1->id, 'kode' => 'TP1'], ['deskripsi' => 'Memahami konsep dasar HTML dan CSS']);
        $tpWeb2 = TujuanPembelajaran::firstOrCreate(['penugasan_mengajar_id' => $penugasan1->id, 'kode' => 'TP2'], ['deskripsi' => 'Menerapkan framework Tailwind CSS']);
        $tpWeb3 = TujuanPembelajaran::firstOrCreate(['penugasan_mengajar_id' => $penugasan1->id, 'kode' => 'TP3'], ['deskripsi' => 'Membuat layout responsif']);

        // 6. Get 4 students from this rombel
        $siswas = Siswa::where('rombel_id', $rombel1->id)->take(4)->get();
        if ($siswas->count() < 4) {
            $this->info('Found less than 4 students in Rombel, creating dummies...');
            for ($i = $siswas->count(); $i < 4; $i++) {
                $siswas->push(Siswa::create([
                    'nama' => 'Siswa Dummy ' . ($i + 1),
                    'nipd' => '1000' . $i,
                    'nisn' => '001000' . $i,
                    'jk' => 'L',
                    'tanggal_lahir' => '2005-01-01',
                    'rombel_id' => $rombel1->id
                ]));
            }
        }

        // 7. Inject Grades for Web
        foreach ($siswas as $index => $siswa) {
            // Random formatives
            $n1 = rand(70, 95);
            $n2 = rand(75, 100);
            $n3 = rand(60, 90);

            NilaiFormatif::updateOrCreate(['siswa_id' => $siswa->id, 'tujuan_pembelajaran_id' => $tpWeb1->id], ['nilai' => $n1]);
            NilaiFormatif::updateOrCreate(['siswa_id' => $siswa->id, 'tujuan_pembelajaran_id' => $tpWeb2->id], ['nilai' => $n2]);
            NilaiFormatif::updateOrCreate(['siswa_id' => $siswa->id, 'tujuan_pembelajaran_id' => $tpWeb3->id], ['nilai' => $n3]);

            $sts = rand(75, 95);
            $sas = rand(70, 100);

            NilaiSumatif::updateOrCreate(
                ['siswa_id' => $siswa->id, 'penugasan_mengajar_id' => $penugasan1->id],
                ['sts' => $sts, 'sas' => $sas]
            );

            // Calculate and inject Nilai Akhir
            $rata = ($n1 + $n2 + $n3) / 3;
            $na = ($rata * 0.5) + ($sts * 0.25) + ($sas * 0.25);
            
            // Just basic logic for desc
            $descT = "Menunjukkan penguasaan yang sangat baik dalam " . lcfirst($tpWeb2->deskripsi) . ".";
            $descR = "Perlu bimbingan lebih lanjut dalam " . lcfirst($tpWeb3->deskripsi) . ".";

            NilaiAkhir::updateOrCreate(
                ['siswa_id' => $siswa->id, 'penugasan_mengajar_id' => $penugasan1->id],
                [
                    'rata_formatif' => $rata,
                    'sts' => $sts,
                    'sas' => $sas,
                    'nilai_akhir' => $na,
                    'deskripsi_tertinggi' => $descT,
                    'deskripsi_terendah' => $descR,
                ]
            );
        }

        // 8. Dummy Ekstrakurikuler
        $instruktur = User::firstOrCreate(
            ['username' => 'instruktur_dummy'],
            [
                'name' => 'Kak Pembina, S.Pd',
                'password' => Hash::make('password'),
                'is_active' => true,
                'force_password_change' => false,
            ]
        );
        $instruktur->assignRole('Instruktur Ekskul');

        $pegawaiInstruktur = \App\Modules\Kepegawaian\Models\Pegawai::firstOrCreate(
            ['nip' => '199001012020011002'],
            [
                'user_id' => $instruktur->id,
                'nama_lengkap' => 'Kak Pembina',
                'gelar_depan' => '',
                'gelar_belakang' => 'S.Pd',
                'jk' => 'L',
                'status_pegawai' => 'Non PNS',
            ]
        );

        $ekskul = \App\Modules\Ekstrakurikuler\Models\Ekstrakurikuler::firstOrCreate([
            'nama' => 'Pramuka',
            'pembina_id' => $pegawaiInstruktur->id
        ]);

        foreach ($siswas as $siswa) {
            \App\Modules\Ekstrakurikuler\Models\EkstrakurikulerAnggota::firstOrCreate([
                'ekstrakurikuler_id' => $ekskul->id,
                'siswa_id' => $siswa->id,
                'semester_id' => $semester->id,
            ], [
                'nilai_angka' => rand(80, 95),
                'nilai_huruf' => 'Sangat Baik',
                'deskripsi' => 'Aktif dalam setiap kegiatan dan memiliki jiwa kepemimpinan'
            ]);
        }
        
        // 9. Assign Wali Kelas to Guru Dummy
        $rombel1->wali_kelas_id = $pegawai->id;
        $rombel1->save();

        $this->info('Dummy data successfully injected (including Ekskul)!');
        $this->info('Login as guru_dummy / password to test Wali Kelas (Rapor).');
        $this->info('Login as instruktur_dummy / password to test Instruktur (Ekskul).');
    }
}
