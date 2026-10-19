<div style="font-size: 10pt; line-height: 1.5; color: #1e293b;">
    <p>
        Berdasarkan hasil pemeriksaan fisik dan evaluasi teknis kondisi barang inventaris sekolah pada tanggal <strong>{{ $tanggalSurat }}</strong>, panitia pemeriksa sarana dan prasarana dengan ini menyatakan bahwa barang-barang di bawah ini telah mengalami <strong>rusak berat / aus / kadaluarsa</strong> dan secara teknis maupun ekonomis tidak dapat diperbaiki kembali:
    </p>

    <table style="width: 100%; border-collapse: collapse; margin: 15px 0; font-size: 9pt;">
        <thead>
            <tr style="background-color: #f1f5f9;">
                <th style="border: 1px solid #cbd5e1; padding: 6px; text-align: center; width: 30px;">No</th>
                <th style="border: 1px solid #cbd5e1; padding: 6px; text-align: left;">Kode Aset</th>
                <th style="border: 1px solid #cbd5e1; padding: 6px; text-align: left;">Nama Barang & Merk</th>
                <th style="border: 1px solid #cbd5e1; padding: 6px; text-align: left;">Lokasi Terakhir</th>
                <th style="border: 1px solid #cbd5e1; padding: 6px; text-align: center;">Tahun Perolehan</th>
                <th style="border: 1px solid #cbd5e1; padding: 6px; text-align: left;">Sebab Penghapusan / Kondisi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($asets as $idx => $a)
                <tr>
                    <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: center;">{{ $idx + 1 }}</td>
                    <td style="border: 1px solid #cbd5e1; padding: 6px; font-family: monospace; font-weight: bold;">{{ $a->kode_aset }}</td>
                    <td style="border: 1px solid #cbd5e1; padding: 6px;">{{ $a->nama_barang }} ({{ $a->merk_tipe ?: '-' }})</td>
                    <td style="border: 1px solid #cbd5e1; padding: 6px;">{{ $a->ruangan?->nama_ruangan ?? '-' }}</td>
                    <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: center;">{{ $a->tahun_perolehan }}</td>
                    <td style="border: 1px solid #cbd5e1; padding: 6px;">{{ $a->keterangan ?: 'Rusak Berat / Aus masa pakai' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="border: 1px solid #cbd5e1; padding: 10px; text-align: center;">Tidak ada barang yang tercatat untuk dimusnahkan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p style="margin-top: 12px;">
        Oleh karena itu, panitia merekomendasikan dan memutuskan bahwa seluruh barang yang tercantum di atas untuk <strong>dihapuskan dari Buku Induk Inventaris Sekolah</strong> dan dilakukan tindakan pemusnahan / daur ulang sesuai dengan peraturan perundang-undangan dan pedoman pengelolaan aset sekolah yang berlaku.
    </p>

    <p style="margin-top: 10px;">
        Demikian Berita Acara Pemusnahan / Penghapusan Barang Inventaris ini dibuat dengan sesungguhnya untuk dapat digunakan sebagai dasar penghapusan catatan aset sekolah.
    </p>
</div>
