<?php

namespace App\Http\Utils;

class TimeDurationCalculator
{
    /**
     * Calculate the duration between two dates.
     *
     * @param \Carbon\Carbon $startDate
     * @param \Carbon\Carbon $endDate
     * @return string
     */
    public static function calculateTimeDuration($startDate, $endDate)
    {
        $days = $startDate->diffInDays($endDate);
        $hours = $startDate->diffInHours($endDate) % 24;
        $minutes = $startDate->diffInMinutes($endDate) % 60;

        return sprintf('%dD %dHrs %dMins', $days, $hours, $minutes);
    }
}
