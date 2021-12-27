<?php
namespace App\Service;

use App\Entity\Absence;
use App\Entity\AutorisationSortie;
use App\Entity\Conger;
use App\Entity\Dbf;
use App\Entity\Pointage;
use App\Interfaces\FileUploaderInterface;
use App\Traits\DbfEntityTrait;
use App\Traits\TableReaderTrait;
use DateTime;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use XBase\TableReader;

class DbfService extends PointageService implements FileUploaderInterface
{
    use TableReaderTrait;
    use DbfEntityTrait;
    public function upload(UploadedFile $file)
    {
        $dbfs = new TableReader($file);
        $ignoredDay =  array_merge($this->getDbfDateInDB(), $this->getPointageDateInDB($this->getEmployer()), $this->getJourFeriers());

        while ($record = $dbfs->nextRecord()) {
            $this->setDate($this->dateString_d_m_Y_ToDateTime($record->get('attdate')));
            if (!in_array($this->getDate()->format('Y-m-d'), $ignoredDay)) {
                $dbf = new Dbf();
                $dbf->setUserid($record->get('userid'));
                $dbf->setBadgenumbe($record->get('badgenumbe'));
                $dbf->setSsn($record->get('ssn'));
                $dbf->setUsername($record->get('username'));
                $dbf->setAutosch($record->get('autosch'));
                $dbf->setAttdate($record->get('getDate()'));
                $dbf->setSchid($record->get('schid'));
                $dbf->setClockintim((new DateTime())->setTimestamp(strtotime($record->get('clockintim'))));
                $dbf->setClockoutti((new DateTime())->setTimestamp(strtotime($record->get('clockoutti'))));
                $dbf->setStarttime($record->get('getEntrer()'));
                $dbf->setEndtime($record->get('getSortie()'));
                $dbf->setWorkday($record->get('workday'));
                $dbf->setRealworkda($record->get('realworkda'));
                $dbf->setLate((new DateTime())->setTimestamp(strtotime($record->get('late'))));
                $dbf->setEarly((new DateTime())->setTimestamp(strtotime($record->get('early'))));
                $dbf->setAbsent($record->get('absent'));
                $dbf->setOvertime((new DateTime())->setTimestamp(strtotime($record->get('overtime'))));
                $dbf->setWorktime((new DateTime())->setTimestamp(strtotime($record->get('worktime'))));
                $dbf->setExceptioni($record->get('exceptioni'));
                $dbf->setMustin($record->get('mustin'));
                $dbf->setMustout($record->get('mustout'));
                $dbf->setDeptid($record->get('deptid'));
                $dbf->setSspedaynor($record->get('sspedaynor'));
                $dbf->setSspedaywee($record->get('sspedaywee'));
                $dbf->setSspedayhol($record->get('sspedayhol'));
                $dbf->setAtttime($record->get('atttime'));
                $dbf->setAttchktime($record->get('attchktime'));
                /*   $this->setClockoutti($this->timeStringToDateTime($record->get('clockoutti')));
                  $this->setEntrer($this->timeStringToDateTime($record->get('starttime')));
                  $this->setSortie($this->timeStringToDateTime($record->get('endtime')));
                  $this->getWorkday($record->get('workday'));
                  $this->setRealworkda($record->get('realworkda'));
                  $this->setLate($this->timeStringToDateTime($record->get('late')));
                  $this->setEarly($this->timeStringToDateTime($record->get('early')));
                  $this->setAbsent($record->get('absent'));
                  $this->setOvertime($this->timeStringToDateTime($record->get('overtime')));
                  $this->setWorktime($this->timeStringToDateTime($record->get('worktime')));
                  $this->setExceptioni($record->get('exceptioni'));
                  $this->setMustin($record->get('mustin'));
                  $this->setMustout($record->get('mustout'));
                  $this->setDeptid($record->get('deptid'));
                  $this->setSspedaynor($record->get('sspedaynor'));
                  $this->setSspedaywee($record->get('sspedaywee'));
                  $this->setSspedayhol($record->get('sspedayhol'));
                  $this->setAtttime($this->generateTime($record->get('atttime')));
                  $this->setAttchktime(explode(" ", $record->get('attchktime'))); */
            }
        }
        return $this->getEmployer();
    }
    /**
     * Dbf
     *
     * @var Dbf
     */
    private $dbf;


   

