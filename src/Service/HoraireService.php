<?php

namespace App\Service;

use DateTime;
use DateInterval;
use App\Entity\Horaire;
use App\Entity\WorkTime;
use App\Service\TimeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

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
     * workTime
     *
     * @var WorkTime[]
     */
    private $workTime;

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
     * security
     *
     * @var Security
     */
    private $security;

    /**
     * HeursJournerDeTravaille
     *
     * @var DateTime
     */
    private $HeursJournerDeTravaille;
    /**
     * HeursDemiJournerDeTravaille
     *
     * @var DateTime
     */
    private $HeursDemiJournerDeTravaille;
    /**
     * HeursQuardJournerDeTravaille
     *
     * @var DateTime
     */
    private $HeursQuardJournerDeTravaille;

    /**
     * __construct
     *
     * @param EntityManagerInterface $em
     * @param TimeService $timeService
     */
    public function __construct(EntityManagerInterface $em, TimeService $timeService, Security $security)
    {
        $this->em = $em;
        $this->timeService = $timeService;
        $this->horaires = $this->em->getRepository(Horaire::class)->findAll();
        $this->workTime = $this->em->getRepository(WorkTime::class)->findAll();
        $this->security = $security;
    }

    /**
     * @param DateTime $dateTime
     * @return Horaire
     */
    public function getHoraireForDate(DateTime $dateTime): Horaire
    {
        $resultat = reset($this->horaires);
        $resultat = current($this->horaires);
        $trouve = false;
        while ($horaire = next($this->horaires) and !$trouve) {
            if (!$horaire->getDateFin())
                $horaire->setDateFin(new DateTime());
            if ($dateTime >= $horaire->getDateDebut() and $dateTime <= $horaire->getDateFin()) {
                $resultat = $horaire;
                $trouve = true;
            }
        }
        $this->horaire = $resultat;
        $trouve = false;
        $workTime  = reset($this->workTime);
        $workTime = current($this->workTime);
        if ($workTime) {
            if (!$workTime->getDateFin())
                $workTime->setDateFin(new DateTime());
            do {
                if (
                    $dateTime >= $workTime->getDateDebut()
                    and $dateTime <= $workTime->getDateFin()
                    and $this->security->getUser() == $workTime->getEmployer()
                    and $this->horaire == $workTime->getHoraire()
                ) {
                    if ($workTime->getHeurDebutTravaille()) {
                        $this->horaire->setHeurDebutTravaille($workTime->getHeurDebutTravaille());
                    }
                    if ($workTime->getHeurFinTravaille()) {
                        $this->horaire->setHeurFinTravaille($workTime->getHeurFinTravaille());
                    }
                    if ($workTime->getDebutPauseMatinal()) {
                        $this->horaire->setDebutPauseMatinal($workTime->getDebutPauseMatinal());
                    }
                    if ($workTime->getDebutPauseMidi()) {
                        $this->horaire->setDebutPauseMidi($workTime->getDebutPauseMidi());
                    }
                    if ($workTime->getDebutPauseDejeuner()) {
                        $this->horaire->setDebutPauseDejeuner($workTime->getDebutPauseDejeuner());
                    }
                    if ($workTime->getFinPauseMatinal()) {
                        $this->horaire->setFinPauseMatinal($workTime->getFinPauseMatinal());
                    }
                    if ($workTime->getFinPauseMidi()) {
                        $this->horaire->setFinPauseMidi($workTime->getFinPauseMidi());
                    }
                    if ($workTime->getFinPauseDejeuner()) {
                        $this->horaire->setFinPauseDejeuner($workTime->getFinPauseDejeuner());
                    }
                    $trouve = true;
                }
            } while ($workTime = next($this->workTime) and !$trouve);
        }
        if ($this->horaire) {
            $this->HeursJournerDeTravaille = new DateTime($this->horaire->getHeurFinTravaille()->format("H:i:s"));
            $heurDebutTravaille = $this->horaire->getHeurDebutTravaille();
            $e = $this->sumPause();
            $this->HeursJournerDeTravaille->sub($this->timeService->dateTimeToDateInterval($e));
            $this->HeursJournerDeTravaille = $this->timeService->diffTime($this->HeursJournerDeTravaille, $heurDebutTravaille);
            $this->HeursJournerDeTravaille = $this->timeService->dateIntervalToDateTime($this->HeursJournerDeTravaille);
            $this->HeursDemiJournerDeTravaille = new DateTime(intdiv($this->HeursJournerDeTravaille->format('H'), 2) . ':' . intdiv($this->HeursJournerDeTravaille->format('i'), 2) . ':' . intdiv($this->HeursJournerDeTravaille->format('s'), 2));
            $this->HeursQuardJournerDeTravaille = new DateTime(intdiv($this->HeursJournerDeTravaille->format('H'), 4) . ':' . intdiv($this->HeursJournerDeTravaille->format('i'), 4) . ':' . intdiv($this->HeursJournerDeTravaille->format('s'), 4));
        }
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

    public function sumPause()
    {
        $e = new DateTime('00:00:00');
        $e->add($this->diffPauseMatinalTime());
        $e->add($this->diffPauseDejeunerTime());
        $e->add($this->diffPauseMidiTime());
        return $e;
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

    /**
     * Get heursJournerDeTravaille
     *
     * @return  DateTime
     */
    public function getHeursJournerDeTravaille()
    {
        return $this->HeursJournerDeTravaille;
    }

    /**
     * Get heursDemiJournerDeTravaille
     *
     * @return  DateTime
     */
    public function getHeursDemiJournerDeTravaille()
    {
        return $this->HeursDemiJournerDeTravaille;
    }

    /**
     * Get heursQuardJournerDeTravaille
     *
     * @return  DateTime
     */
    public function getHeursQuardJournerDeTravaille()
    {
        return $this->HeursQuardJournerDeTravaille;
    }
}
