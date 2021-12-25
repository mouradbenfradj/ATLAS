<?php


namespace App\Implement;

use App\Entity\Dbf;
use App\Interfaces\FileUploaderInterface;
use DateTime;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use XBase\TableReader;

class DbfImpl implements FileUploaderInterface
{
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
}
