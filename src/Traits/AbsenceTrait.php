<?php
namespace App\Traits;

use App\Entity\Absence;
use App\Entity\User;
use DateTime;

trait AbsenceTrait
{
    /**
     * matchAvecUneAbsence
     *
     * @param DateTime $date
     * @return boolean
     */
    public function matchAvecUneAbsence(DateTime $date): bool
    {
        return current(array_filter(array_map(
            fn ($absence): bool => ($absence->getDebut() <=  $date &&  $date  <= $absence->getFin()) ? true : false,
            $this->getEmployer()->getAbsences()->toArray()
        )));
    }
    /**
     * getAbsence
     *
     * @param DateTime $date
     * @return Absence|null
     */
    public function getAbsence(User $employer, DateTime $date): ?Absence
    {
        $absence =  current(array_filter(array_map(
            fn ($absence): ?Absence => ($absence->getDebut() <= $date && $date <= $absence->getFin()) ? $absence : null,
            $employer->getAbsences()->toArray()
        )));
        if ($absence) {
            return  $absence;
        }
        return null;
    }
}
