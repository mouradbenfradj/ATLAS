<?php

namespace App\Service;

use DateInterval;
use App\Entity\Dbf;
use App\Entity\User;
use App\Entity\Absence;
use DateTime;

class AbsenceService
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
     * absenceDays
     *
     * @var array
     */
    private $absenceDays;

    public function __construct(CongerService $congerService)
    {
        $this->absenceDays = [];
        $this->congerService = $congerService;
    }


    public function partielConstruct(?User $employer = null, ?DateTime $debut = null, ?DateTime $fin = null, array $pointages = [])
    {
        $this->employer = $employer;
        $this->debut = $debut;
        $this->fin = $fin;
        $this->pointages = $pointages;
    }

    public function constructEntity(): Absence
    {
        $absence = new Absence();
        $absence->setDebut($this->debut);
        $absence->setFin($this->fin);
        $absence->setEmployer($this->employer);
        return $absence;
    }

    public function findOrCreate(?DateTime $entrer, ?DateTime $sortie): ?Absence
    {
        $this->congerService->partielConstruct($this->employer, $this->debut, $this->fin);
        $absence =  current(array_filter(array_map(
            fn ($absence): ?Absence => ($absence->getDebut() <= $this->debut and $this->fin <= $absence->getFin()) ? $absence : null,
            $this->employer->getAbsences()->toArray()
        )));
        if ($absence) {
            return  $absence;
        }
        if (!$entrer and !$sortie and !$this->congerService->estUnConger()) {
            return  $this->constructEntity();
        }
        return null;
    }






    /**
     * absenceEmployer
     *
     * @return array
     */
    public function absenceEmployer(): array
    {
        foreach ($this->employer->getAbsences() as $absence) {
            do {
                array_push($this->absenceDays, $absence->getDebut()->format("Y-m-d"));
                $absence->getDebut()->add(new DateInterval('P1D'));
            } while ($absence->getDebut() <= $absence->getFin());
        }
        return  $this->absenceDays;
    }
    /**
     * getAbsence
     *
     * @param User $user
     * @param DateTime $date
     * @return Absence|null
     */
    public function estAbscent(): ?Absence
    {
        $absence =  current(array_filter(array_map(
            fn ($absence): ?Absence => ($absence->getDebut() <= $this->date and $this->date <= $absence->getFin()) ? $absence : null,
            $this->employer->getAbsences()->toArray()
        )));
        if ($absence) {
            return  $absence;
        }
        return null;
    }
}
