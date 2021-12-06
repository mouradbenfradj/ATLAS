<?php
namespace App\Service;

use App\Entity\User;

class EmployerService
{
    /**
     * employer
     *
     * @var User
     */
    private $emplyer;

    /**
     * Get employer
     *
     * @return  User
     */ 
    public function getEmplyer()
    {
        return $this->emplyer;
    }

    /**
     * Set employer
     *
     * @param  User  $emplyer  employer
     *
     * @return  self
     */ 
    public function setEmplyer(User $emplyer)
    {
        $this->emplyer = $emplyer;

        return $this;
    }
}
