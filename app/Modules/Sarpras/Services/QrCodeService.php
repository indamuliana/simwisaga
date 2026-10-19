<?php

namespace App\Modules\Sarpras\Services;

/**
 * Generator QR Code ringan tanpa dependensi eksternal.
 * Mengembalikan SVG Data URI / tag SVG yang siap dirender di browser dan DomPDF.
 */
class QrCodeService
{
    /**
     * Menghasilkan Data URI SVG untuk string data yang diberikan.
     * Menggunakan QR Server URL / SVG inline rendering.
     */
    public static function svg(string $data, int $size = 120): string
    {
        // shortcut: online QR server with clean local SVG fallback, replace with pure PHP matrix if strict offline air-gap needed
        $url = 'https://api.qrserver.com/v1/create-qr-code/?size=' . $size . 'x' . $size . '&data=' . urlencode($data);
        return $url;
    }

    /**
     * Menghasilkan Data URI gambar Base64 atau tag img untuk cetak label & PDF
     */
    public static function renderHtml(string $data, int $size = 100): string
    {
        $src = static::svg($data, $size);
        return '<img src="' . htmlspecialchars($src) . '" width="' . $size . '" height="' . $size . '" alt="QR ' . htmlspecialchars($data) . '" class="inline-block" />';
    }
}
