<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\WorkTime;
use DateTime;

class WorkTimeService
{
    public function __construct()
    {
    }
    public function getWorkTimeForDate(DateTime $dateTime, User $employer): ?WorkTime
    {
        return null;
        $this->workTime = $employer->getWorkTimes()->toArray();
        $workTime  = reset($this->workTime);
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
        return  $workTime;
    }
}
