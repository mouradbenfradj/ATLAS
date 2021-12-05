<?php

namespace App\Service;

use DateTime;
use DateInterval;
use App\Entity\Horaire;
use App\Entity\User;
use App\Entity\WorkTime;
use App\Service\TimeService;
use Doctrine\ORM\EntityManagerInterface;

class HoraireService
{
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
     * workTimeService
     *
     * @var WorkTimeService
     */
    private $workTimeService;

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
     * @param EntityManagerInterface $manager
     * @param TimeService $timeService
     */
    public function __construct(EntityManagerInterface $manager, TimeService $timeService, WorkTimeService $workTimeService)
    {
        $this->horaires = $manager->getRepository(Horaire::class)->findAll();
        $this->timeService = $timeService;
        $this->workTimeService = $workTimeService;
    }

    /**
     * getHoraireForDate function
     *
     * @param DateTime $dateTime
     * @param User $employer
     * @return Horaire|null
     */
    public function getHoraireForDate(DateTime $dateTime, User $employer, string $horaireName = ""): ?Horaire
    {
        reset($this->horaires);

        do {
            $horair = current($this->horaires);

            if (!$horair->getDateFin()) {
                $horair->setDateFin(new DateTime());
            }
            if ($horair->getDateDebut() <= $dateTime and $dateTime <= $horair->getDateFin()) {
                $this->horaire = current($this->horaires);
            }
        } while ($horair = next($this->horaires) and !$this->horaire);
        if (!$this->horaire and $horaireName != "") {
            $otherHoraire = $this->getHoraireByHoraireName($horaireName);
            $this->horaire = new Horaire();
            $this->horaire->setDateDebut($dateTime);
            $this->horaire->setDateFin($dateTime);
            $this->horaire->setHoraire($horaireName);
            $this->horaire->setDebutPauseMatinal($otherHoraire->getDebutPauseMatinal());
            $this->horaire->setDebutPauseDejeuner($otherHoraire->getDebutPauseDejeuner());
            $this->horaire->setDebutPauseMidi($otherHoraire->getDebutPauseMidi());
            $this->horaire->setHeurDebutTravaille($otherHoraire->getHeurDebutTravaille());
            $this->horaire->setFinPauseDejeuner($otherHoraire->getFinPauseDejeuner());
            $this->horaire->setFinPauseMatinal($otherHoraire->getFinPauseMatinal());
            $this->horaire->setFinPauseMidi($otherHoraire->getFinPauseMidi());
            $this->horaire->setHeurFinTravaille($otherHoraire->getHeurFinTravaille());
            $this->horaire->setMargeDuRetard($otherHoraire->getMargeDuRetard());
            //$this->em->persist($this->horaire);
        }
        $this->workTime = $this->workTimeService->getWorkTimeForDate($dateTime, $employer);

        if ($this->horaire) {
            $this->HeursJournerDeTravaille = $this->timeService->generateTime($this->horaire->getHeurFinTravaille()->format("H:i:s"));
            $heurDebutTravaille = $this->horaire->getHeurDebutTravaille();
            $this->HeursJournerDeTravaille->sub($this->timeService->dateTimeToDateInterval($this->sumPause()));
            $this->HeursJournerDeTravaille = $this->timeService->diffTime($this->HeursJournerDeTravaille, $heurDebutTravaille);
            $this->HeursJournerDeTravaille = $this->timeService->dateIntervalToDateTime($this->HeursJournerDeTravaille);
            $h = (intdiv($this->HeursJournerDeTravaille->format('H'), 2) < 10) ? ('0' . intdiv($this->HeursJournerDeTravaille->format('H'), 2)) : intdiv($this->HeursJournerDeTravaille->format('H'), 2);
            $i = (intdiv($this->HeursJournerDeTravaille->format('i'), 2) < 10) ? ('0' . intdiv($this->HeursJournerDeTravaille->format('i'), 2)) : intdiv($this->HeursJournerDeTravaille->format('i'), 2);
            $s = (intdiv($this->HeursJournerDeTravaille->format('s'), 2) < 10) ? ('0' . intdiv($this->HeursJournerDeTravaille->format('s'), 2)) : intdiv($this->HeursJournerDeTravaille->format('s'), 2);
            $this->HeursDemiJournerDeTravaille = $this->timeService->generateTime($h . ':' . $i . ':' . $s);
            $h = (intdiv($this->HeursJournerDeTravaille->format('H'), 4) < 10) ? ('0' . intdiv($this->HeursJournerDeTravaille->format('H'), 4)) : intdiv($this->HeursJournerDeTravaille->format('H'), 4);
            $i = (intdiv($this->HeursJournerDeTravaille->format('i'), 4) < 10) ? ('0' . intdiv($this->HeursJournerDeTravaille->format('i'), 4)) : intdiv($this->HeursJournerDeTravaille->format('i'), 4);
            $s = (intdiv($this->HeursJournerDeTravaille->format('s'), 4) < 10) ? ('0' . intdiv($this->HeursJournerDeTravaille->format('s'), 4)) : intdiv($this->HeursJournerDeTravaille->format('s'), 4);
            $this->HeursQuardJournerDeTravaille = $this->timeService->generateTime($h . ':' . $i . ':' . $s);
        }
        return $this->horaire;
    }
    public function getHoraireByHoraireName(string $horaireName): ?Horaire
    {
        reset($this->horaires);
        do {
            $horair = current($this->horaires);
            if ($horair->getHoraire() == $horaireName) {
                $this->horaire = current($this->horaires);
            }
        } while ($horair = next($this->horaires) and !$this->horaire);
        //$this->workTime = $this->workTimeService->getWorkTimeForDate($dateTime, $employer);

        if ($this->horaire) {
            $this->HeursJournerDeTravaille = $this->timeService->generateTime($this->horaire->getHeurFinTravaille()->format("H:i:s"));
            $heurDebutTravaille = $this->horaire->getHeurDebutTravaille();
            $this->HeursJournerDeTravaille->sub($this->timeService->dateTimeToDateInterval($this->sumPause()));
            $this->HeursJournerDeTravaille = $this->timeService->diffTime($this->HeursJournerDeTravaille, $heurDebutTravaille);
            $this->HeursJournerDeTravaille = $this->timeService->dateIntervalToDateTime($this->HeursJournerDeTravaille);
            $this->HeursDemiJournerDeTravaille = $this->timeService->generateTime(intdiv($this->HeursJournerDeTravaille->format('H'), 2) . ':' . intdiv($this->HeursJournerDeTravaille->format('i'), 2) . ':' . intdiv($this->HeursJournerDeTravaille->format('s'), 2));
            $this->HeursQuardJournerDeTravaille = $this->timeService->generateTime(intdiv($this->HeursJournerDeTravaille->format('H'), 4) . ':' . intdiv($this->HeursJournerDeTravaille->format('i'), 4) . ':' . intdiv($this->HeursJournerDeTravaille->format('s'), 4));
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

    /**
     * diffPauseMidiTime
     *
     * @return DateInterval
     */
    public function diffPauseMidiTime(): DateInterval
    {
        return  $this->timeService->diffTime($this->horaire->getFinPauseMidi(), $this->horaire->getDebutPauseMidi());
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
     * Get heursQuardJournerDeTravaille
     *
     * @return  DateTime
     */
    public function getHeursQuardJournerDeTravaille()
    {
        return  $this->timeService->generateTime($this->HeursQuardJournerDeTravaille->format('H:i:s'));
    }

    /**
     * Set heursQuardJournerDeTravaille
     *
     * @param  DateTime  $HeursQuardJournerDeTravaille  HeursQuardJournerDeTravaille
     *
     * @return  self
     */
    public function setHeursQuardJournerDeTravaille(DateTime $HeursQuardJournerDeTravaille)
    {
        $this->HeursQuardJournerDeTravaille = $HeursQuardJournerDeTravaille;

        return $this;
    }

    /**
     * Get heursDemiJournerDeTravaille
     *
     * @return  DateTime
     */
    public function getHeursDemiJournerDeTravaille()
    {
        return $this->timeService->generateTime($this->HeursDemiJournerDeTravaille->format('H:i:s'));
    }

    /**
     * Set heursDemiJournerDeTravaille
     *
     * @param  DateTime  $HeursDemiJournerDeTravaille  HeursDemiJournerDeTravaille
     *
     * @return  self
     */
    public function setHeursDemiJournerDeTravaille(DateTime $HeursDemiJournerDeTravaille)
    {
        $this->HeursDemiJournerDeTravaille = $HeursDemiJournerDeTravaille;

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
     * Set heursJournerDeTravaille
     *
     * @param  DateTime  $HeursJournerDeTravaille  HeursJournerDeTravaille
     *
     * @return  self
     */
    public function setHeursJournerDeTravaille(DateTime $HeursJournerDeTravaille)
    {
        $this->HeursJournerDeTravaille = $HeursJournerDeTravaille;

        return $this;
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
     * Set workTime
     *
     * @param  WorkTime[]  $workTime  workTime
     *
     * @return  self
     */
    public function setWorkTime(array $workTime)
    {
        $this->workTime = $workTime;

        return $this;
    }
}
