<?php

namespace App\Service;

use DateTime;
use App\Entity\User;
use App\Entity\Horaire;
use App\Entity\Pointage;
use App\Service\DateService;
use App\Service\JourFerierService;
use Doctrine\ORM\EntityManagerInterface;

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
        HoraireService $horaireService
    ) {
        $this->em = $em;
        $this->jourFerierService = $jourFerierService;
        $this->dateService = $dateService;
        $this->horaireService = $horaireService;
        $this->timeService = $timeService;
        $this->pointageService = $pointageService;
    }
    /**
     * inDB
     * 
     * @param string $dateDbf
     * @param User $user
     * 
     */
    public function inDB(string $dateDbf, User $user)
    {
        return  $this->em->getRepository(Pointage::class)->findOneBy(
            [
                "employer" => $user,
                "date" => new DateTime($dateDbf)
            ]
        );
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
     * @return void
     */
    public function fromXlsxFile($spreadsheet, User $user): void
    {
        $sheetCount = $spreadsheet->getSheetCount();
        for ($i = 0; $i < $sheetCount; $i++) {
            $sheet = $spreadsheet->getSheet($i);
            $sheetData = $sheet->toArray(null, true, true, true);
            foreach ($sheetData as  $ligne) {
                $horaire = $this->em->getRepository(Horaire::class)->findOneBy(["horaire" => $ligne['B']]);
                if ($this->dateService->isDate($ligne['A']) and $horaire) {
                    $dateString = $this->dateService->dateToStringY_m_d($ligne['A']);
                    $isJourFerier = $this->jourFerierService->isJourFerier($dateString);
                    $inDB = $this->inDB($dateString, $user);
                    if (!$isJourFerier and !$inDB)
                        $user = $this->pointageService->addLigne($ligne, $user);
                }
            }
        }
        $this->em->persist($user);
        $this->em->flush();
    }
}
