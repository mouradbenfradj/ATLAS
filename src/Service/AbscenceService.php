<?php

namespace App\Service;

use DateInterval;
use App\Entity\Dbf;
use App\Entity\User;
use App\Entity\Abscence;
use DateTime;

class AbscenceService
{
    private $abscenceDays;
    public function __construct()
    {
        $this->abscenceDays = [];
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
    public function getAbscence(User $user, DateTime $date): ?Abscence
    {
        $abscence =  current(array_filter(array_map(
            fn ($abscence): ?Abscence => ($abscence->getDebut() <= $date and $date <= $abscence->getFin()) ? $abscence : null,
            $user->getAbscences()->toArray()
        )));
        if ($abscence)
            return  $abscence;
        return null;
    }
}
