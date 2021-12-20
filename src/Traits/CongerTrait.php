<?php
namespace App\Traits;

use App\Entity\Conger;
use App\Entity\User;
use DateTime;

trait CongerTrait
{
    /**
     * matchAvecUnConger
     *
     * @param DateTime $date
     * @return boolean
     */
    public function matchAvecUnConger(DateTime $date): bool
    {
        return current(array_filter(array_map(
            fn ($conger): bool => ($conger->getDebut() <=  $date &&  $date  <= $conger->getFin()) ? true : false,
            $this->getEmployer()->getCongers()->toArray()
        )));
    }
    /**
     * getConger
     *
     * @param DateTime $date
     * @return Conger|null
     */
    public function getConger(User $employer, DateTime $date): ?Conger
    {
        $conger = current(array_filter(array_map(
            fn ($conger): ?Conger => ($conger->getDebut() <= $date && $date  <= $conger->getFin()) ? $conger : null,
            $employer->getCongers()->toArray()
        )));
        if ($conger) {
            return $conger;
        }
        return null;
    }
}
