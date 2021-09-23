<?php

namespace App\Service;

use DateTime;
use App\Entity\Pointage;
use App\Service\TimeService;
use App\Service\HoraireService;

class PointageService
{
    /**
     * horaireService
     *
     * @var HoraireService
     */
    private $horaireService;

    /**
     * timeService
     *
     * @var TimeService
     */
    private $timeService;
    /**
     * pointage
     *
     * @var Pointage
     */
    private $pointage;

    /**
     * __construct
     *
     * @param HoraireService $horaireService
     */
    public function __construct(HoraireService $horaireService, TimeService $timeService)
    {
        $this->horaireService = $horaireService;
        $this->timeService = $timeService;
    }

    public function nbrHeurTravailler()
    {
        $time = new DateTime($this->pointage->getSortie()->format("H:i:s"));
        $time->sub($this->horaireService->sumPause());
        $time = $this->timeService->diffTime($time, $this->pointage->getEntrer());
        return $this->timeService->DateIntervalToDateTime($time);
    }

    public function retardEnMinute()
    {
        $time = new DateTime($this->horaireService->getHoraire()->getHeurDebutTravaille()->format("H:i:s"));
        $time->add($this->timeService->margeDuRetard());
        $time = $this->timeService->diffTime($time, $this->pointage->getEntrer());
        return $this->timeService->DateIntervalToDateTime($time);
    }

    /**
     * Get pointage
     *
     * @return  Pointage
     */
    public function getPointage()
    {
        return $this->pointage;
    }

    /**
     * Set pointage
     *
     * @param  Pointage  $pointage  pointage
     *
     * @return  self
     */
    public function setPointage(Pointage $pointage)
    {
        $this->pointage = $pointage;

        return $this;
    }
}
