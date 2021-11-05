<?php

namespace App\DataFixtures;

use App\Entity\Conger;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CongerFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        /* $conger = new Conger();
        $conger->setDebut(new DateTime("2021-01-15"));
        $conger->setFin(new DateTime("2021-01-15"));
        /*  $conger->setIncSoldConger(1.9);
        $conger->setIncAutorisationSortie(new DateTime("00:00:00"));
        $conger->setActive(true);
        $conger->setReinitialisationC(false);
        $conger->setReinitialisationAS(true); 
        $manager->persist($conger);

        $manager->flush(); */
    }
}
