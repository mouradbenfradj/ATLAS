<?php

namespace App\Service;

use DateTime;
use App\Entity\User;
use App\Entity\Conger;
use App\Entity\Absence;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Integer;

class CongerService
{

    /**
     * employer
     *
     * @var User
     */
    private $employer;
    private $debut;
    private $fin;
    private $pointages;
    private $type;
    private $valider;
    private $refuser;
    private $demiJourner;



    /**
     * timeService
     *
     * @var TimeService
     */
    private $timeService;
    /**
     * horaireService
     *
     * @var HoraireService
     */
    private $horaireService;

    /**
     * __construct
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(TimeService $timeService, HoraireService $horaireService)
    {
        //EntityManagerInterface $em,
        //$this->em = $em;
        $this->timeService = $timeService;
        $this->horaireService = $horaireService;
    }
    public function partielConstruct(
        ?User $employer = null,
        ?DateTime $debut = null,
        ?DateTime $fin = null,
        ?string $type = null,
        ?bool $valider = null,
        ?bool $refuser = null,
        ?bool $demiJourner = null,
        ?array $pointages = null
    ) {
        $this->employer = $employer;
        $this->debut = $debut;
        $this->fin = $fin;
        $this->pointages = $pointages;
        $this->type = $type;
        $this->valider = $valider;
        $this->refuser = $refuser;
        $this->demiJourner = $demiJourner;
    }

    public function constructEntity(): Conger
    {
        $conger = new Conger();
        $conger->setDebut($this->debut);
        $conger->setFin($this->fin);
        $conger->setType($this->type);
        $conger->setValider($this->valider);
        $conger->setRefuser($this->refuser);
        $conger->setDemiJourner($this->demiJourner);
        $conger->setEmployer($this->employer);
        return $conger;
    }

    public function constructFromAbsence(Absence $absence): void
    {
        $this->employer = $absence->getEmployer();
        $this->debut = $absence->getDebut();
        $this->fin = $absence->getFin();
        $this->pointages = $absence->getPointages()->toArray();
        $this->type = "CP";
        $this->valider = true;
        $this->refuser = false;
        $this->demiJourner = false;
    }
    public function createEntity(): Conger
    {
        $conger = new Conger();
        $conger->setEmployer($this->employer);
        $conger->setDebut($this->debut);
        $conger->setFin($this->fin);
        foreach ($this->pointages as $pointage) {
            $conger->addPointage($pointage);
        }
        $conger->setType($this->type);
        $conger->setValider($this->valider);
        $conger->setRefuser($this->refuser);
        $conger->setDemiJourner($this->demiJourner);
        return $conger;
    }


    public function findOrCreate(?DateTime $entrer, ?DateTime $sortie): ?Conger
    {
        $conger = current(array_filter(array_map(
            fn ($conger): ?Conger => ($conger->getDebut() <= $this->debut and $this->fin <= $conger->getFin()) ? $conger : null,
            $this->employer->getCongers()->toArray()
        )));
        if ($conger) {
            return $conger;
        }
        $quardJourner = $this->horaireService->getHeursQuardJournerDeTravaille();
        $maxDemiJourner = $this->horaireService->getHeursQuardJournerDeTravaille();
        $demiJourner = $this->horaireService->getHeursDemiJournerDeTravaille();
        $maxDemiJourner->add($this->timeService->dateTimeToDateInterval($demiJourner));
        if (!$entrer and !$sortie) {
            $this->partielConstruct($this->employer, $this->debut, $this->fin, "CP", true, false, false);
            dump($conger);
            dump($entrer);
            dd($sortie);
            return  $this->constructEntity();
        } else {
            $diff = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($entrer, $sortie));
            if ($diff > $quardJourner and $diff < $maxDemiJourner) {
                $this->partielConstruct($this->employer, $this->debut, $this->fin, "CP", true, false, true);
                $conger = $this->constructEntity();
            }
        }
        return  $conger ?  $conger : null;
    }


    public function estUnConger(): ?Conger
    {
        $conger = current(array_filter(array_map(
            fn ($conger): ?Conger => ($conger->getDebut() <= $this->debut and $this->fin  <= $conger->getFin()) ? $conger : null,
            $this->employer->getCongers()->toArray()
        )));
        if ($conger) {
            return $conger;
        }
        return null;
    }

    /**
     * getConger
     *
     * @param User $user
     * @param DateTime $date
     * @return Conger|null
     */
    public function getConger(User $user, DateTime $date): ?Conger
    {
        $conger = current(array_filter(array_map(
            fn ($conger): ?Conger => ($conger->getDebut() <= $date and $date <= $conger->getFin()) ? $conger : null,
            $user->getCongers()->toArray()
        )));
        if ($conger) {
            return $conger;
        }
        return null;
    }
}
