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
    private $horaire;
    private $entrer;
    private $entrer1;
    private $entrer2;
    private $sortie;


    public function __construct(TimeService $timeService)
    {
        $this->timeService=$timeService;
    }
    public function requirement(array $attchktime, Horaire $horaire, DateTime $entrer, DateTime $sortie)
    {
        $this->attchktime=$attchktime;
        $this->horaire=$horaire;
        $this->entrer=$entrer;
        $this->sortie=$sortie;
        $this->attchktime=$attchktime;
        if (count($this->attchktime)<4) {
            dd($this->attchktime);
        }
        $this->entrer1 = $attchktime[1]?$this->timeService->generateTime($attchktime[1]):null;
        $this->entrer2 = $attchktime[2]?$this->timeService->generateTime($attchktime[2]):null;
        $this->attchktime=$attchktime;
        $this->entrer = $entrer?$entrer:$attchktime[0]?$this->timeService->generateTime($attchktime[0]):null;
        $this->entrer1 = $attchktime[1]?$this->timeService->generateTime($attchktime[1]):null;
        $this->entrer2 = $attchktime[2]?$this->timeService->generateTime($attchktime[2]):null;
        $this->sortie = $sortie?$sortie:null;
        dd($this->attchktime);
    }

    public function partielConstruct(
        ?User $employer = null,
        ?DateTime $dateAutorisation = null,
        ?DateTime $de = null,
        ?DateTime $a = null,
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
        $heurDebutTravaille = $this->timeService->generateTime($horaire->getHeurDebutTravaille()->format('H:i:s'));
        $debutPauseMatinal = $this->timeService->generateTime($horaire->getDebutPauseMatinal()->format('H:i:s'));
        $finPauseMatinal = $this->timeService->generateTime($horaire->getFinPauseMatinal()->format('H:i:s'));
        $debutPauseDejeuner = $this->timeService->generateTime($horaire->getDebutPauseDejeuner()->format('H:i:s'));
        $finPauseDejeuner = $this->timeService->generateTime($horaire->getFinPauseDejeuner()->format('H:i:s'));
        $debutPauseMidi = $this->timeService->generateTime($horaire->getDebutPauseMidi()->format('H:i:s'));
        $finPauseMidi = $this->timeService->generateTime($horaire->getFinPauseMidi()->format('H:i:s'));
        $heurFinTravaille = $this->timeService->generateTime($horaire->getHeurFinTravaille()->format('H:i:s'));

        if ($this->attchktime[0] == "") {
            return null;
        }
        $timePos0 = $this->timeService->generateTime($this->attchktime[0]);
        switch (count($this->attchktime)) {
                case 1:
                    if (($timePos0 >= $finPauseMatinal and $timePos0 < $finPauseDejeuner) or $timePos0 >= $debutPauseMidi) {
                        dd($timePos0);
                        return $timePos0;
                    }
                    $timePos0->add($this->timeService->dateTimeToDateInterval($this->horaireService->getHeursDemiJournerDeTravaille()));
                    dump($this->attchktime);
                    dd($timePos0);
                    return $timePos0;
                    break;
                case 2:
                    if (!$this->congerService->getConger($employer, $date)) {
                        $timePos1 = $this->timeService->generateTime($this->attchktime[1]);
                        if ($timePos0 < $debutPauseDejeuner and $debutPauseDejeuner <= $timePos1 and $timePos1 < $finPauseDejeuner) {
                            dd($timePos1);
                            return $timePos1;
                        } else {
                            dump($timePos0);
                            dump($timePos1);
                            dump($this->attchktime);
                            dump($debutPauseDejeuner);
                            dd($finPauseDejeuner);
                            return $timePos0;
                        }
                    }
                    break;
                case 3:
                    if (!$this->autorisationSortieService->getAutorisation()) {
                        $timePos1 = $this->timeService->generateTime($this->attchktime[1]);
                        $timePos2 = $this->timeService->generateTime($this->attchktime[2]);
                        dump($this->attchktime);
                        if ($timePos0 < $finPauseMatinal
                        and $debutPauseMatinal <= $timePos1 and $timePos1 < $finPauseDejeuner
                        and $debutPauseDejeuner <= $timePos2 and $timePos2 < $finPauseMidi) {
                            $sortie = $timePos2;
                            $sortie->add($this->timeService->dateTimeToDateInterval($this->horaireService->getHeursQuardJournerDeTravaille()));
                            dd($sortie);
                            return $sortie;
                        } elseif ($timePos0 < $finPauseMatinal
                        and $debutPauseMatinal <= $timePos1 and $timePos1 < $finPauseDejeuner
                        and $debutPauseMidi <= $timePos2 and $timePos2 < $heurFinTravaille) {
                            dump($timePos0);
                            dump($timePos1);
                            dump($this->attchktime);
                            dump($debutPauseDejeuner);
                            $sortie = $timePos2;
                            $sortie->sub($this->timeService->dateTimeToDateInterval($this->horaireService->getHeursQuardJournerDeTravaille()));
                            dd($sortie);
                            return $sortie;
                        } elseif ($timePos0 < $finPauseMatinal
                        and $debutPauseDejeuner <= $timePos1 and $timePos1 < $finPauseMidi
                        and $debutPauseMidi <= $timePos2 and $timePos2 < $heurFinTravaille) {
                            dump($timePos0);
                            dump($timePos1);
                            dump($this->attchktime);
                            dump($debutPauseDejeuner);
                            $sortie = $timePos2;
                            $sortie->sub($this->timeService->dateTimeToDateInterval($this->horaireService->getHeursQuardJournerDeTravaille()));
                            dd($sortie);
                            return $sortie;
                        } elseif ($timePos0 < $finPauseMatinal
                        and $debutPauseMatinal <= $timePos1 and $timePos1 < $finPauseDejeuner
                        and $debutPauseDejeuner <= $timePos2 and $timePos2 < $finPauseMidi) {
                            dump($timePos0);
                            dump($timePos1);
                            dump($this->attchktime);
                            dump($debutPauseDejeuner);
                            dd($finPauseDejeuner);
                            return $timePos0;
                        } else {
                            dump($timePos0);
                            dump($timePos1);
                            dump($this->attchktime);
                            dump($debutPauseDejeuner);
                            dd($finPauseDejeuner);
                            return $timePos0;
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
                    }
                    break;
                default:
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
        dd("ff");
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
