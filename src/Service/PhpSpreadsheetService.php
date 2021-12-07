<?php
namespace App\Service;

use App\Entity\User;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PhpSpreadsheetService extends XlsxService
{
    public function installXlsxFile(UploadedFile $fileXlsx): User
    {
        $reader = new Xlsx();
        $spreadsheet = $reader->load($fileXlsx);
        $ignoredDay =  array_merge($this->getXlsxDateInDB(), $this->getPointageDateInDB(), $this->getJourFeriers());
        $allSheet = $spreadsheet->getAllSheets();
        foreach ($allSheet as $worksheet) {
            $highestRow = $worksheet->getHighestRow();
            $rows = $worksheet->rangeToArray(
                'A1:O' . $highestRow,
                null,
                true,
                true,
                true
            );
            foreach ($rows as $cols) {
                if ($this->isDate($cols['A']) and $cols['C'] and $cols['D']) {
                    if (!$cols['C'] or  !$cols['D']) {
                        dd($cols['A'], $cols['C'], $cols['D'], $this->isDate($cols['A']));
                    }
                    $this->setDate($this->dateString_d_m_Y_ToDateTime($cols['A']));
                    $this->setHoraire($this->getHoraireForDate($this->getDate()));
                    $this->setEntrer($cols['C']);
                    $this->setSortie($cols['D']);
                    dd(
                        $cols['A'],
                        $this->getDate(),
                        $cols['B'],
                        $this->getHoraire($this->getDate()),
                        $cols['C'],
                        $this->getEntrer(),
                        $cols['D'],
                        $this->getSortie()
                    );
                    $this->setNbrHeursTravailler($cols['E']);
                    $this->setRetardEnMinute($cols['F']);
                    $this->setDepartAnticiper($cols['G']);
                    $this->setRetardMidi($cols['H']);
                    $this->setTotalRetard($cols['I']);
                    $this->setAutorisationSortie($cols['J']);
                    $this->setCongerPayer($cols['K']);
                    $this->setAbsence($cols['L']);
                    $this->setHeurNormalementTravailler($cols['M']);
                    $this->setDiff($cols['N']);
                  
                  
                    dd($this->getDate());
                   
                   
                    if (!in_array($this->getDate()->format('Y-m-d'), $ignoredDay)) {
                        $this->xlsxService->construct($cols, $this->getEmployer());
                        $xlsx = $this->xlsxService->createEntity();
                        $this->getEmployer()->addXlsx($xlsx);
                    }
                }
            }
        }
        /* while ($record = $dbfs->nextRecord()) {
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
        } */
        return $this->getEmployer();
    }
}
