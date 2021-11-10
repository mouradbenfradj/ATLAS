<?php

namespace App\Service;

use App\Entity\Abscence;
use App\Entity\Conger;
use App\Entity\User;
use DateTime;
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

    public function getemployer()
    {
        return $this->employer;
    }
}
