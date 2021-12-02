<?php

namespace App\Service;

use App\Entity\AutorisationSortie;
use App\Entity\Conger;
use App\Entity\Horaire;
use DateInterval;
use DateTime;

class RetardService
{
    /**
     * retardEnMinute variable
     *
     * @var DateInterval
     */
    private $retardEnMinute;
    /**
     * departAnticiper variable
     *
     * @var DateInterval
     */
    private $departAnticiper;
    /**
     * retardMidi variable
     *
     * @var DateInterval
     */
    private $retardMidi;
    /**
     * totalRetard variable
     *
     * @var DateTime
     */
    private $totalRetard;
    /**
     * horaire variable
     *
     * @var Horaire
     */
    private $horaire;
    /**
     * entrer variable
     *
     * @var DateTime|null
     */
    private $entrer;
    /**
     * sortie variable
     *
     * @var DateTime|null
     */
    private $sortie;

    /**
     * timeService variable
     *
     * @var TimeService
     */
    private $timeService;
    /**
     * autorisationSortie variable
     *
     * @var AutorisationSortie|null
     */
    private $autorisationSortie;
    /**
     * heurAutorisation variable
     *
     * @var DateTime
     */
    private $heurAutorisation;
    /**
     * conger variable
     *
     * @var Conger
     */
    private $conger;
    /**
     * attchktime variable
     *
     * @var array
     */
    private $attchktime;


    private $entrer1;
    private $entrer2;
    private $heurDebutTravaille;
    private $debutPauseMatinal;
    private $finPauseMatinal;
    private $debutPauseDejeuner;
    private $finPauseDejeuner;
    private $debutPauseMidi;
    private $finPauseMidi;
    private $heurFinTravaille;
    private $margeDuRetard;
    private $horaireService;

    public function __construct(TimeService $timeService, HoraireService $horaireService)
    {
        $this->timeService = $timeService;
        $this->horaireService = $horaireService;
    }
    public function requirement(array $attchktime, Horaire $horaire, ?DateTime $entrer, ?DateTime $sortie, ?Conger $conger, ?AutorisationSortie $autorisationSortie)
    {
        $this->attchktime = $attchktime;
        $this->horaire = $horaire;
        switch (count($attchktime)) {
            case 3:
                $this->entrer = $entrer;
                $this->sortie = $sortie;
                $this->entrer1 = $attchktime[0] ? $this->timeService->generateTime($attchktime[0]) : null;
                if (!$this->sortie) {
                    $this->entrer1 =$this->attchktime[1]? $this->timeService->generateTime($this->attchktime[1]) : null;
                    if (!$this->autorisationSortie) {
                        //if (!$this->entrer or !$this->entrer1 or !$this->entrer2 or !$this->sortie) {
                        if ($this->heurDebutTravaille <= $this->entrer and $this->finPauseMatinal > $this->entrer) {
                            dump($this->attchktime);
                            dump($this->entrer);
                            dump($this->entrer1);
                            dump($this->entrer2);
                            dd($this->sortie);
                        } else {
                            dump($this->attchktime);
                            dump($this->entrer);
                            dump($this->entrer1);
                            dump($this->entrer2);
                            dd($this->sortie);
                        }
                        if ($this->finPauseDejeuner <= $this->entrer2 and $this->debutPauseMidi > $this->entrer2) {
                            dump($this->attchktime);
                            dump($this->entrer);
                            dump($this->entrer1);
                            dump($this->entrer2);
                            dd($this->sortie);
                        } else {
                            dump($this->attchktime);
                            dump($this->entrer);
                            dump($this->entrer1);
                            dump($this->entrer2);
                            dd($this->sortie);
                        }
                    }
                } else {
                    $this->entrer1 = $this->attchktime[0] ? $this->timeService->generateTime($this->attchktime[0]) : null;
                    if ($this->entrer->format('H:i:s') == $this->entrer1->format('H:i:s')) {
                        $this->entrer1 = $this->attchktime[1] ? $this->timeService->generateTime($this->attchktime[1]) : null;
                        $this->entrer2 = $this->attchktime[2] ? $this->timeService->generateTime($this->attchktime[2]) : null;
                    } else {
                        $this->sortie = $this->attchktime[2] ? $this->timeService->generateTime($this->attchktime[2]) : null;
                        dump($this->attchktime);
                        dump($this->entrer);
                        dump($this->entrer1);
                        dump($this->entrer2);
                        dd($this->sortie);
                    }
                }                break;
            case 4:
                $this->entrer = $entrer;
                $this->sortie = $sortie;
                $this->entrer1 = $attchktime[1] ? $this->timeService->generateTime($attchktime[1]) : null;
                $this->entrer2 = $attchktime[2] ? $this->timeService->generateTime($attchktime[2]) : null;
                break;
            default:
                $this->entrer = $entrer ? $this->timeService->generateTime($entrer->format("H:i:s")) : null;
                $this->sortie = $sortie ? $this->timeService->generateTime($sortie->format("H:i:s")) : null;
                $this->entrer1 = null;
                $this->entrer2 = null;
                break;
        }
        $this->conger = $conger;
        $this->autorisationSortie = $autorisationSortie;
        $this->heurAutorisation = $this->autorisationSortie ?  $this->timeService->generateTime($this->autorisationSortie->getHeurAutoriser()->format('H:i:s')) : null;
        $this->heurDebutTravaille = $this->timeService->generateTime($this->horaire->getHeurDebutTravaille()->format('H:i:s'));
        $this->debutPauseMatinal = $this->timeService->generateTime($this->horaire->getDebutPauseMatinal()->format('H:i:s'));
        $this->finPauseMatinal = $this->timeService->generateTime($this->horaire->getFinPauseMatinal()->format('H:i:s'));
        $this->debutPauseDejeuner = $this->timeService->generateTime($this->horaire->getDebutPauseDejeuner()->format('H:i:s'));
        $this->finPauseDejeuner = $this->timeService->generateTime($this->horaire->getFinPauseDejeuner()->format('H:i:s'));
        $this->debutPauseMidi = $this->timeService->generateTime($this->horaire->getDebutPauseMidi()->format('H:i:s'));
        $this->finPauseMidi = $this->timeService->generateTime($this->horaire->getFinPauseMidi()->format('H:i:s'));

        $this->heurFinTravaille = $this->timeService->generateTime($this->horaire->getHeurFinTravaille()->format('H:i:s'));
        $this->margeDuRetard = $this->timeService->generateTime($this->horaire->getMargeDuRetard()->format('H:i:s'));
    }

