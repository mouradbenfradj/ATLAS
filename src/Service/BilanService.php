<?php
namespace App\Service;

use DateTime;

class BilanService extends PointageService
{
    /**
     * InitBilan
     *
     * @var array
     */
    private $initBilan;
    /**
     * Undocumented function
     */
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
            "absence" =>  0,
            "heurNormalementTravailler" => 0,
            "diff" => 0,
        ];
    }
    /**
     * getBilanGeneral
     *
     * @return array
     */
    public function getBilanGeneral(): array
    {
        $pointages = $this->getEmployer() ? $this->getEmployer()->getPointages()->toArray() : [];
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
            if ($pointage->getDate() >= $nextWeek && $index) {
                $bilanWeek["date"] = $countWeek;
                $bilanWeek["background"] = "Orange";
                $bilanWeek["colspan"] = 4;
                $bilanWeek["date"] = "Semaine " . $bilanWeek["date"];
                array_push($collectGeneral, $bilanWeek);
                $bilanWeek = $this->initBilan;
                $countWeek++;
            }
            if ($thisYear . '-' . $thisMonth !=  $pointage->getDate()->format('Y-m') && $index) {
                $bilanMonth["date"] =   $thisYear . '-' . $thisMonth;
                $bilanMonth["background"] = "DodgerBlue";
                $bilanMonth["colspan"] = 4;
                if ($thisYear &&  $thisMonth) {
                    array_push($collectGeneral, $bilanMonth);
                }
                $bilanMonth = $this->initBilan;
            }
            if ($thisYear !=  $pointage->getDate()->format('Y') && $index) {
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
                "entrer" =>  $pointage->getEntrer() ? $pointage->getEntrer()->format(self::FORMATTIMEHIS) : "",
                "sortie" =>  $pointage->getSortie() ? $pointage->getSortie()->format(self::FORMATTIMEHIS) : "",
                "nbrHeurTravailler" => $pointage->getNbrHeurTravailler() ? $pointage->getNbrHeurTravailler()->format(self::FORMATTIMEHIS) : "",
                "retardEnMinute" => $pointage->getRetardEnMinute() ? $pointage->getRetardEnMinute()->format(self::FORMATTIMEHIS) : "",
                "departAnticiper" => $pointage->getDepartAnticiper() ? $pointage->getDepartAnticiper()->format(self::FORMATTIMEHIS) : "",
                "retardMidi" => $pointage->getRetardMidi() ? $pointage->getRetardMidi()->format(self::FORMATTIMEHIS) : "",
                "totalRetard" => $pointage->getTotaleRetard() ? $pointage->getTotaleRetard()->format(self::FORMATTIMEHIS) : "",
                "autorisationSortie" => $pointage->getAutorisationSortie() ? $pointage->getAutorisationSortie()->getHeurAutoriser()->format(self::FORMATTIMEHIS) : "",
                "congerPayer" =>  $pointage->getCongerPayer(),
                "absence" => $pointage->getAbsence(),
                "heurNormalementTravailler" => $pointage->getHeurNormalementTravailler() ? $pointage->getHeurNormalementTravailler()->format(self::FORMATTIMEHIS) : "",
                "diff" => $pointage->getDiff() ? $pointage->getDiff()->format(self::FORMATTIMEHIS) : "",
            ]);
            $thisMonth =   $pointage->getDate()->format('m');
            $thisYear =   $pointage->getDate()->format('Y');
            $nextWeek =  $pointage->getDate()->setISODate($pointage->getDate()->format('o'), $pointage->getDate()->format('W') + 1);
        }
        return $collectGeneral;
    }
}
