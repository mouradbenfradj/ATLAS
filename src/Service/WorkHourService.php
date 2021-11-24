<?php

namespace App\Service;

use App\Entity\AutorisationSortie;
use App\Entity\Conger;
use App\Entity\Horaire;
use App\Entity\User;
use DateTime;

class WorkHourService
{
    private $attchktime;
    private $date;
    /**
     * timeService variable
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
     * congerPayer variable
     *
     * @var Conger|null
     */
    private $congerPayer;

    /**
     * autorisationSortie variable
     *
     * @var AutorisationSortie|null
     */
    private $autorisationSortie;

    /**
     * nbrHeurTravailler variable
     *
     * @var DateTime
     */
    private $nbrHeurTravailler;


    private $heurDebutTravaille;
    private $debutPauseMatinal;
    private $finPauseMatinal;
    private $debutPauseDejeuner;
    private $finPauseDejeuner;
    private $debutPauseMidi;
    private $finPauseMidi;
    private $heurFinTravaille;
    private $entrer0;
    private $entrer1;
    private $entrer2;
    private $entrer3;
    private $heurNormalementTravailler;
    

    public function __construct(TimeService $timeService, HoraireService $horaireService)
    {
        $this->timeService = $timeService;
        $this->horaireService = $horaireService;
    }
    
    public function requirement(array $attchktime, Horaire $horaire, User $employer, DateTime $date, DateTime $entrer, ?DateTime $sortie)
    {
        /**switch (count($attchktime)) {
                case 1:
                    $this->entrer = $entrer?$this->timeService->generateTime($entrer->format('H:i:s')):$attchktime[0]?$this->timeService->generateTime($attchktime[0]):null;
                    $this->sortie = $sortie?$this->timeService->generateTime($sortie->format('H:i:s')):$attchktime[0]?$this->timeService->generateTime($attchktime[0]):null;
                    break;
                    case 2:
                $this->entrer = $entrer?$this->timeService->generateTime($entrer->format('H:i:s')):$attchktime[0]?$this->timeService->generateTime($attchktime[0]):null;
                $this->sortie = $sortie?$this->timeService->generateTime($sortie->format('H:i:s')):$attchktime[1]?$this->timeService->generateTime($attchktime[1]):null;
                break;
                case 3:
                    $this->entrer = $entrer?$this->timeService->generateTime($entrer->format('H:i:s')):$attchktime[0]?$this->timeService->generateTime($attchktime[0]):null;
                    $this->entrer1 = $attchktime[1]?$this->timeService->generateTime($attchktime[1]):null;
                    $this->sortie = $sortie?$this->timeService->generateTime($sortie->format('H:i:s')):$attchktime[2]?$this->timeService->generateTime($attchktime[2]):null;
                    break;
                    case 4:
                        $this->entrer = $entrer?$this->timeService->generateTime($entrer->format('H:i:s')):$attchktime[0]?$this->timeService->generateTime($attchktime[0]):null;
                        $this->entrer1 = $attchktime[1]?$this->timeService->generateTime($attchktime[1]):null;
                        $this->entrer2 = $attchktime[2]?$this->timeService->generateTime($attchktime[2]):null;
                        $this->sortie = $sortie?$this->timeService->generateTime($sortie->format('H:i:s')):null;
                        break;
                        default:
                        $this->entrer2=null;
                        break;
                    } */
        $this->attchktime=$attchktime;
        $this->entrer = $entrer?$this->timeService->generateTime($entrer->format('H:i:s')):$attchktime[0]?$this->timeService->generateTime($attchktime[0]):null;
        $this->entrer1 = $attchktime[1]?$this->timeService->generateTime($attchktime[1]):null;
        $this->entrer = $entrer;
        $this->sortie = $sortie;
        $this->horaire=$horaire;
        $this->employer=$employer;
        $this->date=$date;
        $this->heurDebutTravaille = $this->timeService->generateTime($this->horaire->getHeurDebutTravaille()->format('H:i:s'));
        $this->debutPauseMatinal = $this->timeService->generateTime($this->horaire->getDebutPauseMatinal()->format('H:i:s'));
        $this->finPauseMatinal = $this->timeService->generateTime($this->horaire->getFinPauseMatinal()->format('H:i:s'));
        $this->debutPauseDejeuner = $this->timeService->generateTime($this->horaire->getDebutPauseDejeuner()->format('H:i:s'));
        $this->finPauseDejeuner = $this->timeService->generateTime($this->horaire->getFinPauseDejeuner()->format('H:i:s'));
        $this->debutPauseMidi = $this->timeService->generateTime($this->horaire->getDebutPauseMidi()->format('H:i:s'));
        $this->finPauseMidi = $this->timeService->generateTime($this->horaire->getFinPauseMidi()->format('H:i:s'));
        $this->heurFinTravaille = $this->timeService->generateTime($this->horaire->getHeurFinTravaille()->format('H:i:s'));
        /* if (count($this->attchktime)<4) {
            dump($this->attchktime);
            dump($this->horaire);
            dump($this->employer);
            dump($this->date);
            dump($this->entrer);
            dump($this->sortie);
            dump($this->entrer1);
            dump($this->entrer2);
            dd($this->entrer);
        } */
    }


