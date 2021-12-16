<?php
namespace App\Service;

use App\Entity\User;
use App\Traits\AbsenceTrait;
use App\Traits\AutorisationSortieTrait;
use App\Traits\CongerTrait;
use Doctrine\ORM\EntityManagerInterface;

class EmployerService extends HoraireService
{
    use AbsenceTrait;
    use CongerTrait;
    use AutorisationSortieTrait;
    /**
     * employer
     *
     * @var User
     */
    private $employer;

    public function __construct(EntityManagerInterface $manager)
    {
        parent::__construct($manager);
    }
    /**
     * getEmployer
     *
     * @return  User
     */
    public function getEmployer()
    {
        return $this->employer;
    }

    /**
     * setEmployer
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
