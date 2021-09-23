<?php

namespace App\Service;

use DateTime;
use DateInterval;

class TimeService
{
    /**
     * margeDuRetard
     */
    private $margeDuRetard = 30;

    /**
     * generateTime
     * 
     * @param string $time
     * 
     * @return DateTime
     */
    public function generateTime(string $time): DateTime
    {
        if ($time != "")
            return new DateTime($time);
        else
            return new DateTime("00:00:00");
    }

    /**
     * dateTimeToDateInterval
     *
     * @param DateTime $dateTime
     * @return DateInterval
     */
    public function dateTimeToDateInterval(DateTime $dateTime): DateInterval
    {
        return new DateInterval('PT' . $dateTime->format('H') . 'H' . $dateTime->format('i')  . 'M' . $dateTime->format('s') . 'S');
    }

    /**
     * dateIntervalToDateTime
     *
     * @param DateInterval $dateInterval
     * @return DateTime
     */
    public function dateIntervalToDateTime(DateInterval $dateInterval): DateTime
    {
        return new DateTime($dateInterval->h . ":" . $dateInterval->i . ":" . $dateInterval->s);
    }

    /**
     * diffTime
     * 
     * @param DateTime $timeMax
     * @param DateTime $timeMix
     * 
     * @return DateInterval
     */
    public function diffTime(DateTime $timeMax, DateTime $timeMix): DateInterval
    {
        $diff =  date_diff($timeMax, $timeMix);
        return new DateInterval('PT' . $diff->h . 'H' . $diff->i . 'M' . $diff->s . 'S');
    }

    /**
     * margeDuRetard
     * 
     * @return DateInterval
     */
    public function margeDuRetard(): DateInterval
    {
        return new DateInterval('PT' . $this->margeDuRetard . 'M');
    }
}
