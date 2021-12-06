<?php


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
                dd($this->getDate());
                $this->setUserid(intval($record->userid));
                $this->setBadgenumbe(intval($record->badgenumbe));
                $this->setFirstName($record->ssn);
                $this->setLastName($record->username);
                $this->setAutosch($record->autosch);
                $this->setAutosch($record->autosch);
                $this->setSchid($record->schid);
                dd($record->schid);
                /*  $this->getHoraireForDate($this->getDate());
                 dd($this->getHoraire());
                 */ $this->setSchid($record->clockintim, $record->clockoutti, $record->starttime, $record->endtime, $record->workday, $record->realworkda, $record->late, $record->early, $record->absent, $record->overtime, $record->worktime, $record->exceptioni, $record->mustin, $record->mustout, $record->deptid, $record->sspedaynor, $record->sspedaywee, $record->sspedayhol, $record->atttime, $record->attchktime);
                
                $this->absenceService->partielConstruct($dbf->getEmployer(), $dbf->getAttdate());
                $this->congerService->partielConstruct($dbf->getEmployer(), $dbf->getAttdate());
                $this->autorisationSortieService->partielConstruct($dbf->getEmployer(), $dbf->getAttdate());
                if (
                    !$this->dateService->isWeek($this->getDate())
                    and (
                        ($dbf->getStarttime() and $dbf->getEndtime())
                        or $this->absenceService->estAbscent()
                        or $this->congerService->estUnConger()
                        or $this->autorisationSortieService->getAutorisation())
                ) {
                    $this->pointageService->constructFromDbf($dbf);
                    $pointage = $this->pointageService->createEntity();
                    $this->getEmployer()->addPointage($pointage);
                } else {
                    $this->getEmployer()->addDbf($dbf);
                }
            }
            dd($this->getDate());
        }
        return $this->getEmployer();
    }
}
