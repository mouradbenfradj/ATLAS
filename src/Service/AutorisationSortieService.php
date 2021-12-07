<?php
namespace App\Service;

use DateTime;

class AutorisationSortieService
{
    public function matchAvecUneAutorisationDeSortie(array $autorisationSorties, DateTime $date): bool
    {
        return current(array_filter(array_map(
            fn ($autorisationSortie): bool => ($autorisationSortie->getDebut() <=  $date and  $date  <= $autorisationSortie->getFin()) ? true : false,
            $autorisationSorties
        )));
    }
}
