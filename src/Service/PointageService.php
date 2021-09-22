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
        $nbrHeurTravailler = $this->timeService->DateIntervalToDateTime($this->timeService->diffTime($this->pointage->getSortie(), $this->pointage->getEntrer()));
        dd($this->horaireService->sumPause());
        $nbrHeurTravailler->sub();
        dd($nbrHeurTravailler);
        $diff = date_diff($this->horaireService->getHoraire()->getHeurFinTravaille(), $this->horaireService->getHoraire()->getHeurDebutTravaille());
        return new DateTime($this->pointage->getDate());
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
