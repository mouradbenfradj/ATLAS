<?php
namespace App\Traits;

use App\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use XBase\TableReader;

trait TableReaderTrait
{
    public function installDbfFile(UploadedFile $fileDbf): User
    {
        $dbfs = new TableReader($fileDbf);
        $ignoredDay =  array_merge($this->getDbfDateInDB(), $this->getPointageDateInDB(), $this->getJourFeriers());
        while ($record = $dbfs->nextRecord()) {
            $this->setDate($this->dateString_d_m_Y_ToDateTime($record->get('attdate')));
            if (!in_array($this->getDate()->format('Y-m-d'), $ignoredDay)) {
                $this->setUserid($record->get('userid'));
                $this->setBadgenumbe($record->get('badgenumbe'));
                $this->setSsn($record->get('ssn'));
                $this->setUsername($record->get('username'));
                $this->setAutosch($record->get('autosch'));
                $this->setSchid($record->get('schid'));
                $this->setClockintim($this->timeStringToDateTime($record->get('clockintim')));
                $this->setClockoutti($this->timeStringToDateTime($record->get('clockoutti')));
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
                $this->setAttchktime(explode(" ", $record->get('attchktime')));
                $this->getEmployer()->addDbf($this->createDbfEntity());
            }
        }
        return $this->getEmployer();
    }
}
