<?php

namespace App\Service;

use App\Entity\Horaire;
use DateInterval;
use DateTime;

class DateTimeService implements DateInterface, TimeInterface
{
    /**
     * horaire
     *
     * @var Horaire
     */
    private $horaire;
    /**
     * jourFerierService
     *
     * @var JourFerierService
     */
    private $jourFerierService;
    /**
     * horaireService
     *
     * @var HoraireService
     */
    private $horaireService;
    /**
     * __construct
     *
     * @param JourFerierService $jourFerierService
     * @param HoraireService $horaireService
     */
    public function __construct(JourFerierService $jourFerierService, HoraireService $horaireService)
    {
        $this->jourFerierService = $jourFerierService;
        $this->horaireService = $horaireService;
    }
    /**
     * dateString_d_m_Y_ToDateTime
     *
     * @param string $dateString
     * @return DateTime
     */
    public function dateString_d_m_Y_ToDateTime(string $dateString): DateTime
    {
        //convert strint to date
        return DateTime::createFromFormat('d/m/Y', $dateString);
    }


    /**
     * isDate
     *
     * @param string|null $dateString
     * @return boolean
     */
    public function isDate(?string $dateString): bool
    {
        return DateTime::createFromFormat('d/m/Y', $dateString) !== false;
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
        if (DateTime::createFromFormat('H:i:s', $timeString) !== false) {
            $time = DateTime::createFromFormat('H:i:s', $timeString);
        } elseif (DateTime::createFromFormat('H:i', $timeString) !== false) {
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
     * @return array
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


    public function getHoraireForDate(DateTime $date)
    {
        $this->horaire = $this->horaireService->getHoraireForDate($date);
        dd($this->horaire);
    }

    /**
     * Get horaire
     *
     * @return  Horaire
     */
    public function getHoraire()
    {
        return $this->horaire;
    }

    /**
     * Set horaire
     *
     * @param  Horaire  $horaire  horaire
     *
     * @return  self
     */
    public function setHoraire(Horaire $horaire)
    {
        $this->horaire = $horaire;

        return $this;
    }
}
