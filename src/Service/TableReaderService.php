<?php
namespace App\Service;

use App\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use XBase\TableReader;

class TableReaderService extends DbfService
{
    public function installDbfFile(UploadedFile $fileDbf): User
    {
        $dbfs = new TableReader($fileDbf);
        $ignoredDay =  array_merge($this->getDbfDateInDB(), $this->getPointageDateInDB(), $this->getJourFeriers());
        while ($record = $dbfs->nextRecord()) {
            $this->setDate($this->dateString_d_m_Y_ToDateTime($record->attdate));
            if (!in_array($this->getDate()->format('Y-m-d'), $ignoredDay)) {
                $this->setUserid($record->userid);
                $this->setBadgenumbe($record->badgenumbe);
                $this->setSsn($record->ssn);
                $this->setUsername($record->username);
                $this->setAutosch($record->autosch);
                $this->setSchid($record->schid);
                $this->setClockintim($this->timeStringToDateTime($record->clockintim));
                $this->setClockoutti($this->timeStringToDateTime($record->clockoutti));
                $this->setEntrer($this->timeStringToDateTime($record->starttime));
                $this->setSortie($this->timeStringToDateTime($record->endtime));
                $this->getWorkday($record->workday);
                $this->setRealworkda($record->realworkda);
                $this->setLate($this->timeStringToDateTime($record->late));
                $this->setEarly($this->timeStringToDateTime($record->early));
                $this->setAbsent($record->absent);
                $this->setOvertime($this->timeStringToDateTime($record->overtime));
                $this->setWorktime($this->timeStringToDateTime($record->worktime));
                $this->setExceptioni($record->exceptioni);
                $this->setMustin($record->mustin);
                $this->setMustout($record->mustout);
                $this->setDeptid($record->deptid);
                $this->setSspedaynor($record->sspedaynor);
                $this->setSspedaywee($record->sspedaywee);
                $this->setSspedayhol($record->sspedayhol);
                $this->setAtttime($this->generateTime($record->atttime));
                $this->setAttchktime(explode(" ", $record->attchktime));
                $this->getEmployer()->addDbf($this->createDbfEntity());
            }
        }
        return $this->getEmployer();
    }
}
