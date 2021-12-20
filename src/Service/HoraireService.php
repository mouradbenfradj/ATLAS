<?php

namespace App\Service;

use App\Entity\Horaire;
use App\Traits\HoraireTrait;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class HoraireService extends DateTimeService
{
    use HoraireTrait;

    /**
    * Horaire
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
     * HoraireName
     *
     * @var string
     */
    private $horaireName;

    public function __construct(EntityManagerInterface $manager)
    {
        parent::__construct($manager);
        $this->listhoraires = $this->getManager()->getRepository(Horaire::class)->findAll();
    }

    public function notExistHoraire()
    {
        $time = new DateTime("00:00:00");
        $this->horaire = new Horaire();
        $this->horaire->setDebutPauseDejeuner($time);
        $this->horaire->setDebutPauseMatinal($time);
        $this->horaire->setDebutPauseMidi($time);
        $this->horaire->setFinPauseDejeuner($time);
        $this->horaire->setFinPauseMatinal($time);
        $this->horaire->setFinPauseMidi($time);
        $this->horaire->setHeurDebutTravaille($time);
        $this->horaire->setHeurFinTravaille($time);
        $this->horaire->setMargeDuRetard($time);
    }
    /**
     * GetHoraireForDate
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
                            return ($horaire->getDateDebut() <= $this->dateTime && $this->dateTime  <= $horaire->getDateFin()) ? $horaire : null;
                        } else {
                            $nowDateTime = new DateTime();
                            return ($horaire->getDateDebut() <= $this->dateTime && $this->dateTime <= $nowDateTime) ? $horaire : null;
                        }
                        //  ($horaire->getDateDebut() <= $dateTime && $dateTime  <= $horaire->getDateFin()) ? $horaire : null : ($horaire->getDateDebut() <= $dateTime and $dateTime  <= $nowDateTime) ? $horaire : null,
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
    
    /**
     * GetHoraireByHoraireName
     *
     * @return Horaire|null
     */
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
    /**
     * GetHoraireByDateOrName
     *
     * @param DateTime $date
     * @param string $horaireName
     * @return Horaire
     */
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

    
    public function addHoraireForDate(Horaire $horaire)
    {
        array_push(
            $this->listhoraires,
            $horaire
        );
        return    $this->listhoraires;
    }

    /**
     * SumPause
     *
     * @return int
     */
    public function sumPauseInSecond():int
    {
        $e = $this->diffTime($this->horaire->getFinPauseMatinal(), $this->horaire->getDebutPauseMatinal());
        $e +=$this->diffTime($this->horaire->getFinPauseDejeuner(), $this->horaire->getDebutPauseDejeuner());
        $e += $this->diffTime($this->horaire->getFinPauseMidi(), $this->horaire->getDebutPauseMidi());
        return $e;
    }
}
