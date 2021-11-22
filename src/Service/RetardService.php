<?php

namespace App\Service;

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
     * attchktime variable
     *
     * @var array
     */
    private $attchktime;

    public function __construct(TimeService $timeService)
    {
        $this->timeService = $timeService;
    }

    public function retardMidi(): ?DateTime
    {
        if ($this->attchktime[0]=="") {
            return null;
        }
        $retardMidi = null;
        $heurDebutTravaille = $this->timeService->generateTime($this->horaire->getHeurDebutTravaille()->format('H:i:s'));
       
        $debutPauseMatinal = $this->timeService->generateTime($this->horaire->getDebutPauseMatinal()->format('H:i:s'));
        $finPauseMatinal = $this->timeService->generateTime($this->horaire->getFinPauseMatinal()->format('H:i:s'));
       
        $debutPauseDejeuner = $this->timeService->generateTime($this->horaire->getDebutPauseDejeuner()->format('H:i:s'));
        $finPauseDejeuner = $this->timeService->generateTime($this->horaire->getFinPauseDejeuner()->format('H:i:s'));
       
        $debutPauseMidi = $this->timeService->generateTime($this->horaire->getDebutPauseMidi()->format('H:i:s'));
        $finPauseMidi = $this->timeService->generateTime($this->horaire->getFinPauseMidi()->format('H:i:s'));
        $heurFinTravaille = $this->timeService->generateTime($this->horaire->getHeurFinTravaille()->format('H:i:s'));
        switch (count($this->attchktime)) {
            case 4:
                $atttime = new DateTime($this->attchktime[0]);
                $sortieMidi = new DateTime($this->attchktime[1]);
                $retourDeSortieMidi = new DateTime($this->attchktime[2]);
                dump($this->attchktime);
                dump($debutPauseDejeuner);
                dump($retourDeSortieMidi);
                dump($finPauseDejeuner);
                if ($sortieMidi >= $debutPauseDejeuner and $sortieMidi <= $finPauseDejeuner) {
                    $diffSR =$this->timeService->diffTime($debutPauseDejeuner, $sortieMidi);
                    $debutPauseDejeuner->add($diffSR);
                    $finPauseDejeuner->add($diffSR);
                    if ($retourDeSortieMidi > $finPauseDejeuner) {
                        $retardMidi = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($retourDeSortieMidi, $finPauseDejeuner));
                    }
                } else {
                    dump($this->attchktime);
                    dump($sortieMidi);
                    dump($debutPauseDejeuner);
                    dump($retourDeSortieMidi);
                    dump($finPauseDejeuner);
                    dd("retardServidce2");
                }
                //return $this->retardMidi;
            break;
            case 3:
                
                $atttime = new DateTime($this->attchktime[0]);
                $sortieMidi = new DateTime($this->attchktime[1]);
                $retourDeSortieMidi = new DateTime($this->attchktime[2]);   dump($debutPauseDejeuner);
                dump($sortieMidi);
                dump($finPauseDejeuner);
                dump($sortieMidi);
            dump($retourDeSortieMidi);
            dump($debutPauseDejeuner);
            dump($this->horaire);
            dump($retardMidi);
            dd($this->attchktime);
             break;
            case 2:
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
                $entrer1 = new DateTime($this->attchktime[0]);
                dump($this->attchktime);
                dump($entrer1);
                dump($debutPauseDejeuner);
                dump($finPauseDejeuner);
                dd("retardServidce2");
             break;
        }
        return $retardMidi;
        /*  else {
            dd("dde");
        } */
        $diffPauseMidi = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($debutPauseDejeuner, $finPauseDejeuner));
        if (count($this->attchktime) < 3) {
            $this->retardMidi = null;
            return $this->retardMidi;
        }

        $atttime = new DateTime($this->attchktime[0]);
        $atttims = new DateTime($this->attchktime[1]);
        $atttim3 = new DateTime($this->attchktime[2]);
        $heurDebutTravaille = $this->timeService->generateTime($this->horaire->getHeurDebutTravaille()->format('H:i:s'));
        $debutPauseMatinal = $this->timeService->generateTime($this->horaire->getDebutPauseMatinal()->format('H:i:s'));
        $finPauseMatinal = $this->timeService->generateTime($this->horaire->getFinPauseMatinal()->format('H:i:s'));
        $debutPauseDejeuner = $this->timeService->generateTime($this->horaire->getDebutPauseDejeuner()->format('H:i:s'));
        $finPauseDejeuner = $this->timeService->generateTime($this->horaire->getFinPauseDejeuner()->format('H:i:s'));
        $finPauseMidi = $this->timeService->generateTime($this->horaire->getFinPauseMidi()->format('H:i:s'));
        $heurFinTravaille = $this->timeService->generateTime($this->horaire->getHeurFinTravaille()->format('H:i:s'));
        $diffAS = new DateInterval('PT' . 1 . 'H' . 0 . 'M' . 0 . 'S');

        $diffSR = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($debutPauseDejeuner, $finPauseDejeuner));
        /*  if ($this->autorisationSortie) {
            $diffAS = $this->timeService->diffTime($this->autorisationSortie->getDe(), $this->autorisationSortie->getA());
        } */
        if ($debutPauseMatinal <= $atttime and $atttime <= $finPauseDejeuner and $debutPauseDejeuner <= $atttims and $atttims <= $finPauseMidi and $debutPauseMidi <= $atttim3 and ($atttim3 <= $heurFinTravaille or $atttim3 >= $heurFinTravaille)) {
            dd($this->attchktime);
            $diff = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $atttims));
            if ($diffSR < $diff) {
                return  $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($diff, $diffSR));
            }
            return null;
        //new DateInterval('PT' . $diff->h . 'H' . $diff->i . 'M' . $diff->s . 'S');
            /*     $this->autorisationSortieService->partielConstruct($this->employer, $this->date, $heurDebutTravaille, $debutPauseMatinal, false, false);
                    $this->autorisationSortie = $this->autorisationSortieService->ConstructEntity();
                    $this->employer->addAutorisationSorties($this->autorisationSortie);
                */
        } elseif ($heurDebutTravaille <= $atttime and $atttime <= $finPauseMatinal and $debutPauseDejeuner <= $atttims and $atttims <= $finPauseMidi and $debutPauseMidi <= $atttim3 and ($atttim3 <= $heurFinTravaille or $atttim3 >= $heurFinTravaille)) {
            dd($this->attchktime);
        /*      $this->autorisationSortieService->partielConstruct($this->employer, $this->date, $debutPauseMatinal, $debutPauseDejeuner, false, false);
        $this->autorisationSortie = $this->autorisationSortieService->ConstructEntity();
        $this->employer->addAutorisationSorties($this->autorisationSortie);
        */
        } elseif ($heurDebutTravaille <= $atttime and $atttime <= $finPauseMatinal and $debutPauseMatinal <= $atttims and $atttims <= $finPauseDejeuner and $debutPauseMidi <= $atttim3 and ($atttim3 <= $heurFinTravaille or $atttim3 >= $heurFinTravaille)) {
            dd($this->attchktime);
        /*        $this->autorisationSortieService->partielConstruct($this->employer, $this->date, $finPauseDejeuner, $debutPauseMidi, false, false);
        $this->autorisationSortie = $this->autorisationSortieService->ConstructEntity();
        $this->employer->addAutorisationSorties($this->autorisationSortie);
          */
        } elseif ($heurDebutTravaille <= $atttime and $atttime <= $finPauseMatinal and $debutPauseMatinal <= $atttims and $atttims <= $finPauseDejeuner and $debutPauseDejeuner <= $atttim3 and $atttim3 <= $finPauseMidi) {
            dd($this->attchktime);
        /*      $this->autorisationSortieService->partielConstruct($this->employer, $this->date, $debutPauseMidi, $heurFinTravaille, false, false);
            $this->autorisationSortie = $this->autorisationSortieService->ConstructEntity();
            $this->employer->addAutorisationSorties($this->autorisationSortie);
           */
        } else {
            dd($this->attchktime);
            $diff = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttim3, $atttims));
            if ($diffSR < $diff) {
                return  $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($diff, $diffSR));
            }
            return null;
        }





        if ($this->sortie) {
            dd($this->attchktime);
            $sortie = $this->sortie->format("H:i:s");
        } else {
            dd($this->attchktime);
            $sortie = $heurFinTravaille;
        }
        if ($this->congerPayer and $this->congerPayer->getValider() and $this->congerPayer->getDemiJourner()) {
            dump('CP');
            dump($this->sortie);
            dd($heurFinTravaille);
        } elseif (!$this->congerPayer and $this->autorisationSortie and $this->autorisationSortie->getValider()) {
            dump('AS');
            dd($this->attchktime);
            $as = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($this->autorisationSortie->getDe(), $this->autorisationSortie->getA()));
            dd($as);
            $heurFinTravaille = $this->timeService->diffTime(new DateTime(date('H:i:s', strtotime($heurFinTravaille->format("H:i:s")))), $this->sortie);
            return $this->timeService->dateIntervalToDateTime($heurFinTravaille);
            dump($this->sortie);
            dd($heurFinTravaille);
        } else {
            dd($this->attchktime);
            $heurFinTravaille->add($this->timeService->dateTimeToDateInterval($this->timeService->generateTime($this->horaire->getMargeDuRetard()))->format('H:i:s'));
            if ($heurFinTravaille > $this->sortie) {
                $heurFinTravaille = $this->timeService->diffTime($heurFinTravaille, $this->sortie);
                return $this->timeService->dateIntervalToDateTime($heurFinTravaille);
            }
            dd($this->attchktime);
            return null;
        }
    }


    /**
     * retardEnMinute
     *
     * @return DateTime
     */
    public function retardEnMinute(): DateTime
    {
        if ($this->attchktime[0] =="") {
            return new DateTime("00:00:00");
        }
        $heurDebutTravaille = $this->timeService->generateTime($this->timeService->generateTime($this->horaire->getHeurDebutTravaille()->format('H:i:s'))->format('H:i:s'));
        $heurDebutTravaille->add($this->timeService->dateTimeToDateInterval($this->timeService->generateTime($this->horaire->getMargeDuRetard()->format('H:i:s'))));
        if ($heurDebutTravaille >= $this->entrer) {
            dump($this->attchktime);
            dump($this->entrer);
            dd($heurDebutTravaille);
            $this->retardEnMinute   = null;
            return $this->retardEnMinute;
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
        if ($this->attchktime[0]=="") {
            return null;
        }
        $heurFinTravaille =$this->timeService->generateTime($this->horaire->getHeurFinTravaille()->format('H:i:s'));
        $debutPauseDejeuner =$this->timeService->generateTime($this->horaire->getDebutPauseDejeuner()->format('H:i:s'));
        $finPauseDejeuner =$this->timeService->generateTime($this->horaire->getFinPauseDejeuner()->format('H:i:s'));
        if ($heurFinTravaille > $this->sortie and $this->sortie < $finPauseDejeuner) {
            $heurFinTravaille = $debutPauseDejeuner;
        }
        $heurFinTravaille->add($this->timeService->dateTimeToDateInterval($this->retardEnMinute()));
        $this->sortie->add($this->timeService->dateTimeToDateInterval($this->retardEnMinute()));
        if ($heurFinTravaille > $this->sortie) {
            $this->departAnticiper = $this->timeService->diffTime($heurFinTravaille, $this->sortie);
            dump($this->departAnticiper);
            dd($this->sortie);
            return  $this->timeService->dateIntervalToDateTime($this->departAnticiper);
        }
        $this->departAnticiper = null;
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
}
