<?php
namespace App\Service;

use App\Entity\Dbf;
use App\Entity\Pointage;
use App\Traits\DbfEntityTrait;
use App\Traits\TableReaderTrait;
use DateTime;

class DbfService extends PointageService
{
    use TableReaderTrait;
    use DbfEntityTrait;
    /**
     * dbf
     *
     * @var Dbf
     */
    private $dbf;


    /**
     * GetDbfDateInDB
     *
     * @return string[]
     */
    public function getDbfDateInDB():array
    {
        return array_map(
            fn ($date): string => $date->getAttdate()->format('Y-m-d'),
            $this->getEmployer()->getDbfs()->toArray()
        );
    }

    /**
     * createEntity
     *
     * @return Dbf
     */
    public function createDbfEntity(): Dbf
    {
        $this->dbf = new Dbf();
        $this->dbf->setUserid($this->userid);
        $this->dbf->setBadgenumbe($this->badgenumbe);
        $this->dbf->setSsn($this->ssn);
        $this->dbf->setUsername($this->username);
        $this->dbf->setAutosch($this->autosch);
        $this->dbf->setAttdate($this->getDate());
        $this->dbf->setSchid($this->schid);
        $this->dbf->setClockintim($this->clockintim);
        $this->dbf->setClockoutti($this->clockoutti);
        $this->dbf->setStarttime($this->getEntrer());
        $this->dbf->setEndtime($this->getSortie());
        $this->dbf->setWorkday($this->workday);
        $this->dbf->setRealworkda($this->realworkda);
        $this->dbf->setLate($this->late);
        $this->dbf->setEarly($this->early);
        $this->dbf->setAbsent($this->absent);
        $this->dbf->setOvertime($this->overtime);
        $this->dbf->setWorktime($this->worktime);
        $this->dbf->setExceptioni($this->exceptioni);
        $this->dbf->setMustin($this->mustin);
        $this->dbf->setMustout($this->mustout);
        $this->dbf->setDeptid($this->deptid);
        $this->dbf->setSspedaynor($this->sspedaynor);
        $this->dbf->setSspedaywee($this->sspedaywee);
        $this->dbf->setSspedayhol($this->sspedayhol);
        $this->dbf->setAtttime($this->atttime);
        $this->dbf->setAttchktime($this->attchktime);
        $this->dbf->setEmployer($this->getEmployer());
        return $this->dbf;
    }

    public function dbfConvertToPointage(Dbf $dbf): Pointage
    {
        $this->pointage = new Pointage();
        $this->pointage->setDate($dbf->getAttdate());
        $this->pointage->setHoraire($this->getHoraireForDate($dbf->getAttdate()));
        $this->pointage->setEntrer($dbf->getStarttime());
        $this->pointage->setSortie($dbf->getEndtime());
        $this->pointage->setNbrHeurTravailler($this->nbrHeurTravailler());
        $this->pointage->setHeurNormalementTravailler($this->heurNormalementTravailler($dbf));
        $this->pointage->setRetardEnMinute($this->retardEnMinute($dbf->getLate()->getTimestamp()));
        $this->pointage->setDepartAnticiper($this->departAnticiper($dbf));
        $this->pointage->setRetardMidi($this->retardMidi(strtotime($dbf->getAttchktime()[0]), strtotime($dbf->getAttchktime()[1]), strtotime($dbf->getAttchktime()[2]), strtotime($dbf->getAttchktime()[3])));
        $this->pointage->setTotaleRetard($this->totaleRetard());
        $this->pointage->setAutorisationSortie($this->getAutorisation($dbf->getEmployer(), $this->pointage->getDate()));
        $this->pointage->setCongerPayer($this->getConger($dbf->getEmployer(), $this->pointage->getDate()));
        $this->pointage->setAbsence($this->getAbsence($dbf->getEmployer(), $this->pointage->getDate()));
        $this->pointage->setDiff($this->diff());
        $this->pointage->setEmployer($dbf->getEmployer());
        return $this->pointage;
    }
      
