<?php

namespace App\Service;

use App\Entity\Horaire;
use App\Entity\User;
use DateTime;

class SortieService
{

    /**
     * timeService
     *
     * @var TimeService
     */
    private $timeService;
    
    /**
     * horaireService variable
     *
     * @var HoraireService
     */
    private $horaireService;

    /**
     * congerService variable
     *
     * @var CongerService
     */
    private $congerService;

    public function __construct(TimeService $timeService, HoraireService $horaireService, CongerService $congerService)
    {
        $this->timeService = $timeService;
        $this->horaireService = $horaireService;
        $this->congerService = $congerService;
    }
    public function getSortieFromArray(array $attchktime, Horaire $horaire, User $employer, DateTime $date)
    {
        $heurDebutTravaille = $this->timeService->generateTime($horaire->getHeurDebutTravaille()->format('H:i:s'));
        $debutPauseMatinal = $this->timeService->generateTime($horaire->getDebutPauseMatinal()->format('H:i:s'));
        $finPauseMatinal = $this->timeService->generateTime($horaire->getFinPauseMatinal()->format('H:i:s'));
        $debutPauseDejeuner = $this->timeService->generateTime($horaire->getDebutPauseDejeuner()->format('H:i:s'));
        $finPauseDejeuner = $this->timeService->generateTime($horaire->getFinPauseDejeuner()->format('H:i:s'));
        $debutPauseMidi = $this->timeService->generateTime($horaire->getDebutPauseMidi()->format('H:i:s'));
        $finPauseMidi = $this->timeService->generateTime($horaire->getFinPauseMidi()->format('H:i:s'));
        $heurFinTravaille = $this->timeService->generateTime($horaire->getHeurFinTravaille()->format('H:i:s'));

        if ($attchktime[0] == "") {
            return null;
        }
        $timePos0 = $this->timeService->generateTime($attchktime[0]);
        
        $atttime = $this->timeService->generateTime($attchktime[0]);

        switch (count($attchktime)) {
                case 1:
                    if (($timePos0 >= $finPauseMatinal and $timePos0 < $finPauseDejeuner) or $timePos0 >= $debutPauseMidi) {
                        dd($timePos0);
                        return $timePos0;
                    }
                    $timePos0->add($this->timeService->dateTimeToDateInterval($this->horaireService->getHeursDemiJournerDeTravaille()));
                    dump($attchktime);
                    dd($timePos0);
                    return $timePos0;
                    break;
                case 2:
                    if (!$this->congerService->getConger($employer, $date)) {
                        $timePos1 = $this->timeService->generateTime($attchktime[1]);
                        if ($timePos0 < $debutPauseDejeuner and $debutPauseDejeuner <= $timePos1 and $timePos1 < $finPauseDejeuner) {
                            return $timePos1;
                        } else {
                            dump($timePos0);
                            dump($timePos1);
                            dump($attchktime);
                            dump($debutPauseDejeuner);
                            dd($finPauseDejeuner);
                            return $timePos0;
                        }
                        //   if($debutPauseDejeuner <= $timePos1 and $timePos1 < $finPauseDejeuner   )
                      
                       
                        // dd($d);
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
                        $atttims = new DateTime($attchktime[1]);
                        $atttim3 = new DateTime($attchktime[2]);
                        $a = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $heurDebutTravaille));
                        $b = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $debutPauseDejeuner));
                        $c = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $finPauseDejeuner));
                        $d = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $heurFinTravaille));
                        dump($attchktime);
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
                    $atttims = new DateTime($attchktime[1]);
                    $atttim3 = new DateTime($attchktime[2]);
                    $a = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $heurDebutTravaille));
                    $b = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $debutPauseDejeuner));
                    $c = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $finPauseDejeuner));
                    $d = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $heurFinTravaille));
                    dump($a);
                    dump($b);
                    dump($c);
                    dd($d);
                    break;
            }
    }
}
