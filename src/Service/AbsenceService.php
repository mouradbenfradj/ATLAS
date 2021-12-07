<?php
namespace App\Service;

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
}
