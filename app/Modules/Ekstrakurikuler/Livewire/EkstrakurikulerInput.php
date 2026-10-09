<?php

namespace App\Modules\Ekstrakurikuler\Livewire;

use Livewire\Component;
use App\Modules\Ekstrakurikuler\Models\Ekstrakurikuler;
use App\Modules\Ekstrakurikuler\Models\EkstrakurikulerAnggota;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Akademik\Models\Semester;
use Livewire\Attributes\Layout;

class EkstrakurikulerInput extends Component
{
    public $ekskul_id;
    public $ekskul;
    public $semester_id;

    public $search_siswa = '';
    public $searchResults = [];
    
    public $anggotaData = []; // [siswa_id => ['nilai_angka', 'deskripsi']]

    public function mount($ekskul_id)
    {
        $this->ekskul_id = $ekskul_id;
        $this->ekskul = Ekstrakurikuler::findOrFail($ekskul_id);
        
        $semester = Semester::where('is_active', true)->first();
        if ($semester) {
            $this->semester_id = $semester->id;
        }

        $this->loadAnggota();
    }

    public function loadAnggota()
    {
        $anggotas = EkstrakurikulerAnggota::with('siswa.rombel')
            ->where('ekstrakurikuler_id', $this->ekskul_id)
            ->where('semester_id', $this->semester_id)
            ->get();

        $this->anggotaData = [];
        foreach ($anggotas as $a) {
            $this->anggotaData[$a->siswa_id] = [
                'siswa' => $a->siswa, // For display
                'nilai_angka' => $a->nilai_angka,
                'deskripsi' => $a->deskripsi
            ];
        }
    }

    public function searchSiswa()
    {
        if (strlen($this->search_siswa) >= 3) {
            $existingIds = array_keys($this->anggotaData);
            
            $this->searchResults = Siswa::with('rombel')
                ->where('nama', 'like', '%' . $this->search_siswa . '%')
                ->whereNotIn('id', $existingIds)
                ->take(10)
                ->get();
        } else {
            $this->searchResults = [];
        }
    }

    public function addAnggota($siswa_id)
    {
        EkstrakurikulerAnggota::firstOrCreate([
            'ekstrakurikuler_id' => $this->ekskul_id,
            'siswa_id' => $siswa_id,
            'semester_id' => $this->semester_id,
        ]);
        
        $this->search_siswa = '';
        $this->searchResults = [];
        $this->loadAnggota();
    }

    public function removeAnggota($siswa_id)
    {
        EkstrakurikulerAnggota::where('ekstrakurikuler_id', $this->ekskul_id)
            ->where('siswa_id', $siswa_id)
            ->where('semester_id', $this->semester_id)
            ->delete();
            
        $this->loadAnggota();
    }

    public function saveNilai()
    {
        foreach ($this->anggotaData as $siswaId => $data) {
            $angka = $data['nilai_angka'];
            
            // Konversi Angka ke Huruf
            $huruf = null;
            if ($angka !== null && $angka !== '') {
                if ($angka >= 90) $huruf = 'Sangat Baik';
                elseif ($angka >= 80) $huruf = 'Baik';
                elseif ($angka >= 70) $huruf = 'Cukup';
                else $huruf = 'Kurang';
            }

            EkstrakurikulerAnggota::where('ekstrakurikuler_id', $this->ekskul_id)
                ->where('siswa_id', $siswaId)
                ->where('semester_id', $this->semester_id)
                ->update([
                    'nilai_angka' => $angka === '' ? null : $angka,
                    'nilai_huruf' => $huruf,
                    'deskripsi' => $data['deskripsi']
                ]);
        }

        session()->flash('message', 'Nilai Ekstrakurikuler berhasil disimpan.');
        $this->loadAnggota();
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('ekstrakurikuler::livewire.input');
    }
}
