<?php

namespace App\Service;

use App\Entity\Pointage;
use DateTime;

class BilanService
{
    /**
     * initBilan
     *
     * @var array
     */
    private $initBilan;
    /**
     * timeService variable
     *
     * @var TimeService
     */
    private $timeService;
    /**
     * pointageService variable
     *
     * @var PointageService
     */
    private $pointageService;

    public function __construct(TimeService $timeService, PointageService $pointageService)
    {
        $this->timeService = $timeService;
        $this->pointageService = $pointageService;
        $this->initBilan = [
            "colspan" => 1,
            "background" => null,
            "date" => null,
            "horaire" => null,
            "entrer" => null,
            "sortie" => null,
            "nbrHeurTravailler" => 0,
            "retardEnMinute" => 0,
            "departAnticiper" => 0,
            "retardMidi" => 0,
            "totalRetard" => 0,
            "autorisationSortie" => 0,
            "congerPayer" =>  0,
            "absence" =>  0,
            "heurNormalementTravailler" => 0,
            "diff" => 0,
        ];
    }



    public function calculateurBilan(Pointage $pointage, array $bilan)
    {
        $bilan["nbrHeurTravailler"] = $this->bilan($pointage->getNbrHeurTravailler(), $bilan["nbrHeurTravailler"]);
        if ($pointage->getNbrHeurTravailler()) {
            $bilan["retardEnMinute"] = $this->bilan($pointage->getRetardEnMinute(), $bilan["retardEnMinute"]);
        }
        if ($pointage->getDepartAnticiper()) {
            $bilan["departAnticiper"] = $this->bilan($pointage->getDepartAnticiper(), $bilan["departAnticiper"]);
        }
        if ($pointage->getRetardMidi()) {
            $bilan["retardMidi"] = $this->bilan($pointage->getRetardMidi(), $bilan["retardMidi"]);
        }
        $bilan["totalRetard"] = $this->bilan($pointage->getTotaleRetard(), $bilan["totalRetard"]);
        if ($pointage->getAutorisationSortie()) {
            $bilan["autorisationSortie"] =
                $this->bilan(
                    $pointage->getAutorisationSortie()->getHeurAutoriser(),
                    $bilan["autorisationSortie"]
                );
        }
        if ($pointage->getCongerPayer()) {
            if ($pointage->congerPayer->getDemiJourner()) {
                $bilan["congerPayer"] += 0.5;
            } else {
                $bilan["congerPayer"] += 1;
            }
        }
        $bilan["absence"] = $pointage->getAbsence() ? $bilan["absence"] + 1 : $bilan["absence"];
        $bilan["heurNormalementTravailler"] = $this->bilan($pointage->getHeurNormalementTravailler(), $bilan["heurNormalementTravailler"]);
        $bilan["diff"] = $this->bilan($pointage->getDiff(), $bilan["diff"]);
        return $bilan;
    }


    public function bilan(?DateTime $time, int $total)
    {
        if (!$time) {
            return $total;
        }
        $total += $time->format('H') * 3600; // Convert the hours to seconds and add to our total
        $total += $time->format('i') * 60;  // Convert the minutes to seconds and add to our total
        $total += $time->format('s'); // Add the seconds to our total
        return $total;
    }
    /**
     * Undocumented function
     *
     * @param Pointage[] $pointages
     * @return array
     */
    public function getBilanSemestriel(array $pointages): array
    {
        $bilan = $this->initBilan;
        $thisWeek = 0;
        $countWeek = 1;
        $collectSemaine = [];
        foreach ($pointages as $pointage) {
            $this->pointageService->constructFromPointage($pointage);
            if ($thisWeek != $pointage->getDate()->format('W')) {
                if ($thisWeek) {
                    array_push($collectSemaine, $bilan);
                    $countWeek++;
                }
                $thisWeek = $pointage->getDate()->format('W');
                $bilan = $this->initBilan;
                $bilan["date"] = $countWeek;
            }
            $bilan = $this->calculateurBilan($pointage, $bilan);
        }
        array_push($collectSemaine, $bilan);
        return $collectSemaine;
    }
    public function getBilanMensuel($pointages)
    {
        $bilan = $this->initBilan;
        $thisYear = 0;
        $thisMonth = 0;
        $collectMensuel = [];
        foreach ($pointages as $pointage) {
            $this->pointageService->constructFromPointage($pointage);
            if ($thisYear . '-' . $thisMonth != $pointage->getDate()->format('Y-m')) {
                if ($thisYear and $thisMonth) {
                    array_push($collectMensuel, $bilan);
                }
                $thisYear =  $pointage->getDate()->format('Y');
                $thisMonth =  $pointage->getDate()->format('m');
                $bilan = $this->initBilan;
                $bilan["date"] =  $pointage->getDate()->format('Y-m');
            }
            $bilan = $this->calculateurBilan($pointage, $bilan);
        }
        array_push($collectMensuel, $bilan);
        return $collectMensuel;
    }
    public function getBilanAnnuel($pointages)
    {
        $bilan = $this->initBilan;
        $thisYear = 0;
        $collectAnnuel = [];
        foreach ($pointages as $pointage) {
            $this->pointageService->constructFromPointage($pointage);
            if ($thisYear != $pointage->getDate()->format('Y')) {
                if ($thisYear) {
                    array_push($collectAnnuel, $bilan);
                }
                $thisYear =  $pointage->getDate()->format('Y');
                $bilan = $this->initBilan;
                $bilan["date"] =  $pointage->getDate()->format('Y');
            }
            $bilan = $this->calculateurBilan($pointage, $bilan);
        }
        array_push($collectAnnuel, $bilan);
        return $collectAnnuel;
    }


