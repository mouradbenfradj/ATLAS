<?php

namespace App\Service;

use DateTime;

class DateServiceCopy
{
    /**
     * dateString_d_m_Y_ToDateTime
     *
     * @param string $dateString
     * @return DateTime
     */
    public function dateString_d_m_Y_ToDateTime(string $dateString): DateTime
    {
        return DateTime::createFromFormat('d/m/Y', $dateString);
    }
    /**
     * isWeek
     *
     * @param DateTime $date
     * @return boolean
     */
    public function isWeek(DateTime $date): bool
    {
        return in_array($date->format("w"), [0, 6]);
    }






















    /**
     * @param string $dateDbf
     * 
     * @return string
     */
    public function dateDbfToStringY_m_d(string $dateDbf)
    {
        $date = DateTime::createFromFormat('d/m/Y', $dateDbf);
        return $date->format("Y-m-d");
    }
    /**
     * dateToStringY_m_d
     * @param string $dateString
     *
     * @return string
     */
    public function dateToStringY_m_d(string $dateString): string
    {
        $date = DateTime::createFromFormat('d/m/Y', $dateString);
        return $date->format("Y-m-d");
    }
    public function dateString_d_m_Y_ToDate_Y_m_d(string $dateString): DateTime
    {
        return DateTime::createFromFormat('d/m/Y', $dateString);
    }
    public function isDate($dateString)
    {
        return DateTime::createFromFormat('d/m/Y', $dateString) !== false;
    }
}
