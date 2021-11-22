<?php

namespace App\Service;

use App\Entity\AutorisationSortie;
use App\Entity\Conger;
use App\Entity\Horaire;
use DateTime;

class WorkHourService
{
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

    public function __construct(TimeService $timeService, HoraireService $horaireService)
    {
        $this->timeService = $timeService;
        $this->horaireService = $horaireService;
    }
    /**
     * heurNormalementTravailler
     *
     * @return DateTime
     */
    public function heurNormalementTravailler(): DateTime
    {
        if ($this->congerPayer and !$this->congerPayer->getDemiJourner()) {
            return new DateTime('00:00:00');
        } elseif ($this->congerPayer and $this->congerPayer->getDemiJourner()) {
            return $this->horaireService->getHeursDemiJournerDeTravaille();
        } elseif (!$this->congerPayer and $this->autorisationSortie) {
            $heursJournerDeTravaille = $this->heursJournerDeTravaille;
            if ($heursJournerDeTravaille) {
                $heursJournerDeTravaille->sub($this->timeService->diffTime($this->autorisationSortie->getDe(), $this->autorisationSortie->getA()));
            }
            return $heursJournerDeTravaille;
        } else {
            return $this->horaireService->getHeursJournerDeTravaille();
        }
    }

    /**
     * nbrHeurTravailler
     *
     * @return DateTime
     */
    public function nbrHeurTravailler(): DateTime
    {
        $entrer = $this->entrer;
        $sortie = $this->sortie;
        if (!$entrer or !$sortie) {
            $this->nbrHeurTravailler =  new DateTime("00:00:00");
            return $this->nbrHeurTravailler;
        }
        $time = new DateTime($sortie->format("H:i:s"));
        if ($this->congerPayer and $this->congerPayer->getDemiJourner()) {
            if ($this->horaire->getDebutPauseMidi() > $entrer) {
                $time->sub($this->timeService->diffTime(
                    $this->horaire->getDebutPauseMatinal(),
                    $this->horaire->getfinPauseMatinal()
                ));
            } else {
                $time->sub($this->timeService->diffTime(
                    $this->horaire->getDebutPauseMidi(),
                    $this->horaire->getFinPauseMidi()
                ));
            }
        } else {
            $time->sub($this->timeService->dateTimeToDateInterval($this->horaireService->sumPause()));
        }
        $time = $this->timeService->diffTime($time, $entrer);
        $this->nbrHeurTravailler =  $this->timeService->dateIntervalToDateTime($time);
        return $this->nbrHeurTravailler;
    }



    public function diff(): DateTime
    {
        if ($this->nbrHeurTravailler) {
            return $this->timeService->dateIntervalToDateTime(
                $this->timeService->diffTime(
                    $this->nbrHeurTravailler,
                    $this->heurNormalementTravailler()
                )
            );
        } else {
            return $this->heurNormalementTravailler();
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
}
