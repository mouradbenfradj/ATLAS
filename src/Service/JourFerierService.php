<?php
namespace App\Service;

use DateInterval;
use App\Entity\JourFerier;
use App\Service\JoursFerierInterface;
use Doctrine\ORM\EntityManagerInterface;

class JourFerierService
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
     * getJourFeriers
     *
     * @return JourFerier[]
     */
    public function getAllJourFeriers(): array
    {
        return $this->em->getRepository(JourFerier::class)->findAll();
    }
}
