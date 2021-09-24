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

    private $sumWeek;
    /**
     * __construct
     *
     * @param HoraireService $horaireService
     */
    public function __construct(HoraireService $horaireService, TimeService $timeService)
    {
        $this->horaireService = $horaireService;
        $this->timeService = $timeService;
        $this->sumWeek = [
            "semaine" => 1,
            "nbrHeurTravailler" => new DateTime("00:00:00"),
            "retardEnMinute" => new DateTime("00:00:00"),
            "departAnticiper" => new DateTime("00:00:00"),
            "retardMidi" => new DateTime("00:00:00"),
            "totaleRetard" => new DateTime("00:00:00"),
            "autorisationSortie" =>  new DateTime("00:00:00"),
            "congerPayer" =>  0,
            "abscence" =>  0,
            "heurNormalementTravailler" =>  new DateTime("00:00:00"),
            "diff" => new DateTime("00:00:00"),
        ];
    }

    public function nbrHeurTravailler()
    {
        $time = new DateTime($this->pointage->getSortie()->format("H:i:s"));
        $time->sub($this->horaireService->sumPause());
        $time = $this->timeService->diffTime($time, $this->pointage->getEntrer());
        return $this->timeService->dateIntervalToDateTime($time);
    }

    public function retardEnMinute()
    {
        $time = new DateTime($this->horaireService->getHoraire()->getHeurDebutTravaille()->format("H:i:s"));
        $time->add($this->timeService->margeDuRetard());
        $time = $this->timeService->diffTime($time, $this->pointage->getEntrer());
        return $this->timeService->dateIntervalToDateTime($time);
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

    public function nextIsWeek()
    {
        dump(new DateTime("first day of this week"));
        dump(new DateTime("next day of this week"));
        dd(new DateTime("next weekday"));

        dump(new DateTime("weekdays"));
        dump(new DateTime("weekdays"));
        dump(new DateTime("weekday"));
        dump(new DateTime('sunday'));
        dump(new DateTime('monday'));
        dump(new DateTime('tuesday'));
        dump(new DateTime('wednesday'));
        dump(new DateTime("weekdays sunday"));
        dump(new DateTime("weekday sunday"));
        dump(new DateTime("weekdays monday"));
        dump(new DateTime("weekday monday"));
        dump(new DateTime("weekdays tuesday"));
        dump(new DateTime("weekday tuesday"));
        dump(new DateTime("weekdays wednesday"));




        dd(new DateTime("weekday wednesday"));
        dd($this->pointage->getdate());
        $nextWeekDate = new DateTime('1st January Next Week');
        dd($nextWeekDate->format('Y-m-d'));
    }
    public function nextIsYear()
    {
        $nextYearDate = new DateTime('1st January Next Year');
        dd($nextYearDate->format('Y-m-d'));
        $checkDate = new DateTime($this->pointage->getDate()->format("Y-m-d"));
    }
    public function nextIsMonth()
    {

        $nextWeekDate = new DateTime('1st January Next Month');
        dd($nextWeekDate->format('Y-m-d'));
        $this->pointage->getDate()->format('N') >= 6;
    }
    public function sumWeek(array $sumpWeek)
    {
        dump($sumpWeek["semaine"]);
        dump($sumpWeek["nbrHeurTravailler"]);
        dump($sumpWeek["retardEnMinute"]);
        dump($sumpWeek["departAnticiper"]);
        dump($sumpWeek["retardMidi"]);
        dump($sumpWeek["totaleRetard"]);
        dump($sumpWeek["autorisationSortie"]);
        dump($sumpWeek["congerPayer"]);
        dump($sumpWeek["abscence"]);
        dump($sumpWeek["heurNormalementTravailler"]);
        dump($sumpWeek["diff"]);

        dd($sumpWeek);
        $sumpWeek = [];
        return $sumpWeek;
    }

    /**
     * totalRetard
     *
     * @return DateTime
     */
    public function totalRetard(): DateTime
    {
        $e = new DateTime('00:00:00');
        if ($this->pointage->getRetardEnMinute())
            $e->add($this->timeService->dateTimeToDateInterval($this->pointage->getRetardEnMinute()));
        if ($this->pointage->getDepartAnticiper())
            $e->add($this->timeService->dateTimeToDateInterval($this->pointage->getDepartAnticiper()));
        if ($this->pointage->getRetardMidi())
            $e->add($this->timeService->dateTimeToDateInterval($this->pointage->getRetardMidi()));
        return $e;
    }

    /**
     * heurNormalementTravailler
     *
     * @return DateTime
     */
    public function heurNormalementTravailler(): DateTime
    {
        $time = new DateTime($this->horaireService->getHoraire()->getHeurFinTravaille()->format("H:i:s"));
        $time->sub($this->horaireService->sumPause());
        if ($this->pointage->getAutorisationSortie())
            $time->sub($this->timeService->dateTimeToDateInterval($this->pointage->getAutorisationSortie()));
        $time = $this->timeService->diffTime($time, $this->horaireService->getHoraire()->getHeurDebutTravaille());
        return $this->timeService->dateIntervalToDateTime($time);
    }
    public function diff(): DateTime
    {
        return $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($this->pointage->getNbrHeurTravailler(), $this->pointage->getHeurNormalementTravailler()));
    }

    /**
     * Get the value of sumWeek
     */
    public function getSumWeek()
    {
        return $this->sumWeek;
    }

    /**
     * Set the value of sumWeek
     *
     * @return  self
     */
    public function setSumWeek($sumWeek)
    {
        $this->sumWeek = $sumWeek;

        return $this;
    }
}
