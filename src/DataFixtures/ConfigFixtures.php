<?php

namespace App\DataFixtures;

use App\Entity\Config;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ConfigFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $config = new Config();
        $config->setDebutSoldConger(0);
        $config->setDebutSoldAS(new DateTime("23:00:00"));
        $config->setIncSoldConger(1.9);
        $config->setIncAutorisationSortie(new DateTime("00:00:00"));
        $config->setActive(true);
        $config->setReinitialisationC(false);
        $config->setReinitialisationAS(true);
        $manager->persist($config);

        $manager->flush();
    }
}
