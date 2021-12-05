<?php

namespace App\Service;

use App\Entity\User;
use App\Interface\JoursFerierInterface;

class DbfService extends PointageService implements JoursFerierInterface
{
    public function ignoredDay()
    {
        $deteInDDb =  array_map(
            fn ($date): string => $date->getAttdate()->format('Y-m-d'),
            $this->getEmployer()->getDbfs()->toArray()
        );

        return  array_merge($deteInDDb, $this->pointagesDateInDB());
    }
}
