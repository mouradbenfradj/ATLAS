<?php

namespace App\Service;

use App\Entity\AutorisationSortie;
use App\Entity\Conger;
use DateTime;
use App\Entity\Pointage;
use App\Entity\User;
use App\Service\TimeService;
use App\Service\HoraireService;
use DateTimeInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

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
     * dateService
     *
     * @var DateService
     */
    private $dateService;
    /**
     * pointage
     *
     * @var Pointage
     */
    private $pointage;

    private $initBilan;
    private $flash;

    /**
     *      
     * @var int
     */
    private $nextYear;
    /**
     * __construct
     *
     * @param HoraireService $horaireService
     */
    public function __construct(
        FlashBagInterface $flash,
        HoraireService $horaireService,
        DateService $dateService,
        TimeService $timeService
    ) {
        $this->horaireService = $horaireService;
        $this->timeService = $timeService;
        $this->dateService = $dateService;
        $this->flash = $flash;

        $this->initBilan = [
            "interval" => 1,
            "nbrHeurTravailler" => 0,
            "retardEnMinute" => 0,
            "departAnticiper" => 0,
            "retardMidi" => 0,
            "totaleRetard" => 0,
            "autorisationSortie" => 0,
            "congerPayer" =>  0,
            "abscence" =>  0,
            "heurNormalementTravailler" => 0,
            "diff" => 0,
        ];
    }
    public function bilan(DateTimeInterface $time, int $total)
    {
        $total += $time->format('H') * 3600; // Convert the hours to seconds and add to our total
        $total += $time->format('i') * 60;  // Convert the minutes to seconds and add to our total
        $total += $time->format('s'); // Add the seconds to our total
        return $total;
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
        if ($time >= $this->pointage->getEntrer())
            return null;
        $time = $this->timeService->diffTime($time, $this->pointage->getEntrer());
        return $this->timeService->dateIntervalToDateTime($time);
    }

    public function nextIsWeek()
    {
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

    public function sumBilan(array $sumpBilan)
    {
        dump($sumpBilan["interval"]);
        dump($sumpBilan["nbrHeurTravailler"]);
        dump($sumpBilan["retardEnMinute"]);
        dump($sumpBilan["departAnticiper"]);
        dump($sumpBilan["retardMidi"]);
        dump($sumpBilan["totaleRetard"]);
        dump($sumpBilan["autorisationSortie"]);
        dump($sumpBilan["congerPayer"]);
        dump($sumpBilan["abscence"]);
        dump($sumpBilan["heurNormalementTravailler"]);
        dump($sumpBilan["diff"]);

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
        if ($this->pointage->getAutorisationSortie())
            $time->sub(
                $this->timeService->dateTimeToDateInterval(
                    $this->pointage->getAutorisationSortie()->getTime()
                )
            );
        $time->sub($this->horaireService->sumPause());
        $time = $this->timeService->diffTime($time, $this->horaireService->getHoraire()->getHeurDebutTravaille());
        return $this->timeService->dateIntervalToDateTime($time);
    }

    public function diff(): DateTime
    {
        return $this->timeService->dateIntervalToDateTime(
            $this->timeService->diffTime(
                $this->pointage->getNbrHeurTravailler(),
                $this->pointage->getHeurNormalementTravailler()
            )
        );
    }


    /**
     * Get the value of initBilan
     */
    public function getInitBilan()
    {
        return $this->initBilan;
    }

    /**
     * Set the value of initBilan
     *
     * @return  self
     */
    public function setInitBilan($initBilan)
    {
        $this->initBilan = $initBilan;

        return $this;
    }

    /**
     * Get the value of nextYear
     *
     * @return  int
     */
    public function getNextYear()
    {
        $this->nextYear = new DateTime($this->pointage->getDate()->format("Y-m-d"));
        $this->nextYear->modify('+1 year');
        return $this->nextYear;
    }

    /**
     * Set the value of nextYear
     *
     * @param  int  $nextYear
     *
     * @return  self
     */
    public function setNextYear(int $nextYear)
    {

        $this->nextYear = $nextYear + 1;
        return $this;
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

    public function addLigne(array $ligne, User $user)
    {
        $this->pointage = new Pointage();
        foreach ($ligne as $char => $colomn) {
            switch ($char) {
                case 'A':
                    $this->pointage->setDate($this->dateService->dateString_d_m_Y_ToDateTime($colomn));
                    break;
                case 'B':
                    $this->pointage->setHoraire($this->horaireService->getHoraireForDate($this->pointage->getDate()));
                    break;
                case 'C':
                    if ($this->timeService->isTimeHi($colomn))
                        $this->pointage->setEntrer($this->timeService->generateTime($colomn));
                    else {
                        if (!in_array($ligne['K'], ['1']) and $colomn != 'CP' and !$colomn)
                            $this->flash->add('warning', 'not set entrer ' . $colomn . ' of ligne ' . implode(" | ", $ligne));
                    }
                    break;
                case 'D':
                    if ($this->timeService->isTimeHi($colomn))
                        $this->pointage->setSortie($this->timeService->generateTime($colomn));
                    else {
                        if (!in_array($ligne['K'], ['1']) and $ligne['C'] != 'CP')
                            $this->flash->add('warning', 'not set sortie ' . $colomn . ' of ligne ' . implode(" | ", $ligne));
                    }
                    break;
                case 'E':
                    if ($this->pointage->getSortie() and $this->pointage->getEntrer())
                        $this->pointage->setNbrHeurTravailler($this->nbrHeurTravailler());
                    else {
                        $this->pointage->setNbrHeurTravailler(new DateTime('00:00:00'));
                        if (!in_array($ligne['K'], ['1']) and $ligne['C'] != 'CP')
                            $this->flash->add('warning', 'set to 0 nbrHeurTravailler, entrer ou sortie non saisie ' . $colomn . ' of ligne ' . implode(" | ", $ligne));
                    }
                    break;
                case 'F':
                    if ($this->pointage->getSortie() and $this->pointage->getEntrer())
                        $this->pointage->setRetardEnMinute($this->retardEnMinute());
                    break;
                case 'G':
                    if ($this->timeService->isTimeHi($colomn))
                        $this->pointage->setDepartAnticiper(new DateTime($colomn));
                    else {
                        if ($colomn)
                            $this->flash->add('warning', 'ignored departAnticiper' . $colomn . ' of ligne ' . implode(" | ", $ligne));
                    }
                    break;
                case 'H':
                    if ($this->timeService->isTimeHi($colomn))
                        $this->pointage->setRetardMidi($this->timeService->generateTime($colomn));
                    else {
                        if ($colomn)
                            $this->flash->add('warning', 'ignored retardMidi' . $colomn . ' of ligne ' . implode(" | ", $ligne));
                    }
                    break;
                case 'I':
                    $this->pointage->setTotaleRetard($this->totalRetard());
                case 'J':
                    if ($this->timeService->isTimeHi($colomn)) {
                        $autrisationSotie = new AutorisationSortie();
                        $autrisationSotie->setDateAutorisation($this->pointage->getDate());
                        $autrisationSotie->setTime(new DateTime($colomn));
                        $autrisationSotie->setEmployer($user);
                        $this->pointage->setAutorisationSortie($autrisationSotie);
                    } else {
                        if ($colomn)
                            $this->flash->add('warning', 'ignored autorisationSortie' . $colomn . ' of ligne ' . implode(" | ", $ligne));
                    }
                    break;
                case 'K':
                    switch ($colomn) {
                        case '0.5':
                            $conger = new Conger();
                            $conger->setEmployer($user);
                            $conger->setDebut($this->pointage->getDate());
                            $conger->setFin($this->pointage->getDate());
                            $conger->setDemiJourner(true);
                            $this->pointage->setCongerPayer($conger);
                            break;
                        case '1':
                            $conger = new Conger();
                            $conger->setEmployer($user);
                            $conger->setDebut($this->pointage->getDate());
                            $conger->setFin($this->pointage->getDate());
                            $conger->setDemiJourner(false);
                            $this->pointage->setCongerPayer($conger);
                            break;
                        default:
                            if ($ligne['C'] == 'CP' and !$colomn) {
                                $conger = new Conger();
                                $conger->setEmployer($user);
                                $conger->setDebut($this->pointage->getDate());
                                $conger->setFin($this->pointage->getDate());
                                $conger->setDemiJourner(false);
                                $this->pointage->setCongerPayer($conger);
                                $this->flash->add('warning', 'cp added automatically of ligne ' . implode(" | ", $ligne));
                            }
                            if ($colomn)
                                $this->flash->add('warning', 'ignored congerPayer' . $colomn . ' of ligne ' . implode(" | ", $ligne));
                            break;
                    }
                    break;
                case 'L':
                    if ($colomn)
                        $this->pointage->setAbscence($colomn);
                    break;
                case 'M':
                    $this->pointage->setHeurNormalementTravailler($this->heurNormalementTravailler());
                    break;
                case 'N':
                    $this->pointage->setDiff($this->diff());
                    break;
                default:
                    //dump($ligne[$char]);
                    break;
            }
        }
        $user->addPointage($this->pointage);
        return $user;
    }
}
