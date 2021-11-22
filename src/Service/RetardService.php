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
     * @var DateTime
     */
    private $entrer;
    /**
     * sortie variable
     *
     * @var DateTime
     */
    private $sortie;

    /**
     * timeService variable
     *
     * @var TimeService
     */
    private $timeService;
    public function __construct(TimeService $timeService)
    {
        $this->timeService = $timeService;
    }

    public function retardMidi(array $attchktime): ?DateTime
    {
        if (count($attchktime) < 3) {
            $this->retardMidi = null;
            return $this->retardMidi;
        }

        $atttime = new DateTime($attchktime[0]);
        $atttims = new DateTime($attchktime[1]);
        $atttim3 = new DateTime($attchktime[2]);
        $heurDebutTravaille = $this->horaire->getHeurDebutTravaille();
        $debutPauseMatinal = $this->horaire->getDebutPauseMatinal();
        $finPauseMatinal = $this->horaire->getFinPauseMatinal();
        $debutPauseDejeuner = $this->horaire->getDebutPauseDejeuner();
        $finPauseDejeuner = $this->horaire->getFinPauseDejeuner();
        $debutPauseMidi = $this->horaire->getDebutPauseMidi();
        $finPauseMidi = $this->horaire->getFinPauseMidi();
        $heurFinTravaille = $this->horaire->getHeurFinTravaille();
        $diffAS = new DateInterval('PT' . 1 . 'H' . 0 . 'M' . 0 . 'S');

        $diffSR = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($debutPauseDejeuner, $finPauseDejeuner));
        /*  if ($this->autorisationSortie) {
            $diffAS = $this->timeService->diffTime($this->autorisationSortie->getDe(), $this->autorisationSortie->getA());
        } */
        if ($debutPauseMatinal <= $atttime and $atttime <= $finPauseDejeuner and $debutPauseDejeuner <= $atttims and $atttims <= $finPauseMidi and $debutPauseMidi <= $atttim3 and ($atttim3 <= $heurFinTravaille or $atttim3 >= $heurFinTravaille)) {
            dd($attchktime);
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
            dd($attchktime);
            /*      $this->autorisationSortieService->partielConstruct($this->employer, $this->date, $debutPauseMatinal, $debutPauseDejeuner, false, false);
            $this->autorisationSortie = $this->autorisationSortieService->ConstructEntity();
            $this->employer->addAutorisationSorties($this->autorisationSortie);
        */
        } elseif ($heurDebutTravaille <= $atttime and $atttime <= $finPauseMatinal and $debutPauseMatinal <= $atttims and $atttims <= $finPauseDejeuner and $debutPauseMidi <= $atttim3 and ($atttim3 <= $heurFinTravaille or $atttim3 >= $heurFinTravaille)) {
            dd($attchktime);
            /*        $this->autorisationSortieService->partielConstruct($this->employer, $this->date, $finPauseDejeuner, $debutPauseMidi, false, false);
            $this->autorisationSortie = $this->autorisationSortieService->ConstructEntity();
            $this->employer->addAutorisationSorties($this->autorisationSortie);
          */
        } elseif ($heurDebutTravaille <= $atttime and $atttime <= $finPauseMatinal and $debutPauseMatinal <= $atttims and $atttims <= $finPauseDejeuner and $debutPauseDejeuner <= $atttim3 and $atttim3 <= $finPauseMidi) {
            dd($attchktime);
            /*      $this->autorisationSortieService->partielConstruct($this->employer, $this->date, $debutPauseMidi, $heurFinTravaille, false, false);
                $this->autorisationSortie = $this->autorisationSortieService->ConstructEntity();
                $this->employer->addAutorisationSorties($this->autorisationSortie);
           */
        } else {
            dd($attchktime);
            $diff = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttim3, $atttims));
            if ($diffSR < $diff) {
                return  $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($diff, $diffSR));
            }
            return null;
        }





        if ($this->sortie) {
            dd($attchktime);
            $sortie = $this->sortie->format("H:i:s");
        } else {
            dd($attchktime);
            $sortie = $heurFinTravaille;
        }
        if ($this->congerPayer and $this->congerPayer->getValider() and $this->congerPayer->getDemiJourner()) {
            dump('CP');
            dump($this->sortie);
            dd($heurFinTravaille);
        } elseif (!$this->congerPayer and $this->autorisationSortie and $this->autorisationSortie->getValider()) {
            dump('AS');
            dd($attchktime);
            $as = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($this->autorisationSortie->getDe(), $this->autorisationSortie->getA()));
            dd($as);
            $heurFinTravaille = $this->timeService->diffTime(new DateTime(date('H:i:s', strtotime($heurFinTravaille->format("H:i:s")))), $this->sortie);
            return $this->timeService->dateIntervalToDateTime($heurFinTravaille);
            dump($this->sortie);
            dd($heurFinTravaille);
        } else {
            dd($attchktime);
            $heurFinTravaille->add($this->timeService->dateTimeToDateInterval($this->horaire->getMargeDuRetard()));
            if ($heurFinTravaille > $this->sortie) {
                $heurFinTravaille = $this->timeService->diffTime($heurFinTravaille, $this->sortie);
                return $this->timeService->dateIntervalToDateTime($heurFinTravaille);
            }
            dd($attchktime);
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
        $heurDebutTravaille = $this->timeService->generateTime($this->horaire->getHeurDebutTravaille()->format('H:i:s'));
        $heurDebutTravaille->add($this->timeService->dateTimeToDateInterval($this->horaire->getMargeDuRetard()));
        if ($heurDebutTravaille >= $this->entrer) {
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
        $heurDebutTravaille = $this->horaire->getHeurDebutTravaille();
        $finPauseDejeuner = $this->horaire->getFinPauseDejeuner();
        $heurFinTravaille = $this->timeService->generateTime($this->horaire->getHeurFinTravaille()->format('H:i:s'));
        $debutPauseDejeuner = $this->horaire->getDebutPauseDejeuner();
        $heurFinTravaille->add($this->timeService->dateTimeToDateInterval($this->horaire->getMargeDuRetard()));
        $this->sortie->add($this->timeService->dateTimeToDateInterval($this->horaire->getMargeDuRetard()));
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
     * Get sortie variable
     *
     * @return  DateTime
     */
    public function getSortie()
    {
        return $this->sortie;
    }

    /**
     * Set sortie variable
     *
     * @param  DateTime  $sortie  sortie variable
     *
     * @return  self
     */
    public function setSortie(DateTime $sortie)
    {
        $this->sortie = $sortie;

        return $this;
    }

    /**
     * Get entrer variable
     *
     * @return  DateTime
     */
    public function getEntrer()
    {
        return $this->entrer;
    }

    /**
     * Set entrer variable
     *
     * @param  DateTime  $entrer  entrer variable
     *
     * @return  self
     */
    public function setEntrer(DateTime $entrer)
    {
        $this->entrer = $entrer;

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

        return $this;
    }
}
