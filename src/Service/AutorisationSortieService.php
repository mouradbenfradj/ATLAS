<?php
namespace App\Service;

use App\Entity\AutorisationSortie;
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
    public function getAutorisation(array $autorisationSorties, DateTime $date): ?AutorisationSortie
    {
        $autorisationSortie =  current(array_filter(array_map(
            fn ($autorisationSortie): ?AutorisationSortie => ($autorisationSortie->getDateAutorisation() <= $date and $date <= $autorisationSortie->getDateAutorisation()) ? $autorisationSortie : null,
            $autorisationSorties
        )));
        if ($autorisationSortie) {
            return $autorisationSortie;
        }
        return null;
    }
}
