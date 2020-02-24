<?php

namespace App\Models\Asset;

use App\Exceptions\BusinessLogicException;

class Units
{
    const   DATETIME = 'Y-m-d h:i:s A';

    private static $prefixes =
        [
            'K' => 1000,
            'M' => 1000 * 1000,
            'G' => 1000 * 1000 * 1000,
            'T' => 1000 * 1000 * 1000 * 1000,
            'P' => 1000 * 1000 * 1000 * 1000 * 1000,
        ];

    private static $units =
        [
            'Kh/s' => 1000,
            'Mh/s' => 1000 * 1000,
            'Gh/s' => 1000 * 1000 * 1000,
            'Th/s' => 1000 * 1000 * 1000 * 1000,
            'Ph/s' => 1000 * 1000 * 1000 * 1000 * 1000,
        ];

    private static $suffixes =
        [
            'K' => 'KH/s',
            'M' => 'MH/s',
            'G' => 'GH/s',
            'T' => 'TH/s',
            'P' => 'PH/s',
        ];

    private static function getSuffixes()
    {
        return
            [
                'hash'  =>
                    [
                        'K' => 'KH/s',
                        'M' => 'MH/s',
                        'G' => 'GH/s',
                        'T' => 'TH/s',
                        'P' => 'PH/s',
                    ],
                'power' =>
                    [
                        'K' => 'kW',
                        'M' => 'MW',
                        'G' => 'GW',
                        'T' => 'TW',
                    ],
            ];
    }

    public static function pretty(float $number, int $roundPoints = 2, ?string $suffix = null): array
    {
        if (empty ($suffix)) {
            $suffix = 'hash';
        }
        $suffixes = self::getSuffixes()[$suffix];

        $possibleUnits = array_reverse(array_keys($suffixes));

        foreach ($possibleUnits as $unit) {
            if ($number / self::$prefixes[$unit] >= 0.5) {
                $result = round($number / self::$prefixes[$unit], $roundPoints);

                return
                    [
                        'value'     => $result,
                        'raw'       => $number,
                        'formatted' => $result . ' ' . $suffixes[$unit],
                        'unit'      => $suffixes[$unit],
                    ];
            }
        }

        return ['raw' => $number, 'value' => $number, 'formatted' => $number . ' H/s', 'unit' => 'H/s'];
    }

    public static function toHashPerSecondLongFormat(float $number, string $fromUnit): float
    {
        if (!in_array($fromUnit, array_keys(self::$units))) {
            throw new BusinessLogicException('Unknown unit ' . $fromUnit);
        }

        return $number * self::$units[$fromUnit];
    }

    public static function toHashPerSecond(float $number, string $fromUnit): float
    {
        if (!in_array($fromUnit, array_keys(self::$prefixes))) {
            throw new BusinessLogicException('Unknown unit ' . $fromUnit);
        }

        return $number * self::$prefixes[$fromUnit];
    }

    public static function HPS(float $number, string $fromUnit, string $toUnit): float
    {
        if (!in_array($toUnit, array_keys(self::$prefixes))) {
            throw new BusinessLogicException('Unknown unit ' . $toUnit);
        }

        $HPS = self::toHashPerSecond($number, $fromUnit);

        return $HPS / self::$prefixes[$toUnit];
    }

    public static function differencePercent($firstNumber, $secondNumber): float
    {
        if ($firstNumber == $secondNumber) {
            return 0;
        }
        if ($secondNumber == 0) {
            $secondNumber = 1;
        }

        $percent = round((float)$firstNumber * 100 / (float)$secondNumber, 2);

        return $percent;
    }

    public static function periodToSeconds(string $period): int
    {
        $period = strtolower($period);

        switch ($period) {
            case '1h':
                return 3600;
                break;
            case '3h':
                return 3600 * 3;
                break;
            case '1d':
                return 3600 * 24;
                break;
            case '7d':
                return 3600 * 24 * 7;
                break;
            case '1m':
                return 3600 * 24 * 30;
                break;
            default:
                return -1;
        }
    }

}
