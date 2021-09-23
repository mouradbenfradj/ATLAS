<?php

namespace App\DataFixtures;

use App\Entity\Horaire;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class HoraireFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $horaire = new Horaire();
        $horaire->setHoraire("NORMAL");
        $horaire->setDateDebut(new DateTime("2021/01/01"));
        $horaire->setDateFin(new DateTime("2021/12/31"));
        $horaire->setHeurDebutTravaille(new DateTime("08:00:00"));
        $horaire->setHeurFinTravaille(new DateTime("17:30:00"));
        $horaire->setDebutPauseMatinal(new DateTime("10:00:00"));
        $horaire->setFinPauseMatinal(new DateTime("10:15:00"));
        $horaire->setDebutPauseDejeuner(new DateTime("12:00:00"));
        $horaire->setFinPauseDejeuner(new DateTime("13:00:00"));
        $horaire->setDebutPauseMidi(new DateTime("15:45:00"));
        $horaire->setFinPauseMidi(new DateTime("16:00:00"));
        $manager->persist($horaire);

        $horaire = new Horaire();
        $horaire->setHoraire("RAMADAN");
        $horaire->setDateDebut(new DateTime("2021/04/13"));
        $horaire->setDateFin(new DateTime("2021/05/14"));
        $horaire->setHeurDebutTravaille(new DateTime("08:00:00"));
        $horaire->setHeurFinTravaille(new DateTime("14:30:00"));
        $horaire->setDebutPauseMatinal(new DateTime("00:00:00"));
        $horaire->setFinPauseMatinal(new DateTime("00:00:00"));
        $horaire->setDebutPauseDejeuner(new DateTime("00:00:00"));
        $horaire->setFinPauseDejeuner(new DateTime("00:00:00"));
        $horaire->setDebutPauseMidi(new DateTime("00:00:00"));
        $horaire->setFinPauseMidi(new DateTime("00:00:00"));
        $manager->persist($horaire);

        $horaire = new Horaire();
        $horaire->setHoraire("SU");
        $horaire->setDateDebut(new DateTime("2021/07/01"));
        $horaire->setDateFin(new DateTime("2021/08/31"));
        $horaire->setHeurDebutTravaille(new DateTime("08:00:00"));
        $horaire->setHeurFinTravaille(new DateTime("14:30:00"));
        $horaire->setDebutPauseMatinal(new DateTime("00:00:00"));
        $horaire->setFinPauseMatinal(new DateTime("00:00:00"));
        $horaire->setDebutPauseDejeuner(new DateTime("00:00:00"));
        $horaire->setFinPauseDejeuner(new DateTime("00:00:00"));
        $horaire->setDebutPauseMidi(new DateTime("00:00:00"));
        $horaire->setFinPauseMidi(new DateTime("00:00:00"));
        $manager->persist($horaire);

        $manager->flush();
    }
}
