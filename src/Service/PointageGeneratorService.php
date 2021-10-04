<?php

namespace App\Service;

use DateTime;
use App\Entity\User;
use App\Entity\Horaire;
use App\Entity\Pointage;
use App\Service\DateService;
use App\Service\JourFerierService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class PointageGeneratorService
{

    /**
     * jourFerierService
     *
     * @var JourFerierService
     */
    private $jourFerierService;

    /**
     * em
     *
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var DateService
     */
    private $dateService;

    /**
     * @var TimeService
     */
    private $timeService;
    /**
     * @var HoraireService
     */
    private $horaireService;
    /**
     * @var PointageService
     */
    private $pointageService;
    private $flash;

    /**
     * @param EntityManagerInterface $em
     * @param JourFerierService $jourFerierService
     * @param DateService $dateService
     * @param TimeService $timeService
     * @param HoraireService $horaireService
     * @param PointageService $pointageService
     */
    public function __construct(
        EntityManagerInterface $em,
        PointageService $pointageService,
        JourFerierService $jourFerierService,
        DateService $dateService,
        TimeService $timeService,
        HoraireService $horaireService,
        FlashBagInterface $flash
    ) {
        $this->em = $em;
        $this->jourFerierService = $jourFerierService;
        $this->dateService = $dateService;
        $this->horaireService = $horaireService;
        $this->timeService = $timeService;
        $this->pointageService = $pointageService;
        $this->flash = $flash;
    }
    /**
     * inDB
     * 
     * @param string $dateDbf
     * @param User $user
     * 
     */
    public function dateInDB(User $user)
    {
        return array_map(fn ($value): string => $value->getDate()->format('Y-m-d'), $this->em->getRepository(Pointage::class)->findByEmployer($user));
    }




    /**
     * fromDbfFile
     * 
     * @param  $record
     * 
     * @return Pointage
     */
    public function fromDbfFile($record): Pointage
    {
        $pointage = new Pointage();
        $pointage->setDate($this->dateService->dateDbfToDateTime($record->attdate));
        $pointage->setHoraire($this->horaireService->getHoraireForDate($pointage->getDate()));
        $pointage->setEntrer($this->timeService->generateTime($record->starttime));
        $pointage->setSortie($this->timeService->generateTime($record->endtime));
        $this->pointageService->setPointage($pointage);
        $pointage->setNbrHeurTravailler($this->pointageService->nbrHeurTravailler());
        $pointage->setRetardEnMinute($this->pointageService->retardEnMinute());
        $pointage->setDepartAnticiper(null);
        $pointage->setRetardMidi(null);
        $pointage->setTotaleRetard($this->pointageService->totalRetard());
        $pointage->setAutorisationSortie(null);
        $pointage->setCongerPayer(null);
        $pointage->setAbscence(null);
        $pointage->setHeurNormalementTravailler($this->pointageService->heurNormalementTravailler());
        $pointage->setDiff($this->pointageService->diff());
        return $pointage;
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
                    $record->attchktime; 
                */
    }

    /**
     * fromXlsxFile
     *
     * @param [type] $spreadsheet
     * @param integer $userId
     * @return User
     */
    public function fromXlsxFile($spreadsheet, User $user): User
    {
        $nowDate = new DateTime();
        $horaires = [];
        $arrayDate = $this->dateInDB($user);
        foreach ($this->em->getRepository(Horaire::class)->findAll() as $horaire) {
            $horaires[$horaire->getHoraire()] = $horaire;
        }
        $sheetCount = $spreadsheet->getSheetCount();
        for ($i = 0; $i < $sheetCount; $i++) {
            $sheet = $spreadsheet->getSheet($i);
            $sheetData = $sheet->toArray(null, true, true, true);
            foreach ($sheetData as  $ligne) {
                if ($this->dateService->isDate($ligne['A']) and isset($horaires[$ligne['B']])) {
                    $dateString = $this->dateService->dateToStringY_m_d($ligne['A']);
                    $isJourFerier = $this->jourFerierService->isJourFerier($dateString);
                    $date = $this->dateService->dateString_d_m_Y_ToDateTime($ligne['A']);
                    if (
                        $isJourFerier
                        or
                        $ligne['C'] == 'CP'
                        or
                        $this->timeService->isTimeHi($ligne['C'])
                        or
                        $this->timeService->isTimeHi($ligne['D'])
                        or
                        in_array($ligne['K'], ['1', '1.5'])
                        or
                        $ligne['L']
                        or
                        $nowDate >= $date
                    ) {
                        $horaire = $horaires[$ligne['B']];
                        if (!$isJourFerier and !in_array($dateString, $arrayDate)) {
                            array_push($arrayDate, $dateString);
                            $user = $this->pointageService->addLigne($ligne, $user);
                        }
                    } else
                        $this->flash->add('danger ', 'ignored ligne ' . implode(" | ", $ligne));
                }
            }
        }
        return $user;
    }
}
