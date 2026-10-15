<?php

namespace App\Modules\BeasiswaOta\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\BeasiswaOta\Models\OtaSetting;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class OtaSettingIndex extends Component
{
    use GuardsPermission, WithFileUploads;

    public $certificateBgFile;
    public ?string $currentBgPath = null;

    public float|int|string $defaultTargetKelompok = 2000000;
    public string $titimangsaKota = 'Garut';
    public string $kepsekNama = '';
    public string $kepsekNip = '';
    public string $ketuaOtaNama = '';
    public string $bendaharaNama = '';

    public function mount()
    {
        $this->currentBgPath = OtaSetting::get('certificate_bg_path');
        $this->defaultTargetKelompok = (float) OtaSetting::get('default_target_kelompok', 2000000);
        $this->titimangsaKota = OtaSetting::get('titimangsa_kota', 'Garut');
        $this->kepsekNama = OtaSetting::get('kepsek_nama', 'H. Dedik, M.Pd.');
        $this->kepsekNip = OtaSetting::get('kepsek_nip', '-');
        $this->ketuaOtaNama = OtaSetting::get('ketua_ota_nama', 'Koordinator OTA');
        $this->bendaharaNama = OtaSetting::get('bendahara_nama', 'Bendahara Program OTA');
    }

    public function saveSettings()
    {
        $this->validate([
            'defaultTargetKelompok' => 'required|numeric|min:0',
            'titimangsaKota' => 'required|string|max:100',
            'kepsekNama' => 'required|string|max:150',
            'ketuaOtaNama' => 'required|string|max:150',
            'bendaharaNama' => 'required|string|max:150',
            'certificateBgFile' => 'nullable|image|max:5120', // max 5MB
        ]);

        if ($this->certificateBgFile) {
            // Hapus file lama jika ada
            if ($this->currentBgPath && Storage::disk('public')->exists($this->currentBgPath)) {
                Storage::disk('public')->delete($this->currentBgPath);
            }

            $path = $this->certificateBgFile->store('ota/templates', 'public');
            OtaSetting::set('certificate_bg_path', $path);
            $this->currentBgPath = $path;
            $this->certificateBgFile = null;
        }

        OtaSetting::set('default_target_kelompok', (string) $this->defaultTargetKelompok);
        OtaSetting::set('titimangsa_kota', $this->titimangsaKota);
        OtaSetting::set('kepsek_nama', $this->kepsekNama);
        OtaSetting::set('kepsek_nip', $this->kepsekNip);
        OtaSetting::set('ketua_ota_nama', $this->ketuaOtaNama);
        OtaSetting::set('bendahara_nama', $this->bendaharaNama);

        session()->flash('message', 'Pengaturan Beasiswa OTA dan template sertifikat berhasil disimpan!');
    }

    public function removeTemplateBg()
    {
        if ($this->currentBgPath && Storage::disk('public')->exists($this->currentBgPath)) {
            Storage::disk('public')->delete($this->currentBgPath);
        }

        OtaSetting::set('certificate_bg_path', null);
        $this->currentBgPath = null;
        session()->flash('message', 'Template background sertifikat dikembalikan ke desain standar bawaan.');
    }

    public function render()
    {
        return view('beasiswaota::livewire.setting-index')
            ->layout('components.layouts.app', ['title' => 'Pengaturan & Template Beasiswa OTA']);
    }
}
