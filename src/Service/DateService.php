<?php

namespace App\Service;

use DateTime;

class DateService
{
    public function __construct()
    {
    }

    /**
     * @param string $dateDbf
     * 
     * @return DateTime
     */
    public function dateDbfToDateTime(string $dateDbf)
    {
        return DateTime::createFromFormat('d/m/Y', $dateDbf);
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
}
