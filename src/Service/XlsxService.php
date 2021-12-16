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
        $this->xlsx = new Xlsx();
        $this->xlsx->setDate($this->getDate());
        $this->xlsx->setHoraire($this->getHoraire());
        $this->xlsx->setEntrer($this->getEntrer());
        $this->xlsx->setSortie($this->getSortie());
        $this->xlsx->setNbrHeurTravailler($this->getNbrHeurTravailler());
        $this->xlsx->setRetardEnMinute($this->getRetardEnMinute());
        $this->xlsx->setDepartAnticiper($this->getDepartAnticiper());
        $this->xlsx->setRetardMidi($this->getRetardMidi());
        $this->xlsx->setTotalRetard($this->getTotaleRetard());
        $this->xlsx->setAutorisationSortie($this->getAutorisation($this->getDate()));
        $this->xlsx->setCongerPayer($this->getConger($this->getDate()));
        $this->xlsx->setAbsence($this->getAbsence($this->getDate()));
        $this->xlsx->setHeurNormalementTravailler($this->getHeurNormalementTravailler());
        $this->xlsx->setDiff($this->getDiff());
        $this->xlsx->setEmployer($this->getEmployer());
        return $this->xlsx;
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
