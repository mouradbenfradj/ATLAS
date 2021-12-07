<?php
namespace App\Service;

use App\Entity\Absence;
use DateTime;

class AbsenceService
{
    public function matchAvecUneAbsence(array $absences, DateTime $date): bool
    {
        return current(array_filter(array_map(
            fn ($absence): bool => ($absence->getDebut() <=  $date and  $date  <= $absence->getFin()) ? true : false,
            $absences
        )));
    }
    public function getAbsence(array $absences, DateTime $date): ?Absence
    {
        $absence =  current(array_filter(array_map(
            fn ($absence): ?Absence => ($absence->getDebut() <= $date and $date <= $absence->getFin()) ? $absence : null,
            $absences
        )));
        if ($absence) {
            return  $absence;
        }
        return null;
    }
}
