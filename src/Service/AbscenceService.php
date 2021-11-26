<?php

namespace App\Service;

use DateInterval;
use App\Entity\Dbf;
use App\Entity\User;
use App\Entity\Abscence;
use DateTime;

class AbscenceService
{

    /**
     * employer
     *
     * @var User
     */
    private $employer;

    /**
     * debut
     *
     * @var DateTime
     */
    private $debut;

    /**
     * fin
     *
     * @var DateTime
     */
    private $fin;

    /**
     * pointages
     *
     * @var array
     */
    private $pointages;

    /**
     * congerService
     *
     * @var CongerService
     */
    private $congerService;
    
    /**
     * abscenceDays
     *
     * @var array
     */
    private $abscenceDays;

    public function __construct(CongerService $congerService)
    {
        $this->abscenceDays = [];
        $this->congerService = $congerService;
    }


    public function partielConstruct(
        ?User $employer = null,
        ?DateTime $debut = null,
        ?DateTime $fin = null,
        array $pointages = []
    ) {
        $this->employer = $employer;
        $this->debut = $debut;
        $this->fin = $fin;
        $this->pointages = $pointages;
    }

    public function ConstructEntity(): Abscence
    {
        $abscence = new Abscence();
        $abscence->setDebut($this->debut);
        $abscence->setFin($this->fin);
        $abscence->setEmployer($this->employer);
        return $abscence;
    }

    public function findOrCreate(?DateTime $entrer, ?DateTime $sortie): ?Abscence
    {
        $this->congerService->partielConstruct($this->employer, $this->debut, $this->fin);
        $abscence =  current(array_filter(array_map(
            fn ($abscence): ?Abscence => ($abscence->getDebut() <= $this->debut and $this->fin <= $abscence->getFin()) ? $abscence : null,
            $this->employer->getAbscences()->toArray()
        )));
        if ($abscence) {
            return  $abscence;
        }
        if (!$entrer and !$sortie and !$this->congerService->estUnConger()) {
            return  $this->ConstructEntity();
        }
        return null;
    }






    /**
     * abscenceEmployer
     *
     * @return array
     */
    public function abscenceEmployer(): array
    {
        foreach ($this->employer->getAbscences() as $abscence) {
            do {
                array_push($this->abscenceDays, $abscence->getDebut()->format("Y-m-d"));
                $abscence->getDebut()->add(new DateInterval('P1D'));
            } while ($abscence->getDebut() <= $abscence->getFin());
        }
        return  $this->abscenceDays;
    }
    /**
     * getAbscence
     *
     * @param User $user
     * @param DateTime $date
     * @return Abscence|null
     */
    public function estAbscent(): ?Abscence
    {
        $abscence =  current(array_filter(array_map(
            fn ($abscence): ?Abscence => ($abscence->getDebut() <= $this->date and $this->date <= $abscence->getFin()) ? $abscence : null,
            $this->employer->getAbscences()->toArray()
        )));
        if ($abscence) {
            return  $abscence;
        }
        return null;
    }
}
