<?php

namespace App\Service;

use App\Entity\AutorisationSortie;
use App\Entity\User;
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
}
