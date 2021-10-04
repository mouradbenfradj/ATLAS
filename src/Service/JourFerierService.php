<?php

namespace App\Service;

use DateInterval;
use App\Entity\JourFerier;
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
            $dt = $jf->getDebut();
            do {
                array_push($this->ignoreDay, $dt->format("Y-m-d"));
                $dt->add(new DateInterval('P1D'));
            } while ($dt <= $jf->getFin());
        }
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
