<?php

namespace App\Service;

use App\Entity\Horaire;
use App\Repository\HoraireRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class HoraireService
{
    /**
     * horaire
     *
     * @var Horaire
     */
    private $horaire;


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
    private $dateTime;
    public function getHoraireForDate(DateTime $dateTime): ?Horaire
    {
        $this->dateTime= $dateTime;
        $this->horaire = current(array_filter(array_map(
            function (Horaire $horaire): ?Horaire {
                if ($horaire->getDateFin()) {
                    return ($horaire->getDateDebut() <= $this->dateTime and $this->dateTime  <= $horaire->getDateFin()) ? $horaire : null ;
                } else {
                    $nowDateTime = new DateTime();
                    dump($horaire->getDateDebut(), $horaire->getDateFin(), $this->dateTime, $nowDateTime);
                    return ($horaire->getDateDebut() <= $this->dateTime and $nowDateTime  <= $horaire->getDateFin()) ? $horaire : null ;
                }
                //  ($horaire->getDateDebut() <= $dateTime and $dateTime  <= $horaire->getDateFin()) ? $horaire : null : ($horaire->getDateDebut() <= $dateTime and $dateTime  <= $nowDateTime) ? $horaire : null,
            },
            $this->em->getRepository(Horaire::class)->findAll()
        )));
        if ($this->horaire) {
            return $this->horaire;
        }
        return null;
    }
}
