<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kepegawaian\Models\PenugasanPembimbingRayon;
use App\Modules\Kepegawaian\Models\PenugasanWaliKelas;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class KepegawaianInitialSeeder extends Seeder
{
    public function run(): void
    {
        $ta = TahunAjaran::where('is_active', true)->first();

        $daftarGtk = [
            [
                'username' => 'dedik.kepsek',
                'nama_lengkap' => 'Dedik',
                'gelar_depan' => 'H.',
                'gelar_belakang' => 'M.Pd.',
                'nip' => '197005121998021001',
                'nuptk' => '4534748651200022',
                'nik' => '3205011205700001',
                'jenis_kelamin' => 'L',
                'jenis_gtk' => 'guru',
                'status_kepegawaian' => 'pns',
                'role' => 'Kepala Sekolah',
                'email' => 'dedik@wikrama.sch.id',
                'no_hp' => '081220123456',
            ],
            [
                'username' => 'beni.iskandar',
                'nama_lengkap' => 'Beni Iskandar',
                'gelar_depan' => '',
                'gelar_belakang' => 'S.Kom.',
                'nip' => '198501012010011001',
                'nuptk' => '8534748651200033',
                'nik' => '3205020101850002',
                'jenis_kelamin' => 'L',
                'jenis_gtk' => 'guru',
                'status_kepegawaian' => 'gty',
                'role' => 'Guru',
                'email' => 'beni.iskandar@wikrama.sch.id',
                'no_hp' => '081321654987',
                'is_wali_kelas_rombel' => 'X PPLG 1',
            ],
            [
                'username' => 'asep.gunawan',
                'nama_lengkap' => 'Asep Gunawan',
                'gelar_depan' => '',
                'gelar_belakang' => 'S.T.',
                'nip' => '198803152012011002',
                'nuptk' => '2534748651200044',
                'nik' => '3205031503880003',
                'jenis_kelamin' => 'L',
                'jenis_gtk' => 'guru',
                'status_kepegawaian' => 'gty',
                'role' => 'Guru',
                'email' => 'asep.gunawan@wikrama.sch.id',
                'no_hp' => '081398765432',
                'is_wali_kelas_rombel' => 'X TJKT 1',
            ],
            [
                'username' => 'rina.marlina',
                'nama_lengkap' => 'Rina Marlina',
                'gelar_depan' => '',
                'gelar_belakang' => 'M.Pd.',
                'nip' => '199008202015022001',
                'nuptk' => '6534748651200055',
                'nik' => '3205042008900004',
                'jenis_kelamin' => 'P',
                'jenis_gtk' => 'guru',
                'status_kepegawaian' => 'gty',
                'role' => 'Guru',
                'email' => 'rina.marlina@wikrama.sch.id',
                'no_hp' => '082123456789',
                'is_pembimbing_rayon' => 'Bales 1',
            ],
            [
                'username' => 'dedi.tu',
                'nama_lengkap' => 'Dedi Supriadi',
                'gelar_depan' => '',
                'gelar_belakang' => 'S.E.',
                'nip' => '199204102018011003',
                'nuptk' => '9534748651200066',
                'nik' => '3205051004920005',
                'jenis_kelamin' => 'L',
                'jenis_gtk' => 'tendik',
                'status_kepegawaian' => 'gty',
                'role' => 'Tata Usaha',
                'email' => 'dedi.tu@wikrama.sch.id',
                'no_hp' => '085211223344',
            ],
        ];

        foreach ($daftarGtk as $data) {
            $user = User::firstOrCreate(
                ['username' => $data['username']],
                [
                    'name' => $data['nama_lengkap'],
                    'email' => $data['email'],
                    'password' => Hash::make('Wikrama2024!'),
                    'is_active' => true,
                    'must_change_password' => false,
                ]
            );

            if (!$user->hasRole($data['role'])) {
                $user->assignRole($data['role']);
            }

            $pegawai = Pegawai::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nip' => $data['nip'],
                    'nuptk' => $data['nuptk'],
                    'nik' => $data['nik'],
                    'nama_lengkap' => $data['nama_lengkap'],
                    'gelar_depan' => $data['gelar_depan'] ?: null,
                    'gelar_belakang' => $data['gelar_belakang'] ?: null,
                    'jenis_kelamin' => $data['jenis_kelamin'],
                    'jenis_gtk' => $data['jenis_gtk'],
                    'status_kepegawaian' => $data['status_kepegawaian'],
                    'email' => $data['email'],
                    'no_hp' => $data['no_hp'],
                    'is_active' => true,
                ]
            );

            // Hubungkan jika wali kelas
            if (!empty($data['is_wali_kelas_rombel']) && $ta) {
                $rombel = Rombel::where('nama', $data['is_wali_kelas_rombel'])->first();
                if ($rombel) {
                    $rombel->update(['wali_kelas_id' => $user->id]);
                    PenugasanWaliKelas::updateOrCreate(
                        ['tahun_ajaran_id' => $ta->id, 'rombel_id' => $rombel->id],
                        ['pegawai_id' => $pegawai->id]
                    );
                }
            }

            // Hubungkan jika pembimbing rayon
            if (!empty($data['is_pembimbing_rayon']) && $ta) {
                $rayon = Rayon::where('nama', $data['is_pembimbing_rayon'])->first();
                if ($rayon) {
                    $rayon->update(['pembimbing_id' => $user->id]);
                    PenugasanPembimbingRayon::updateOrCreate(
                        ['tahun_ajaran_id' => $ta->id, 'rayon_id' => $rayon->id, 'pegawai_id' => $pegawai->id],
                        []
                    );
                }
            }
        }
    }
}