    public function nbrHeurTravailler():DateTime
    {
        $date = new DateTime();
        $atttime = 0;
        $atttime += $this->dateIntervalToSeconds(date_diff($this->pointage->getEntrer(), $this->pointage->getSortie()));
        return $date->setTimestamp($atttime-$this->sumPauseInSecond());
    }
    public function heurNormalementTravailler(Dbf $dbf):DateTime
    {
        $seconds =date_diff($dbf->getClockoutti(), $dbf->getClockintim());
        $seconds =$this->dateIntervalToSeconds($seconds);
        if ($seconds) {
            $seconds -=$this->sumPauseInSecond();
        }
        $date = new DateTime();
        return $date->setTimestamp($seconds);
    }
    public function retardEnMinute(int $late):DateTime
    {
        $date = new DateTime();
        $retard = 0;
        if ($this->getHoraire()->getMargeDuRetard()->getTimestamp() < $late) {
            $retard =$late- $this->getHoraire()->getMargeDuRetard()->getTimestamp();
        }
        if ($retard) {
            return $date->setTimestamp($retard);
        } else {
            return null;
        }
    }
    public function retardMidi(int $entrer, int $dpd, int $fpd, int $sortie):?DateTime
    {
        if (!$entrer || !$dpd || !$fpd || !$sortie) {
            dd($entrer, $dpd, $fpd, $sortie);
        }
        $intervallePauseDej = $this->getHoraire()->getFinPauseDejeuner()->getTimestamp()- $this->getHoraire()->getDebutPauseDejeuner()->getTimestamp();
        $date = new DateTime();
        $retardMidi = 0;
        if (($fpd-$dpd)>$intervallePauseDej) {
            $retardMidi= $fpd-$dpd;
            dump($date->setTimestamp($entrer), $date->setTimestamp($dpd), $date->setTimestamp($fpd), $date->setTimestamp($sortie));
            dump($date->setTimestamp($retardMidi));
            dd($retardMidi);
        }
    
        if ($retardMidi) {
            return $date->setTimestamp($retardMidi);
        } else {
            return null;
        }
    }
    public function totaleRetard():DateTime
    {
        $date = new DateTime();
        $totaleRetard = 0;
        if ($this->pointage->getRetardEnMinute()) {
            $totaleRetard += $this->pointage->getRetardEnMinute()->getTimestamp();
        }
        if ($this->pointage->getRetardMidi()) {
            dump($totaleRetard);
            $totaleRetard += $this->pointage->getRetardMidi()->getTimestamp();
        }
        if ($this->pointage->getDepartAnticiper()) {
            dump($totaleRetard);
            $totaleRetard += $this->pointage->getDepartAnticiper()->getTimestamp();
        }
        return $date->setTimestamp($totaleRetard);
    }
    public function diff():DateTime
    {
        $date = new DateTime();
        $diff = 0;
        if ($this->pointage->getHeurNormalementTravailler()->getTimestamp() >= $this->pointage->getNbrHeurTravailler()->getTimestamp()) {
            dump($diff);
            $diff +=$this->pointage->getHeurNormalementTravailler()->getTimestamp()- $this->pointage->getNbrHeurTravailler()->getTimestamp();
        } else {
            $diff +=$this->pointage->getNbrHeurTravailler()->getTimestamp()- $this->pointage->getHeurNormalementTravailler()->getTimestamp();
        }
        return $date->setTimestamp($diff);
    }
    public function departAnticiper(Dbf $dbf)
    {
        $date = new DateTime();
        $departAnticiper = 0;
        if ($this->pointage->getNbrHeurTravailler()->getTimestamp() < $this->pointage->getHeurNormalementTravailler()->getTimestamp()) {
            $departAnticiper +=$this->pointage->getHeurNormalementTravailler()->getTimestamp()- $this->pointage->getNbrHeurTravailler()->getTimestamp();
        }
        if ($departAnticiper) {
            return $date->setTimestamp($departAnticiper);
        } else {
            return null;
        }
    }
}
