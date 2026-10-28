<?php

namespace App\Modules\Sarpras\Services;

/**
 * Generator QR Code ringan offline berbasis PHP murni tanpa dependensi eksternal.
 * Menghasilkan Data URI SVG / tag SVG yang siap dirender di browser dan DomPDF tanpa koneksi internet.
 */
class QrCodeService
{
    private static array $exp = [];
    private static array $log = [];
    private static bool $initGf = false;

    private static function initGf(): void
    {
        if (self::$initGf) return;
        self::$exp = array_fill(0, 512, 0);
        self::$log = array_fill(0, 256, 0);
        $x = 1;
        for ($i = 0; $i < 255; $i++) {
            self::$exp[$i] = $x;
            self::$log[$x] = $i;
            $x <<= 1;
            if ($x & 0x100) {
                $x ^= 0x11d;
            }
        }
        for ($i = 255; $i < 512; $i++) {
            self::$exp[$i] = self::$exp[$i - 255];
        }
        self::$initGf = true;
    }

    private static function gfMul(int $x, int $y): int
    {
        if ($x === 0 || $y === 0) return 0;
        return self::$exp[self::$log[$x] + self::$log[$y]];
    }

    private static function rsPoly(int $n): array
    {
        $poly = [1];
        for ($i = 0; $i < $n; $i++) {
            $next = [0];
            $factor = self::$exp[$i];
            foreach ($poly as $c) {
                $next[] = self::gfMul($c, $factor);
            }
            for ($j = 0; $j < count($poly); $j++) {
                $next[$j] ^= $poly[$j];
            }
            $poly = $next;
        }
        return $poly;
    }

    private static function rsEncode(array $data, int $ecCount): array
    {
        $gen = self::rsPoly($ecCount);
        $res = array_fill(0, $ecCount, 0);
        foreach ($data as $b) {
            $factor = $b ^ $res[0];
            array_shift($res);
            $res[] = 0;
            for ($i = 0; $i < $ecCount; $i++) {
                $res[$i] ^= self::gfMul($gen[$i + 1], $factor);
            }
        }
        return $res;
    }

    /**
     * Encode text ke matrix QR (Version 1-4, Level M, Byte mode)
     */
    public static function encodeMatrix(string $text): array
    {
        self::initGf();
        $len = strlen($text);
        
        $capacity = [1 => [16, 10, 21], 2 => [28, 16, 25], 3 => [44, 26, 29], 4 => [64, 36, 33]];
        
        if ($len <= 14) $version = 1;
        elseif ($len <= 26) $version = 2;
        elseif ($len <= 42) $version = 3;
        else $version = 4;

        [$totalBytes, $ecBytes, $size] = $capacity[$version];
        $dataCapacity = $totalBytes - $ecBytes;

        $bits = "0100";
        $bits .= str_pad(decbin($len), 8, '0', STR_PAD_LEFT);
        for ($i = 0; $i < $len; $i++) {
            $bits .= str_pad(decbin(ord($text[$i])), 8, '0', STR_PAD_LEFT);
        }

        $maxBits = $dataCapacity * 8;
        $bits = substr($bits, 0, $maxBits);
        $rem = $maxBits - strlen($bits);
        if ($rem > 0) {
            $bits .= str_repeat('0', min(4, $rem));
        }
        while (strlen($bits) % 8 !== 0) {
            $bits .= '0';
        }
        $pad = ["11101100", "00010001"];
        $pIdx = 0;
        while (strlen($bits) < $maxBits) {
            $bits .= $pad[$pIdx % 2];
            $pIdx++;
        }

        $dataBytes = [];
        for ($i = 0; $i < strlen($bits); $i += 8) {
            $dataBytes[] = bindec(substr($bits, $i, 8));
        }

        $ec = self::rsEncode($dataBytes, $ecBytes);
        $allBytes = array_merge($dataBytes, $ec);

        $allBits = [];
        foreach ($allBytes as $byte) {
            for ($b = 7; $b >= 0; $b--) {
                $allBits[] = ($byte >> $b) & 1;
            }
        }

        $m = array_fill(0, $size, array_fill(0, $size, null));

        self::placeFinder($m, 0, 0);
        self::placeFinder($m, $size - 7, 0);
        self::placeFinder($m, 0, $size - 7);

        for ($i = 8; $i < $size - 8; $i++) {
            $m[6][$i] = ($i % 2 === 0);
            $m[$i][6] = ($i % 2 === 0);
        }

        $m[$size - 8][8] = true;

        if ($version >= 2) {
            $alignPos = [2 => [18], 3 => [22], 4 => [26]];
            foreach ($alignPos[$version] as $r) {
                foreach ($alignPos[$version] as $c) {
                    if ($m[$r][$c] === null) {
                        self::placeAlignment($m, $r, $c);
                    }
                }
            }
        }

        for ($i = 0; $i < 9; $i++) {
            if ($m[8][$i] === null) $m[8][$i] = false;
            if ($m[$i][8] === null) $m[$i][8] = false;
        }
        for ($i = $size - 8; $i < $size; $i++) {
            if ($m[8][$i] === null) $m[8][$i] = false;
            if ($m[$i][8] === null) $m[$i][8] = false;
        }

        $bitIdx = 0;
        $dir = -1;
        $col = $size - 1;
        while ($col > 0) {
            if ($col === 6) $col--;
            $rows = ($dir === -1) ? range($size - 1, 0, -1) : range(0, $size - 1, 1);
            foreach ($rows as $row) {
                foreach ([$col, $col - 1] as $c) {
                    if ($m[$row][$c] === null) {
                        $bit = ($bitIdx < count($allBits)) ? $allBits[$bitIdx] : 0;
                        $mask = (($row + $c) % 2 === 0);
                        $m[$row][$c] = ($bit === 1) ^ $mask;
                        $bitIdx++;
                    }
                }
            }
            $col -= 2;
            $dir = -$dir;
        }

        $fmt = 0x5412;
        $fmtBits = [];
        for ($i = 14; $i >= 0; $i--) {
            $fmtBits[] = (bool)(($fmt >> $i) & 1);
        }

        $m[8][0] = $fmtBits[0]; $m[8][1] = $fmtBits[1]; $m[8][2] = $fmtBits[2];
        $m[8][3] = $fmtBits[3]; $m[8][4] = $fmtBits[4]; $m[8][5] = $fmtBits[5];
        $m[8][7] = $fmtBits[6]; $m[8][8] = $fmtBits[7]; $m[7][8] = $fmtBits[8];
        $m[5][8] = $fmtBits[9]; $m[4][8] = $fmtBits[10]; $m[3][8] = $fmtBits[11];
        $m[2][8] = $fmtBits[12]; $m[1][8] = $fmtBits[13]; $m[0][8] = $fmtBits[14];

        for ($i = 0; $i < 7; $i++) {
            $m[$size - 1 - $i][8] = $fmtBits[$i];
        }
        for ($i = 0; $i < 8; $i++) {
            $m[8][$size - 8 + $i] = $fmtBits[7 + $i];
        }

        return $m;
    }

