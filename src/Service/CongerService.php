<?php

namespace App\Service;

use DateTime;
use App\Entity\User;
use App\Entity\Conger;
use App\Entity\Abscence;
use App\Service\AbscenceService;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

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






    //private $em;
    /**
     * abscenceService
     *
     * @var AbscenceService
     */
    private $abscenceService;

    /**
     * __construct
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(AbscenceService $abscenceService)
    {
        //EntityManagerInterface $em,
        //$this->em = $em;
        $this->abscenceService = $abscenceService;
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

    public function ConstructEntity(): Conger
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

    public function constructFromAbscence(Abscence $abscence): void
    {
        $this->employer = $abscence->getEmployer();
        $this->debut = $abscence->getDebut();
        $this->fin = $abscence->getFin();
        $this->pointages = $abscence->getPointages()->toArray();
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
        foreach ($this->pointages as $pointage)
            $conger->addPointage($pointage);
        $conger->setType($this->type);
        $conger->setValider($this->valider);
        $conger->setRefuser($this->refuser);
        $conger->setDemiJourner($this->demiJourner);
        return $conger;
    }


    public function findOrCreate(?DateTime $entrer, ?DateTime $sortie): Conger
    {
        $conger = current(array_filter(array_map(
            fn ($conger): ?Conger => ($conger->getDebut() <= $this->debut and $this->fin <= $conger->getFin()) ? $conger : null,
            $this->employer->getCongers()->toArray()
        )));
        if ($conger) {
            return $conger;
        }
        if (!$entrer and !$sortie) {
            $this->partielConstruct($this->employer, $this->debut, $this->fin, "CP", true, false, false);
            return  $this->ConstructEntity();
        } elseif (!$entrer or !$sortie) {
            dd($entrer);
        }            /* elseif (
            (
                ($this->timeService->generateTime($this->horaire->getDebutPauseDejeuner()->format('H:i:s')) <= $atttime
                    and
                    $atttime <= $this->timeService->generateTime($this->horaire->getHeurFinTravaille()->format('H:i:s')))
                or
                (($this->timeService->generateTime($this->horaire->getHeurDebutTravaille()->format('H:i:s')) <= $atttime
                    and
                    $atttime <= $this->timeService->generateTime($this->horaire->getFinPauseDejeuner()->format('H:i:s')))
                    and
                    $this->horaire->getFinPauseDejeuner() <= $this->horaire->getHeurFinTravaille()))
            and $this->horaire->getFinPauseDejeuner() <= $this->horaire->getHeurFinTravaille()
        ) {
            $this->congerService->partielConstruct($this->employer, $this->date, $this->date, "CP", true, false, true);
            $this->congerPayer = $this->congerService->ConstructEntity();
            $this->employer->addConger($this->congerPayer);
        }  */ else {
            dump($entrer);
            dd($sortie);
            return null;
        }
    }


    public function estUnConger(): ?Conger
    {
        $conger = current(array_filter(array_map(
            fn ($conger): ?Conger => ($conger->getDebut() <= $this->debut and $this->fin  <= $conger->getFin()) ? $conger : null,
            $this->employer->getCongers()->toArray()
        )));
        if ($conger)
            return $conger;
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
        if ($conger)
            return $conger;
        return null;
    }




    /**
     * getIfConger
     *
     * @param string $date
     * @param User $employer
     * @return Conger|null
     */
    public function getIfConger(string $date, User $employer): ?Conger
    {
        $this->employer = $employer;
        $conger =  $this->em->getRepository(Conger::class)->findOneByEmployerAndDate($date, $this->employer);
        if ($conger and !$conger->getDemiJourner() and $conger->getValider()) {
            $this->employer->setSoldConger($this->employer->getSoldConger() - 1);
        } else if ($conger and $conger->getDemiJourner() and $conger->getValider()) {
            $this->employer->setSoldConger($this->employer->getSoldConger() - 0.5);
        }
        return $conger;
    }
}
