<?php

namespace App\Service;

use DateTime;

class DateService
{
    public function __construct()
    {
    }

    /**
     * @param string $dateString_d_m_Y_ToDateTime
     * @return DateTime
     */
    public function dateString_d_m_Y_ToDateTime(string $dateString): DateTime
    {
        return DateTime::createFromFormat('d/m/Y', $dateString);
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
     * @param string $dateString
     * 
     * @return string
     */
    public function dateToStringY_m_d(string $dateString)
    {
        $date = DateTime::createFromFormat('d/m/Y', $dateString);
        return $date->format("Y-m-d");
    }
    public function isDate($dateString)
    {
        return DateTime::createFromFormat('d/m/Y', $dateString) !== false;
    }
}
