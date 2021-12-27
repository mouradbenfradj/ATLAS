<?php
namespace App\Service;

use App\Entity\User;
use App\Singleton\EmployerSingleton;
use App\Traits\AbsenceTrait;
use App\Traits\AutorisationSortieTrait;
use App\Traits\CongerTrait;
use Doctrine\ORM\EntityManagerInterface;

class EmployerService extends EmployerSingleton
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
    protected function __construct(User $employer)
    {
        $this->employer = $employer;
    }
}
