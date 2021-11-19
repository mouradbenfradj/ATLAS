<?php

namespace App\Service;

use App\Entity\User;

class EmployerService
{
    private $configService;
    
    public function __construct(ConfigService $configService)
    {
        $this->configService = $configService;
    }
    public function calculerSoldConger(User $employer)
    {
        dd($employer);
    }
    public function calculerAS(User $employer)
    {
        dd($employer);
    }
}
