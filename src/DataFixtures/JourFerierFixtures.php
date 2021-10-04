<?php

namespace App\DataFixtures;

use App\Entity\JourFerier;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class JourFerierFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $jourferier = new JourFerier();
        $jourferier->setNom("Jour de l'an");
        $jourferier->setDebut(new DateTime("2021/01/01"));
        $jourferier->setFin(new DateTime("2021/01/01"));
        $manager->persist($jourferier);
        $jourferier = new JourFerier();
        $jourferier->setNom("Fête de la révolution");
        $jourferier->setDebut(new DateTime("2021/01/14"));
        $jourferier->setFin(new DateTime("2021/01/14"));
        $manager->persist($jourferier);
        $jourferier = new JourFerier();
        $jourferier->setNom("Fête de l'indépendance");
        $jourferier->setDebut(new DateTime("2021/03/20"));
        $jourferier->setFin(new DateTime("2021/03/20"));
        $manager->persist($jourferier);
        $jourferier = new JourFerier();
        $jourferier->setNom("Un pont");
        $jourferier->setDebut(new DateTime("2021/04/08"));
        $jourferier->setFin(new DateTime("2021/04/08"));
        $manager->persist($jourferier);
        $jourferier = new JourFerier();
        $jourferier->setNom("Fête des Martyrs");
        $jourferier->setDebut(new DateTime("2021/04/09"));
        $jourferier->setFin(new DateTime("2021/04/09"));
        $manager->persist($jourferier);
        $jourferier = new JourFerier();
        $jourferier->setNom("Fête du travaille");
        $jourferier->setDebut(new DateTime("2021/05/01"));
        $jourferier->setFin(new DateTime("2021/05/01"));
        $manager->persist($jourferier);
        $jourferier = new JourFerier();
        $jourferier->setNom("Fête du Aid al-Fitr");
        $jourferier->setDebut(new DateTime("2021/05/13"));
        $jourferier->setFin(new DateTime("2021/05/15"));
        $manager->persist($jourferier);
        $jourferier = new JourFerier();
        $jourferier->setNom("Fête du Aid al-Adha");
        $jourferier->setDebut(new DateTime("2021/07/20"));
        $jourferier->setFin(new DateTime("2021/07/21"));
        $manager->persist($jourferier);
        $jourferier = new JourFerier();
        $jourferier->setNom("Journée de la républic");
        $jourferier->setDebut(new DateTime("2021/07/25"));
        $jourferier->setFin(new DateTime("2021/07/25"));
        $manager->persist($jourferier);
        $jourferier = new JourFerier();
        $jourferier->setNom("Fête du Ras El Am El Hijri");
        $jourferier->setDebut(new DateTime("2021/08/09"));
        $jourferier->setFin(new DateTime("2021/08/09"));
        $manager->persist($jourferier);
        $jourferier = new JourFerier();
        $jourferier->setNom("Fête de la femme");
        $jourferier->setDebut(new DateTime("2021/08/13"));
        $jourferier->setFin(new DateTime("2021/08/13"));
        $manager->persist($jourferier);
        $jourferier = new JourFerier();
        $jourferier->setNom("Un pont");
        $jourferier->setDebut(new DateTime("2021/09/17"));
        $jourferier->setFin(new DateTime("2021/09/17"));
        $manager->persist($jourferier);
        $jourferier = new JourFerier();
        $jourferier->setNom("Fête de l'évacuation");
        $jourferier->setDebut(new DateTime("2021/10/15"));
        $jourferier->setFin(new DateTime("2021/10/15"));
        $manager->persist($jourferier);
        $jourferier = new JourFerier();
        $jourferier->setNom("Mouled");
        $jourferier->setDebut(new DateTime("2021/10/18"));
        $jourferier->setFin(new DateTime("2021/10/18"));
        $manager->persist($jourferier);

        $manager->flush();
    }
}
