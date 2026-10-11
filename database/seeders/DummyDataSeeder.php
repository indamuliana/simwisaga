<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Mapel;
use App\Modules\Akademik\Models\Program;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\StrukturKurikulum;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Alumni\Models\Alumni;
use App\Modules\Ekstrakurikuler\Models\Ekstrakurikuler;
use App\Modules\Ekstrakurikuler\Models\EkstrakurikulerAnggota;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kepegawaian\Models\PenugasanMengajar;
use App\Modules\Kepegawaian\Models\PenugasanPembimbingRayon;
use App\Modules\Kepegawaian\Models\PenugasanWaliKelas;
use App\Modules\Kesiswaan\Models\RombelSiswa;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Kesiswaan\Models\SiswaOrangTua;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $ta2024 = TahunAjaran::find(1);
        $ta2025 = TahunAjaran::find(2); // Aktif
        $sem3 = Semester::find(3); // Aktif (Ganjil 2025/2026)

        // 1. Akun Pengguna & Pegawai
        $staffAccounts = [
            ['username' => 'admin', 'name' => 'Super Administrator', 'email' => 'admin@wikrama.sch.id', 'role' => 'Super Admin', 'pegawai' => null],
            ['username' => 'kepsek', 'name' => 'Kunedi, S.Pd., M.M.', 'email' => 'kepsek.kunedi@wikrama.sch.id', 'role' => 'Kepala Sekolah', 'pegawai' => ['nama' => 'Kunedi, S.Pd., M.M.', 'nip' => '197005121998021001', 'jk' => 'L', 'gtk' => 'guru', 'status' => 'pns']],
            ['username' => 'kurikulum', 'name' => 'Hendra Wijaya, S.T., M.Kom.', 'email' => 'kurikulum.hendra@wikrama.sch.id', 'role' => 'Kurikulum', 'pegawai' => ['nama' => 'Hendra Wijaya, S.T., M.Kom.', 'nip' => '198204122008011005', 'jk' => 'L', 'gtk' => 'guru', 'status' => 'pns']],
            ['username' => 'kesiswaan', 'name' => 'Ahmad Fauzi, S.Pd., M.Pd.', 'email' => 'kesiswaan.fauzi@wikrama.sch.id', 'role' => 'Kesiswaan', 'pegawai' => ['nama' => 'Ahmad Fauzi, S.Pd., M.Pd.', 'nip' => '198307202009021003', 'jk' => 'L', 'gtk' => 'guru', 'status' => 'pns']],
            ['username' => 'alumni_koor', 'name' => 'Siti Nurhaliza, S.Psi.', 'email' => 'alumni.siti@wikrama.sch.id', 'role' => 'Koordinator Alumni', 'pegawai' => ['nama' => 'Siti Nurhaliza, S.Psi.', 'nip' => '198902142014022002', 'jk' => 'P', 'gtk' => 'guru', 'status' => 'gty']],
            ['username' => 'operator', 'name' => 'Rizky Ramadhan, S.Kom.', 'email' => 'operator.rizky@wikrama.sch.id', 'role' => 'Operator', 'pegawai' => ['nama' => 'Rizky Ramadhan, S.Kom.', 'nip' => '199309112020011004', 'jk' => 'L', 'gtk' => 'tendik', 'status' => 'pppk']],
            ['username' => 'bendahara', 'name' => 'Nurul Fitriani, S.E.', 'email' => 'bendahara.nurul@wikrama.sch.id', 'role' => 'Bendahara', 'pegawai' => ['nama' => 'Nurul Fitriani, S.E.', 'nip' => '199105152016022001', 'jk' => 'P', 'gtk' => 'tendik', 'status' => 'gty']],
            ['username' => 'tu', 'name' => 'Dedi Supriadi, A.Md.', 'email' => 'tu.dedi@wikrama.sch.id', 'role' => 'Tata Usaha', 'pegawai' => ['nama' => 'Dedi Supriadi, A.Md.', 'nip' => '199204102018011003', 'jk' => 'L', 'gtk' => 'tendik', 'status' => 'gty']],
            ['username' => 'instruktur', 'name' => 'Bayu Pratama, S.Pd. (Kak Pembina)', 'email' => 'instruktur.bayu@wikrama.sch.id', 'role' => 'Instruktur Ekskul', 'pegawai' => ['nama' => 'Bayu Pratama, S.Pd.', 'nip' => '199001012020011002', 'jk' => 'L', 'gtk' => 'guru', 'status' => 'gty']],
        ];

        $guruList = [
            ['username' => 'guru_pplg', 'name' => 'Beni Iskandar, S.Kom.', 'email' => 'guru.pplg@wikrama.sch.id', 'nip' => '198501012010011001', 'jk' => 'L'],
            ['username' => 'guru_tjkt', 'name' => 'Deni Hermawan, S.T.', 'email' => 'guru.tjkt@wikrama.sch.id', 'nip' => '198603122011011002', 'jk' => 'L'],
            ['username' => 'guru_pmn', 'name' => 'Maya Sartika, S.E.', 'email' => 'guru.pmn@wikrama.sch.id', 'nip' => '198711202012022001', 'jk' => 'P'],
            ['username' => 'guru_htl', 'name' => 'Dewi Anggraeni, S.Tr.Par.', 'email' => 'guru.htl@wikrama.sch.id', 'nip' => '198809152014022003', 'jk' => 'P'],
            ['username' => 'guru_mtk', 'name' => 'Rina Marlina, S.Pd.', 'email' => 'guru.mtk@wikrama.sch.id', 'nip' => '199008202015022001', 'jk' => 'P'],
            ['username' => 'guru_indo', 'name' => 'Asep Gunawan, S.Pd.', 'email' => 'guru.indo@wikrama.sch.id', 'nip' => '198803152012011002', 'jk' => 'L'],
            ['username' => 'guru_inggris', 'name' => 'Sarah Lestari, S.Pd., M.Hum.', 'email' => 'guru.inggris@wikrama.sch.id', 'nip' => '198904102015022002', 'jk' => 'P'],
            ['username' => 'guru_pai', 'name' => 'Ustadz M. Ridwan, S.Ag.', 'email' => 'guru.pai@wikrama.sch.id', 'nip' => '198102142006041001', 'jk' => 'L'],
            ['username' => 'guru_pjok', 'name' => 'Budi Santoso, S.Pd.', 'email' => 'guru.pjok@wikrama.sch.id', 'nip' => '198001012005011001', 'jk' => 'L'],
            ['username' => 'guru_ppkn', 'name' => 'Yanti Sumiati, S.Pd.', 'email' => 'guru.ppkn@wikrama.sch.id', 'nip' => '198406182009022004', 'jk' => 'P'],
            ['username' => 'guru_sejarah', 'name' => 'Fajar Nugraha, S.Pd.', 'email' => 'guru.sejarah@wikrama.sch.id', 'nip' => '199103252017011001', 'jk' => 'L'],
            ['username' => 'guru_ipas', 'name' => 'Dr. Ir. Wahyu Hidayat, M.Si.', 'email' => 'guru.ipas@wikrama.sch.id', 'nip' => '197508192003121002', 'jk' => 'L'],
            ['username' => 'guru_seni', 'name' => 'Anita Rahayu, S.Sn.', 'email' => 'guru.seni@wikrama.sch.id', 'nip' => '199207112019022001', 'jk' => 'P'],
            ['username' => 'guru_sunda', 'name' => 'Cecep Supriatna, S.Pd.', 'email' => 'guru.sunda@wikrama.sch.id', 'nip' => '198705052011011003', 'jk' => 'L'],
            ['username' => 'guru_pkwu', 'name' => 'Gina Gandina, S.E., M.M.', 'email' => 'guru.pkwu@wikrama.sch.id', 'nip' => '198612122010022002', 'jk' => 'P'],
            ['username' => 'guru_p5', 'name' => 'Ilham Maulana, S.Kom.', 'email' => 'guru.p5@wikrama.sch.id', 'nip' => '199401152021011003', 'jk' => 'L'],
        ];

        $createdPegawais = [];

        $findOrMakeUser = function($username, $name, $email, $role) {
            $user = User::where('username', $username)->first() ?? User::where('email', $email)->first();
            if (!$user) {
                $user = User::create([
                    'username' => $username,
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'is_active' => true,
                    'must_change_password' => false,
                ]);
            } else {
                $user->update([
                    'username' => $username,
                    'name' => $name,
                    'password' => Hash::make('password'),
                    'is_active' => true,
                    'must_change_password' => false,
                ]);
            }
            $user->syncRoles([$role]);
            return $user;
        };

        foreach ($staffAccounts as $st) {
            $user = $findOrMakeUser($st['username'], $st['name'], $st['email'], $st['role']);
            if ($st['pegawai']) {
                $pegawai = Pegawai::updateOrCreate(
                    ['nip' => $st['pegawai']['nip']],
                    [
                        'user_id' => $user->id,
                        'nama_lengkap' => $st['pegawai']['nama'],
                        'jenis_kelamin' => $st['pegawai']['jk'],
                        'jenis_gtk' => $st['pegawai']['gtk'],
                        'status_kepegawaian' => $st['pegawai']['status'],
                        'is_active' => true,
                    ]
                );
                $createdPegawais[$st['username']] = $pegawai;
            }
        }

        foreach ($guruList as $g) {
            $user = $findOrMakeUser($g['username'], $g['name'], $g['email'], 'Guru');
            $pegawai = Pegawai::updateOrCreate(
                ['nip' => $g['nip']],
                [
                    'user_id' => $user->id,
                    'nama_lengkap' => $g['name'],
                    'jenis_kelamin' => $g['jk'],
                    'jenis_gtk' => 'guru',
                    'status_kepegawaian' => 'gty',
                    'is_active' => true,
                ]
            );
            $createdPegawais[$g['username']] = $pegawai;
        }

        // 2. Wali Kelas & Pembimbing Rayon
        $rombels2025 = Rombel::where('tahun_ajaran_id', $ta2025->id)->get();
        $teachersList = array_values($createdPegawais);

        $tIndex = 0;
        foreach ($rombels2025 as $r) {
            $teacher = $teachersList[$tIndex % count($teachersList)];
            $tIndex++;
            $r->update(['wali_kelas_id' => $teacher->user_id]);
            PenugasanWaliKelas::updateOrCreate(
                ['tahun_ajaran_id' => $ta2025->id, 'rombel_id' => $r->id],
                ['pegawai_id' => $teacher->id, 'sk_penugasan' => "SK/WALI/2025/00{$r->id}"]
            );
        }

        $rayons = Rayon::all();
        $rIndex = 0;
        foreach ($rayons as $ray) {
            $teacher = $teachersList[$rIndex % count($teachersList)];
            $rIndex++;
            $ray->update(['pembimbing_id' => $teacher->user_id]);
            PenugasanPembimbingRayon::updateOrCreate(
                ['tahun_ajaran_id' => $ta2025->id, 'rayon_id' => $ray->id],
                ['pegawai_id' => $teacher->id, 'sk_penugasan' => "SK/RAYON/2025/00{$ray->id}"]
            );
        }
    }
}
