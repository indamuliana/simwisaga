<?php

namespace App\Modules\Penilaian\Livewire;

use App\Modules\Penilaian\Models\PengaturanPenilaian;
use Livewire\Attributes\Layout;
use Livewire\Component;

class PengaturanPenilaianIndex extends Component
{
    public $bobot_tp = 50;
    public $bobot_sts = 30;
    public $bobot_nh = 20;
    public $intervals = [];

    public function mount()
    {
        if (!auth()->user()->hasAnyRole(['Super Admin', 'Admin', 'Kepala Sekolah', 'Waka Kurikulum'])) {
            abort(403, 'Akses terbatas untuk Administrator Kurikulum.');
        }

        $setting = PengaturanPenilaian::getActive();
        $this->bobot_tp = $setting->bobot_tp ?? 50;
        $this->bobot_sts = $setting->bobot_sts ?? 30;
        $this->bobot_nh = $setting->bobot_nh ?? 20;
        $raw = $setting->predikat_intervals ?: PengaturanPenilaian::defaultIntervals();
        $this->intervals = array_values($raw);
    }

    public function addInterval()
    {
        $this->intervals[] = [
            'min' => 0.0,
            'max' => 74.99,
            'label' => 'Perlu Intervensi',
            'deskripsi' => 'Memerlukan intervensi khusus.',
        ];
    }

    public function removeInterval($index)
    {
        unset($this->intervals[$index]);
        $this->intervals = array_values($this->intervals);
    }

    public function resetToDefault()
    {
        $this->bobot_tp = 50;
        $this->bobot_sts = 30;
        $this->bobot_nh = 20;
        $this->intervals = PengaturanPenilaian::defaultIntervals();

        $setting = PengaturanPenilaian::first();
        if ($setting) {
            $setting->bobot_tp = 50;
            $setting->bobot_sts = 30;
            $setting->bobot_nh = 20;
            $setting->predikat_intervals = $this->intervals;
            $setting->save();
        }

        session()->flash('message', 'Pengaturan berhasil dikembalikan ke standar awal.');
    }

    public function save()
    {
        $this->validate([
            'bobot_tp' => 'required|integer|min:0|max:100',
            'bobot_sts' => 'required|integer|min:0|max:100',
            'bobot_nh' => 'required|integer|min:0|max:100',
            'intervals' => 'required|array|min:1',
            'intervals.*.min' => 'required|numeric|min:0|max:100',
            'intervals.*.max' => 'required|numeric|min:0|max:100',
            'intervals.*.label' => 'required|string|max:50',
            'intervals.*.deskripsi' => 'nullable|string|max:255',
        ]);

        $totalBobot = $this->bobot_tp + $this->bobot_sts + $this->bobot_nh;
        if ($totalBobot !== 100) {
            $this->addError('bobot_total', "Total bobot harus bernilai 100%. Saat ini: {$totalBobot}%.");
            return;
        }

        $setting = PengaturanPenilaian::first();
        if (!$setting) {
            $setting = new PengaturanPenilaian();
        }

        $setting->bobot_tp = $this->bobot_tp;
        $setting->bobot_sts = $this->bobot_sts;
        $setting->bobot_nh = $this->bobot_nh;
        $setting->predikat_intervals = $this->intervals;
        $setting->save();

        session()->flash('message', 'Pengaturan bobot dan skala predikat berhasil disimpan.');
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('penilaian::livewire.pengaturan-penilaian-index');
    }
}
