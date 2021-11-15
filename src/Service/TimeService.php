<?php

namespace App\Service;

use DateTime;
use DateInterval;

class TimeService
{
    /**
     * timeStringToDateTime
     *
     * @param string $timeString
     * @return DateTime|null
     */
    public function timeStringToDateTime(string $timeString): ?DateTime
    {
        $time = null;
        if (DateTime::createFromFormat('H:i', $timeString))
            $time = DateTime::createFromFormat('H:i', $timeString);
        elseif (DateTime::createFromFormat('H:i:', $timeString))
            $time = DateTime::createFromFormat('H:i:', $timeString);
        return $time;
    }













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
    public function generateTime(string $timeString): DateTime
    {
        if ($timeString != "" and (DateTime::createFromFormat('H:i', $timeString) !== false or DateTime::createFromFormat('H:i:s', $timeString) !== false))
            return new DateTime($timeString);
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
    public function diffTime(DateTime $timeMax, DateTime $timeMin): DateInterval
    {
        $timeMax = new DateTime(date('H:i:s', strtotime($timeMax->format("H:i:s"))));
        $timeMin = new DateTime(date('H:i:s', strtotime($timeMin->format("H:i:s"))));

        $diff =  date_diff($timeMax, $timeMin);
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


    public function isTimeHi($timeString)
    {
        return DateTime::createFromFormat('H:i', $timeString) !== false;
    }
    public function timeString_HiToDateTime($timeString): DateTime
    {
        return DateTime::createFromFormat('H:i', $timeString);
    }

    public function isTimeHis($timeString)
    {
        return DateTime::createFromFormat('H:i:s', $timeString) !== false;
    }
}
