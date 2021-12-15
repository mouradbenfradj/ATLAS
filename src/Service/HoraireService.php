<?php

namespace App\Service;

use App\Entity\Horaire;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class HoraireService extends JourFerierService
{
    /**
    * Horaire
    *
    * @var Horaire
    */
    private $horaire;

    /**
     * NewHoraire
     *
     * @var Horaire
     */
    private $newHoraire;
    /**
     * LastHoraire
     *
     * @var Horaire
     */
    private $lastHoraire;
   

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
     * HoraireName
     *
     * @var string
     */
    private $horaireName;

    public function __construct(EntityManagerInterface $manager)
    {
        parent::__construct($manager)        ;
        $time = new DateTime("00:00:00");
        $this->listhoraires = $this->getManager()->getRepository(Horaire::class)->findAll();
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

    /**
     * getHoraireForDate
     *
     * @param DateTime $date
     * @return Horaire|null
     */
    protected function getHoraireForDate(DateTime $date): ?Horaire
    {
        $this->dateTime = $date;
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
    
    public function getHoraireByHoraireName(): ?Horaire
    {
        $this->horaire = current(
            array_filter(
                array_map(
                    fn ($horaire): ?Horaire => ($horaire->getHoraire() == $this->horaireName) ? $horaire : null,
                    $this->listhoraires
                )
            )
        );
        if ($this->horaire) {
            return $this->horaire;
        }
        return  null;
    }
    protected function getHoraireByDateOrName(DateTime $date, string $horaireName): Horaire
    {
        $this->dateTime = $date;
        $this->horaireName = $horaireName;
        $this->horaire = $this->getHoraireForDate($date);
        if (!$this->horaire) {
            $this->horaire =  $this->getHoraireByHoraireName();
            if (!$this->horaire) {
                dd($this->horaire);
            } else {
                $this->horaire = clone $this->getHoraireByHoraireName();
                $this->horaire->setIdANull();
                $this->horaire->setDateDebut($date);
                $this->horaire->setDateFin(null);
                $this->addHoraireForDate($this->horaire);
            }
        }
        if ($this->lastHoraire && $this->lastHoraire->getHoraire() != $horaireName) {
            $fin = clone $date;
            $this->lastHoraire->setDateFin(date_modify($fin, '-1 day'));
            dd($horaireName, $this->addHoraireForDate($this->horaire), $this->lastHoraire, $this->horaire);
        }
        $this->lastHoraire = $this->horaire;


        return  $this->horaire;
    }

    /**
     * Get horaire
     *
     * @return  Horaire
     */
    public function getHoraire()
    {
        return $this->horaire;
    }

    /**
     * Set horaire
     *
     * @param  Horaire  $horaire  horaire
     *
     * @return  self
     */
    public function setHoraire(Horaire $horaire)
    {
        $this->horaire = $horaire;

        return $this;
    }

    
    public function addHoraireForDate(Horaire $horaire)
    {
        array_push(
            $this->listhoraires,
            $horaire
        );
        return    $this->listhoraires;
    }
}
