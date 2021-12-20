<?php
namespace App\Traits;

use App\Entity\AutorisationSortie;
use App\Entity\User;
use DateTime;

trait AutorisationSortieTrait
{
  
    /**
     * matchAvecUneAutorisationDeSortie
     *
     * @param AutorisationSortie[] $autorisationSorties
     * @param DateTime $date
     * @return bool
     */
    public function matchAvecUneAutorisationDeSortie(array $autorisationSorties, DateTime $date): bool
    {
        return current(array_filter(array_map(
            fn ($autorisationSortie): bool => ($autorisationSortie->getDebut() <=  $date &&  $date  <= $autorisationSortie->getFin()) ? true : false,
            $autorisationSorties
        )));
    }

    /**
     * getAutorisation
     *
     * @param array $autorisationSorties
     * @param DateTime $date
     * @return AutorisationSortie|null
     */
    public function getAutorisation(User $employer, DateTime $date): ?AutorisationSortie
    {
        $autorisationSortie =  current(array_filter(array_map(
            fn ($autorisationSortie): ?AutorisationSortie => ($autorisationSortie->getDateAutorisation() <= $date && $date <= $autorisationSortie->getDateAutorisation()) ? $autorisationSortie : null,
            $employer->getAutorisationSorties()->toArray()
        )));
        if ($autorisationSortie) {
            return $autorisationSortie;
        }
        return null;
    }
}