    public function retardMidi(): ?DateTime
    {
        $this->retardMidi = null;
        $heurDebutTravaille = $this->heurDebutTravaille;
        $debutPauseMatinal = $this->debutPauseMatinal;
        $finPauseMatinal = $this->finPauseMatinal;
        $debutPauseDejeuner = $this->debutPauseDejeuner;
        $finPauseDejeuner = $this->finPauseDejeuner;
        $debutPauseMidi = $this->debutPauseMidi;
        $finPauseMidi = $this->finPauseMidi;
        $heurFinTravaille = $this->heurFinTravaille;
        switch (count($this->attchktime)) {
            case 4:
                if ($this->entrer1 < $finPauseDejeuner) {
                    $diffSR = $this->timeService->diffTime($debutPauseDejeuner, $this->entrer1);
                    $debutPauseDejeuner->add($diffSR);
                    $finPauseDejeuner->add($diffSR);
                    if ($this->entrer2 > $finPauseDejeuner) {
                        $this->retardMidi = $this->timeService->diffTime($this->entrer2, $finPauseDejeuner);
                    }
                } else {
                    $diffpauseDej = $this->timeService->diffTime($debutPauseDejeuner, $finPauseDejeuner);
                    $debutPauseDejeuner->add($diffpauseDej);
                    $finPauseDejeuner->add($diffpauseDej);
                    if ($this->entrer1 < $finPauseDejeuner) {
                        $diffSR = $this->timeService->diffTime($debutPauseDejeuner, $this->entrer1);
                        $debutPauseDejeuner->add($diffSR);
                        $finPauseDejeuner->add($diffSR);
                        if ($this->entrer2 > $finPauseDejeuner) {
                            $this->retardMidi = $this->timeService->diffTime($this->entrer2, $finPauseDejeuner);
                        }
                    } else {
                        dd("fff");
                    }
                }
                break;
            case 3:
                if ($this->entrer1 >= $debutPauseDejeuner and $this->entrer1 <= $finPauseDejeuner) {
                    $diffSR = $this->timeService->diffTime($debutPauseDejeuner, $this->entrer1);
                    $debutPauseDejeuner->add($diffSR);
                    $finPauseDejeuner->add($diffSR);
                    if ($this->entrer2 > $finPauseDejeuner) {
                        $this->retardMidi = $this->timeService->diffTime($this->entrer2, $finPauseDejeuner);
                    }
                } else {
                    $debutPauseDejeuner->add(new DateInterval('PT1H'));
                    $finPauseDejeuner->add(new DateInterval('PT1H'));
                    if ($this->entrer1 >= $debutPauseDejeuner and $this->entrer1 <= $finPauseDejeuner) {
                        $diffSR = $this->timeService->diffTime($debutPauseDejeuner, $this->entrer1);
                        $debutPauseDejeuner->add($diffSR);
                        $finPauseDejeuner->add($diffSR);
                        if ($this->entrer2 > $finPauseDejeuner) {
                            $this->retardMidi = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($this->entrer1, $finPauseDejeuner));
                            dump($this->attchktime);
                            dump($this->entrer1);
                            dump($this->entrer2);
                            dump($debutPauseDejeuner);
                            dump($finPauseDejeuner);
                            dd($this->retardMidi);
                        }
                    } else {
                        dump($this->attchktime);
                        dump($this->horaire);
                        dump($this->entrer);
                        dump($this->entrer1);
                        dump($this->entrer2);
                        dump($this->sortie);
                        dump($heurDebutTravaille);
                        dump($debutPauseMatinal);
                        dump($finPauseMatinal);
                        dump($debutPauseDejeuner);
                        dump($finPauseDejeuner);
                        dump($debutPauseMidi);
                        dump($finPauseMidi);
                        dump($heurFinTravaille);
                        dd($this->retardMidi);
                        $debutPauseDejeuner->add(new DateInterval('PT1H'));
                        $finPauseDejeuner->add(new DateInterval('PT1H'));
                        $diffSR = $this->timeService->diffTime($debutPauseDejeuner, $this->entrer1);
                        $debutPauseDejeuner->add($diffSR);
                        $finPauseDejeuner->add($diffSR);
                        if ($this->entrer2 > $finPauseDejeuner) {
                            $retardMidi = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($this->entrer2, $finPauseDejeuner));
                        }
                        dump($this->attchktime);
                        dump($this->entrer1);
                        dump($debutPauseDejeuner);
                        dump($this->entrer2);
                        dump($finPauseDejeuner);
                        dd("retardServidce2");
                    }
                }
                /* dump($this->attchktime);
                dump($this->horaire);
                dump($this->entrer);
                dump($this->entrer1);
                dump($this->entrer2);
                dump($this->sortie);
                dump($heurDebutTravaille);
                dump($debutPauseMatinal);
                dump($finPauseMatinal);
                dump($debutPauseDejeuner);
                dump($finPauseDejeuner);
                dump($debutPauseMidi);
                dump($finPauseMidi);
                dump($heurFinTravaille);
                dd($this->retardMidi);
                         $atttime = $this->timeService->generateTime($this->attchktime[0]);
                $sortieMidi = $this->timeService->generateTime($this->attchktime[1]);
                $retourDeSortieMidi = $this->timeService->generateTime($this->attchktime[2]);
                if ($sortieMidi >= $debutPauseDejeuner and $sortieMidi <= $finPauseDejeuner) {
                    $diffSR =$this->timeService->diffTime($debutPauseDejeuner, $sortieMidi);
                    $debutPauseDejeuner->add($diffSR);
                    $finPauseDejeuner->add($diffSR);
                    if ($retourDeSortieMidi > $finPauseDejeuner) {
                        $retardMidi = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($retourDeSortieMidi, $finPauseDejeuner));
                    }
                }
            dd($this->attchktime); */
                break;
                /* case 2:
                if (!$this->sortie) {
                    $this->sortie = $this->sortie?$this->timeService->generateTime($this->sortie->format('H:i:s')):$this->attchktime[1]?$this->timeService->generateTime($this->attchktime[1]):null;
                }
        dump($this->attchktime);
        dump($this->entrer);
        dump($this->entrer1);
        dump($this->entrer2);
        dump($this->sortie);
        dd($this->retardMidi);
                $entrer1 = new DateTime($this->attchktime[0]);
                $entrer2 = new DateTime($this->attchktime[1]);
                   dump($entrer1);
                   dump($entrer2);
            dump($debutPauseDejeuner);
            dump($this->horaire);
            dump($retardMidi);
            if ($entrer1 >= $heurDebutTravaille and $entrer2 <= $finPauseDejeuner) {
            } else {
                dump($this->attchktime);
                dump($entrer1);
                dump($debutPauseDejeuner);
                dump($entrer2);
                dump($finPauseDejeuner);
                dd("retardServidce2");
            }
            break; */
            case 1:

