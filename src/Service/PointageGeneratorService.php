<?php

namespace App\Service;

use App\Entity\AutorisationSortie;
use App\Entity\Conger;
use DateTime;
use App\Entity\User;
use App\Entity\Horaire;
use App\Entity\Pointage;
use App\Service\DateService;
use App\Service\JourFerierService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

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
                if (DateTime::createFromFormat('d/m/Y', $ligne['A']) !== false and $horaire) {
                    $dateDbf = $this->dateService->dateDbfToStringY_m_d($ligne['A']);
                    $isJourFerier = $this->jourFerierService->isJourFerier($dateDbf);
                    $inDB = $this->inDB($dateDbf, $user);
                    if (!$isJourFerier and !$inDB) {
                        $pointage = new Pointage();
                        foreach ($ligne as $char => $colomn) {
                            switch ($char) {
                                case 'A':
                                    if ($colomn)
                                        $pointage->setDate(DateTime::createFromFormat('d/m/Y', $colomn));
                                    break;
                                case 'B':
                                    $pointage->setHoraire($this->horaireService->getHoraireForDate($pointage->getDate()));
                                    break;
                                case 'C':
                                    if (DateTime::createFromFormat('H:i:s', $colomn) !== false)
                                        dd(DateTime::createFromFormat('H:i:s',  $colomn));
                                    if (DateTime::createFromFormat('H:i', $colomn) !== false)
                                        $pointage->setEntrer($this->timeService->generateTime($colomn));
                                    break;
                                case 'D':
                                    if (DateTime::createFromFormat('H:i:s', $colomn) !== false)
                                        dd(DateTime::createFromFormat('H:i:s',  $colomn));
                                    if (DateTime::createFromFormat('H:i', $colomn) !== false)
                                        $pointage->setSortie($this->timeService->generateTime($colomn));
                                    break;
                                case 'E':
                                    $this->pointageService->setPointage($pointage);
                                    if ($pointage->getSortie())
                                        $pointage->setNbrHeurTravailler($this->pointageService->nbrHeurTravailler());
                                    else
                                        $pointage->setNbrHeurTravailler(new DateTime("00:00:00"));
                                    break;
                                case 'F':
                                    if ($pointage->getEntrer())
                                        $pointage->setRetardEnMinute($this->pointageService->retardEnMinute());
                                    break;
                                case 'G':
                                    if (DateTime::createFromFormat('H:i:s', $colomn) !== false)
                                        dd(DateTime::createFromFormat('H:i:s',  $colomn));
                                    if (DateTime::createFromFormat('H:i', $colomn) !== false)
                                        $pointage->setDepartAnticiper(new DateTime($colomn));
                                    break;
                                case 'H':
                                    if (DateTime::createFromFormat('H:i:s', $colomn) !== false)
                                        dd(DateTime::createFromFormat('H:i:s',  $colomn));
                                    if (DateTime::createFromFormat('H:i', $colomn) !== false)
                                        $pointage->setRetardMidi($colomn);
                                    break;
                                case 'I':
                                    $pointage->setTotaleRetard($this->pointageService->totalRetard());
                                case 'J':
                                    if (DateTime::createFromFormat('H:i:s', $colomn) !== false)
                                        dd(DateTime::createFromFormat('H:i:s',  $colomn));
                                    if (DateTime::createFromFormat('H:i', $colomn) !== false) {
                                        $autrisationSotie = new AutorisationSortie();
                                        $autrisationSotie->setDateAutorisation($pointage->getDate());
                                        $autrisationSotie->setTime(new DateTime($colomn));
                                        $autrisationSotie->setEmployer($user);
                                        $pointage->setAutorisationSortie($autrisationSotie);
                                    }
                                    break;
                                case 'K':
                                    switch ($colomn) {
                                        case '0.5':
                                            $conger = new Conger();
                                            $conger->setEmployer($user);
                                            $conger->setDebut($pointage->getDate());
                                            $conger->setFin($pointage->getDate());
                                            $conger->setDemiJourner(true);
                                            $pointage->setCongerPayer($conger);
                                            break;
                                        case '1':
                                            $conger = new Conger();
                                            $conger->setEmployer($user);
                                            $conger->setDebut($pointage->getDate());
                                            $conger->setFin($pointage->getDate());
                                            $conger->setDemiJourner(false);
                                            $pointage->setCongerPayer($conger);
                                            break;
                                        default:
                                            break;
                                    }

                                    break;
                                case 'L':
                                    if ($colomn)
                                        $pointage->setAbscence($colomn);
                                    break;
                                case 'M':
                                    $pointage->setHeurNormalementTravailler($this->pointageService->heurNormalementTravailler());
                                    break;
                                case 'N':
                                    $pointage->setDiff($this->pointageService->diff());
                                    break;
                                default:
                                    //dump($ligne[$char]);
                                    break;
                            }
                        }
                        $user->addPointage($pointage);
                    }
                }
            }
        }
        $this->em->persist($user);
        $this->em->flush();
    }
}
