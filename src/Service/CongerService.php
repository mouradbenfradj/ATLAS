<?php
namespace App\Service;

use App\Entity\Conger;
use DateTime;

class CongerService
{
    public function matchAvecUnConger(array $congers, DateTime $date): bool
    {
        return current(array_filter(array_map(
            fn ($conger): bool => ($conger->getDebut() <=  $date and  $date  <= $conger->getFin()) ? true : false,
            $congers
        )));
    }
    public function getConger(array $congers, DateTime $date): ?Conger
    {
        $conger = current(array_filter(array_map(
            fn ($conger): ?Conger => ($conger->getDebut() <= $date and $date  <= $conger->getFin()) ? $conger : null,
            $congers
        )));
        if ($conger) {
            return $conger;
        }
        return null;
    }
}
