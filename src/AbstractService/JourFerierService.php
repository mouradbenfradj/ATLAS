<?php

namespace App\AbstractService;

use DateTime;
use DateInterval;
use App\Entity\JourFerier;
use App\Interface\JoursFerierInterface;
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
     * jourFeriers
     * 
     * @var JourFerier $jourFeriers[]
     */
    private $jourFeriers;

    private $ignoreDay;

    /**
     * __construct
     * 
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->jourFeriers = $this->em->getRepository(JourFerier::class)->findAll();
        $this->ignoreDay = [];
        foreach ($this->jourFeriers as $jf) {
            do {
                array_push($this->ignoreDay,  $jf->getDebut()->format("Y-m-d"));
                $jf->getDebut()->add(new DateInterval('P1D'));
            } while ($jf->getDebut() <= $jf->getFin());
        }
    }
    /**
     * getJourFeriers
     *
     * @param EntityManagerInterface $em
     * @return array
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

    /**
     * jourFerier
     *
     * @return array
     */
    public function jourFerier(): array
    {
        return $this->ignoreDay;
    }
    /**
     * @return string[]
     */
    public function isJourFerier(string $date)
    {
        if (in_array($date, $this->ignoreDay)) {
            return true;
        } else {
            return false;
        }
    }
}
