<?php

namespace App\Service;

use DateTime;
use DateInterval;
use App\Entity\User;
use App\Entity\Horaire;
use App\Entity\Pointage;
use App\Entity\JourFerier;
use Doctrine\ORM\EntityManagerInterface;

class PointageGenerator
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function fromDbfFile($table, $userId): void
    {
        $user = $this->em->getRepository(User::class)->find($userId);
        $horaires = $this->em->getRepository(Horaire::class)->findAll();
        $jourFerier = $this->em->getRepository(JourFerier::class)->findAll();
        $ignoreDay = [];
        foreach ($jourFerier as $jf) {
            $dt = $jf->getDebut();
            do {
                array_push($ignoreDay, $dt->format("Y-m-d"));
                $dt->add(new DateInterval('P1D'));
            } while ($dt <= $jf->getFin());
        }
        while ($record = $table->nextRecord()) {
            $pointage = new Pointage();
            $pointage->setDate(DateTime::createFromFormat('d/m/Y', $record->attdate));

            if (!in_array($pointage->getDate()->format("Y-m-d"), $ignoreDay)) {
                foreach ($horaires as $horaire) {
                    if ($pointage->getDate() >= $horaire->getDateDebut() and $pointage->getDate() <= $horaire->getDateFin())
                        $pointage->setHoraire($horaire);
                }
                if ($record->starttime != "")
                    $pointage->setEntrer(new DateTime($record->starttime));
                if ($record->endtime != "")
                    $pointage->setSortie(new DateTime($record->endtime));



                $pointage->setTotaleRetard(new DateTime("00:00:00"));
                $pointage->setHeurNormalementTravailler(new DateTime("00:00:00"));
                $pointage->setDiff(new DateTime("00:00:00"));
                $user->addPointage($pointage);
                $this->em->persist($user);
                /*  
                $record->userid;
            $record->badgenumbe;
            $record->ssn;
            $record->username;
            $record->autosch;
            $record->attdate;
            $record->schid;
            $record->clockintim;
            $record->clockoutti;
            $record->;
            $record->;
            $record->workday;
            $record->realworkda;
            $record->late;
            $record->early;
            $record->absent;
            $record->overtime;
            $record->worktime;
            $record->exceptioni;
            $record->mustin;
            $record->mustout;
            $record->deptid;
            $record->sspedaynor;
            $record->sspedaywee;
            $record->sspedayhol;
            $record->atttime;
            $record->attchktime; */
            }
        }
        $this->em->flush();
    }
}
