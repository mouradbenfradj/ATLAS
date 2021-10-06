<?php

namespace App\Service;

use DateTime;
use DateInterval;
use App\Entity\Horaire;
use App\Service\TimeService;
use Doctrine\ORM\EntityManagerInterface;

class HoraireService
{
    /**
     * em
     *
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * horaires
     * 
     * @var Horaire[]
     */
    private $horaires;

    /**
     * horaire
     *
     * @var Horaire
     */
    private $horaire;

    /**
     * timeService
     *
     * @var TimeService
     */
    private $timeService;

    /**
     * __construct
     *
     * @param EntityManagerInterface $em
     * @param TimeService $timeService
     */
    public function __construct(EntityManagerInterface $em, TimeService $timeService)
    {
        $this->em = $em;
        $this->timeService = $timeService;
        $this->horaires = $this->em->getRepository(Horaire::class)->findAll();
    }

    /**
     * @param DateTime $dateTime
     * 
     * @return Horaire
     */
    public function getHoraireForDate(DateTime $dateTime): Horaire
    {
        $this->horaires = $this->em->getRepository(Horaire::class)->findAll();
        $resultat = current($this->horaires);
        $trouve = false;
        while ($horaire = next($this->horaires) and !$trouve) {

            if ($dateTime >= $horaire->getDateDebut() and $dateTime <= $horaire->getDateFin()) {
                $resultat = $horaire;
                $trouve = true;
            }
        }
        $this->horaire = $resultat;
        return $this->horaire;
    }


    /**
     *diffPauseMatinalTime
     *
     * @return DateInterval
     */
    public function diffPauseMatinalTime(): DateInterval
    {
        return $this->timeService->diffTime($this->horaire->getFinPauseMatinal(), $this->horaire->getDebutPauseMatinal());
    }

    /**
     * diffPauseDejeunerTime
     *
     * @return DateInterval
     */
    public function diffPauseDejeunerTime(): DateInterval
    {
        return  $this->timeService->diffTime($this->horaire->getFinPauseDejeuner(), $this->horaire->getDebutPauseDejeuner());
    }
    /**
     * diffPauseMidiTime
     *
     * @return DateInterval
     */
    public function diffPauseMidiTime(): DateInterval
    {
        return  $this->timeService->diffTime($this->horaire->getFinPauseMidi(), $this->horaire->getDebutPauseMidi());
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
