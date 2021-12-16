<?php

namespace App\Service;

use App\Entity\Xlsx;

class XlsxService extends PointageService
{
    /**
     * xlsx
     *
     * @var Xlsx
     */
    private $xlsx;


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
        $xlsx->setNbrHeurTravailler($this->getNbrHeurTravailler());
        $xlsx->setRetardEnMinute($this->getRetardEnMinute());
        $xlsx->setDepartAnticiper($this->getDepartAnticiper());
        $xlsx->setRetardMidi($this->getRetardMidi());
        $xlsx->setTotalRetard($this->getTotaleRetard());
        $xlsx->setAutorisationSortie($this->getAutorisation($this->getDate()));
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
