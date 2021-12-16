<?php
namespace App\Interfaces;

use DateTime;

interface TimeInterface
{
    /**
     * timeStringToDateTime
     *
     * @param string $timeString
     * @return DateTime|null
     */
    public function timeStringToDateTime(string $timeString): ?DateTime;
    /**
     * generateTime
     *
     * @param string $timeString
     * @return DateTime
     */
    public function generateTime(string $timeString): DateTime;
}