    public function getEntrerFromArray()
    {
        switch (count($this->attchktime)) {
                case 0:dump($this->attchktime);
                    dump($this->horaire);
                    dump($this->employer);
                    dump($this->date);
                    dump($this->entrer);
                    dump($this->entrer);
                    dump($this->entrer1);
                    dump($this->entrer2);
                    dd($this->entrer);
                    $entrer = null;
                break;
                case 1:
                    if ($this->entrer < $this->finPauseMatinal or ($this->entrer >= $this->debutPauseMidi and $this->entrer < $this->finPauseMidi)) {
                        dump($this->attchktime);
                        dump($this->horaire);
                        dump($this->employer);
                        dump($this->date);
                        dump($this->entrer);
                        dump($this->entrer);
                        dump($this->entrer1);
                        dump($this->entrer2);
                        dd($this->entrer);
                        return $this->entrer;
                    }

                     
                    dump($this->attchktime);
                    dump($this->horaire);
                    dump($this->employer);
                    dump($this->date);
                    dump($this->entrer);
                    dump($this->entrer);
                    dump($this->entrer1);
                    dump($this->entrer2);
                    dd($this->entrer);
                    if (($this->heurDebutTravaille <= $this->entrer and $this->entrer <= $this->debutPauseDejeuner or
                            $this->finPauseDejeuner <= $this->entrer and $this->entrer <= $this->heurFinTravaille)
                        and
                        $this->finPauseDejeuner <= $this->heurFinTravaille
                    ) {
                        return   $this->entrer;
                    } else {
                        return null;
                    }
                    break;
                case 2:
                    $this->entrer = $this->entrer?$this->timeService->generateTime($this->entrer->format('H:i:s')):$this->attchktime[0]?$this->timeService->generateTime($this->attchktime[0]):null;
                    break;
                case 3:
                    if (!$this->autorisationSortie) {
                        dump($this->attchktime);
                        dump($this->horaire);
                        dump($this->employer);
                        dump($this->date);
                        dump($this->entrer);
                        dump($this->entrer);
                        dump($this->entrer);
                        dump($this->entrer1);
                        dump($this->entrer2);
                        dd($this->entrer);
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
                case 4:
                    if (! $this->entrer) {
                        dump($this->attchktime);
                        dump($this->horaire);
                        dump($this->employer);
                        dump($this->date);
                        dump($this->entrer);
                        dump($this->entrer);
                        dump($this->entrer);
                        dump($this->entrer1);
                        dump($this->entrer2);
                        dd($this->entrer);
                        $this->entrer = $this->entrer ? $this->entrer : $this->attchktime[0]?$this->timeService->generateTime($this->attchktime[0]):null;
                    }
                    break;
                default:
                    dump($this->attchktime);
                    dump($this->horaire);
                    dump($this->employer);
                    dump($this->date);
                    dump($this->entrer);
                    dump($this->entrer);
                    dump($this->entrer);
                    dump($this->entrer1);
                    dump($this->entrer2);
                    dd($this->entrer);
                     break;
            }
        return $this->entrer;
    }

    public function getSortieFromArray()
    {
        switch (count($this->attchktime)) {
        case 0:
            
            dump($this->attchktime);
            dump($this->horaire);
            dump($this->employer);
            dump($this->date);
            dump($this->sortie);
            dump($this->entrer1);
            dump($this->entrer2);
            dd($this->sortie);
            $entrer = null;
        break;
        case 1:
            if ($this->entrer < $this->finPauseMatinal or ($this->entrer >= $this->debutPauseMidi and $this->entrer < $this->finPauseMidi)) {
                dump($this->attchktime);
                dump($this->horaire);
                dump($this->employer);
                dump($this->date);

                dump($this->sortie);

                dump($this->entrer1);
                dump($this->entrer2);
                dd($this->sortie);
                return $this->entrer;
            }

             
            dump($this->attchktime);
            dump($this->horaire);
            dump($this->employer);
            dump($this->date);
            dump($this->sortie);
            dump($this->entrer1);
            dump($this->entrer2);
            dd($this->sortie);
            if (($this->heurDebutTravaille <= $this->entrer and $this->entrer <= $this->debutPauseDejeuner or
                    $this->finPauseDejeuner <= $this->entrer and $this->entrer <= $this->heurFinTravaille)
                and
                $this->finPauseDejeuner <= $this->heurFinTravaille
            ) {
                return   $this->entrer;
            } else {
                return null;
            }
            break;
        case 2:
            $this->sortie = $this->sortie?$this->timeService->generateTime($this->sortie->format('H:i:s')):$this->attchktime[1]?$this->timeService->generateTime($this->attchktime[1]):null;
            break;
        case 3:
            if (!$this->autorisationSortie) {
                dump($this->attchktime);
                dump($this->horaire);
                dump($this->employer);
                dump($this->date);

                dump($this->sortie);

                dump($this->entrer1);
                dump($this->entrer2);
                dd($this->sortie);
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
        case 4:
            if (!$this->sortie) {
                dump($this->attchktime);
                dump($this->horaire);
                dump($this->employer);
                dump($this->date);

                dump($this->sortie);

                dump($this->entrer1);
                dump($this->entrer2);
                dd($this->sortie);
                $this->sortie = $this->sortie ? $this->sortie : $this->attchktime[3]?$this->timeService->generateTime($this->attchktime[3]):null;
            }
            break;
        default:
            dump($this->attchktime);
            dump($this->horaire);
            dump($this->employer);
            dump($this->date);
            dump($this->sortie);
            dump($this->entrer1);
            dump($this->entrer2);
            dd($this->sortie);
             break;
    }
        return $this->sortie;
    }

    /**
     * heurNormalementTravailler
     *
     * @return DateTime
     */
    public function heurNormalementTravailler(): DateTime
    {
        if ($this->congerPayer and !$this->congerPayer->getDemiJourner()) {
            dump($this->attchktime);
            dump($this->horaire);
            dump($this->employer);
            dump($this->date);
            dump($this->congerPayer);
            dump($this->autorisationSortie);
            dump($this->entrer);
            dump($this->sortie);
            dump($this->entrer);
            dump($this->entrer1);
            dump($this->entrer2);
            dd($this->sortie);
            return new DateTime('00:00:00');
        } elseif ($this->congerPayer and $this->congerPayer->getDemiJourner()) {
            return $this->horaireService->getHeursDemiJournerDeTravaille();
        } elseif (!$this->congerPayer and $this->autorisationSortie) {
            dump($this->attchktime);
            dump($this->horaire);
            dump($this->employer);
            dump($this->date);
            dump($this->congerPayer);
            dump($this->autorisationSortie);
            dump($this->entrer);
            dump($this->sortie);
            dump($this->entrer);
            dump($this->entrer1);
            dump($this->entrer2);
            dd($this->sortie);
            $heursJournerDeTravaille = $this->heursJournerDeTravaille;
            if ($heursJournerDeTravaille) {
                $heursJournerDeTravaille->sub($this->timeService->diffTime($this->autorisationSortie->getDe(), $this->autorisationSortie->getA()));
            }
            return $heursJournerDeTravaille;
        } else {
            $this->heurNormalementTravailler= $this->horaireService->getHeursJournerDeTravaille();
        }
        return $this->heurNormalementTravailler;
    }

    /**
     * nbrHeurTravailler
     *
     * @return DateTime
     */
    public function nbrHeurTravailler(): DateTime
    {
        if (count($this->attchktime)<4) {
            dump($this->attchktime);
            dump($this->horaire);
            dump($this->employer);
            dump($this->date);
            dump($this->congerPayer);
            dump($this->autorisationSortie);
            dump($this->entrer);
            dump($this->sortie);
            dump($this->entrer);
            dump($this->entrer1);
            dump($this->entrer2);
            dd($this->nbrHeurTravailler);
        }
        if (!$this->entrer or !$this->sortie) {
            dump($this->attchktime);
            dump($this->horaire);
            dump($this->employer);
            dump($this->date);
            dump($this->congerPayer);
            dump($this->autorisationSortie);
            dump($this->entrer);
            dump($this->sortie);
            dump($this->entrer1);
            dump($this->entrer2);
            dd($this->nbrHeurTravailler);
            $this->nbrHeurTravailler =  new DateTime("00:00:00");
            return $this->nbrHeurTravailler;
        }
        
        $entrer = $this->entrer;
        $sortie = $this->sortie;
        $this->nbrHeurTravailler =  $this->sortie;
        if ($this->congerPayer and $this->congerPayer->getDemiJourner()) {
            dump($this->attchktime);
            dump($this->horaire);
            dump($this->employer);
            dump($this->date);
            dump($this->congerPayer);
            dump($this->autorisationSortie);
            dump($this->entrer);
            dump($this->sortie);
            dump($this->entrer);
            dump($this->entrer1);
            dump($this->entrer2);
            dd($this->nbrHeurTravailler);
            if ($this->horaire->getDebutPauseMidi() > $this->entrer) {
                dump($this->attchktime);
                dump($this->horaire);
                dump($this->employer);
                dump($this->date);
                dump($this->congerPayer);
                dump($this->autorisationSortie);
                dump($this->entrer);
                dump($this->sortie);
                dump($this->entrer1);
                dump($this->entrer2);
                dd($this->nbrHeurTravailler);
                $this->nbrHeurTravailler->sub($this->timeService->diffTime(
                    $this->horaire->getDebutPauseMatinal(),
                    $this->horaire->getfinPauseMatinal()
                ));
            } else {
                dump($this->attchktime);
                dump($this->horaire);
                dump($this->employer);
                dump($this->date);
                dump($this->congerPayer);
                dump($this->autorisationSortie);
                dump($this->entrer);
                dump($this->sortie);
                dump($this->entrer1);
                dump($this->entrer2);
                dd($this->nbrHeurTravailler);
                $this->nbrHeurTravailler->sub($this->timeService->diffTime(
                    $this->horaire->getDebutPauseMidi(),
                    $this->horaire->getFinPauseMidi()
                ));
            }
        }
        $this->nbrHeurTravailler->sub($this->timeService->dateTimeToDateInterval($this->horaireService->sumPause()));
        $this->nbrHeurTravailler = $this->timeService->diffTime($this->nbrHeurTravailler, $entrer);
        $this->nbrHeurTravailler =  $this->timeService->dateIntervalToDateTime($this->nbrHeurTravailler);
        return $this->nbrHeurTravailler;
    }



    public function diff(): DateTime
    {
        if ($this->nbrHeurTravailler) {
            return $this->timeService->dateIntervalToDateTime(
                $this->timeService->diffTime(
                    $this->nbrHeurTravailler,
                    $this->heurNormalementTravailler
                )
            );
        } else {
            dd($this->heurNormalementTravailler);

            return $this->heurNormalementTravailler;
        }
    }

    /**
     * Get congerPayer variable
     *
     * @return  Conger|null
     */
    public function getCongerPayer()
    {
        return $this->congerPayer;
    }

    /**
     * Set congerPayer variable
     *
     * @param  Conger|null  $congerPayer  congerPayer variable
     *
     * @return  self
     */
    public function setCongerPayer($congerPayer)
    {
        $this->congerPayer = $congerPayer;

        return $this;
    }

    /**
     * Get autorisationSortie variable
     *
     * @return  AutorisationSortie|null
     */
    public function getAutorisationSortie()
    {
        return $this->autorisationSortie;
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
        $this->horaireService->setHoraire($this->horaire);
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
}
