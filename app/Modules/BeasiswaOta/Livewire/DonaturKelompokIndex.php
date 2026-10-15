<?php

namespace App\Modules\BeasiswaOta\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\BeasiswaOta\Models\DonasiMasuk;
use App\Modules\BeasiswaOta\Models\Donatur;
use App\Modules\BeasiswaOta\Models\KasMutasi;
use App\Modules\BeasiswaOta\Models\Kelompok;
use App\Modules\BeasiswaOta\Models\KelompokAnggota;
use App\Modules\BeasiswaOta\Models\OtaSetting;
use App\Modules\BeasiswaOta\Services\OtaExcelService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DonaturKelompokIndex extends Component
{
    use GuardsPermission, WithPagination, WithFileUploads;

    public string $activeTab = 'kelompok'; // 'kelompok' | 'donatur'

    // Bulk Selection for Kelompok
    public array $selectedKelompokIds = [];
    public bool $selectAllKelompok = false;

    // Bulk Selection for Donatur
    public array $selectedDonaturIds = [];
    public bool $selectAllDonatur = false;

    // Import State
    public $fileImportDonatur;
    public bool $showImportDonaturModal = false;
    public $fileImportKelompok;
    public bool $showImportKelompokModal = false;

    // Kelompok state
    public bool $showKelompokModal = false;
    public ?int $kelompokId = null;
    public string $kodeKelompok = '';
    public string $namaKelompok = '';
    public float|int|string $targetNominal = 2000000;
    public string $keteranganKelompok = '';

    // Kelompok Anggota modal state
    public bool $showAnggotaModal = false;
    public ?int $selectedKelompokId = null;
    public array $selectedDonaturModalIds = [];
    public bool $selectAllModalDonatur = false;
    public string $searchDonaturModal = '';
    public float|int|string $nominalKomitmen = 150000;
    public string $keteranganKomitmen = '';

    // Donatur state
    public bool $showDonaturModal = false;
    public ?int $donaturId = null;
    public string $namaDonatur = '';
    public string $tipeDonatur = 'rutin'; // 'rutin' | 'insidental'
    public string $nomorTelepon = '';
    public string $email = '';
    public string $alamat = '';
    public string $keteranganDonatur = '';
    public string $searchDonatur = '';

    public function mount()
    {
        $defaultTarget = (float) OtaSetting::get('default_target_kelompok', 2000000);
        $this->targetNominal = $defaultTarget;
    }

    public function updatedActiveTab()
    {
        $this->resetPage();
        $this->reset([
            'selectedKelompokIds', 'selectAllKelompok',
            'selectedDonaturIds', 'selectAllDonatur',
        ]);
    }

    public function updatedSearchDonatur()
    {
        $this->resetPage();
        $this->reset(['selectedDonaturIds', 'selectAllDonatur']);
    }

    // --- Kelompok Methods ---
    public function openCreateKelompok()
    {
        $this->resetKelompokForm();
        $count = Kelompok::count() + 1;
        $this->kodeKelompok = sprintf('KEL-%02d', $count);
        $this->showKelompokModal = true;
    }

    public function editKelompok(int $id)
    {
        $k = Kelompok::findOrFail($id);
        $this->kelompokId = $k->id;
        $this->kodeKelompok = $k->kode_kelompok;
        $this->namaKelompok = $k->nama_kelompok;
        $this->targetNominal = (float) $k->target_nominal;
        $this->keteranganKelompok = $k->keterangan ?? '';
        $this->showKelompokModal = true;
    }

    public function saveKelompok()
    {
        $this->validate([
            'kodeKelompok' => 'required|string|max:50|unique:ota_kelompoks,kode_kelompok,' . $this->kelompokId,
            'namaKelompok' => 'required|string|max:255',
            'targetNominal' => 'required|numeric|min:0',
        ]);

        Kelompok::updateOrCreate(
            ['id' => $this->kelompokId],
            [
                'kode_kelompok' => $this->kodeKelompok,
                'nama_kelompok' => $this->namaKelompok,
                'target_nominal' => $this->targetNominal,
                'keterangan' => $this->keteranganKelompok,
            ]
        );

        $this->showKelompokModal = false;
        $this->resetKelompokForm();
        session()->flash('message', 'Kelompok donatur berhasil disimpan!');
    }

    public function deleteKelompok(int $id)
    {
        $kelompok = Kelompok::findOrFail($id);
        if ($kelompok->donasiMasuks()->exists() || $kelompok->alokasis()->exists()) {
            session()->flash('error', 'Kelompok tidak dapat dihapus karena sudah memiliki riwayat donasi atau alokasi.');
            return;
        }

        $kelompok->delete();
        session()->flash('message', 'Kelompok donatur berhasil dihapus.');
    }

    protected function resetKelompokForm()
    {
        $this->kelompokId = null;
        $this->kodeKelompok = '';
        $this->namaKelompok = '';
        $this->targetNominal = (float) OtaSetting::get('default_target_kelompok', 2000000);
        $this->keteranganKelompok = '';
    }

    public static function cleanNominal(mixed $val): float
    {
        if (is_int($val) || is_float($val)) {
            return (float) $val;
        }
        if (empty($val)) {
            return 0.0;
        }
        $str = trim((string) $val);
        $str = preg_replace('/[.,]00$/', '', $str);
        $cleaned = preg_replace('/[^\d]/', '', $str);
        return (float) ($cleaned ?: 0);
    }

    // --- Kelompok Anggota Methods ---
    public function manageAnggota(int $kelompokId)
    {
        $this->selectedKelompokId = $kelompokId;
        $this->selectedDonaturModalIds = [];
        $this->selectAllModalDonatur = false;
        $this->searchDonaturModal = '';
        $this->nominalKomitmen = 150000;
        $this->keteranganKomitmen = '';
        $this->showAnggotaModal = true;
    }

    public function updatedSelectAllModalDonatur($value)
    {
        if ($value && $this->selectedKelompokId) {
            $existingIds = KelompokAnggota::where('kelompok_id', $this->selectedKelompokId)->pluck('donatur_id')->toArray();
            
            $this->selectedDonaturModalIds = Donatur::where('tipe', 'rutin')
                ->where('is_aktif', true)
                ->whereNotIn('id', $existingIds)
                ->when($this->searchDonaturModal, fn($q) => $q->where(function($sq) {
                    $sq->where('nama', 'like', "%{$this->searchDonaturModal}%")
                       ->orWhere('nomor_telepon', 'like', "%{$this->searchDonaturModal}%");
                }))
                ->pluck('id')
                ->map(fn($id) => (string) $id)
                ->toArray();
        } else {
            $this->selectedDonaturModalIds = [];
        }
    }

    public function updatedSearchDonaturModal()
    {
        // Reset "select all" when search query changes
        $this->selectAllModalDonatur = false;
    }

    public function addAnggotaToKelompok()
    {
        $this->validate([
            'selectedKelompokId' => 'required|exists:ota_kelompoks,id',
            'selectedDonaturModalIds' => 'required|array|min:1',
            'nominalKomitmen' => 'required',
        ], [
            'selectedDonaturModalIds.required' => 'Pilih minimal satu donatur terlebih dahulu.',
            'selectedDonaturModalIds.min' => 'Pilih minimal satu donatur terlebih dahulu.',
            'nominalKomitmen.required' => 'Nominal iuran komitmen wajib diisi.',
        ]);

        $nominal = self::cleanNominal($this->nominalKomitmen);
        if ($nominal <= 0) {
            session()->flash('error_anggota', 'Nominal iuran komitmen harus lebih besar dari Rp 0.');
            return;
        }

        $existingIds = KelompokAnggota::where('kelompok_id', $this->selectedKelompokId)
            ->whereIn('donatur_id', $this->selectedDonaturModalIds)
            ->pluck('donatur_id')
            ->toArray();

        $toAdd = array_diff($this->selectedDonaturModalIds, $existingIds);
        $countAdded = 0;

        foreach ($toAdd as $donaturId) {
            KelompokAnggota::create([
                'kelompok_id' => $this->selectedKelompokId,
                'donatur_id' => (int) $donaturId,
                'nominal_komitmen' => $nominal,
                'keterangan' => $this->keteranganKomitmen ?: null,
            ]);
            $countAdded++;
        }

        $this->selectedDonaturModalIds = [];
        $this->selectAllModalDonatur = false;
        $this->searchDonaturModal = '';

        if ($countAdded > 0) {
            session()->flash('message_anggota', "Berhasil menambahkan {$countAdded} donatur ke kelompok ini!");
        } else {
            session()->flash('error_anggota', 'Semua donatur yang dipilih sudah terdaftar di kelompok ini.');
        }
    }

    public function removeAnggota(int $anggotaId)
    {
        KelompokAnggota::where('id', $anggotaId)->delete();
        session()->flash('message_anggota', 'Donatur dikeluarkan dari kelompok.');
    }

    // --- Donatur Methods ---
    public function openCreateDonatur()
    {
        $this->resetDonaturForm();
        $this->showDonaturModal = true;
    }

    public function editDonatur(int $id)
    {
        $d = Donatur::findOrFail($id);
        $this->donaturId = $d->id;
        $this->namaDonatur = $d->nama;
        $this->tipeDonatur = $d->tipe;
        $this->nomorTelepon = $d->nomor_telepon ?? '';
        $this->email = $d->email ?? '';
        $this->alamat = $d->alamat ?? '';
        $this->keteranganDonatur = $d->keterangan ?? '';
        $this->showDonaturModal = true;
    }

    public function saveDonatur()
    {
        $this->validate([
            'namaDonatur' => 'required|string|max:255',
            'tipeDonatur' => 'required|in:rutin,insidental',
            'nomorTelepon' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
        ]);

        Donatur::updateOrCreate(
            ['id' => $this->donaturId],
            [
                'nama' => $this->namaDonatur,
                'tipe' => $this->tipeDonatur,
                'nomor_telepon' => $this->nomorTelepon,
                'email' => $this->email,
                'alamat' => $this->alamat,
                'keterangan' => $this->keteranganDonatur,
                'is_aktif' => true,
            ]
        );

        $this->showDonaturModal = false;
        $this->resetDonaturForm();
        session()->flash('message', 'Data donatur berhasil disimpan!');
    }

    public function deleteDonatur(int $id)
    {
        $donatur = Donatur::findOrFail($id);
        if ($donatur->donasiMasuks()->exists()) {
            session()->flash('error', 'Donatur tidak dapat dihapus karena sudah memiliki riwayat transaksi donasi.');
            return;
        }

        $donatur->delete();
        session()->flash('message', 'Data donatur berhasil dihapus.');
    }

    protected function resetDonaturForm()
    {
        $this->donaturId = null;
        $this->namaDonatur = '';
        $this->tipeDonatur = 'rutin';
        $this->nomorTelepon = '';
        $this->email = '';
        $this->alamat = '';
        $this->keteranganDonatur = '';
    }

    // --- Export & Import Donatur ---
    public function exportDonaturExcel()
    {
        $spreadsheet = OtaExcelService::exportDonaturs();
        $timestamp = now()->format('Ymd_His');

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, "Daftar_Donatur_OTA_{$timestamp}.xlsx", [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    public function downloadTemplateDonatur()
    {
        $spreadsheet = OtaExcelService::templateDonaturs();

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, "Template_Import_Donatur_OTA.xlsx", [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    public function importDonaturExcel()
    {
        $this->validate([
            'fileImportDonatur' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        $res = OtaExcelService::importDonaturs($this->fileImportDonatur->getRealPath());

        $this->showImportDonaturModal = false;
        $this->fileImportDonatur = null;

        session()->flash('message', "Import Donatur Selesai! {$res['imported']} donatur baru ditambahkan, {$res['updated']} donatur diperbarui.");
    }

    // --- Export & Import Kelompok ---
    public function exportKelompokExcel()
    {
        $spreadsheet = OtaExcelService::exportKelompoks();
        $timestamp = now()->format('Ymd_His');

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, "Kelompok_Donatur_OTA_{$timestamp}.xlsx", [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    public function downloadTemplateKelompok()
    {
        $spreadsheet = OtaExcelService::templateKelompoks();

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, "Template_Import_Kelompok_OTA.xlsx", [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    public function importKelompokExcel()
    {
        $this->validate([
            'fileImportKelompok' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        $res = OtaExcelService::importKelompoks($this->fileImportKelompok->getRealPath());

        $this->showImportKelompokModal = false;
        $this->fileImportKelompok = null;

        session()->flash('message', "Import Kelompok Selesai! {$res['kelompok_created']} kelompok baru dibuat, {$res['kelompok_updated']} kelompok diperbarui, {$res['anggota_added']} donatur anggota ditautkan.");
    }

    // --- Bulk Action Kelompok ---
    public function updatedSelectAllKelompok($value)
    {
        if ($value) {
            $this->selectedKelompokIds = Kelompok::pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selectedKelompokIds = [];
        }
    }

    public function bulkDeleteKelompok()
    {
        if (empty($this->selectedKelompokIds)) {
            return;
        }

        $count = count($this->selectedKelompokIds);
        Kelompok::whereIn('id', $this->selectedKelompokIds)->delete();
        $this->selectedKelompokIds = [];
        $this->selectAllKelompok = false;

        session()->flash('message', "{$count} kelompok donatur berhasil dihapus.");
    }

    public function deleteAllKelompok()
    {
        $count = Kelompok::count();
        Kelompok::query()->delete();
        $this->selectedKelompokIds = [];
        $this->selectAllKelompok = false;

        session()->flash('message', "Seluruh ({$count}) kelompok donatur berhasil dihapus bersih.");
    }

    // --- Bulk Action Donatur ---
    public function updatedSelectAllDonatur($value)
    {
        if ($value) {
            $this->selectedDonaturIds = Donatur::query()
                ->when($this->searchDonatur, fn($q) => $q->where('nama', 'like', "%{$this->searchDonatur}%")->orWhere('nomor_telepon', 'like', "%{$this->searchDonatur}%"))
                ->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selectedDonaturIds = [];
        }
    }

    public function bulkDeleteDonatur()
    {
        if (empty($this->selectedDonaturIds)) {
            return;
        }

        $count = count($this->selectedDonaturIds);

        // Hapus donasi dan mutasi kas terkait
        $donasiIds = DonasiMasuk::whereIn('donatur_id', $this->selectedDonaturIds)->pluck('id');
        KasMutasi::where('referensi_tipe', 'donasi')->whereIn('referensi_id', $donasiIds)->delete();
        DonasiMasuk::whereIn('id', $donasiIds)->delete();

        Donatur::whereIn('id', $this->selectedDonaturIds)->delete();
        $this->selectedDonaturIds = [];
        $this->selectAllDonatur = false;

        session()->flash('message', "{$count} donatur berhasil dihapus.");
    }

    public function deleteAllDonatur()
    {
        $count = Donatur::count();

        // Hapus donasi dan mutasi kas terkait
        $donasiIds = DonasiMasuk::pluck('id');
        KasMutasi::where('referensi_tipe', 'donasi')->whereIn('referensi_id', $donasiIds)->delete();
        DonasiMasuk::query()->delete();

        Donatur::query()->delete();
        $this->selectedDonaturIds = [];
        $this->selectAllDonatur = false;

        session()->flash('message', "Seluruh ({$count}) donatur berhasil dihapus bersih.");
    }

    public function render()
    {
        $kelompoks = Kelompok::with(['anggotaKelompok.donatur', 'siswaPenerima.siswa.rombel'])
            ->latest('id')
            ->paginate(10);

        $donaturs = Donatur::query()
            ->when($this->searchDonatur, fn($q) => $q->where('nama', 'like', "%{$this->searchDonatur}%")->orWhere('nomor_telepon', 'like', "%{$this->searchDonatur}%"))
            ->with(['kelompoks'])
            ->latest('id')
            ->paginate(15);

        $availableDonaturs = Donatur::where('tipe', 'rutin')->where('is_aktif', true)->with('kelompoks')->orderBy('nama')->get();

        $selectedKelompok = $this->selectedKelompokId ? Kelompok::with(['anggotaKelompok.donatur'])->find($this->selectedKelompokId) : null;

        return view('beasiswaota::livewire.donatur-kelompok-index', [
            'kelompoks' => $kelompoks,
            'donaturs' => $donaturs,
            'availableDonaturs' => $availableDonaturs,
            'selectedKelompok' => $selectedKelompok,
        ])->layout('components.layouts.app', ['title' => 'Pengaturan Donatur & Kelompok OTA']);
    }
}
