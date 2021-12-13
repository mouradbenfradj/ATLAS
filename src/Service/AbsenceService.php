<?php
namespace App\Service;

use App\Entity\Absence;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class AbsenceService extends EmployerService
{
    public function __construct(EntityManagerInterface $manager)
    {
        parent::__construct($manager);
    }
    /**
     * matchAvecUneAbsence
     *
     * @param DateTime $date
     * @return boolean
     */
    public function matchAvecUneAbsence(DateTime $date): bool
    {
        return current(array_filter(array_map(
            fn ($absence): bool => ($absence->getDebut() <=  $date and  $date  <= $absence->getFin()) ? true : false,
            $this->getEmployer()->getAbsences()->toArray()
        )));
    }
    /**
     * getAbsence
     *
     * @param DateTime $date
     * @return Absence|null
     */
    public function getAbsence(DateTime $date): ?Absence
    {
        $absence =  current(array_filter(array_map(
            fn ($absence): ?Absence => ($absence->getDebut() <= $date and $date <= $absence->getFin()) ? $absence : null,
            $this->getEmployer()->getAbsences()->toArray()
        )));
        if ($absence) {
            return  $absence;
        }
        return null;
    }
}
