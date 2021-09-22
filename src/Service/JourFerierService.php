<?php

namespace App\Service;

use DateTime;
use DateInterval;
use App\Entity\User;
use App\Entity\Horaire;
use App\Entity\Pointage;
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

    /**
     * __construct
     * 
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->jourFeriers = $this->em->getRepository(JourFerier::class)->findAll();
    }

    /**
     * @return string[]
     */
    public function allDates()
    {
        $ignoreDay = [];
        foreach ($this->jourFeriers as $jf) {
            $dt = $jf->getDebut();
            do {
                array_push($ignoreDay, $dt->format("Y-m-d"));
                $dt->add(new DateInterval('P1D'));
            } while ($dt <= $jf->getFin());
        }
        return $ignoreDay;
    }
    /**
     * @return string[]
     */
    public function isJourFerier(string $date)
    {
        $ignoreDay = [];
        foreach ($this->jourFeriers as $jf) {
            $dt = $jf->getDebut();
            do {
                array_push($ignoreDay, $dt->format("Y-m-d"));
                $dt->add(new DateInterval('P1D'));
            } while ($dt <= $jf->getFin());
        }
        if (in_array($date, $ignoreDay)) {
            return true;
        } else {
            return false;
        }
    }
}
