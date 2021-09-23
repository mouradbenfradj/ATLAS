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
    public function getHoraireForDate(DateTime $dateTime)
    {

        $resultat = current($this->horaires);
        $trouve = false;
        while ($horaire = next($this->horaires) and !$trouve) {
            if ($dateTime >= $horaire->getDateDebut() and $dateTime <= $horaire->getDateFin()) {
                $resultat = $horaire;
                dump($resultat);
                $trouve = true;
                dd($trouve);
            }
        }
        $this->horaire = $resultat;
        return $this->horaire;
    }



    public function diffPauseMatinalTime()
    {
        return $this->timeService->diffTime($this->horaire->getFinPauseMatinal(), $this->horaire->getDebutPauseMatinal());
    }
    public function diffPauseDejeunerTime()
    {
        return  $this->timeService->diffTime($this->horaire->getFinPauseDejeuner(), $this->horaire->getDebutPauseDejeuner());
    }
    public function diffPauseMidiTime()
    {
        return  $this->timeService->diffTime($this->horaire->getFinPauseMidi(), $this->horaire->getDebutPauseMidi());
    }
    public function sumPause()
    {
        $e = new DateTime('00:00:00');
        $e->add($this->diffPauseMatinalTime());
        $e->add($this->diffPauseDejeunerTime());
        $e->add($this->diffPauseMidiTime());
        return new DateInterval('PT' . $e->format('H') . 'H' . $e->format('i') . 'M' . $e->format('s') . 'S');
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