    /**
     * CreateEntity
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

    /**
     * DbfConvertToPointage
     *
     * @param Dbf $dbf
     * @param Absence|null $absence
     * @param Conger|null $conger
     * @param AutorisationSortie|null $autorisationSortie
     * @return Pointage
     */
    public function dbfConvertToPointage(Dbf $dbf, ?Absence $absence, ?Conger $conger, ?AutorisationSortie $autorisationSortie): Pointage
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
        $this->pointage->setRetardMidi($this->retardMidi($dbf->getAttchktime()));
        $this->pointage->setTotaleRetard($this->totaleRetard());
        $this->pointage->setAutorisationSortie($autorisationSortie);
        $this->pointage->setCongerPayer($conger);
        $this->pointage->setAbsence($absence);
        $this->pointage->setDiff($this->diff());
        $this->pointage->setEmployer($dbf->getEmployer());
        return $this->pointage;
    }
      
    /**
     * NbrHeurTravailler
     *
     * @return DateTime
     */
    public function nbrHeurTravailler():DateTime
    {
        $date = new DateTime();
        $atttime = 0;
        if (!$this->pointage->getEntrer() ||!$this->pointage->getSortie()) {
            dd($this->pointage->getEntrer(), $this->pointage->getSortie());
        }
        $atttime += $this->dateIntervalToSeconds(date_diff($this->pointage->getEntrer(), $this->pointage->getSortie()));
        return $date->setTimestamp($atttime-$this->sumPauseInSecond());
    }

    /**
     * HeurNormalementTravailler
     *
     * @param Dbf $dbf
     * @return DateTime
     */
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
    /**
     * RetardEnMinute
     *
     * @param integer $late
     * @return DateTime|null
     */
    public function retardEnMinute(int $late):?DateTime
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
    /**
     * RetardMidi
     *
     * @param array $attchktime
     * @return DateTime|null
     */
    public function retardMidi(array $attchktime):?DateTime
    {
        $entrer = $dpd = $fpd  = $sortie = null;
        $intervallePauseDej = $this->getHoraire()->getFinPauseDejeuner()->getTimestamp()- $this->getHoraire()->getDebutPauseDejeuner()->getTimestamp();
        $retardMidi = 0;
      
        switch (count($attchktime)) {
            case 4:
                $entrer =strtotime($attchktime[0]);
        $dpd =strtotime($attchktime[1]);
        $fpd =strtotime($attchktime[2]);
        $sortie =strtotime($attchktime[3]);
        if (($fpd-$dpd)>$intervallePauseDej) {
            $retardMidi= ($fpd-$dpd)-  $intervallePauseDej;
        }
         break;
            case 3:
                 $entrer =strtotime($attchktime[0]);
            $dpd =strtotime($attchktime[1]);
            $fpd =strtotime($attchktime[2]);
            if (($fpd-$dpd)>$intervallePauseDej) {
                $retardMidi= ($fpd-$dpd)-  $intervallePauseDej;
            }
            break;
            case 2:
                $entrer =strtotime($attchktime[0]);
                $dpd =strtotime($attchktime[1]);
         break;
            case 1:
                dd($attchktime);
                $entrer =strtotime($attchktime[0]);
                   break;
            default:
            dd($attchktime);
             break;
        }
        
       
        if ($retardMidi) {
            return new DateTime(date('H:i:s', $retardMidi));
        } else {
            return null;
        }
    }
    /**
     * TotaleRetard
     *
     * @return DateTime
     */
    public function totaleRetard():DateTime
    {
        $date = new DateTime();
        $totaleRetard = 0;
        if ($this->pointage->getRetardEnMinute()) {
            $totaleRetard += $this->pointage->getRetardEnMinute()->getTimestamp();
        }
        if ($this->pointage->getRetardMidi()) {
            $totaleRetard += $this->pointage->getRetardMidi()->getTimestamp();
        }
        if ($this->pointage->getDepartAnticiper()) {
            $totaleRetard += $this->pointage->getDepartAnticiper()->getTimestamp();
        }
        return $date->setTimestamp($totaleRetard);
    }
    /**
     * Diff
     *
     * @return DateTime
     */
    public function diff():DateTime
    {
        $date = new DateTime();
        $diff = 0;
        if ($this->pointage->getHeurNormalementTravailler()->getTimestamp() >= $this->pointage->getNbrHeurTravailler()->getTimestamp()) {
            $diff +=$this->pointage->getHeurNormalementTravailler()->getTimestamp()- $this->pointage->getNbrHeurTravailler()->getTimestamp();
        } else {
            $diff +=$this->pointage->getNbrHeurTravailler()->getTimestamp()- $this->pointage->getHeurNormalementTravailler()->getTimestamp();
        }
        return $date->setTimestamp($diff);
    }
    /**
     * DepartAnticiper
     *
     * @param Dbf $dbf
     * @return DateTime|null
     */
    public function departAnticiper(Dbf $dbf):?DateTime
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
