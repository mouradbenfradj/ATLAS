<?php


use App\Entity\Horaire;
use App\Entity\User;
use DateInterval;
use DateTime;

interface HoraireInterface
{
    public function getHoraireForDate(DateTime $dateTime): ?Horaire;
    public function getHoraireByHoraireName(string $horaireName): ?Horaire;
    public function diffPauseMatinalTime(): DateInterval;
    public function diffPauseDejeunerTime(): DateInterval;
    public function diffPauseMidiTime(): DateInterval;
    public function sumPause();
    public function getHeursQuardJournerDeTravaille();
    public function setHeursQuardJournerDeTravaille(DateTime $HeursQuardJournerDeTravaille);
    public function getHeursDemiJournerDeTravaille();
    public function setHeursDemiJournerDeTravaille(DateTime $HeursDemiJournerDeTravaille);
    public function getHeursJournerDeTravaille();
    public function setHeursJournerDeTravaille(DateTime $HeursJournerDeTravaille);
    public function setWorkTime(array $workTime);
    public function getHoraire();
    public function setHoraire(Horaire $horaire);
}
