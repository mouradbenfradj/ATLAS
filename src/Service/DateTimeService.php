<?php
namespace App\Service;

use DateInterval;
use DateTime;

class DateTimeService implements DateInterface, TimeInterface
{
    private $jourFerierService;
    public function __construct(JourFerierService $jourFerierService)
    {
        $this->jourFerierService=$jourFerierService;
    }
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
     * timeStringToDateTime
     *
     * @param string $timeString
     * @return DateTime|null
     */
    public function timeStringToDateTime(string $timeString): ?DateTime
    {
        $time = null;
        if (DateTime::createFromFormat('H:i:s', $timeString)!== false) {
            $time = DateTime::createFromFormat('H:i:s', $timeString);
        } elseif (DateTime::createFromFormat('H:i', $timeString)!== false) {
            $time = DateTime::createFromFormat('H:i', $timeString);
        }
        return $time;
    }

    /**
     * generateTime
     *
     * @param string $timeString
     * @return DateTime
     */
    public function generateTime(string $timeString): DateTime
    {
        if ($timeString != "" and (DateTime::createFromFormat('H:i:s', $timeString) !== false or DateTime::createFromFormat('H:i', $timeString) !== false)) {
            return new DateTime($timeString);
        } else {
            return new DateTime("00:00:00");
        }
    }
    /**
     * getJourFeriers
     *
     * @return string[]
     */
    public function getJourFeriers(): array
    {
        $jourFeriers = $this->jourFerierService->getAllJourFeriers();
        $ignoreDay = [];
        foreach ($jourFeriers as $jf) {
            do {
                array_push($ignoreDay, $jf->getDebut()->format("Y-m-d"));
                $jf->getDebut()->add(new DateInterval('P1D'));
            } while ($jf->getDebut() <= $jf->getFin());
        }
        return $ignoreDay;
    }
}
