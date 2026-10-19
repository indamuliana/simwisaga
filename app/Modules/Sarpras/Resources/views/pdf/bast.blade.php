<div style="font-size: 10pt; line-height: 1.5; color: #1e293b;">
    <p>
        Pada hari ini, <strong>{{ now()->translatedFormat('l') }}</strong> tanggal <strong>{{ now()->translatedFormat('d') }}</strong> bulan <strong>{{ now()->translatedFormat('F') }}</strong> tahun <strong>{{ now()->translatedFormat('Y') }}</strong>, kami yang bertanda tangan di bawah ini:
    </p>

    <table style="width: 100%; margin: 10px 0; font-size: 9.5pt;">
        <tr>
            <td style="width: 25px; vertical-align: top;">1.</td>
            <td style="width: 160px; vertical-align: top;">Nama / Instansi</td>
            <td style="width: 10px; vertical-align: top;">:</td>
            <td style="vertical-align: top;"><strong>{{ $pihakPertama }}</strong></td>
        </tr>
        <tr>
            <td></td>
            <td style="vertical-align: top;">Kedudukan</td>
            <td style="vertical-align: top;">:</td>
            <td style="vertical-align: top;">Selanjutnya disebut sebagai <strong>PIHAK PERTAMA (Yang Menyerahkan)</strong></td>
        </tr>
        <tr>
            <td style="vertical-align: top; padding-top: 6px;">2.</td>
            <td style="vertical-align: top; padding-top: 6px;">Nama / Jabatan</td>
            <td style="vertical-align: top; padding-top: 6px;">:</td>
            <td style="vertical-align: top; padding-top: 6px;"><strong>{{ $pihakKedua }}</strong></td>
        </tr>
        <tr>
            <td></td>
            <td style="vertical-align: top;">Kedudukan</td>
            <td style="vertical-align: top;">:</td>
            <td style="vertical-align: top;">Selanjutnya disebut sebagai <strong>PIHAK KEDUA (Yang Menerima)</strong></td>
        </tr>
    </table>

    <p style="margin-top: 12px;">
        Dengan ini menyatakan bahwa <strong>PIHAK PERTAMA</strong> telah menyerahkan barang inventaris sekolah dalam keadaan baik dan lengkap kepada <strong>PIHAK KEDUA</strong>, dan <strong>PIHAK KEDUA</strong> telah menerima penyerahan barang tersebut dengan rincian sebagai berikut:
    </p>

    <table style="width: 100%; border-collapse: collapse; margin: 15px 0; font-size: 9pt;">
        <thead>
            <tr style="background-color: #f1f5f9;">
                <th style="border: 1px solid #cbd5e1; padding: 6px; text-align: center; width: 30px;">No</th>
                <th style="border: 1px solid #cbd5e1; padding: 6px; text-align: left;">Kode Aset</th>
                <th style="border: 1px solid #cbd5e1; padding: 6px; text-align: left;">Nama Barang & Spesifikasi</th>
                <th style="border: 1px solid #cbd5e1; padding: 6px; text-align: left;">Merk / No. Seri</th>
                <th style="border: 1px solid #cbd5e1; padding: 6px; text-align: center;">Jumlah</th>
                <th style="border: 1px solid #cbd5e1; padding: 6px; text-align: right;">Harga Satuan (Rp)</th>
                <th style="border: 1px solid #cbd5e1; padding: 6px; text-align: center;">Kondisi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($asets as $idx => $a)
                <tr>
                    <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: center;">{{ $idx + 1 }}</td>
                    <td style="border: 1px solid #cbd5e1; padding: 6px; font-family: monospace; font-weight: bold;">{{ $a->kode_aset }}</td>
                    <td style="border: 1px solid #cbd5e1; padding: 6px;">{{ $a->nama_barang }}</td>
                    <td style="border: 1px solid #cbd5e1; padding: 6px;">{{ $a->merk_tipe ?: '-' }} @if($a->nomor_seri)<br><small>SN: {{ $a->nomor_seri }}</small>@endif</td>
                    <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: center;">1 unit</td>
                    <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: right;">{{ number_format($a->harga_perolehan, 0, ',', '.') }}</td>
                    <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: center;">{{ $a->kondisi }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="border: 1px solid #cbd5e1; padding: 10px; text-align: center;">Tidak ada rincian aset.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f8fafc; font-weight: bold;">
                <td colspan="5" style="border: 1px solid #cbd5e1; padding: 6px; text-align: right;">Total Nilai Perolehan:</td>
                <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: right;">Rp {{ number_format($asets->sum('harga_perolehan'), 0, ',', '.') }}</td>
                <td style="border: 1px solid #cbd5e1;"></td>
            </tr>
        </tfoot>
    </table>

    <p style="margin-top: 12px;">
        Demikian Berita Acara Serah Terima ini dibuat dengan sebenarnya dalam rangkap secukupnya untuk dipergunakan sebagaimana mestinya dalam pembukuan inventaris sarana dan prasarana sekolah.
    </p>
</div>
