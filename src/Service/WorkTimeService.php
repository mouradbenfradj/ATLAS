<?php

namespace App\Service;

class WorkTimeService
{
    public function __construct()
    {
    }
    public function getHoraireForDate(DateTime $dateTime, User $employer): Horaire
    {
        $resultat = reset($this->horaires);
        $resultat = current($this->horaires);
        $trouve = false;
        while ($horaire = next($this->horaires) and !$trouve) {
            if (!$horaire->getDateFin()) {
                $horaire->setDateFin(new DateTime());
            }
            if ($dateTime >= $horaire->getDateDebut() and $dateTime <= $horaire->getDateFin()) {
                $resultat = $horaire;
                $trouve = true;
            }
        }
        $this->horaire = $resultat;
        $trouve = false;
        $this->workTime = $this->workTimeService->$workTime  = reset($this->workTime);
        $workTime = current($this->workTime);
        if ($workTime) {
            if (!$workTime->getDateFin()) {
                $workTime->setDateFin(new DateTime());
            }
            do {
                if (
                    $dateTime >= $workTime->getDateDebut()
                    and $dateTime <= $workTime->getDateFin()
                    and $employer == $workTime->getEmployer()
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
}