                dump($this->attchktime);
                dump($this->horaire);
                dump($this->entrer);
                dump($this->entrer1);
                dump($this->entrer2);
                dump($this->sortie);
                dump($heurDebutTravaille);
                dump($debutPauseMatinal);
                dump($finPauseMatinal);
                dump($debutPauseDejeuner);
                dump($finPauseDejeuner);
                dump($debutPauseMidi);
                dump($finPauseMidi);
                dump($heurFinTravaille);
                dd($this->retardMidi);
                $entrer1 = new DateTime($this->attchktime[0]);
                dump($this->attchktime);
                dump($entrer1);
                dump($debutPauseDejeuner);
                dump($finPauseDejeuner);
                dd("retardServidce2");
                break;
            default:
                /* dump($this->attchktime);
             dump($this->horaire);
             dump($this->entrer);
             dump($this->entrer1);
             dump($this->entrer2);
             dump($this->sortie);
             dump($heurDebutTravaille);
             dump($debutPauseMatinal);
             dump($finPauseMatinal);
             dump($debutPauseDejeuner);
             dump($finPauseDejeuner);
             dump($debutPauseMidi);
             dump($finPauseMidi);
             dump($heurFinTravaille);
             dd($this->retardMidi); */
                break;
        }
        if ($this->retardMidi) {
            return $this->timeService->dateIntervalToDateTime($this->retardMidi);
        }
        return $this->retardMidi;
    }


    /**
     * retardEnMinute function
     *
     * @return DateTime|null
     */
    public function retardEnMinute(): ?DateTime
    {
        $this->retardEnMinute = null;
        $heurDebutTravaille = $this->heurDebutTravaille;
        $heurDebutTravaille->add($this->timeService->dateTimeToDateInterval($this->margeDuRetard));

        switch (count($this->attchktime)) {
            case 1:
                dump($this->attchktime);
                dump($this->horaire);
                dump($this->entrer);
                dump($this->sortie);
                dump($this->conger);
                dump($this->autorisationSortie);
                dd($this->retardEnMinute);
                break;
            case 2:
                if ($heurDebutTravaille  >= $this->entrer) {
                    return null;
                }
                if ($heurDebutTravaille < $this->entrer and $this->entrer < $this->finPauseDejeuner) {
                    $this->retardEnMinute = $this->timeService->diffTime($heurDebutTravaille, $this->entrer);
                } else {
                    $diffPauseDejeuner =  $this->timeService->diffTime($this->debutPauseDejeuner, $this->finPauseDejeuner);
                    //$heurDebutTravaille->add($this->timeService->dateTimeToDateInterval($this->horaireService->getHeursDemiJournerDeTravaille()));
                    $heurDebutTravaille = $this->finPauseDejeuner;
                    //$heurDebutTravaille->add($this->timeService->dateTimeToDateInterval($this->horaireService->getHeursDemiJournerDeTravaille()));
                    $heurDebutTravaille->add($diffPauseDejeuner);
                    if ($heurDebutTravaille < $this->entrer and $this->entrer < $this->heurFinTravaille) {
                        $this->retardEnMinute = $this->timeService->diffTime($heurDebutTravaille, $this->entrer);
                        dump($this->attchktime);
                        dump($this->horaire);
                        dump($this->margeDuRetard);
                        dump($heurDebutTravaille);
                        dump($this->finPauseDejeuner);
                        dump($this->entrer);
                        dump($this->sortie);
                        dump($this->conger);
                        dump($this->autorisationSortie);
                        dd($this->retardEnMinute);
                    } else {
                        $this->retardEnMinute = $this->timeService->diffTime($heurDebutTravaille, $this->entrer);
                        dump($this->attchktime);
                        dump($this->horaire);
                        dump($this->margeDuRetard);
                        dump($heurDebutTravaille);
                        dump($this->finPauseDejeuner);
                        dump($this->entrer);
                        dump($this->sortie);
                        dump($this->conger);
                        dump($this->autorisationSortie);
                        dd($this->retardEnMinute);
                    }
                }
                break;
            case 3:
                if ($heurDebutTravaille < $this->entrer) {
                    $this->retardEnMinute = $this->timeService->diffTime($heurDebutTravaille, $this->entrer);
                    if ($this->conger) {
                        if ($this->conger->getDemiJourner()) {
                            $this->retardEnMinute = $this->timeService->dateIntervalToDateTime($this->retardEnMinute);
                            if ($this->retardEnMinute > $this->horaireService->getHeursDemiJournerDeTravaille()) {
                                $this->retardEnMinute->sub($this->timeService->dateTimeToDateInterval($this->horaireService->getHeursDemiJournerDeTravaille()));
                                $this->retardEnMinute = $this->timeService->dateTimeToDateInterval($this->retardEnMinute);
                                if ($this->horaire->getDebutPauseDejeuner() < $this->entrer) {
                                }
                                dump($this->heurAutorisation);
                                dump($this->retardEnMinute);
                                dump($this->entrer);
                                dump($this->sortie);
                                dd($this->conger);
                            } else {
                                dump($this->heurAutorisation);
                                dump($this->retardEnMinute);
                                dump($this->entrer);
                                dump($this->sortie);
                                dd($this->conger);
                            }
                        } else {
                            dump($this->heurAutorisation);
                            dump($this->retardEnMinute);
                            dump($this->entrer);
                            dump($this->sortie);
                            dd($this->conger);
                        }
                    }
                    if ($this->autorisationSortie) {
                        if ($this->heurAutorisation >= $this->timeService->dateIntervalToDateTime($this->retardEnMinute)) {
                            $this->heurAutorisation->sub($this->retardEnMinute);
                            $this->retardEnMinute = null;
                        } else {
                            dump($this->heurAutorisation);
                            dump($this->entrer);
                            dump($this->sortie);
                            dd($this->retardEnMinute);
                            $this->retardEnMinute =$this->timeService->dateIntervalToDateTime($this->retardEnMinute);
                            $this->retardEnMinute->sub($this->timeService->dateTimeToDateInterval($this->heurAutorisation));
                            $this->heurAutorisation = new DateTime('00:00:00');
                            $this->retardEnMinute = $this->timeService->dateTimeToDateInterval($this->retardEnMinute);
                            dump($this->heurAutorisation);
                            dump($this->entrer);
                            dump($this->sortie);
                            dd($this->retardEnMinute);
                        }
                    }
                }
                break;
            case 4:
                if ($heurDebutTravaille < $this->entrer) {
                    $this->retardEnMinute = $this->timeService->diffTime($heurDebutTravaille, $this->entrer);
                }
                break;
            default:
                if ($heurDebutTravaille < $this->entrer) {
                    $this->retardEnMinute = $this->timeService->diffTime($heurDebutTravaille, $this->entrer);
                    if ($this->conger) {
                        if ($this->conger->getDemiJourner()) {
                            $this->retardEnMinute = $this->timeService->dateIntervalToDateTime($this->retardEnMinute);
                            if ($this->retardEnMinute > $this->horaireService->getHeursDemiJournerDeTravaille()) {
                                $this->retardEnMinute->sub($this->timeService->dateTimeToDateInterval($this->horaireService->getHeursDemiJournerDeTravaille()));
                                $this->retardEnMinute = $this->timeService->dateTimeToDateInterval($this->retardEnMinute);
                                if ($this->horaire->getDebutPauseDejeuner() < $this->entrer) {
                                }
                                dump($this->heurAutorisation);
                                dump($this->retardEnMinute);
                                dump($this->entrer);
                                dump($this->sortie);
                                dd($this->conger);
                            } else {
                                dump($this->heurAutorisation);
                                dump($this->retardEnMinute);
                                dump($this->entrer);
                                dump($this->sortie);
                                dd($this->conger);
                            }
                        } else {
                            dump($this->heurAutorisation);
                            dump($this->retardEnMinute);
                            dump($this->entrer);
                            dump($this->sortie);
                            dd($this->conger);
                        }
                    }
                    if ($this->autorisationSortie) {
                        if ($this->heurAutorisation >= $this->timeService->dateIntervalToDateTime($this->retardEnMinute)) {
                            $this->heurAutorisation->sub($this->retardEnMinute);
                            $this->retardEnMinute = null;
                        } else {
                            dump($this->heurAutorisation);
                            dump($this->entrer);
                            dump($this->sortie);
                            dump($this->retardEnMinute);
                            dd($this->autorisationSortie->getHeurAutoriser());
                        }
                    }
                }
                break;
        }
        if (!$this->retardEnMinute) {
            return $this->retardEnMinute;
        }
        return $this->timeService->dateIntervalToDateTime($this->retardEnMinute);
    }


    /**
     * departAnticiper
     *
     * @return DateTime|null
     */
    public function departAnticiper(): ?DateTime
    {
        $this->departAnticiper = null;
        $heurDebutTravaille = $this->heurDebutTravaille;
        $debutPauseMatinal = $this->debutPauseMatinal;
        $finPauseMatinal = $this->finPauseMatinal;
        $debutPauseDejeuner = $this->debutPauseDejeuner;
        $finPauseDejeuner = $this->finPauseDejeuner;
        $debutPauseMidi = $this->debutPauseMidi;
        $finPauseMidi = $this->finPauseMidi;
        $heurFinTravaille = $this->heurFinTravaille;
        switch (count($this->attchktime)) {
            case 1:
                dump($this->attchktime);
                dump($this->horaire);
                dump($this->entrer);
                dump($this->entrer1);
                dump($this->entrer2);
                dump($this->sortie);
                dump($heurDebutTravaille);
                dump($debutPauseMatinal);
                dump($finPauseMatinal);
                dump($debutPauseDejeuner);
                dump($finPauseDejeuner);
                dump($debutPauseMidi);
                dump($finPauseMidi);
                dump($heurFinTravaille);
                dd($this->departAnticiper);

                break;
            case 2:
                if ($heurFinTravaille > $this->sortie and $this->sortie < $finPauseDejeuner and $this->conger and $this->conger->getDemiJourner()) {
                    $heurFinTravaille = $debutPauseDejeuner;
                    $heurFinTravaille->add($this->retardEnMinute);
                }

                if ($heurFinTravaille > $this->sortie) {
                    $this->departAnticiper = $this->timeService->diffTime($heurFinTravaille, $this->sortie);
                }

                break;
            case 3:
                if ($heurFinTravaille > $this->sortie) {
                    $heurDebutTravaille = $this->heurDebutTravaille;

                    if ($this->conger) {
                        if ($this->conger->getDemiJourner()) {
                            $heurFinTravaille= $this->timeService->generateTime($heurDebutTravaille->format('H:i:s'));
                            $heurFinTravaille= $heurFinTravaille->add($this->timeService->dateTimeToDateInterval($this->horaireService->getHeursDemiJournerDeTravaille()));
                        } else {
                            $this->departAnticiper = null;
                            return $this->departAnticiper;
                        }
                    }
                   
                    if ($this->retardEnMinute) {
                        $heurFinTravaille->add($this->timeService->dateTimeToDateInterval($this->margeDuRetard));
                    } else {
                        $heurFinTravaille->add($this->timeService->diffTime($this->entrer, $this->heurDebutTravaille));
                    }
                    $this->departAnticiper = $this->timeService->diffTime($heurFinTravaille, $this->sortie);

                    if ($this->autorisationSortie) {
                        if ($this->heurAutorisation >= $this->timeService->dateIntervalToDateTime($this->departAnticiper)) {
                            $this->heurAutorisation->sub($this->departAnticiper);
                            $this->departAnticiper = null;
                            dump($this->attchktime);
                            dump($heurFinTravaille);
                            dump($this->horaireService->getHeursDemiJournerDeTravaille());
                            dump($this->sortie);
                            dump($this->entrer);
                            dump($heurDebutTravaille);
                            dump($this->conger);
                            dump($this->heurAutorisation);
                            dd($this->departAnticiper);
                        } else {
                            $this->departAnticiper = $this->timeService->dateIntervalToDateTime($this->departAnticiper);
                            $this->departAnticiper->sub($this->timeService->dateTimeToDateInterval($this->heurAutorisation));
                            $this->departAnticiper =$this->timeService->dateTimeToDateInterval($this->departAnticiper);
                        }
                    }

                    return $this->timeService->dateIntervalToDateTime($this->departAnticiper);
                }

                break;
            case 4:

                if ($heurFinTravaille > $this->sortie) {
                    dump($this->attchktime);
                    dump($heurFinTravaille);
                    dump($this->sortie);
                    dump($heurDebutTravaille);
                    dd($this->departAnticiper);
                    $this->departAnticiper = $this->timeService->diffTime($heurFinTravaille, $this->sortie);
                    dump($this->departAnticiper);
                    dd($this->sortie);
                    return $this->timeService->dateIntervalToDateTime($this->departAnticiper);
                }
                break;
            default:
                if ($heurFinTravaille > $this->sortie) {
                    $heurDebutTravaille = $this->heurDebutTravaille;

                    if ($this->conger) {
                        if ($this->conger->getDemiJourner()) {
                            $heurFinTravaille= $this->timeService->generateTime($heurDebutTravaille->format('H:i:s'));
                            $heurFinTravaille= $heurFinTravaille->add($this->timeService->dateTimeToDateInterval($this->horaireService->getHeursDemiJournerDeTravaille()));
                        } else {
                            $this->departAnticiper = null;
                            return $this->departAnticiper;
                        }
                    }
                   
                    if ($this->retardEnMinute) {
                        $heurFinTravaille->add($this->timeService->dateTimeToDateInterval($this->margeDuRetard));
                    } else {
                        $heurFinTravaille->add($this->timeService->diffTime($this->entrer, $this->heurDebutTravaille));
                    }
                    $this->departAnticiper = $this->timeService->diffTime($heurFinTravaille, $this->sortie);

                    if ($this->autorisationSortie) {
                        if ($this->heurAutorisation >= $this->timeService->dateIntervalToDateTime($this->departAnticiper)) {
                            $this->heurAutorisation->sub($this->departAnticiper);
                            $this->departAnticiper = null;
                            dump($this->attchktime);
                            dump($heurFinTravaille);
                            dump($this->horaireService->getHeursDemiJournerDeTravaille());
                            dump($this->sortie);
                            dump($this->entrer);
                            dump($heurDebutTravaille);
                            dump($this->conger);
                            dump($this->heurAutorisation);
                            dd($this->departAnticiper);
                        } else {
                            $this->departAnticiper = $this->timeService->dateIntervalToDateTime($this->departAnticiper);
                            $this->departAnticiper->sub($this->timeService->dateTimeToDateInterval($this->heurAutorisation));
                            $this->departAnticiper =$this->timeService->dateTimeToDateInterval($this->departAnticiper);
                        }
                    }

                    return $this->timeService->dateIntervalToDateTime($this->departAnticiper);
                }
                break;
        }

        if ($this->departAnticiper) {
            return $this->timeService->dateIntervalToDateTime($this->departAnticiper);
        }
        return $this->departAnticiper;
    }




    /**
     * totalRetard
     *
     * @return DateTime
     */
    public function totalRetard(): DateTime
    {
        $this->totalRetard = new DateTime('00:00:00');
        if ($this->retardEnMinute) {
            $this->totalRetard->add($this->retardEnMinute);
        }
        if ($this->departAnticiper) {
            $this->totalRetard->add($this->departAnticiper);
        }
        if ($this->retardMidi) {
            $this->totalRetard->add($this->retardMidi);
        }
        return $this->totalRetard;
    }


    /**
     * Get horaire variable
     *
     * @return  Horaire
     */
    public function getHoraire()
    {
        return $this->horaire;
    }

    /**
     * Set horaire variable
     *
     * @param  Horaire  $horaire  horaire variable
     *
     * @return  self
     */
    public function setHoraire(Horaire $horaire)
    {
        $this->horaire = $horaire;

        return $this;
    }

    /**
     * Set entrer variable
     *
     * @param  DateTime|null  $entrer  entrer variable
     *
     * @return  self
     */
    public function setEntrer($entrer)
    {
        $this->entrer = $entrer;

        return $this;
    }

    /**
     * Set sortie variable
     *
     * @param  DateTime|null  $sortie  sortie variable
     *
     * @return  self
     */
    public function setSortie($sortie)
    {
        $this->sortie = $sortie;

        return $this;
    }

    /**
     * Set attchktime variable
     *
     * @param  array  $attchktime  attchktime variable
     *
     * @return  self
     */
    public function setAttchktime(array $attchktime)
    {
        $this->attchktime = $attchktime;

        return $this;
    }

    /**
     * Set autorisationSortie variable
     *
     * @param  AutorisationSortie|null  $autorisationSortie  autorisationSortie variable
     *
     * @return  self
     */
    public function setAutorisationSortie($autorisationSortie)
    {
        $this->autorisationSortie = $autorisationSortie;

        return $this;
    }
}
