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
     * abscenceDays
     *
     * @var array
     */
    private $abscenceDays;

    public function __construct()
    {
        $this->abscenceDays = [];
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
    /**
     * abscenceEmployer
     *
     * @param User $user
     * @return array
     */
    public function abscenceEmployer(User $user): array
    {
        foreach ($user->getAbscences() as $abscence) {
            do {
                array_push($this->abscenceDays,  $abscence->getDebut()->format("Y-m-d"));
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
    public function estAbscent(DateTime $date): ?Abscence
    {
        $abscence =  current(array_filter(array_map(
            fn ($abscence): ?Abscence => ($abscence->getDebut() <= $date and $date <= $abscence->getFin()) ? $abscence : null,
            $this->employer->getAbscences()->toArray()
        )));
        if ($abscence)
            return  $abscence;
        return null;
    }
}
