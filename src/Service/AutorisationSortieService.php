<?php

namespace App\Service;

use App\Entity\AutorisationSortie;
use App\Entity\Horaire;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class AutorisationSortieService
{
    /**
     * employer
     *
     * @var User
     */
    private $employer;

    /**
     * dateAutorisation
     *
     * @var DateTime
     */
    private $dateAutorisation;

    /**
     * pointages
     *
     * @var array
     */
    private $pointages;

    /**
     * valider
     *
     * @var bool
     */
    private $valider;

    /**
     * refuser
     *
     * @var bool
     */
    private $refuser;

    /**
     * de
     *
     * @var DateTime
     */
    private $de;

    /**
     * a
     *
     * @var DateTime
     */
    private $a;
    
    /**
     * attchktime variable
     *
     * @var array
     */
    private $attchktime;

    /**
     * timeService variable
     *
     * @var TimeService
     */
    private $timeService;
    private $horaireService;
    private $horaire;
    private $entrer;
    private $entrer1;
    private $entrer2;
    private $sortie;
    private $heurDebutTravaille;
    private $debutPauseMatinal;
    private $finPauseMatinal;
    private $debutPauseDejeuner;
    private $finPauseDejeuner;
    private $debutPauseMidi;
    private $finPauseMidi;
    private $heurFinTravaille;



    public function __construct(TimeService $timeService, HoraireService $horaireService)
    {
        $this->timeService=$timeService;
        $this->horaireService=$horaireService;
    }
    public function requirement(array $attchktime, Horaire $horaire, DateTime $entrer, DateTime $sortie)
    {
        $this->attchktime=$attchktime;
        $this->horaire=$horaire;
        $this->entrer = $entrer;
        $this->entrer1 = $attchktime[1]?$this->timeService->generateTime($attchktime[1]):null;
        $this->entrer2 = $attchktime[2]?$this->timeService->generateTime($attchktime[2]):null;
        $this->sortie = $sortie;

        $this->heurDebutTravaille = $this->timeService->generateTime($this->horaire->getHeurDebutTravaille()->format('H:i:s'));
        $this->debutPauseMatinal = $this->timeService->generateTime($this->horaire->getDebutPauseMatinal()->format('H:i:s'));
        $this->finPauseMatinal = $this->timeService->generateTime($this->horaire->getFinPauseMatinal()->format('H:i:s'));
        $this->debutPauseDejeuner = $this->timeService->generateTime($this->horaire->getDebutPauseDejeuner()->format('H:i:s'));
        $this->finPauseDejeuner = $this->timeService->generateTime($this->horaire->getFinPauseDejeuner()->format('H:i:s'));
        $this->debutPauseMidi = $this->timeService->generateTime($this->horaire->getDebutPauseMidi()->format('H:i:s'));
        $this->finPauseMidi = $this->timeService->generateTime($this->horaire->getFinPauseMidi()->format('H:i:s'));
        $this->heurFinTravaille = $this->timeService->generateTime($this->horaire->getHeurFinTravaille()->format('H:i:s'));
    }

    public function partielConstruct(
        ?User $employer = null,
        ?DateTime $dateAutorisation = null,
        ?DateTime $de = null,
        ?DateTime $a =null,
        ?bool $valider = null,
        ?bool $refuser = null,
        ?array $pointages = null
    ) {
        $this->employer = $employer;
        $this->dateAutorisation = $dateAutorisation;
        $this->de = $de;
        $this->a = $a;
        $this->pointages = $pointages;
        $this->valider = $valider;
        $this->refuser = $refuser;
    }

    public function de():?DateTime
    {
        /*  dump($this->entrer);
         dump($this->entrer1);
         dump($this->entrer2);
         dump($this->sortie);
         dd($this->entrer); */
        switch (count($this->attchktime)) {
                case 0:
                
                    dump($this->attchktime);
                    dd($this->entrer);    return null;
                    break;
                case 1:
                    dump($this->attchktime);
                    dd($this->entrer);
                    if (($this->entrer >= $finPauseMatinal and $this->entrer < $finPauseDejeuner) or $this->entrer >= $debutPauseMidi) {
                        dd($this->entrer);
                        return $this->entrer;
                    }
                    $this->entrer->add($this->timeService->dateTimeToDateInterval($this->horaireService->getHeursDemiJournerDeTravaille()));
                    dump($this->attchktime);
                    dd($this->entrer);
                    return $this->entrer;
                    break;
                case 2:
                    dump($this->attchktime);
                    dd($this->entrer);
                    if (!$this->congerService->getConger($employer, $date)) {
                        $this->entrer1 = $this->timeService->generateTime($this->attchktime[1]);
                        if ($this->entrer < $debutPauseDejeuner and $debutPauseDejeuner <= $this->entrer1 and $this->entrer1 < $finPauseDejeuner) {
                            dd($this->entrer1);
                            return $this->entrer1;
                        } else {
                            dump($this->entrer);
                            dump($this->entrer1);
                            dump($this->attchktime);
                            dump($debutPauseDejeuner);
                            dd($finPauseDejeuner);
                            return $this->entrer;
                        }
                    }
                    break;
                case 3:
                  
                    if (!in_array($this->entrer->format("H:i"), $this->attchktime)) {
                        dump($this->entrer);
                        dump($this->entrer1);
                        dump($this->entrer2);
                        dump($this->sortie);
                    } elseif (!in_array($this->entrer1->format("H:i"), $this->attchktime)) {
                        dump($this->entrer);
                        dump($this->entrer1);
                        dump($this->entrer2);
                        dump($this->sortie);
                    } elseif (!in_array($this->entrer2->format("H:i"), $this->attchktime)) {
                        dump($this->entrer);
                        dump($this->entrer1);
                        dump($this->entrer2);
                        dump($this->sortie);
                    } elseif (!in_array($this->sortie->format("H:i"), $this->attchktime)) {
                        return $this->sortie;
                    } else {
                        dump($this->entrer);
                        dump($this->entrer1);
                        dump($this->entrer2);
                        dump($this->sortie);
                    }
                    /* dump($this->attchktime);
                    dd($this->debutPauseDejeuner);
                    if (!$this->getAutorisation()) {
                        dump($this->attchktime);
                        if ($this->entrer < $finPauseMatinal
                        and $debutPauseMatinal <= $this->entrer1 and $this->entrer1 < $finPauseDejeuner
                        and $debutPauseDejeuner <= $this->entrer2 and $this->entrer2 < $finPauseMidi) {
                            dump($this->entrer);
                            dump($this->entrer1);
                            dump($this->attchktime);
                            dd($debutPauseDejeuner);
                            /*   $this->sortie->add($this->timeService->dateTimeToDateInterval($this->horaireService->getHeursQuardJournerDeTravaille()));
                            dd($this->sortie);
                            return $this->sortie;
                        } elseif ($this->entrer < $finPauseMatinal
                        and $debutPauseMatinal <= $this->entrer1 and $this->entrer1 < $finPauseDejeuner
                        and $debutPauseMidi <= $this->entrer2 and $this->entrer2 < $heurFinTravaille) {
                            dump($this->entrer);
                            dump($this->entrer1);
                            dump($this->attchktime);
                            dump($debutPauseDejeuner);
                            $sortie = $this->entrer2;
                            $sortie->sub($this->timeService->dateTimeToDateInterval($this->horaireService->getHeursQuardJournerDeTravaille()));
                            dd($sortie);
                            return $sortie;
                        } elseif ($this->entrer < $finPauseMatinal
                        and $debutPauseDejeuner <= $this->entrer1 and $this->entrer1 < $finPauseMidi
                        and $debutPauseMidi <= $this->entrer2 and $this->entrer2 < $heurFinTravaille) {
                            dump($this->entrer);
                            dump($this->entrer1);
                            dump($this->attchktime);
                            dump($debutPauseDejeuner);
                            $sortie = $this->entrer2;
                            $sortie->sub($this->timeService->dateTimeToDateInterval($this->horaireService->getHeursQuardJournerDeTravaille()));
                            dd($sortie);
                            return $sortie;
                        } elseif ($this->entrer < $finPauseMatinal
                        and $debutPauseMatinal <= $this->entrer1 and $this->entrer1 < $finPauseDejeuner
                        and $debutPauseDejeuner <= $this->entrer2 and $this->entrer2 < $finPauseMidi) {
                            dump($this->entrer);
                            dump($this->entrer1);
                            dump($this->attchktime);
                            dump($debutPauseDejeuner);
                            dd($finPauseDejeuner);
                            return $this->entrer;
                        } else {
                            dump($this->entrer);
                            dump($this->entrer1);
                            dump($this->attchktime);
                            dump($debutPauseDejeuner);
                            dd($finPauseDejeuner);
                            return $this->entrer;
                        }
                        if ($debutPauseMatinal <= $atttime and $atttime <= $finPauseDejeuner and $debutPauseDejeuner <= $atttims and $atttims <= $finPauseMidi and $debutPauseMidi <= $atttim3 and ($atttim3 <= $heurFinTravaille or $atttim3 >= $heurFinTravaille)) {
                            $this->autorisationSortieService->partielConstruct($this->employer, $this->date, $heurDebutTravaille, $debutPauseMatinal, true, false);
                            $this->autorisationSortie = $this->autorisationSortieService->ConstructEntity();
                            $this->employer->addAutorisationSorties($this->autorisationSortie);
                        } elseif ($heurDebutTravaille <= $atttime and $atttime <= $finPauseMatinal and $debutPauseDejeuner <= $atttims and $atttims <= $finPauseMidi and $debutPauseMidi <= $atttim3 and ($atttim3 <= $heurFinTravaille or $atttim3 >= $heurFinTravaille)) {
                            $this->autorisationSortieService->partielConstruct($this->employer, $this->date, $debutPauseMatinal, $debutPauseDejeuner, true, false);
                            $this->autorisationSortie = $this->autorisationSortieService->ConstructEntity();
                            $this->employer->addAutorisationSorties($this->autorisationSortie);
                        } elseif ($heurDebutTravaille <= $atttime and $atttime <= $finPauseMatinal and $debutPauseMatinal <= $atttims and $atttims <= $finPauseDejeuner and $debutPauseMidi <= $atttim3 and ($atttim3 <= $heurFinTravaille or $atttim3 >= $heurFinTravaille)) {
                            $this->autorisationSortieService->partielConstruct($this->employer, $this->date, $finPauseDejeuner, $debutPauseMidi, true, false);
                            $this->autorisationSortie = $this->autorisationSortieService->ConstructEntity();
                            $this->employer->addAutorisationSorties($this->autorisationSortie);
                        } elseif ($heurDebutTravaille <= $atttime and $atttime <= $finPauseMatinal and $debutPauseMatinal <= $atttims and $atttims <= $finPauseDejeuner and $debutPauseDejeuner <= $atttim3 and $atttim3 <= $finPauseMidi) {
                            $this->autorisationSortieService->partielConstruct($this->employer, $this->date, $debutPauseMidi, $heurFinTravaille, true, false);
                            $this->autorisationSortie = $this->autorisationSortieService->ConstructEntity();
                            $this->employer->addAutorisationSorties($this->autorisationSortie);
                        }
                        //$this->retardMidi = $this->retardMidi($this->attchktime);
                    } else {
                        dd("autorisaitonsortie");
                    } */
                    break;
                case 4 : return null;break;
                default:
                dump($this->attchktime);
                dd($this->entrer);
                    $atttims = new DateTime($this->attchktime[1]);
                    $atttim3 = new DateTime($this->attchktime[2]);
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
    public function a()
    {
        switch (count($this->attchktime)) {
            case 0:
                
            dump($this->attchktime);
            dd($this->entrer);    return null;
            break;
        case 1:
            dump($this->attchktime);
            dd($this->entrer);
            if (($this->entrer >= $finPauseMatinal and $this->entrer < $finPauseDejeuner) or $this->entrer >= $debutPauseMidi) {
                dd($this->entrer);
                return $this->entrer;
            }
            $this->entrer->add($this->timeService->dateTimeToDateInterval($this->horaireService->getHeursDemiJournerDeTravaille()));
            dump($this->attchktime);
            dd($this->entrer);
            return $this->entrer;
            break;
        case 2:
            dump($this->attchktime);
            dd($this->entrer);
            if (!$this->congerService->getConger($employer, $date)) {
                $this->entrer1 = $this->timeService->generateTime($this->attchktime[1]);
                if ($this->entrer < $debutPauseDejeuner and $debutPauseDejeuner <= $this->entrer1 and $this->entrer1 < $finPauseDejeuner) {
                    dd($this->entrer1);
                    return $this->entrer1;
                } else {
                    dump($this->entrer);
                    dump($this->entrer1);
                    dump($this->attchktime);
                    dump($debutPauseDejeuner);
                    dd($finPauseDejeuner);
                    return $this->entrer;
                }
            }
            break;
        case 3:
            if (!in_array($this->entrer->format("H:i"), $this->attchktime)) {
                dump($this->entrer);
                dump($this->entrer1);
                dump($this->entrer2);
                dump($this->sortie);
            } elseif (!in_array($this->entrer1->format("H:i"), $this->attchktime)) {
                dump($this->entrer);
                dump($this->entrer1);
                dump($this->entrer2);
                dump($this->sortie);
            } elseif (!in_array($this->entrer2->format("H:i"), $this->attchktime)) {
                dump($this->entrer);
                dump($this->entrer1);
                dump($this->entrer2);
                dump($this->sortie);
            } elseif (!in_array($this->sortie->format("H:i"), $this->attchktime)) {
                $aAutorisation = $this->timeService->generateTime($this->sortie->format("H:i:s"));
                $aAutorisation->add($this->timeService->dateTimeToDateInterval($this->horaireService->getHeursQuardJournerDeTravaille()));
                return $aAutorisation;
            // dd($aAutorisation);
            } else {
                dump($this->entrer);
                dump($this->entrer1);
                dump($this->entrer2);
                dump($this->sortie);
            }
            /* dump($this->entrer);
            dump($this->entrer1);
            dump($this->attchktime);
            dd($this->debutPauseDejeuner);
            if (!$this->getAutorisation()) {
                dump($this->attchktime);
                if ($this->entrer < $finPauseMatinal
                and $debutPauseMatinal <= $this->entrer1 and $this->entrer1 < $finPauseDejeuner
                and $debutPauseDejeuner <= $this->entrer2 and $this->entrer2 < $finPauseMidi) {
                    dump($this->entrer);
                    dump($this->entrer1);
                    dump($this->attchktime);
                    dd($debutPauseDejeuner);
                    /*   $this->sortie->add($this->timeService->dateTimeToDateInterval($this->horaireService->getHeursQuardJournerDeTravaille()));
                    dd($this->sortie);
                    return $this->sortie;
                } elseif ($this->entrer < $finPauseMatinal
                and $debutPauseMatinal <= $this->entrer1 and $this->entrer1 < $finPauseDejeuner
                and $debutPauseMidi <= $this->entrer2 and $this->entrer2 < $heurFinTravaille) {
                    dump($this->entrer);
                    dump($this->entrer1);
                    dump($this->attchktime);
                    dump($debutPauseDejeuner);
                    $sortie = $this->entrer2;
                    $sortie->sub($this->timeService->dateTimeToDateInterval($this->horaireService->getHeursQuardJournerDeTravaille()));
                    dd($sortie);
                    return $sortie;
                } elseif ($this->entrer < $finPauseMatinal
                and $debutPauseDejeuner <= $this->entrer1 and $this->entrer1 < $finPauseMidi
                and $debutPauseMidi <= $this->entrer2 and $this->entrer2 < $heurFinTravaille) {
                    dump($this->entrer);
                    dump($this->entrer1);
                    dump($this->attchktime);
                    dump($debutPauseDejeuner);
                    $sortie = $this->entrer2;
                    $sortie->sub($this->timeService->dateTimeToDateInterval($this->horaireService->getHeursQuardJournerDeTravaille()));
                    dd($sortie);
                    return $sortie;
                } elseif ($this->entrer < $finPauseMatinal
                and $debutPauseMatinal <= $this->entrer1 and $this->entrer1 < $finPauseDejeuner
                and $debutPauseDejeuner <= $this->entrer2 and $this->entrer2 < $finPauseMidi) {
                    dump($this->entrer);
                    dump($this->entrer1);
                    dump($this->attchktime);
                    dump($debutPauseDejeuner);
                    dd($finPauseDejeuner);
                    return $this->entrer;
                } else {
                    dump($this->entrer);
                    dump($this->entrer1);
                    dump($this->attchktime);
                    dump($debutPauseDejeuner);
                    dd($finPauseDejeuner);
                    return $this->entrer;
                }
                if ($debutPauseMatinal <= $atttime and $atttime <= $finPauseDejeuner and $debutPauseDejeuner <= $atttims and $atttims <= $finPauseMidi and $debutPauseMidi <= $atttim3 and ($atttim3 <= $heurFinTravaille or $atttim3 >= $heurFinTravaille)) {
                    $this->autorisationSortieService->partielConstruct($this->employer, $this->date, $heurDebutTravaille, $debutPauseMatinal, true, false);
                    $this->autorisationSortie = $this->autorisationSortieService->ConstructEntity();
                    $this->employer->addAutorisationSorties($this->autorisationSortie);
                } elseif ($heurDebutTravaille <= $atttime and $atttime <= $finPauseMatinal and $debutPauseDejeuner <= $atttims and $atttims <= $finPauseMidi and $debutPauseMidi <= $atttim3 and ($atttim3 <= $heurFinTravaille or $atttim3 >= $heurFinTravaille)) {
                    $this->autorisationSortieService->partielConstruct($this->employer, $this->date, $debutPauseMatinal, $debutPauseDejeuner, true, false);
                    $this->autorisationSortie = $this->autorisationSortieService->ConstructEntity();
                    $this->employer->addAutorisationSorties($this->autorisationSortie);
                } elseif ($heurDebutTravaille <= $atttime and $atttime <= $finPauseMatinal and $debutPauseMatinal <= $atttims and $atttims <= $finPauseDejeuner and $debutPauseMidi <= $atttim3 and ($atttim3 <= $heurFinTravaille or $atttim3 >= $heurFinTravaille)) {
                    $this->autorisationSortieService->partielConstruct($this->employer, $this->date, $finPauseDejeuner, $debutPauseMidi, true, false);
                    $this->autorisationSortie = $this->autorisationSortieService->ConstructEntity();
                    $this->employer->addAutorisationSorties($this->autorisationSortie);
                } elseif ($heurDebutTravaille <= $atttime and $atttime <= $finPauseMatinal and $debutPauseMatinal <= $atttims and $atttims <= $finPauseDejeuner and $debutPauseDejeuner <= $atttim3 and $atttim3 <= $finPauseMidi) {
                    $this->autorisationSortieService->partielConstruct($this->employer, $this->date, $debutPauseMidi, $heurFinTravaille, true, false);
                    $this->autorisationSortie = $this->autorisationSortieService->ConstructEntity();
                    $this->employer->addAutorisationSorties($this->autorisationSortie);
                }
                //$this->retardMidi = $this->retardMidi($this->attchktime);
            } else {
                dd("autorisaitonsortie");
            } */
            break;
        case 4 : return null;break;
        default:
        dump($this->attchktime);
        dd($this->entrer);
            $atttims = new DateTime($this->attchktime[1]);
            $atttim3 = new DateTime($this->attchktime[2]);
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

    public function ConstructEntity(): AutorisationSortie
    {
        $autorisationSortie = new AutorisationSortie();
        $autorisationSortie->setDateAutorisation($this->dateAutorisation);
        $autorisationSortie->setDe($this->de);
        $autorisationSortie->setA($this->a);
        $autorisationSortie->setValider($this->valider);
        $autorisationSortie->setRefuser($this->refuser);
        $autorisationSortie->setEmployer($this->employer);
        return $autorisationSortie;
    }

    /*
        public function getIfAutorisationSortie(string $date, User $employer): ?AutorisationSortie
        {
            return $this->em->getRepository(AutorisationSortie::class)->findOneByEmployerAndDate($date, $employer);
        } */


    public function getAutorisation(): ?AutorisationSortie
    {
        $autorisationSortie =  current(array_filter(array_map(
            fn ($autorisationSortie): ?AutorisationSortie => ($autorisationSortie->getDateAutorisation() <= $this->dateAutorisation and $this->dateAutorisation <= $autorisationSortie->getDateAutorisation()) ? $autorisationSortie : null,
            $this->employer->getAutorisationSorties()->toArray()
        )));
        if ($autorisationSortie) {
            return $autorisationSortie;
        }
        return null;
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
}
