<?php

namespace App\Service;

use DateTime;
use DateInterval;
use App\Entity\Horaire;
use App\Entity\WorkTime;
use App\Service\TimeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class HoraireService
{
    /**
     * em
     *
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * horaires
     *
     * @var Horaire[]
     */
    private $horaires;

    /**
     * workTime
     *
     * @var WorkTime[]
     */
    private $workTime;

    /**
     * horaire
     *
     * @var Horaire
     */
    private $horaire;

    /**
     * timeService
     *
     * @var TimeService
     */
    private $timeService;

    /**
     * security
     *
     * @var Security
     */
    private $security;

    /**
     * __construct
     *
     * @param EntityManagerInterface $em
     * @param TimeService $timeService
     */
    public function __construct(EntityManagerInterface $em, TimeService $timeService, Security $security)
    {
        $this->em = $em;
        $this->timeService = $timeService;
        $this->horaires = $this->em->getRepository(Horaire::class)->findAll();
        $this->workTime = $this->em->getRepository(WorkTime::class)->findAll();
        $this->security = $security;
    }

    /**
     * @param DateTime $dateTime
     * @return Horaire
     */
    public function getHoraireForDate(DateTime $dateTime): Horaire
    {
        $resultat = reset($this->horaires);
        $resultat = current($this->horaires);
        $trouve = false;
        while ($horaire = next($this->horaires) and !$trouve) {
            if (!$horaire->getDateFin())
                $horaire->setDateFin(new DateTime());
            if ($dateTime >= $horaire->getDateDebut() and $dateTime <= $horaire->getDateFin()) {
                $resultat = $horaire;
                $trouve = true;
            }
        }
        $this->horaire = $resultat;
        $trouve = false;
        $workTime  = reset($this->workTime);
        $workTime = current($this->workTime);
        if ($workTime) {
            if (!$workTime->getDateFin())
                $workTime->setDateFin(new DateTime());
            do {
                if (
                    $dateTime >= $workTime->getDateDebut()
                    and $dateTime <= $workTime->getDateFin()
                    and $this->security->getUser() == $workTime->getEmployer()
                    and $this->horaire == $workTime->getHoraire()
                ) {
                    if ($workTime->getHeurDebutTravaille()) {
                        $this->horaire->setHeurDebutTravaille($workTime->getHeurDebutTravaille());
                    }
                    if ($workTime->getHeurFinTravaille()) {
                        $this->horaire->setHeurFinTravaille($workTime->getHeurFinTravaille());
                    }
                    if ($workTime->getDebutPauseMatinal()) {
                        $this->horaire->setDebutPauseMatinal($workTime->getDebutPauseMatinal());
                    }
                    if ($workTime->getDebutPauseMidi()) {
                        $this->horaire->setDebutPauseMidi($workTime->getDebutPauseMidi());
                    }
                    if ($workTime->getDebutPauseDejeuner()) {
                        $this->horaire->setDebutPauseDejeuner($workTime->getDebutPauseDejeuner());
                    }
                    if ($workTime->getFinPauseMatinal()) {
                        $this->horaire->setFinPauseMatinal($workTime->getFinPauseMatinal());
                    }
                    if ($workTime->getFinPauseMidi()) {
                        $this->horaire->setFinPauseMidi($workTime->getFinPauseMidi());
                    }
                    if ($workTime->getFinPauseDejeuner()) {
                        $this->horaire->setFinPauseDejeuner($workTime->getFinPauseDejeuner());
                    }
                    $trouve = true;
                }
            } while ($workTime = next($this->workTime) and !$trouve);
        }
        return   $this->horaire;
    }





    /**
     *diffPauseMatinalTime
     *
     * @return DateInterval
     */
    public function diffPauseMatinalTime(): DateInterval
    {
        return $this->timeService->diffTime($this->horaire->getFinPauseMatinal(), $this->horaire->getDebutPauseMatinal());
    }

    /**
     * diffPauseDejeunerTime
     *
     * @return DateInterval
     */
    public function diffPauseDejeunerTime(): DateInterval
    {
        return  $this->timeService->diffTime($this->horaire->getFinPauseDejeuner(), $this->horaire->getDebutPauseDejeuner());
    }
    /**
     * diffPauseMidiTime
     *
     * @return DateInterval
     */
    public function diffPauseMidiTime(): DateInterval
    {
        return  $this->timeService->diffTime($this->horaire->getFinPauseMidi(), $this->horaire->getDebutPauseMidi());
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
}
