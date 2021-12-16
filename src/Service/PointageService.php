<?php

namespace App\Service;

use App\Entity\Dbf;
use App\Entity\Pointage;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class PointageService extends AutorisationSortieService
{
    use PointageEntityTrait;

    public function calculeNbrHeurTravailler($dbf)
    {
        dd($dbf, $this->pointage);
    }
    

    public function __construct(EntityManagerInterface $manager)
    {
        parent::__construct($manager);
    }

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

    public function dbfConvertToPointage(Dbf $dbf): Pointage
    {
        $this->pointage = new Pointage();
        $this->pointage->setDate($dbf->getAttdate());
        $this->pointage->setHoraire($this->getHoraireForDate($dbf->getAttdate()));
        $this->pointage->setEntrer($dbf->getStarttime());
        $this->pointage->setSortie($dbf->getEndtime());
        $this->pointage->setNbrHeurTravailler($this->calculeNbrHeurTravailler($dbf));
        //$this->pointage->setRetardEnMinute($dbf->getRetardEnMinute());
        //$this->pointage->setDepartAnticiper($dbf->getDepartAnticiper());
        //$this->pointage->setRetardMidi($dbf->getRetardMidi());
        //$this->pointage->setTotaleRetard($dbf->getTotaleRetard());
        //$this->pointage->setAutorisationSortie($dbf->getAutorisation($dbf->getDate()));
        //$this->pointage->setCongerPayer($dbf->getConger($dbf->getDate()));
        //$this->pointage->setAbsence($dbf->getAbsence($dbf->getDate()));
        //$this->pointage->setHeurNormalementTravailler($dbf->getHeurNormalementTravailler());
        //$this->pointage->setDiff($dbf->getDiff());
        $this->pointage->setEmployer($dbf->getEmployer());
        dd($this->pointage, $dbf);
        return $this->pointage;
    }


    
}
