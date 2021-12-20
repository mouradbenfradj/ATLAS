<?php

namespace App\Service;

use App\Entity\Dbf;
use App\Entity\Pointage;
use App\Traits\PointageEntityTrait;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class PointageService extends EmployerService
{
    use PointageEntityTrait;
    /**
         * pointage
         *
         * @var Pointage
         */
    private $pointage;
    
    public function __construct(EntityManagerInterface $manager)
    {
        parent::__construct($manager);
    }

    /**
     * dateInDB
     *
     * @return array
     */
    public function getPointageDateInDB(): array
    {
        return array_map(
            fn ($date): string => $date->getDate()->format('Y-m-d'),
            $this->getEmployer()->getPointages()->toArray()
        );
    }

    
}
