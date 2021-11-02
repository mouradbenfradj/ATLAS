<?php

namespace App\Service;

use App\Entity\Config;
use App\Entity\Conger;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class CongerService
{
    private $em;
    private $conger;
    /**
     * __construct
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    public function getIfConger(string $date, User $employer)
    {
        $this->conger = $this->em->getRepository(Conger::class)->findOneByEmployerAndDate($date, $employer);
        if ($this->conger) {
            dd($this->conger);
        }
    }
}
