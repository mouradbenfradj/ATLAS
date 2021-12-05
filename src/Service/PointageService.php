<?php

namespace App\Service;

use App\Entity\User;
use App\Interface\PointageInterface;

class PointageService implements PointageInterface
{
    /**
     * employer
     *
     * @var User
     */
    private $employer;
    /**
     * dateInDB
     *
     * @return array
     */
    public function mentrerLesDateDansLaBaseDeDonner(): array
    {
        //, $this->jourFerierService->jourFerier()
        return array_map(
            fn ($date): string => $date->getDate()->format('Y-m-d'),
            $this->employer->getPointages()->toArray()
        );
    }

    /**
     * Get employer
     *
     * @return  User
     */
    public function getEmployer()
    {
        return $this->employer;
    }

    /**
     * Set employer
     *
     * @param  User  $employer  employer
     *
     * @return  self
     */
    public function setEmployer(User $employer)
    {
        $this->employer = $employer;

        return $this;
    }
}
