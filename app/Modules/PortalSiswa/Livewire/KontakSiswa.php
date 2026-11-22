<?php

namespace App\Modules\PortalSiswa\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Akademik\Models\PenugasanMengajar;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kesiswaan\Models\Siswa;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Kontak Guru & Pegawai')]
class KontakSiswa extends Component
{
    use GuardsPermission;

    public string $search = '';

    public string $kategori = 'semua'; // 'semua', 'guru', 'tendik', 'struktural'

    public function mount(): void
    {
        $this->guard('portal_siswa.agenda.lihat');
    }

    protected function getSiswa(): ?Siswa
    {
        $user = auth()->user();
        if (! $user) {
            return null;
        }

        if ($user->siswa) {
            return $user->siswa;
        }

        return Siswa::where('user_id', $user->id)
            ->orWhere('nisn', $user->username)
            ->orWhere('nipd', $user->username)
            ->first();
    }

    public function formatWaUrl(?string $phone, ?string $pesan = null): ?string
    {
        if (! $phone) {
            return null;
        }

        $clean = preg_replace('/[^0-9]/', '', $phone);
        if (empty($clean)) {
            return null;
        }

        if (str_starts_with($clean, '0')) {
            $clean = '62'.substr($clean, 1);
        }

        $url = 'https://wa.me/'.$clean;
        if ($pesan) {
            $url .= '?text='.urlencode($pesan);
        }

        return $url;
    }

    public function formatPhoneDisplay(?string $phone): string
    {
        if (! $phone) {
            return '-';
        }

        $clean = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($clean, '62') && strlen($clean) >= 10) {
            return '+62 '.substr($clean, 2, 3).'-'.substr($clean, 5, 4).'-'.substr($clean, 9);
        } elseif (str_starts_with($clean, '0') && strlen($clean) >= 10) {
            return substr($clean, 0, 4).'-'.substr($clean, 4, 4).'-'.substr($clean, 8);
        }

