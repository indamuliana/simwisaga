<?php

namespace App\Core\Services;

use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Output\QRGdImagePNG;
use chillerlan\QRCode\Output\QRMarkupSVG;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

/**
 * Generator QR Code offline berstandar ISO/IEC 18004.
 * Menghasilkan Data URI SVG / PNG raster yang siap dirender di browser dan DomPDF tanpa koneksi internet.
 */
class QrCodeService
{
    /**
     * Menghasilkan string raw XML SVG untuk string data yang diberikan.
     */
    public static function rawSvg(string $data, int $size = 120): string
    {
        $options = new QROptions([
            'outputInterface' => QRMarkupSVG::class,
            'outputBase64'    => false,
            'addQuietzone'    => true,
            'quietzoneSize'   => 4,
            'eccLevel'        => EccLevel::M,
            'svgAddXmlHeader' => false,
        ]);

        $svg = (new QRCode($options))->render($data);

        if (!str_contains($svg, 'width=') && !str_contains($svg, 'height=')) {
            $svg = preg_replace('/<svg /', '<svg width="' . $size . '" height="' . $size . '" ', $svg, 1);
        }

        return $svg;
    }

    /**
     * Alias kompatibilitas untuk generateSvg.
     */
    public static function generateSvg(string $data, int $size = 120): string
    {
        return self::rawSvg($data, $size);
    }

    /**
     * Menghasilkan Data URI SVG base64 lokal untuk string data yang diberikan.
     */
    public static function svg(string $data, int $size = 120): string
    {
        return 'data:image/svg+xml;base64,' . base64_encode(self::rawSvg($data, $size));
    }

    /**
     * Menghasilkan Data URI raster PNG Base64 lokal (kompatibel 100% dengan DomPDF dan mudah discan).
     */
    public static function pngDataUri(string $data, int $targetSize = 150): string
    {
        // Skala 6 menghasilkan resolusi tajam ~250px dengan quiet zone 4 modul standar scanner
        $scale = max(4, (int) round($targetSize / 25));

        $options = new QROptions([
            'outputInterface' => QRGdImagePNG::class,
            'outputBase64'    => true,
            'scale'           => $scale,
            'addQuietzone'    => true,
            'quietzoneSize'   => 4,
            'eccLevel'        => EccLevel::M,
        ]);

        return (new QRCode($options))->render($data);
    }

    /**
     * Menghasilkan tag img Data URI untuk cetak label & PDF.
     */
    public static function renderHtml(string $data, int $size = 100): string
    {
        $src = static::svg($data, $size);
        return '<img src="' . $src . '" width="' . $size . '" height="' . $size . '" alt="QR ' . htmlspecialchars($data) . '" class="inline-block" />';
    }
}
