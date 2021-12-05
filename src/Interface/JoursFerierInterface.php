<?php

namespace App\Interface;

use Doctrine\ORM\EntityManagerInterface;

interface JoursFerierInterface
{
    /**
     * getJourFeriers function
     *
     * @return array
     */
    public function getJourFeriers(): array;
}