        return $phone;
    }

    public function render()
    {
        $siswa = $this->getSiswa();

        $pembimbingRayon = null;
        $waliKelas = null;
        $guruPengampus = collect();
        $pembinaEkskul = collect();
        $guruSeni = collect();
        $pembimbingCustom = collect();

        if ($siswa) {
            $siswa->loadMissing([
                'rayon.pembimbing.pegawai',
                'rombel.waliKelas.pegawai',
                'customKelompoks.pembimbing.pegawai',
                'ekstrakurikulerAnggotas.ekstrakurikuler.pembina.pegawai',
                'seniBudayaAnggotas.seniBudaya.guru.pegawai',
            ]);

            // Pembimbing Rayon
            if ($siswa->rayon && $siswa->rayon->pembimbing) {
                $userRayon = $siswa->rayon->pembimbing;
                $pegRayon = $userRayon->pegawai;
                $pembimbingRayon = [
                    'nama' => $pegRayon?->nama_gelar ?: $userRayon->name,
                    'jabatan' => 'Pembimbing Rayon '.$siswa->rayon->nama,
                    'peran' => 'Pembimbing Bimbingan Konseling & Karakter',
                    'no_hp' => $pegRayon?->no_hp,
                    'email' => $pegRayon?->email ?: $userRayon->email,
                    'foto_path' => $pegRayon?->foto_path,
                    'wa_pesan' => "Assalamu'alaikum wr. wb. Halo Bapak/Ibu, saya {$siswa->nama} siswa dari Rayon {$siswa->rayon->nama}. Saya ingin berkonsultasi mengenai...",
                ];
            }

            // Wali Kelas
            if ($siswa->rombel && $siswa->rombel->waliKelas) {
                $userWali = $siswa->rombel->waliKelas;
                $pegWali = $userWali->pegawai;
                $waliKelas = [
                    'nama' => $pegWali?->nama_gelar ?: $userWali->name,
                    'jabatan' => 'Wali Kelas '.$siswa->rombel->nama,
                    'peran' => 'Wali Akademik & Kelas',
                    'no_hp' => $pegWali?->no_hp,
                    'email' => $pegWali?->email ?: $userWali->email,
                    'foto_path' => $pegWali?->foto_path,
                    'wa_pesan' => "Assalamu'alaikum wr. wb. Halo Bapak/Ibu, saya {$siswa->nama} dari kelas {$siswa->rombel->nama}. Saya ingin menanyakan terkait...",
                ];
            }

            // Guru Mapel Rombel Semester Aktif
            $semesterAktif = Semester::where('is_active', true)->first();
            if ($siswa->rombel_id && $semesterAktif) {
                $penugasans = PenugasanMengajar::with(['pegawai', 'mapel'])
                    ->where('rombel_id', $siswa->rombel_id)
                    ->where('semester_id', $semesterAktif->id)
                    ->get();

                foreach ($penugasans as $pm) {
                    if ($pm->pegawai) {
                        $guruPengampus->push([
                            'id' => $pm->pegawai->id,
                            'nama' => $pm->pegawai->nama_gelar,
                            'mapel' => $pm->mapel?->nama ?: 'Mata Pelajaran',
                            'no_hp' => $pm->pegawai->no_hp,
                            'email' => $pm->pegawai->email,
                            'foto_path' => $pm->pegawai->foto_path,
                            'wa_pesan' => "Assalamu'alaikum wr. wb. Halo Bapak/Ibu {$pm->pegawai->nama_lengkap}, saya {$siswa->nama} dari kelas {$siswa->rombel?->nama} ingin bertanya materi pelajaran {$pm->mapel?->nama}...",
                        ]);
                    }
                }
            }

            // Pembina Ekstrakurikuler (UPD)
            if ($siswa->ekstrakurikulerAnggotas) {
                foreach ($siswa->ekstrakurikulerAnggotas as $ea) {
                    $ekskul = $ea->ekstrakurikuler;
                    $pembinaUser = $ekskul?->pembina;
                    if ($pembinaUser) {
                        $peg = $pembinaUser->pegawai;
                        $pembinaEkskul->push([
                            'nama' => $peg?->nama_gelar ?: $pembinaUser->name,
                            'ekskul' => $ekskul->nama,
                            'peran' => 'Pembina Ekstrakurikuler (UPD)',
                            'no_hp' => $peg?->no_hp,
                            'email' => $peg?->email ?: $pembinaUser->email,
                            'foto_path' => $peg?->foto_path,
                            'wa_pesan' => "Assalamu'alaikum wr. wb. Halo Bapak/Ibu, saya {$siswa->nama} anggota {$ekskul->nama}...",
                        ]);
                    }
                }
            }

            // Guru Seni Budaya
            if ($siswa->seniBudayaAnggotas) {
                foreach ($siswa->seniBudayaAnggotas as $sa) {
                    $seni = $sa->seniBudaya;
                    $guruUser = $seni?->guru;
                    if ($guruUser) {
                        $peg = $guruUser->pegawai;
                        $guruSeni->push([
                            'nama' => $peg?->nama_gelar ?: $guruUser->name,
                            'seni' => $seni->nama,
                            'peran' => 'Guru Seni Budaya',
                            'no_hp' => $peg?->no_hp,
                            'email' => $peg?->email ?: $guruUser->email,
                            'foto_path' => $peg?->foto_path,
                            'wa_pesan' => "Assalamu'alaikum wr. wb. Halo Bapak/Ibu, saya {$siswa->nama} dari kelas Seni Budaya {$seni->nama}...",
                        ]);
                    }
                }
            }

            // Pembimbing Kelompok Custom
            if ($siswa->customKelompoks) {
                foreach ($siswa->customKelompoks as $ck) {
                    $pembUser = $ck->pembimbing;
                    if ($pembUser) {
                        $peg = $pembUser->pegawai;
                        $pembimbingCustom->push([
                            'nama' => $peg?->nama_gelar ?: $pembUser->name,
                            'kelompok' => $ck->nama,
                            'kategori' => $ck->kategori ?: 'Khusus',
                            'warna' => $ck->warna ?: '#3b82f6',
                            'peran' => 'Pembimbing Kelompok Custom',
                            'no_hp' => $peg?->no_hp,
                            'email' => $peg?->email ?: $pembUser->email,
                            'foto_path' => $peg?->foto_path,
                            'wa_pesan' => "Assalamu'alaikum wr. wb. Halo Bapak/Ibu, saya {$siswa->nama} anggota kelompok {$ck->nama}...",
                        ]);
                    }
                }
            }
        }

        // Query Direktori Pegawai / Guru
        $pegawaiQuery = Pegawai::query()->where('is_active', true);

        if ($this->search) {
            $q = '%'.trim($this->search).'%';
            $pegawaiQuery->where(function ($sub) use ($q) {
                $sub->where('nama_lengkap', 'like', $q)
                    ->orWhere('gelar_depan', 'like', $q)
                    ->orWhere('gelar_belakang', 'like', $q)
                    ->orWhere('jabatan_struktural', 'like', $q)
                    ->orWhere('mapel_diampu', 'like', $q)
                    ->orWhere('email', 'like', $q)
                    ->orWhere('no_hp', 'like', $q);
            });
        }

        if ($this->kategori === 'guru') {
            $pegawaiQuery->where('jenis_gtk', 'like', '%guru%');
        } elseif ($this->kategori === 'tendik') {
            $pegawaiQuery->where(function ($sub) {
                $sub->where('jenis_gtk', 'like', '%tendik%')
                    ->orWhere('jenis_gtk', 'like', '%kependidikan%');
            });
        } elseif ($this->kategori === 'struktural') {
            $pegawaiQuery->whereNotNull('jabatan_struktural')->where('jabatan_struktural', '!=', '');
        }

        $direktoriPegawai = $pegawaiQuery->orderBy('nama_lengkap')->get();

        return view('portalsiswa::livewire.kontak-siswa', [
            'siswa' => $siswa,
            'pembimbingRayon' => $pembimbingRayon,
            'waliKelas' => $waliKelas,
            'guruPengampus' => $guruPengampus,
            'pembinaEkskul' => $pembinaEkskul,
            'guruSeni' => $guruSeni,
            'pembimbingCustom' => $pembimbingCustom,
            'direktoriPegawai' => $direktoriPegawai,
        ]);
    }
}
