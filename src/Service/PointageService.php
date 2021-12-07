<?php
namespace App\Service;

use App\Entity\Pointage;
use DateTime;

class PointageService extends EmployerService
{
    /**
     * pointage
     *
     * @var Pointage
     */
    private $pointage;

    /**
     * date
     *
     * @var DateTime
     */
    private $date;

    /**
     * entrer
     *
     * @var DateTime|null
     */
    private $entrer;

    /**
     * sortie
     *
     * @var DateTime|null
     */
    private $sortie;

    /**
     * nbrHeurTravailler
     *
     * @var DateTime|null
     */
    private $nbrHeurTravailler;

    /**
     * retardEnMinute
     *
     * @var DateTime|null
     */
    private $retardEnMinute;

    /**
     * departAnticiper
     *
     * @var DateTime|null
     */
    private $departAnticiper;

    
    /**
     * retardMidi
     *
     * @var DateTime|null
     */
    private $retardMidi;

    
    /**
     * totaleRetard
     *
     * @var DateTime
     */
    private $totaleRetard;

    
    /**
     * heurNormalementTravailler
     *
     * @var DateTime
     */
    private $heurNormalementTravailler;

    
    /**
     * diff
     *
     * @var DateTime
     */
    private $diff;

    private $employer;
    private $horaire;
    private $congerPayer;
    private $autorisationSortie;
    private $workTime;
    private $absence;

    

    /**
         * dateInDB
         *
         * @return array
         */
    public function getPointageDateInDB(): array
    {
        return array_map(
            fn ($date): string => $date->getDate()->format('Y-m-d'),
            $this->getEmployer()->getPointages()->toArray()
        );
    }
    /**
     * Get pointage
     *
     * @return  Pointage
     */
    public function getPointage()
    {
        return $this->pointage;
    }

    /**
     * Set pointage
     *
     * @param  Pointage  $pointage  pointage
     *
     * @return  self
     */
    public function setPointage(Pointage $pointage)
    {
        $this->pointage = $pointage;

        return $this;
    }

    /**
     * Get date
     *
     * @return  DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set date
     *
     * @param  DateTime  $date  date
     *
     * @return  self
     */
    public function setDate(DateTime $date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get entrer
     *
     * @return  DateTime|null
     */
    public function getEntrer()
    {
        return $this->entrer;
    }

    /**
     * Set entrer
     *
     * @param  DateTime|null  $entrer  entrer
     *
     * @return  self
     */
    public function setEntrer($entrer)
    {
        $this->entrer = $entrer;

        return $this;
    }

    /**
     * Get sortie
     *
     * @return  DateTime|null
     */
    public function getSortie()
    {
        return $this->sortie;
    }

    /**
     * Set sortie
     *
     * @param  DateTime|null  $sortie  sortie
     *
     * @return  self
     */
    public function setSortie($sortie)
    {
        $this->sortie = $sortie;

        return $this;
    }
}


/*

                $this->absenceService->partielConstruct($dbf->getEmployer(), $dbf->getAttdate());
                $this->congerService->partielConstruct($dbf->getEmployer(), $dbf->getAttdate());
                $this->autorisationSortieService->partielConstruct($dbf->getEmployer(), $dbf->getAttdate());
                if (!$this->dateService->isWeek($this->getDate())
                    and (
                        ($dbf->getStarttime() and $dbf->getEndtime())
                        or $this->absenceService->estAbscent()
                        or $this->congerService->estUnConger()
                        or $this->autorisationSortieService->getAutorisation())
                ) {
                    $this->pointageService->constructFromDbf($dbf);
                    $pointage = $this->pointageService->createEntity();
                    $this->getEmployer()->addPointage($pointage);
                } else {
                    $this->getEmployer()->addDbf($dbf);
                }
*/