    private static function placeFinder(array &$m, int $r, int $c): void
    {
        for ($y = -1; $y <= 7; $y++) {
            for ($x = -1; $x <= 7; $x++) {
                $row = $r + $y;
                $col = $c + $x;
                if ($row < 0 || $row >= count($m) || $col < 0 || $col >= count($m)) continue;
                if ($y === -1 || $y === 7 || $x === -1 || $x === 7) {
                    $m[$row][$col] = false;
                } elseif ($y === 0 || $y === 6 || $x === 0 || $x === 6) {
                    $m[$row][$col] = true;
                } elseif ($y >= 2 && $y <= 4 && $x >= 2 && $x <= 4) {
                    $m[$row][$col] = true;
                } else {
                    $m[$row][$col] = false;
                }
            }
        }
    }

    private static function placeAlignment(array &$m, int $centerR, int $centerC): void
    {
        for ($y = -2; $y <= 2; $y++) {
            for ($x = -2; $x <= 2; $x++) {
                $r = $centerR + $y;
                $c = $centerC + $x;
                $isBorder = (abs($y) === 2 || abs($x) === 2);
                $isCenter = ($y === 0 && $x === 0);
                $m[$r][$c] = ($isBorder || $isCenter);
            }
        }
    }

    /**
     * Menghasilkan string raw XML SVG untuk string data yang diberikan.
     */
    public static function rawSvg(string $data, int $size = 120): string
    {
        $matrix = self::encodeMatrix($data);
        $count = count($matrix);
        $margin = 2;
        $totalSize = $count + ($margin * 2);

        $path = '';
        for ($y = 0; $y < $count; $y++) {
            for ($x = 0; $x < $count; $x++) {
                if ($matrix[$y][$x]) {
                    $rx = $x + $margin;
                    $ry = $y + $margin;
                    $path .= "M{$rx},{$ry}h1v1h-1z ";
                }
            }
        }

        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $totalSize . ' ' . $totalSize . '" width="' . $size . '" height="' . $size . '" shape-rendering="crispEdges">'
            . '<rect width="100%" height="100%" fill="#ffffff"/>'
            . '<path d="' . rtrim($path) . '" fill="#000000"/>'
            . '</svg>';
    }

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
     * Menghasilkan Data URI gambar Base64 atau tag img untuk cetak label & PDF
     */
    public static function renderHtml(string $data, int $size = 100): string
    {
        $src = static::svg($data, $size);
        return '<img src="' . $src . '" width="' . $size . '" height="' . $size . '" alt="QR ' . htmlspecialchars($data) . '" class="inline-block" />';
    }
}
