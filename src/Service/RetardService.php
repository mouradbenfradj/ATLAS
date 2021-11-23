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

    public function __construct(TimeService $timeService)
    {
        $this->timeService = $timeService;
    }
    public function requirement(array $attchktime, Horaire $horaire, DateTime $entrer, dateTime $sortie, ?Conger $conger, ?AutorisationSortie $autorisationSortie)
    {
        $this->attchktime = $attchktime;
        $this->horaire = $horaire;
        $this->entrer = $entrer;
        $this->entrer1 = $attchktime[1]?$this->timeService->generateTime($attchktime[1]):null;
        $this->entrer2 = $attchktime[2]?$this->timeService->generateTime($attchktime[2]):null;
        
        $this->sortie = $sortie;
        $this->conger = $conger;
        $this->autorisationSortie = $autorisationSortie;
        
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
        $heurDebutTravaille =$this->heurDebutTravaille;
        $debutPauseMatinal = $this->debutPauseMatinal;
        $finPauseMatinal = $this->finPauseMatinal;
        $debutPauseDejeuner = $this->debutPauseDejeuner;
        $finPauseDejeuner = $this->finPauseDejeuner;
        $debutPauseMidi = $this->debutPauseMidi;
        $finPauseMidi = $this->finPauseMidi;
        $heurFinTravaille = $this->heurFinTravaille;
        if (count($this->attchktime)<4) {
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
        }
        switch (count($this->attchktime)) {
            case 4:
                if ($this->entrer1 >= $debutPauseDejeuner and $this->entrer1 <= $finPauseDejeuner) {
                    $diffSR =$this->timeService->diffTime($debutPauseDejeuner, $this->entrer1);
                    $debutPauseDejeuner->add($diffSR);
                    $finPauseDejeuner->add($diffSR);
                    if ($this->entrer2 > $finPauseDejeuner) {
                        $this->retardMidi = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($this->entrer2, $finPauseDejeuner));
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
                    }
                } else {
                    $debutPauseDejeuner->add(new DateInterval('PT1H'));
                    $finPauseDejeuner->add(new DateInterval('PT1H'));
                    if ($this->entrer1 >= $debutPauseDejeuner and $this->entrer1 <= $finPauseDejeuner) {
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
                        $diffSR =$this->timeService->diffTime($debutPauseDejeuner, $this->entrer1);
                        $debutPauseDejeuner->add($diffSR);
                        $finPauseDejeuner->add($diffSR);
                        if ($this->entrer2 > $finPauseDejeuner) {
                            $retardMidi = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($this->entrer2, $finPauseDejeuner));
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
                        $diffSR =$this->timeService->diffTime($debutPauseDejeuner, $this->entrer1);
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
                //return $this->retardMidi;
            break;
            case 3:

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
                dd($this->retardMidi);                $atttime = $this->timeService->generateTime($this->attchktime[0]);
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
            dd($this->attchktime);
             break;
            case 2:
                
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
            break;
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
             break;
        }
        return $this->retardMidi;
    }


    /**
     * retardEnMinute
     *
     * @return DateTime
     */
    public function retardEnMinute(): DateTime
    {
        $heurDebutTravaille =$this->heurDebutTravaille;
        $heurDebutTravaille->add($this->timeService->dateTimeToDateInterval($this->margeDuRetard));
        if (count($this->attchktime)<4) {
            dump($this->attchktime);
            dump($this->horaire);
            dump($this->entrer);
            dump($this->sortie);
            dump($this->autorisationSortie);
            dd($heurDebutTravaille);
        }
        if ($heurDebutTravaille >= $this->entrer) {
            $this->retardEnMinute = $this->timeService->dateTimeToDateInterval(new DateTime("00:00:00"));
            return new DateTime("00:00:00");
            //return new DateTime("00:00:00");
        }

        $this->retardEnMinute = $this->timeService->diffTime($heurDebutTravaille, $this->entrer);
        return $this->timeService->dateIntervalToDateTime($this->retardEnMinute);
    }


    /**
     * departAnticiper
     *
     * @return DateTime|null
     */
    public function departAnticiper(): ?DateTime
    {
        $this->retardMidi = null;
        $heurDebutTravaille =$this->heurDebutTravaille;
        $debutPauseMatinal = $this->debutPauseMatinal;
        $finPauseMatinal = $this->finPauseMatinal;
        $debutPauseDejeuner = $this->debutPauseDejeuner;
        $finPauseDejeuner = $this->finPauseDejeuner;
        $debutPauseMidi = $this->debutPauseMidi;
        $finPauseMidi = $this->finPauseMidi;
        $heurFinTravaille = $this->heurFinTravaille;
        if (count($this->attchktime)<4) {
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
        }
        if ($heurFinTravaille > $this->sortie and $this->sortie < $finPauseDejeuner) {
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
            $heurFinTravaille = $debutPauseDejeuner;
        }
        $margeEntrer = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($this->entrer, $this->heurDebutTravaille));
        if ($this->retardEnMinute) {
            $margeEntrer->sub($this->retardEnMinute);
            $heurFinTravaille->add($this->timeService->dateTimeToDateInterval($margeEntrer));
        }

        if ($heurFinTravaille > $this->sortie) {
            dump($this->attchktime);
            dump($this->sortie);
            dump($heurDebutTravaille);
            dd($this->departAnticiper);
            $this->departAnticiper = $this->timeService->diffTime($heurFinTravaille, $this->sortie);
            dump($this->departAnticiper);
            dd($this->sortie);
            return  $this->timeService->dateIntervalToDateTime($this->departAnticiper);
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
