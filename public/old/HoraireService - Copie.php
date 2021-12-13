<?php


use DateTime;
use DateInterval;
use App\Entity\Horaire;
use App\Entity\User;
use App\Entity\WorkTime;
use App\Service\TimeService;
use Doctrine\ORM\EntityManagerInterface;

class HoraireService extends DateTimeService implements HoraireInterface
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
     * getHoraireForDate function
     *
     * @param DateTime $dateTime
     * @param User $employer
     * @return Horaire|null
     */
    public function getHoraireForDate(DateTime $dateTime): ?Horaire
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

        if ($this->horaire) {
            $this->HeursJournerDeTravaille = $this->generateTime($this->horaire->getHeurFinTravaille()->format("H:i:s"));
            $heurDebutTravaille = $this->horaire->getHeurDebutTravaille();
            $this->HeursJournerDeTravaille->sub($this->dateTimeToDateInterval($this->sumPause()));
            $this->HeursJournerDeTravaille = $this->diffTime($this->HeursJournerDeTravaille, $heurDebutTravaille);
            $this->HeursJournerDeTravaille = $this->dateIntervalToDateTime($this->HeursJournerDeTravaille);
            $h = (intdiv($this->HeursJournerDeTravaille->format('H'), 2) < 10) ? ('0' . intdiv($this->HeursJournerDeTravaille->format('H'), 2)) : intdiv($this->HeursJournerDeTravaille->format('H'), 2);
            $i = (intdiv($this->HeursJournerDeTravaille->format('i'), 2) < 10) ? ('0' . intdiv($this->HeursJournerDeTravaille->format('i'), 2)) : intdiv($this->HeursJournerDeTravaille->format('i'), 2);
            $s = (intdiv($this->HeursJournerDeTravaille->format('s'), 2) < 10) ? ('0' . intdiv($this->HeursJournerDeTravaille->format('s'), 2)) : intdiv($this->HeursJournerDeTravaille->format('s'), 2);
            $this->HeursDemiJournerDeTravaille = $this->generateTime($h . ':' . $i . ':' . $s);
            $h = (intdiv($this->HeursJournerDeTravaille->format('H'), 4) < 10) ? ('0' . intdiv($this->HeursJournerDeTravaille->format('H'), 4)) : intdiv($this->HeursJournerDeTravaille->format('H'), 4);
            $i = (intdiv($this->HeursJournerDeTravaille->format('i'), 4) < 10) ? ('0' . intdiv($this->HeursJournerDeTravaille->format('i'), 4)) : intdiv($this->HeursJournerDeTravaille->format('i'), 4);
            $s = (intdiv($this->HeursJournerDeTravaille->format('s'), 4) < 10) ? ('0' . intdiv($this->HeursJournerDeTravaille->format('s'), 4)) : intdiv($this->HeursJournerDeTravaille->format('s'), 4);
            $this->HeursQuardJournerDeTravaille = $this->generateTime($h . ':' . $i . ':' . $s);
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
            $this->HeursJournerDeTravaille = $this->generateTime($this->horaire->getHeurFinTravaille()->format("H:i:s"));
            $heurDebutTravaille = $this->horaire->getHeurDebutTravaille();
            $this->HeursJournerDeTravaille->sub($this->dateTimeToDateInterval($this->sumPause()));
            $this->HeursJournerDeTravaille = $this->diffTime($this->HeursJournerDeTravaille, $heurDebutTravaille);
            $this->HeursJournerDeTravaille = $this->dateIntervalToDateTime($this->HeursJournerDeTravaille);
            $this->HeursDemiJournerDeTravaille = $this->generateTime(intdiv($this->HeursJournerDeTravaille->format('H'), 2) . ':' . intdiv($this->HeursJournerDeTravaille->format('i'), 2) . ':' . intdiv($this->HeursJournerDeTravaille->format('s'), 2));
            $this->HeursQuardJournerDeTravaille = $this->generateTime(intdiv($this->HeursJournerDeTravaille->format('H'), 4) . ':' . intdiv($this->HeursJournerDeTravaille->format('i'), 4) . ':' . intdiv($this->HeursJournerDeTravaille->format('s'), 4));
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
        return $this->diffTime($this->horaire->getFinPauseMatinal(), $this->horaire->getDebutPauseMatinal());
    }

    /**
     * diffPauseDejeunerTime
     *
     * @return DateInterval
     */
    public function diffPauseDejeunerTime(): DateInterval
    {
        return  $this->diffTime($this->horaire->getFinPauseDejeuner(), $this->horaire->getDebutPauseDejeuner());
    }

    /**
     * diffPauseMidiTime
     *
     * @return DateInterval
     */
    public function diffPauseMidiTime(): DateInterval
    {
        return  $this->diffTime($this->horaire->getFinPauseMidi(), $this->horaire->getDebutPauseMidi());
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
        return  $this->generateTime($this->HeursQuardJournerDeTravaille->format('H:i:s'));
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
        return $this->generateTime($this->HeursDemiJournerDeTravaille->format('H:i:s'));
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
