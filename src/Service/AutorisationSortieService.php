<?php

namespace App\Service;

use App\Entity\AutorisationSortie;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class AutorisationSortieService
{
    /**
     * em
     *
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * __construct
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    /**
     * getIfAutorisationSortie
     *
     * @param string $date
     * @param User $employer
     * @return AutorisationSortie|null
     */
    public function getIfAutorisationSortie(string $date, User $employer): ?AutorisationSortie
    {
        return $this->em->getRepository(AutorisationSortie::class)->findOneByEmployerAndDate($date, $employer);
    }


    public function getAutorisation(User $user, DateTime $date): ?AutorisationSortie
    {
        $autorisationSortie =  current(array_filter(array_map(
            fn ($autorisationSortie): ?AutorisationSortie => ($autorisationSortie->getDateAutorisation() <= $date and $date <= $autorisationSortie->getDateAutorisation()) ? $autorisationSortie : null,
            $user->getAutorisationSorties()->toArray()
        )));
        if ($autorisationSortie)
            return $autorisationSortie;
        return null;
    }
}
