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

    public function __construct()
    {
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
            "abscence" =>  0,
            "heurNormalementTravailler" => 0,
            "diff" => 0,
        ];
    }



    public function calculateurBilan(Pointage $pointage, array $bilan)
    {
        $bilan["nbrHeurTravailler"] = $this->bilan($this->nbrHeurTravailler, $bilan["nbrHeurTravailler"]);
        if ($this->retardEnMinute) {
            $bilan["retardEnMinute"] = $this->bilan($this->retardEnMinute, $bilan["retardEnMinute"]);
        }
        if ($this->departAnticiper) {
            $bilan["departAnticiper"] = $this->bilan($this->departAnticiper, $bilan["departAnticiper"]);
        }
        if ($this->retardMidi) {
            $bilan["retardMidi"] = $this->bilan($this->retardMidi, $bilan["retardMidi"]);
        }
        $bilan["totalRetard"] = $this->bilan($this->totalRetard, $bilan["totalRetard"]);
        if ($this->autorisationSortie) {
            $bilan["autorisationSortie"] = $this->bilan($this->autorisationSortie->getTime(), $bilan["autorisationSortie"]);
        }
        if ($this->congerPayer) {
            if ($this->congerPayer->getDemiJourner()) {
                $bilan["congerPayer"] += 0.5;
            } else {
                $bilan["congerPayer"] += 1;
            }
        }
        $bilan["abscence"] = $this->abscence ? $bilan["abscence"] + 1 : $bilan["abscence"];
        $bilan["heurNormalementTravailler"] = $this->bilan($pointage->getHeurNormalementTravailler(), $bilan["heurNormalementTravailler"]);
        $bilan["diff"] = $this->bilan($this->diff, $bilan["diff"]);
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
    public function getBilanSemestriel($pointages)
    {
        $bilan = $this->initBilan;
        $thisWeek = 0;
        $countWeek = 1;
        $collectSemaine = [];
        foreach ($pointages as $this->pointage) {
            $this->constructFromPointage($this->pointage);
            if ($thisWeek != $this->date->format('W')) {
                if ($thisWeek) {
                    array_push($collectSemaine, $bilan);
                    $countWeek++;
                }
                $thisWeek = $this->date->format('W');
                $bilan = $this->initBilan;
                $bilan["date"] = $countWeek;
            }
            $bilan = $this->calculateurBilan($this->pointage, $bilan);
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
        foreach ($pointages as $this->pointage) {
            $this->constructFromPointage($this->pointage);
            if ($thisYear . '-' . $thisMonth != $this->date->format('Y-m')) {
                if ($thisYear and $thisMonth) {
                    array_push($collectMensuel, $bilan);
                }
                $thisYear =  $this->date->format('Y');
                $thisMonth =  $this->date->format('m');
                $bilan = $this->initBilan;
                $bilan["date"] =  $this->date->format('Y-m');
            }
            $bilan = $this->calculateurBilan($this->pointage, $bilan);
        }
        array_push($collectMensuel, $bilan);
        return $collectMensuel;
    }
    public function getBilanAnnuel($pointages)
    {
        $bilan = $this->initBilan;
        $thisYear = 0;
        $collectAnnuel = [];
        foreach ($pointages as $this->pointage) {
            $this->constructFromPointage($this->pointage);
            if ($thisYear != $this->date->format('Y')) {
                if ($thisYear) {
                    array_push($collectAnnuel, $bilan);
                }
                $thisYear =  $this->date->format('Y');
                $bilan = $this->initBilan;
                $bilan["date"] =  $this->date->format('Y');
            }
            $bilan = $this->calculateurBilan($this->pointage, $bilan);
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
                "horaire" =>  $this->horaire,
                "entrer" =>  $this->entrer ? $this->entrer->format('H:i:s') : "",
                "sortie" =>  $this->sortie ? $this->sortie->format('H:i:s') : "",
                "nbrHeurTravailler" => $this->nbrHeurTravailler ? $this->nbrHeurTravailler->format('H:i:s') : "",
                "retardEnMinute" => $this->retardEnMinute ? $this->retardEnMinute->format('H:i:s') : "",
                "departAnticiper" => $this->departAnticiper ? $this->departAnticiper->format('H:i:s') : "",
                "retardMidi" => $this->retardMidi ? $this->retardMidi->format('H:i:s') : "",
                "totalRetard" => $this->totalRetard ? $this->totalRetard->format('H:i:s') : "",
                "autorisationSortie" => $this->autorisationSortie ? $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($this->autorisationSortie->getDe(), $this->autorisationSortie->getA()))->format('H:i:s') : "",
                "congerPayer" =>  $this->congerPayer,
                "abscence" => $this->abscence,
                "heurNormalementTravailler" => $this->heurNormalementTravailler() ? $this->heurNormalementTravailler()->format('H:i:s') : "",
                "diff" => $this->diff ? $this->diff->format('H:i:s') : "",
            ]);
            $thisMonth =   $pointage->getDate()->format('m');
            $thisYear =   $pointage->getDate()->format('Y');
            $nextWeek =  $pointage->getDate()->setISODate($pointage->getDate()->format('o'),  $pointage->getDate()->format('W') + 1);
        }
        return $collectGeneral;
    }
}
