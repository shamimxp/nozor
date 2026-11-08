<?php

namespace App\Support;

class Code128
{
    /**
     * Code 128 bar/space widths. Index 106 is the stop pattern.
     *
     * @var array<int, string>
     */
    private const PATTERNS = [
        '212222', '222122', '222221', '121223', '121322', '131222', '122213', '122312', '132212', '221213',
        '221312', '231212', '112232', '122132', '122231', '113222', '123122', '123221', '223211', '221132',
        '221231', '213212', '223112', '312131', '311222', '321122', '321221', '312212', '322112', '322211',
        '212123', '212321', '232121', '111323', '131123', '131321', '112313', '132113', '132311', '211313',
        '231113', '231311', '112133', '112331', '132131', '113123', '113321', '133121', '313121', '211331',
        '231131', '213113', '213311', '213131', '311123', '311321', '331121', '312113', '312311', '332111',
        '314111', '221411', '431111', '111224', '111422', '121124', '121421', '141122', '141221', '112214',
        '112412', '122114', '122411', '142112', '142211', '241211', '221114', '413111', '241112', '134111',
        '111242', '121142', '121241', '114212', '124112', '124211', '411212', '421112', '421211', '212141',
        '214121', '412121', '111143', '111341', '131141', '114113', '114311', '411113', '411311', '113141',
        '114131', '311141', '411131', '211412', '211214', '211232', '2331112',
    ];

    public static function file(string $value): string
    {
        $directory = storage_path('app/barcodes');
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $path = $directory . '/' . md5($value) . '.svg';
        if (! is_file($path)) {
            file_put_contents($path, self::svg($value));
        }

        return $path;
    }

    public static function svg(string $value): string
    {
        $bits = self::bits($value);
        $scale = 2;
        $height = 40;
        $quiet = 10 * $scale;
        $width = (strlen($bits) * $scale) + ($quiet * 2);
        $rects = '';
        $x = $quiet;
        $length = strlen($bits);
        $index = 0;

        while ($index < $length) {
            if ($bits[$index] === '1') {
                $start = $index;
                while ($index < $length && $bits[$index] === '1') {
                    $index++;
                }
                $barWidth = ($index - $start) * $scale;
                $rects .= '<rect x="' . $x . '" y="0" width="' . $barWidth . '" height="' . $height . '" fill="#000000"/>';
                $x += $barWidth;
            } else {
                $x += $scale;
                $index++;
            }
        }

        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<svg xmlns="http://www.w3.org/2000/svg" width="' . $width . '" height="' . $height . '" viewBox="0 0 ' . $width . ' ' . $height . '">'
            . '<rect x="0" y="0" width="' . $width . '" height="' . $height . '" fill="#ffffff"/>'
            . $rects
            . '</svg>';
    }

    private static function bits(string $value): string
    {
        $value = $value === '' ? '0' : $value;
        $symbols = [104];

        $length = strlen($value);
        for ($i = 0; $i < $length; $i++) {
            $ord = ord($value[$i]);
            $symbols[] = ($ord >= 32 && $ord <= 127) ? $ord - 32 : 0;
        }

        $checksum = $symbols[0];
        $count = count($symbols);
        for ($i = 1; $i < $count; $i++) {
            $checksum += $symbols[$i] * $i;
        }
        $symbols[] = $checksum % 103;
        $symbols[] = 106;

        $bits = '';
        foreach ($symbols as $symbol) {
            $bits .= self::patternBits(self::PATTERNS[$symbol]);
        }

        return $bits;
    }

    private static function patternBits(string $pattern): string
    {
        $bits = '';
        $drawBar = true;
        $length = strlen($pattern);
        for ($i = 0; $i < $length; $i++) {
            $bits .= str_repeat($drawBar ? '1' : '0', (int) $pattern[$i]);
            $drawBar = ! $drawBar;
        }

        return $bits;
    }
}
