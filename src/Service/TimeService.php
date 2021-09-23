<?php

namespace App\Service;

use DateTime;
use DateInterval;
use App\Entity\Horaire;
use App\Entity\Pointage;

class TimeService
{
    /**
     * 
     */
    private $margeDuRetard = 30;

    /**
     * __construct
     */
    public function __construct()
    {
    }

    /**
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
     * DateIntervalToDateTime
     *
     * @param DateInterval $dateInterval
     * @return DateTime
     */
    public function DateIntervalToDateTime(DateInterval $dateInterval): DateTime
    {
        return new DateTime($dateInterval->h . ":" . $dateInterval->i . ":" . $dateInterval->s);
    }

    /**
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
     * @return DateInterval
     */
    public function margeDuRetard(): DateInterval
    {
        return new DateInterval('PT' . $this->margeDuRetard . 'M');
    }
}
