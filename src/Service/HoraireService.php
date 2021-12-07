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

    public function getHoraireForDate(DateTime $dateTime): ?Horaire
    {
        $nowDateTime = new DateTime();
        $this->horaire = current(array_filter(array_map(
            fn ($horaire): ?Horaire => $horaire->getDateFin() ? (($horaire->getDateDebut() <= $dateTime and $dateTime  <= $horaire->getDateFin()) ? $horaire : ($horaire->getDateDebut() <= $dateTime and $dateTime  <= $nowDateTime) ? $horaire : null)            )),
            $this->em->getRepository(Horaire::class)->findAll()
        )));
        if ($this->horaire) {
            return $this->horaire;
        }
        return null;
    }
}
