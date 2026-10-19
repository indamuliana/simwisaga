<?php

namespace App\Modules\Sarpras\Exports;

use App\Modules\Sarpras\Models\Aset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AsetExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $ruanganId;
    protected $kategoriId;
    protected $kondisi;
    protected $sumberDana;

    public function __construct($ruanganId = null, $kategoriId = null, $kondisi = null, $sumberDana = null)
    {
        $this->ruanganId = $ruanganId;
        $this->kategoriId = $kategoriId;
        $this->kondisi = $kondisi;
        $this->sumberDana = $sumberDana;
    }

    public function collection()
    {
        $query = Aset::with(['kategori', 'ruangan'])->latest();

        if ($this->ruanganId) {
            $query->where('ruangan_id', $this->ruanganId);
        }
        if ($this->kategoriId) {
            $query->where('kategori_id', $this->kategoriId);
        }
        if ($this->kondisi) {
            $query->where('kondisi', $this->kondisi);
        }
        if ($this->sumberDana) {
            $query->where('sumber_dana', $this->sumberDana);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Aset',
            'Nama Barang',
            'Kategori',
            'Merk / Tipe',
            'Nomor Seri',
            'Ruangan / Lokasi',
            'Tahun Perolehan',
            'Sumber Dana',
            'Harga Perolehan (Rp)',
            'Kondisi',
            'Status',
            'Keterangan',
        ];
    }

    private int $rowNumber = 0;

    /**
     * @param Aset $aset
     */
    public function map($aset): array
    {
        $this->rowNumber++;
        return [
            $this->rowNumber,
            $aset->kode_aset,
            $aset->nama_barang,
            $aset->kategori?->nama ?? '-',
            $aset->merk_tipe ?? '-',
            $aset->nomor_seri ?? '-',
            $aset->ruangan?->nama_ruangan ?? 'Belum Ditentukan',
            $aset->tahun_perolehan,
            $aset->sumber_dana,
            (float) $aset->harga_perolehan,
            $aset->kondisi,
            $aset->status,
            $aset->keterangan ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1E293B'],
                ],
            ],
        ];
    }
}
