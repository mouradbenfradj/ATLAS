<?php
namespace App\Service;

use App\Entity\Conger;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class CongerService extends AbsenceService
{
    public function __construct(EntityManagerInterface $manager)
    {
        parent::__construct($manager);
    }
    /**
     * matchAvecUnConger
     *
     * @param DateTime $date
     * @return boolean
     */
    public function matchAvecUnConger(DateTime $date): bool
    {
        return current(array_filter(array_map(
            fn ($conger): bool => ($conger->getDebut() <=  $date and  $date  <= $conger->getFin()) ? true : false,
            $this->getEmployer()->getCongers()->toArray()
        )));
    }
    /**
     * getConger
     *
     * @param DateTime $date
     * @return Conger|null
     */
    public function getConger(DateTime $date): ?Conger
    {
        $conger = current(array_filter(array_map(
            fn ($conger): ?Conger => ($conger->getDebut() <= $date and $date  <= $conger->getFin()) ? $conger : null,
            $this->getEmployer()->getCongers()->toArray()
        )));
        if ($conger) {
            return $conger;
        }
        return null;
    }
}
