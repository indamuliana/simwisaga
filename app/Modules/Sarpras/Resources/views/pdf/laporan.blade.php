<div style="font-size: 8.5pt; line-height: 1.4; color: #1e293b;">
    <table style="width: 100%; border-collapse: collapse; margin-top: 5px;">
        <thead>
            <tr style="background-color: #0f172a; color: #ffffff;">
                <th style="border: 1px solid #334155; padding: 5px 4px; text-align: center; width: 25px;">No</th>
                <th style="border: 1px solid #334155; padding: 5px 4px; text-align: left;">Kode Aset</th>
                <th style="border: 1px solid #334155; padding: 5px 4px; text-align: left;">Nama Barang</th>
                <th style="border: 1px solid #334155; padding: 5px 4px; text-align: left;">Kategori</th>
                <th style="border: 1px solid #334155; padding: 5px 4px; text-align: left;">Merk / No. Seri</th>
                <th style="border: 1px solid #334155; padding: 5px 4px; text-align: left;">Ruangan</th>
                <th style="border: 1px solid #334155; padding: 5px 4px; text-align: center;">Tahun / Sumber</th>
                <th style="border: 1px solid #334155; padding: 5px 4px; text-align: center;">Kondisi</th>
                <th style="border: 1px solid #334155; padding: 5px 4px; text-align: center;">Status</th>
                <th style="border: 1px solid #334155; padding: 5px 4px; text-align: right;">Nilai (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($asets as $idx => $a)
                <tr style="{{ $idx % 2 == 1 ? 'background-color: #f8fafc;' : '' }}">
                    <td style="border: 1px solid #cbd5e1; padding: 4px; text-align: center;">{{ $idx + 1 }}</td>
                    <td style="border: 1px solid #cbd5e1; padding: 4px; font-family: monospace; font-weight: bold;">{{ $a->kode_aset }}</td>
                    <td style="border: 1px solid #cbd5e1; padding: 4px; font-weight: 600;">{{ $a->nama_barang }}</td>
                    <td style="border: 1px solid #cbd5e1; padding: 4px;">{{ $a->kategori?->nama }}</td>
                    <td style="border: 1px solid #cbd5e1; padding: 4px;">{{ $a->merk_tipe ?: '-' }} @if($a->nomor_seri) ({{ $a->nomor_seri }}) @endif</td>
                    <td style="border: 1px solid #cbd5e1; padding: 4px;">{{ $a->ruangan?->nama_ruangan ?? 'Belum Ditentukan' }}</td>
                    <td style="border: 1px solid #cbd5e1; padding: 4px; text-align: center;">{{ $a->tahun_perolehan }}<br><small>{{ $a->sumber_dana }}</small></td>
                    <td style="border: 1px solid #cbd5e1; padding: 4px; text-align: center;">{{ $a->kondisi }}</td>
                    <td style="border: 1px solid #cbd5e1; padding: 4px; text-align: center;">{{ $a->status }}</td>
                    <td style="border: 1px solid #cbd5e1; padding: 4px; text-align: right;">{{ number_format($a->harga_perolehan, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="border: 1px solid #cbd5e1; padding: 12px; text-align: center;">Tidak ada data aset yang ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <td colspan="9" style="border: 1px solid #cbd5e1; padding: 6px; text-align: right;">Total Nilai Perolehan Inventaris:</td>
                <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: right;">Rp {{ number_format($totalNilai, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</div>
