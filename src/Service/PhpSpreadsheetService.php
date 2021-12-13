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
                    if (!in_array($this->getDate()->format('Y-m-d'), $ignoredDay)) {
                        $horaire = $this->getHoraireForDate($this->getDate());
                        if ($horaire) {
                            $this->setHoraire($this->getHoraireForDate($this->getDate()));
                            //$this->setHoraire($this->getHoraireByDateOrName($this->getDate(), $cols['B']));
                            $this->setEntrer($this->timeStringToDateTime($cols['C']));
                            $this->setSortie($this->timeStringToDateTime($cols['D']));
                            $this->setNbrHeurTravailler($this->generateTime($cols['E']));
                            $this->setRetardEnMinute($this->timeStringToDateTime($cols['F']));
                            $this->setDepartAnticiper($this->timeStringToDateTime($cols['G']));
                            $this->setRetardMidi($cols['H']);
                            $this->setTotaleRetard($this->generateTime($cols['I']));
                            $this->setHeurNormalementTravailler($this->generateTime($cols['M']));
                            $this->setDiff($this->diffTime($this->getHeurNormalementTravailler(), $this->getNbrHeurTravailler()));
                            //$this->xlsxService->construct($cols, $this->getEmployer());
                            $xlsx = $this->createXlsxEntity();
                            $this->getEmployer()->addXlsx($xlsx);
                        } else {
                            $this->addFlash('danger', 'pas d\'horaire '. $cols['B'].' enregistrer pour la date '.$cols['A']);
                            //dd($this->getDate(), $cols['B'], $cols['A']);
                        }
                    }
                }
            }
        }
        return $this->getEmployer();
    }
}
