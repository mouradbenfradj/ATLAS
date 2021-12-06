<?php

namespace App\Service;

use DateInterval;
use App\Entity\JourFerier;
use App\Service\JoursFerierInterface;
use Doctrine\ORM\EntityManagerInterface;

abstract class JourFerierService implements JoursFerierInterface
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
     * @return string[]
     */
    public function getJourFeriers(): array
    {
        $jourFeriers = $this->em->getRepository(JourFerier::class)->findAll();
        $ignoreDay = [];
        foreach ($jourFeriers as $jf) {
            do {
                array_push($ignoreDay,  $jf->getDebut()->format("Y-m-d"));
                $jf->getDebut()->add(new DateInterval('P1D'));
            } while ($jf->getDebut() <= $jf->getFin());
        }
        return $ignoreDay;
    }
}
