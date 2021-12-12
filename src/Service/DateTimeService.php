<?php

namespace App\Service;

use App\Entity\Horaire;
use DateInterval;
use DateTime;

class DateTimeService implements DateInterface, TimeInterface
{
    const FORMATTIMEHI = 'H:i';
    const FORMATTIMEHIS = self::FORMATTIMEHI . ':s';


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
        if (DateTime::createFromFormat(self::FORMATTIMEHIS, $timeString) !== false) {
            $time = DateTime::createFromFormat(self::FORMATTIMEHIS, $timeString);
        } elseif (DateTime::createFromFormat(self::FORMATTIMEHI, $timeString) !== false) {
            $time = DateTime::createFromFormat(self::FORMATTIMEHI, $timeString);
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
        if ($timeString != "" && (DateTime::createFromFormat(self::FORMATTIMEHIS, $timeString) !== false || DateTime::createFromFormat(self::FORMATTIMEHI, $timeString) !== false)) {
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
                /**
                 * @var DateTime
                 */
                $debut = $jf->getDebut();
                $debut->add(new DateInterval('P1D'));
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
