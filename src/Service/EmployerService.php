<?php

namespace App\Service;

use App\Entity\User;
use DateTime;
use EmployerInterface;

class EmployerService implements EmployerInterface
{

    /**
     * employer
     *
     * @var User
     */
    private $employer;

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
