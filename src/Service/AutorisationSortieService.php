<?php
namespace App\Service;

use App\Entity\AutorisationSortie;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class AutorisationSortieService extends CongerService
{
    public function __construct(EntityManagerInterface $manager)
    {
        parent::__construct($manager);
    }
    /**
     * matchAvecUneAutorisationDeSortie
     *
     * @param array $autorisationSorties
     * @param DateTime $date
     * @return boolean
     */
    public function matchAvecUneAutorisationDeSortie(array $autorisationSorties, DateTime $date): bool
    {
        return current(array_filter(array_map(
            fn ($autorisationSortie): bool => ($autorisationSortie->getDebut() <=  $date and  $date  <= $autorisationSortie->getFin()) ? true : false,
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
    public function getAutorisation(DateTime $date): ?AutorisationSortie
    {
        $autorisationSortie =  current(array_filter(array_map(
            fn ($autorisationSortie): ?AutorisationSortie => ($autorisationSortie->getDateAutorisation() <= $date and $date <= $autorisationSortie->getDateAutorisation()) ? $autorisationSortie : null,
            $this->getEmployer()->getAutorisationSorties()->toArray()
        )));
        if ($autorisationSortie) {
            return $autorisationSortie;
        }
        return null;
    }
}
