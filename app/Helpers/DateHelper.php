<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    /**
     * Format tanggal Indonesia dengan waktu
     */
    public static function indonesianDateTime($date, $withTime = true)
    {
        if (!$date) return '-';

        $carbonDate = Carbon::parse($date);

        $months = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        $day = $carbonDate->format('d');
        $month = $months[$carbonDate->format('n') - 1];
        $year = $carbonDate->format('Y');

        if ($withTime) {
            $time = $carbonDate->format('H:i');
            return "{$day} {$month} {$year} {$time} WIB";
        }

        return "{$day} {$month} {$year}";
    }

    /**
     * Format tanggal singkat Indonesia
     */
    public static function indonesianShortDate($date)
    {
        if (!$date) return '-';

        $carbonDate = Carbon::parse($date);

        $months = [
            'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
            'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
        ];

        $day = $carbonDate->format('d');
        $month = $months[$carbonDate->format('n') - 1];
        $year = $carbonDate->format('Y');

        return "{$day} {$month} {$year}";
    }

    /**
     * Format waktu saja
     */
    public static function indonesianTime($date)
    {
        if (!$date) return '-';

        $carbonDate = Carbon::parse($date);
        return $carbonDate->format('H:i') . ' WIB';
    }
}
