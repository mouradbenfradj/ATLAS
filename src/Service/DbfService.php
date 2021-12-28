<?php
namespace App\Service;

use App\Entity\Dbf;
use App\Entity\User;
use App\Util\FileInterface;
use DateTime;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class DbfService
{
    const FORMAT_TIME_HI = 'H:i';
    private $dbfFile;

    public function __construct(FileInterface $dbfFile)
    {
        $this->dbfFile = $dbfFile;
    }

    public function upload(User $employer, UploadedFile $file): User
    {
        $dbfs = $this->dbfFile->upload($file);
        while ($record = $dbfs->nextRecord()) {
            $date = DateTime::createFromFormat('d/m/Y H:i:s', $record->get('attdate').' 00:00:00');
            $dbf = new Dbf();
            $dbf->setUserid($record->get('userid'));
            $dbf->setBadgenumbe(intval($record->get('badgenumbe')));
            $dbf->setSsn($record->get('ssn'));
            $dbf->setUsername($record->get('username'));
            $dbf->setAutosch($record->get('autosch'));
            $dbf->setAttdate($date);
            $dbf->setSchid($record->get('schid'));
            $dbf->setClockintim(DateTime::createFromFormat(self::FORMAT_TIME_HI, $record->get('clockintim'))?DateTime::createFromFormat(self::FORMAT_TIME_HI, $record->get('clockintim')):null);
            $dbf->setClockoutti(DateTime::createFromFormat(self::FORMAT_TIME_HI, $record->get('clockoutti'))?DateTime::createFromFormat(self::FORMAT_TIME_HI, $record->get('clockoutti')):null);
            $dbf->setStarttime(DateTime::createFromFormat(self::FORMAT_TIME_HI, $record->get('starttime'))?DateTime::createFromFormat(self::FORMAT_TIME_HI, $record->get('starttime')):null);
            $dbf->setEndtime(DateTime::createFromFormat(self::FORMAT_TIME_HI, $record->get('endtime'))?DateTime::createFromFormat(self::FORMAT_TIME_HI, $record->get('endtime')):null);
            $dbf->setWorkday($record->get('workday'));
            $dbf->setRealworkda($record->get('realworkda'));
            $dbf->setLate(DateTime::createFromFormat(self::FORMAT_TIME_HI, $record->get('late'))?DateTime::createFromFormat(self::FORMAT_TIME_HI, $record->get('late')):null);
            $dbf->setEarly(DateTime::createFromFormat(self::FORMAT_TIME_HI, $record->get('early'))?DateTime::createFromFormat(self::FORMAT_TIME_HI, $record->get('early')):null);
            $dbf->setAbsent($record->get('absent'));
            $dbf->setOvertime(DateTime::createFromFormat(self::FORMAT_TIME_HI, $record->get('overtime'))?DateTime::createFromFormat(self::FORMAT_TIME_HI, $record->get('overtime')):null);
            $dbf->setWorktime(DateTime::createFromFormat(self::FORMAT_TIME_HI, $record->get('worktime'))?DateTime::createFromFormat(self::FORMAT_TIME_HI, $record->get('worktime')):null);
            $dbf->setExceptioni($record->get('exceptioni'));
            $dbf->setMustin($record->get('mustin'));
            $dbf->setMustout($record->get('mustout'));
            $dbf->setDeptid($record->get('deptid'));
            $dbf->setSspedaynor($record->get('sspedaynor'));
            $dbf->setSspedaywee($record->get('sspedaywee'));
            $dbf->setSspedayhol($record->get('sspedayhol'));
            $dbf->setAtttime(DateTime::createFromFormat(self::FORMAT_TIME_HI, $record->get('atttime'))?DateTime::createFromFormat(self::FORMAT_TIME_HI, $record->get('atttime')):null);
            $dbf->setAttchktime(explode(" ", $record->get('attchktime')));
            $dbf->setEmployer($employer);
            $employer->addDbf($dbf);
        }
        return $employer;

        // ... connect to Twitter and send the encoded status
    }
}
