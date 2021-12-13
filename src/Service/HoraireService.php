<?php

namespace App\Service;

use App\Entity\Horaire;
use App\Repository\HoraireRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class HoraireService extends EmployerService
{
    /**
     * horaire
     *
     * @var Horaire
     */
    private $newHoraire;
    /**
     * horaire
     *
     * @var Horaire
     */
    private $horaire;


    /**
     * Listhoraires
     *
     * @var Horaire[]
     */
    private $listhoraires;

    /**
     * DateTime
     *
     * @var DateTime
     */
    private $dateTime;

    /**
     * __construct
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $time = new DateTime("00:00:00");
        $this->listhoraires = $em->getRepository(Horaire::class)->findAll();
        $this->newHoraire = new Horaire();
        $this->newHoraire->setDebutPauseDejeuner($time);
        $this->newHoraire->setDebutPauseMatinal($time);
        $this->newHoraire->setDebutPauseMidi($time);
        $this->newHoraire->setFinPauseDejeuner($time);
        $this->newHoraire->setFinPauseMatinal($time);
        $this->newHoraire->setFinPauseMidi($time);
        $this->newHoraire->setHeurDebutTravaille($time);
        $this->newHoraire->setHeurFinTravaille($time);
        $this->newHoraire->setMargeDuRetard($time);
    }

    public function getHoraireForDate(DateTime $dateTime): ?Horaire
    {
        $this->dateTime = $dateTime;
        $this->horaire = current(
            array_filter(
                array_map(
                    function (Horaire $horaire): ?Horaire {
                        if ($horaire->getDateFin()) {
                            return ($horaire->getDateDebut() <= $this->dateTime and $this->dateTime  <= $horaire->getDateFin()) ? $horaire : null;
                        } else {
                            $nowDateTime = new DateTime();
                            return ($horaire->getDateDebut() <= $this->dateTime and $nowDateTime  <= $horaire->getDateFin()) ? $horaire : null;
                        }
                        //  ($horaire->getDateDebut() <= $dateTime and $dateTime  <= $horaire->getDateFin()) ? $horaire : null : ($horaire->getDateDebut() <= $dateTime and $dateTime  <= $nowDateTime) ? $horaire : null,
                    },
                    $this->listhoraires
                )
            )
        );
        if ($this->horaire) {
            return $this->horaire;
        }
        return  null;
    }
    public function addHoraireForDate(Horaire $horaire)
    {
        array_push(
            $this->listhoraires,
            $horaire
        );
        return    $this->listhoraires;
    }
    public function getHoraireByHoraireName(string $horaireName): ?Horaire
    {
        $this->horaire = current(
            array_filter(
                array_map(
                    fn ($horaire): ?Horaire => ($horaire->getHoraire() == $horaireName) ? $horaire : null,
                    $this->listhoraires
                )
            )
        );
        if ($this->horaire) {
            return $this->horaire;
        }
        return  null;
    }
}