    /**
     * getBilanGeneral
     *
     * @param Pointage[] $pointages
     * @return array
     */
    public function getBilanGeneral(array $pointages): array
    {
        usort($pointages, fn ($a, $b) => $a->getDate() > $b->getDate());
        $collectGeneral = [];
        $bilanWeek = $this->initBilan;
        $bilanMonth = $this->initBilan;
        $bilanYear = $this->initBilan;
        $thisMonth = 0;
        $thisYear = 0;
        $countWeek = 1;
        $nextWeek = new DateTime("0000-00-00");
        foreach ($pointages as $index => $pointage) {
            if ($pointage->getDate() >= $nextWeek and $index) {
                $bilanWeek["date"] = $countWeek;
                $bilanWeek["background"] = "Orange";
                $bilanWeek["colspan"] = 4;
                $bilanWeek["date"] = "Semaine " . $bilanWeek["date"];
                array_push($collectGeneral, $bilanWeek);
                $bilanWeek = $this->initBilan;
                $countWeek++;
            }
            if ($thisYear . '-' . $thisMonth !=  $pointage->getDate()->format('Y-m') and $index) {
                $bilanMonth["date"] =   $thisYear . '-' . $thisMonth;
                $bilanMonth["background"] = "DodgerBlue";
                $bilanMonth["colspan"] = 4;
                if ($thisYear and  $thisMonth) {
                    array_push($collectGeneral, $bilanMonth);
                }
                $bilanMonth = $this->initBilan;
            }
            if ($thisYear !=  $pointage->getDate()->format('Y') and $index) {
                $bilanYear["date"] =     $thisYear;
                $bilanYear["background"] = "MediumSeaGreen";
                $bilanMonth["colspan"] = 4;
                if ($thisYear) {
                    array_push($collectGeneral, $bilanYear);
                }
                $bilanYear = $this->initBilan;
            }

            $bilanWeek = $this->calculateurBilan($pointage, $bilanWeek);
            $bilanMonth = $this->calculateurBilan($pointage, $bilanMonth);
            $bilanYear = $this->calculateurBilan($pointage, $bilanYear);
            //if (!( $pointage->getDate()->format("W") == 0) and  !( $pointage->getDate()->format("W") == 6))
            array_push($collectGeneral, [
                "colspan" => 1,
                "date" =>   $pointage->getDate()->format('Y-m-d'),
                "horaire" =>  $pointage->getHoraire(),
                "entrer" =>  $pointage->getEntrer() ? $pointage->getEntrer()->format('H:i:s') : "",
                "sortie" =>  $pointage->getSortie() ? $pointage->getSortie()->format('H:i:s') : "",
                "nbrHeurTravailler" => $pointage->getNbrHeurTravailler() ? $pointage->getNbrHeurTravailler()->format('H:i:s') : "",
                "retardEnMinute" => $pointage->getRetardEnMinute() ? $pointage->getRetardEnMinute()->format('H:i:s') : "",
                "departAnticiper" => $pointage->getDepartAnticiper() ? $pointage->getDepartAnticiper()->format('H:i:s') : "",
                "retardMidi" => $pointage->getRetardMidi() ? $pointage->getRetardMidi()->format('H:i:s') : "",
                "totalRetard" => $pointage->getTotaleRetard() ? $pointage->getTotaleRetard()->format('H:i:s') : "",
                "autorisationSortie" => $pointage->getAutorisationSortie() ? $pointage->getAutorisationSortie()->getHeurAutoriser()->format('H:i:s') : "",
                "congerPayer" =>  $pointage->getCongerPayer(),
                "absence" => $pointage->getAbsence(),
                "heurNormalementTravailler" => $pointage->getHeurNormalementTravailler() ? $pointage->getHeurNormalementTravailler()->format('H:i:s') : "",
                "diff" => $pointage->getDiff() ? $pointage->getDiff()->format('H:i:s') : "",
            ]);
            $thisMonth =   $pointage->getDate()->format('m');
            $thisYear =   $pointage->getDate()->format('Y');
            $nextWeek =  $pointage->getDate()->setISODate($pointage->getDate()->format('o'), $pointage->getDate()->format('W') + 1);
        }
        return $collectGeneral;
    }
}
