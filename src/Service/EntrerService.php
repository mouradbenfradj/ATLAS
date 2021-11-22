<?php

namespace App\Service;

use App\Entity\Horaire;

class EntrerService
{

    /**
     * timeService
     *
     * @var TimeService
     */
    private $timeService;

    public function __construct(TimeService $timeService)
    {
        $this->timeService = $timeService;
    }
    public function getEntrerFromArray(array $attchktime, Horaire $horaire)
    {

        $heurDebutTravaille = $this->timeService->generateTime($horaire->getHeurDebutTravaille()->format('H:i:s'));
        $debutPauseMatinal = $this->timeService->generateTime($horaire->getDebutPauseMatinal()->format('H:i:s'));
        $finPauseMatinal = $this->timeService->generateTime($horaire->getFinPauseMatinal()->format('H:i:s'));
        $debutPauseDejeuner = $this->timeService->generateTime($horaire->getDebutPauseDejeuner()->format('H:i:s'));
        $finPauseDejeuner = $this->timeService->generateTime($horaire->getFinPauseDejeuner()->format('H:i:s'));
        $debutPauseMidi = $this->timeService->generateTime($horaire->getDebutPauseMidi()->format('H:i:s'));
        $finPauseMidi = $this->timeService->generateTime($horaire->getFinPauseMidi()->format('H:i:s'));
        $heurFinTravaille = $this->timeService->generateTime($horaire->getHeurFinTravaille()->format('H:i:s'));
        if ($attchktime[0] != "") {
            switch (count($attchktime)) {
                case 1:
                    $atttime = $this->timeService->generateTime($attchktime[0]);
                    if ($attchktime < $finPauseMatinal or ($attchktime >= $debutPauseMidi and $attchktime < $finPauseMidi)) {
                        dd($atttime);
                        return $atttime;
                    }
                    $a = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $heurDebutTravaille));
                    $b = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $debutPauseDejeuner));
                    $c = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $finPauseDejeuner));
                    $d = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $heurFinTravaille));
                    dump($attchktime);
                    dump($atttime);
                    dump($a);
                    dump($b);
                    dump($c);
                    dd($d);
                    if (($heurDebutTravaille <= $atttime and $atttime <= $debutPauseDejeuner or
                            $finPauseDejeuner <= $atttime and $atttime <= $heurFinTravaille)
                        and
                        $finPauseDejeuner <= $heurFinTravaille
                    ) {
                        return   $atttime;
                    } else {
                        return null;
                    }
                    break;
                case 2:
                    if (!$this->congerPayer) {
                        $atttime = $this->timeService->generateTime($attchktime[0]);
                        $atttims = $this->timeService->generateTime($attchktime[1]);
                        $a = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $heurDebutTravaille));
                        $b = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $debutPauseDejeuner));
                        $c = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $finPauseDejeuner));
                        $d = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $heurFinTravaille));
                        dump($atttime);
                        dump($atttims);
                        dump($a);
                        dump($b);
                        dump($c);
                        dd($d);
                        if (
                            !$this->congerPayer
                            and (
                                ($heurDebutTravaille <= $atttime and $atttime <= $finPauseMatinal and $debutPauseMatinal <= $atttims and $atttims <= $finPauseDejeuner)
                                or
                                ($debutPauseMatinal <= $atttime and $atttime <= $finPauseDejeuner and $debutPauseDejeuner <= $atttims and $atttims <= $finPauseMidi)
                                or
                                ($debutPauseDejeuner <= $atttime and $atttime <= $finPauseMidi and $debutPauseMidi <= $atttims and $atttims <= $heurFinTravaille))
                            and $finPauseDejeuner <= $heurFinTravaille
                        ) {
                            $this->congerService->partielConstruct($this->employer, $this->date, $this->date, "CP", true, false, true);
                            $this->congerPayer = $this->congerService->ConstructEntity();
                            $this->employer->addConger($this->congerPayer);
                        }
                    }
                    break;
                case 3:
                    if (!$this->autorisationSortie) {
                        $atttime = new DateTime($attchktime[0]);
                        $atttims = new DateTime($attchktime[1]);
                        $atttim3 = new DateTime($attchktime[2]);
                        $a = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $heurDebutTravaille));
                        $b = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $debutPauseDejeuner));
                        $c = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $finPauseDejeuner));
                        $d = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $heurFinTravaille));
                        dump($atttime);
                        dump($atttims);
                        dump($atttim3);
                        dump($a);
                        dump($b);
                        dump($c);
                        dd($d);
                        if (!$this->autorisationSortie and $debutPauseMatinal <= $atttime and $atttime <= $finPauseDejeuner and $debutPauseDejeuner <= $atttims and $atttims <= $finPauseMidi and $debutPauseMidi <= $atttim3 and ($atttim3 <= $heurFinTravaille or $atttim3 >= $heurFinTravaille)) {
                            $this->autorisationSortieService->partielConstruct($this->employer, $this->date, $heurDebutTravaille, $debutPauseMatinal, true, false);
                            $this->autorisationSortie = $this->autorisationSortieService->ConstructEntity();
                            $this->employer->addAutorisationSorties($this->autorisationSortie);
                        } elseif (!$this->autorisationSortie and $heurDebutTravaille <= $atttime and $atttime <= $finPauseMatinal and $debutPauseDejeuner <= $atttims and $atttims <= $finPauseMidi and $debutPauseMidi <= $atttim3 and ($atttim3 <= $heurFinTravaille or $atttim3 >= $heurFinTravaille)) {
                            $this->autorisationSortieService->partielConstruct($this->employer, $this->date, $debutPauseMatinal, $debutPauseDejeuner, true, false);
                            $this->autorisationSortie = $this->autorisationSortieService->ConstructEntity();
                            $this->employer->addAutorisationSorties($this->autorisationSortie);
                        } elseif (!$this->autorisationSortie and $heurDebutTravaille <= $atttime and $atttime <= $finPauseMatinal and $debutPauseMatinal <= $atttims and $atttims <= $finPauseDejeuner and $debutPauseMidi <= $atttim3 and ($atttim3 <= $heurFinTravaille or $atttim3 >= $heurFinTravaille)) {
                            $this->autorisationSortieService->partielConstruct($this->employer, $this->date, $finPauseDejeuner, $debutPauseMidi, true, false);
                            $this->autorisationSortie = $this->autorisationSortieService->ConstructEntity();
                            $this->employer->addAutorisationSorties($this->autorisationSortie);
                        } elseif (!$this->autorisationSortie and $heurDebutTravaille <= $atttime and $atttime <= $finPauseMatinal and $debutPauseMatinal <= $atttims and $atttims <= $finPauseDejeuner and $debutPauseDejeuner <= $atttim3 and $atttim3 <= $finPauseMidi) {
                            $this->autorisationSortieService->partielConstruct($this->employer, $this->date, $debutPauseMidi, $heurFinTravaille, true, false);
                            $this->autorisationSortie = $this->autorisationSortieService->ConstructEntity();
                            $this->employer->addAutorisationSorties($this->autorisationSortie);
                        }
                        //$this->retardMidi = $this->retardMidi($attchktime);
                    }
                    break;
                default:
                    $atttime = new DateTime($attchktime[0]);
                    $atttims = new DateTime($attchktime[1]);
                    $atttim3 = new DateTime($attchktime[2]);
                    $atttim4 = new DateTime($attchktime[3]);
                    $a = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $heurDebutTravaille));
                    $b = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $debutPauseDejeuner));
                    $c = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $finPauseDejeuner));
                    $d = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $heurFinTravaille));
                    dump($atttime);
                    dump($atttims);
                    dump($atttim3);
                    dump($atttim4);
                    dump($a);
                    dump($b);
                    dump($c);
                    dd($d);
                    break;
            }
        } else {
            return null;
        }
    }
}
