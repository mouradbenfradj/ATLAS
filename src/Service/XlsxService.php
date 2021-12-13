<?php

namespace App\Service;

use App\Entity\Absence;
use App\Entity\AutorisationSortie;
use App\Entity\Conger;
use App\Entity\Xlsx;
use DateTime;

class XlsxService extends PointageService
{
    /**
     * xlsx
     *
     * @var Xlsx
     */
    private $xlsx;


    /**
     * autorisationSortie
     *
     * @var AutorisationSortie
     */
    private $autorisationSortie;

    /**
     * congerPayer
     *
     * @var Conger
     */
    private $congerPayer;

    /**
     * absence
     *
     * @var Absence
     */
    private $absence;

    /**
     * heurNormalementTravailler
     *
     * @var DateTime|null
     */
    private $heurNormalementTravailler;

    /**
     * diff
     *
     * @var DateTime|null
     */
    private $diff;

    public function getXlsxDateInDB()
    {
        return array_map(
            fn ($date): string => $date->getDate()->format('Y-m-d'),
            $this->getEmployer()->getXlsxes()->toArray()
        );
    }

    public function createXlsxEntity(): Xlsx
    {
        $xlsx = new Xlsx();
        $xlsx->setDate($this->getDate());
        $xlsx->setHoraire($this->getHoraire());
        $xlsx->setEntrer($this->getEntrer());
        $xlsx->setSortie($this->getSortie());
        $xlsx->setNbrHeursTravailler($this->getNbrHeurTravailler());
        $xlsx->setRetardEnMinute($this->getRetardEnMinute());
        $xlsx->setDepartAnticiper($this->getDepartAnticiper());
        $xlsx->setRetardMidi($this->getRetardMidi());
        $xlsx->setTotalRetard($this->getTotaleRetard());
        $xlsx->setAutorisationSortie($this->getAutorisationSortie($this->getDate()));
        $xlsx->setCongerPayer($this->getConger($this->getDate()));
        $xlsx->setAbsence($this->getAbsence($this->getDate()));
        $xlsx->setHeurNormalementTravailler($this->getHeurNormalementTravailler());
        $xlsx->setDiff($this->getDiff());
        $xlsx->setEmployer($this->getEmployer());
        return $xlsx;
    }


    /**
     * Get xlsx
     *
     * @return  Xlsx
     */
    public function getXlsx()
    {
        return $this->xlsx;
    }

    /**
     * Set xlsx
     *
     * @param  Xlsx  $xlsx  xlsx
     *
     * @return  self
     */
    public function setXlsx(Xlsx $xlsx)
    {
        $this->xlsx = $xlsx;

        return $this;
    }
}
