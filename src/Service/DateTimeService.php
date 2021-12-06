<?php

namespace App\Service;

use DateTime;

abstract class DateTimeService extends JourFerierService implements DateInterface, TimeInterface
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
}
