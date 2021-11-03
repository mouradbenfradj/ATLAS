<?php

namespace App\Service;

use App\Entity\Conger;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class CongerService
{
    /**
     * em
     *
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * employer
     *
     * @var User
     */
    private $employer;

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
     * getIfConger
     *
     * @param string $date
     * @param User $employer
     * @return Conger|null
     */
    public function getIfConger(string $date, User $employer): ?Conger
    {
        $this->employer = $employer;
        $conger =  $this->em->getRepository(Conger::class)->findOneByEmployerAndDate($date, $this->employer);
        if ($conger and !$conger->getDemiJourner() and $conger->getValider()) {
            $this->employer->setSoldConger($this->employer->getSoldConger() - 1);
        } else if ($conger and $conger->getDemiJourner() and $conger->getValider()) {
            $this->employer->setSoldConger($this->employer->getSoldConger() - 0.5);
        }
        return $conger;
    }

    public function getemployer()
    {
        return $this->employer;
    }
}
