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
}
