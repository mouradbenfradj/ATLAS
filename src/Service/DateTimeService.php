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
     * Horaire
     *
     * @var Horaire
     */
    private $lastHoraire;
    /**
     * Horaire
     *
     * @var Horaire
     */
    private $horaire;


    private $horaireName = null;
    /**
     * JourFerierService
     *
     * @var JourFerierService
     */
    private $jourFerierService;
    /**
     * HoraireService
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
    public function __construct(JourFerierService $jourFerierService)
    {
        $this->jourFerierService = $jourFerierService;
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



    public function timeStringToDateTime(?string $timeString): ?DateTime
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
     * GenerateTime
     *
     * @param string $timeString date generer du dbf 
     * 
     * @return DateTime
     */
    public function generateTime(?string $timeString): DateTime
    {
        if ($timeString != "" && (DateTime::createFromFormat(self::FORMATTIMEHIS, $timeString) !== false || DateTime::createFromFormat(self::FORMATTIMEHI, $timeString) !== false)) {
            return new DateTime($timeString);
        } else {
            return new DateTime("00:00:00");
        }
    }
    public function diffTime(DateTime $timeMax, DateTime $timeMin): DateTime
    {
        $timeMax = new DateTime(date('H:i:s', strtotime($timeMax->format("H:i:s"))));
        $timeMin = new DateTime(date('H:i:s', strtotime($timeMin->format("H:i:s"))));

        $diff =  date_diff($timeMax, $timeMin);
        return new DateTime($diff->h . ':' . $diff->i . ':' . $diff->s);
    }
    /**
     * GetJourFeriers
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


    public function getHoraireByDateOrName(DateTime $date, string $horaireName): Horaire
    {
        $this->horaire = $this->horaireService->getHoraireForDate($date);
        if (!$this->horaire) {
            $this->horaire =  $this->horaireService->getHoraireByHoraireName($horaireName);
            if (!$this->horaire) {
                dd($this->horaire);
            } else {
                $this->horaire = clone $this->horaireService->getHoraireByHoraireName($horaireName);
                $this->horaire->setIdANull();
                $this->horaire->setDateDebut($date);
                $this->horaire->setDateFin(null);
                $this->horaireService->addHoraireForDate($this->horaire);
            }
        }
        if ($this->lastHoraire && $this->lastHoraire->getHoraire() != $horaireName) {
            $fin = clone $date;
            $this->lastHoraire->setDateFin(date_modify($fin, '-1 day'));
            dd($horaireName, $this->horaireService->addHoraireForDate($this->horaire), $this->lastHoraire, $this->horaire);
        }
        $this->lastHoraire = $this->horaire;


        return  $this->horaire;
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
