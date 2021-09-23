<?php

namespace App\Service;

use DateTime;
use DateInterval;
use App\Entity\User;
use App\Entity\Horaire;
use App\Entity\Pointage;
use App\Entity\JourFerier;
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
    public function __construct(EntityManagerInterface $em, PointageService $pointageService, JourFerierService $jourFerierService, DateService $dateService, TimeService $timeService, HoraireService $horaireService)
    {
        $this->em = $em;
        $this->jourFerierService = $jourFerierService;
        $this->dateService = $dateService;
        $this->horaireService = $horaireService;
        $this->timeService = $timeService;
        $this->pointageService = $pointageService;
    }

    /**
     * fromDbfFile
     * 
     * @param object $table
     * @param User $user
     * 
     * @return void
     */
    public function fromDbfFile(object $dbfs, User $user): void
    {
        while ($record = $dbfs->nextRecord()) {
            $dateDbf = $this->dateService->dateDbfToStringY_m_d($record->attdate);
            $isJourFerier = $this->jourFerierService->isJourFerier($dateDbf);
            if (!$isJourFerier) {
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
                    $record->attchktime; 
                */
            }
        }
        $this->em->flush();
    }

    /**
     * fromXlsxFile
     *
     * @param [type] $spreadsheet
     * @param integer $userId
     * @return void
     */
    public function fromXlsxFile($spreadsheet, int $userId): void
    {
        $user = $this->em->getRepository(User::class)->find($userId);
        //$horaires = $this->em->getRepository(Horaire::class)->findAll();
        //$jourFerier = $this->em->getRepository(JourFerier::class)->findAll();
        /* $ignoreDay = [];
        foreach ($jourFerier as $jf) {
            $dt = $jf->getDebut();
            do {
                array_push($ignoreDay, $dt->format("Y-m-d"));
                $dt->add(new DateInterval('P1D'));
            } while ($dt <= $jf->getFin());
        } */
        $sheetCount = $spreadsheet->getSheetCount();
        dd($sheetCount);
        for ($i = 0; $i < $sheetCount; $i++) {
            $sheet = $spreadsheet->getSheet($i);
            $sheetData = $sheet->toArray(null, true, true, true);
            foreach ($sheetData as $ligne) {
                $horaire = $this->em->getRepository(Horaire::class)->findOneBy(["horaire" => $ligne['B']]);

                if (DateTime::createFromFormat('d/m/Y', $ligne['A']) !== false && $horaire) {
                    $pointage = new Pointage();
                    foreach ($ligne as $char => $colomn) {
                        switch ($char) {
                            case 'A':
                                if ($colomn)
                                    $pointage->setDate(DateTime::createFromFormat('d/m/Y', $colomn));
                                break;
                            case 'B':
                                $pointage->setHoraire($horaire);
                                break;
                            case 'C':
                                switch ($colomn) {
                                    case 'CP':
                                        $pointage->setCongerPayer(1);
                                        break;
                                    default:
                                        $pointage->setEntrer(new DateTime($colomn));
                                        break;
                                }
                                break;
                            case 'D':
                                switch ($colomn) {
                                    case 'CP':
                                        $pointage->setCongerPayer(1);
                                        break;
                                    default:
                                        $pointage->setSortie(new DateTime($colomn));
                                        break;
                                }
                                break;
                            case 'E':
                                if ($colomn)
                                    try {
                                        $pointage->setNbrHeurTravailler(new DateTime($colomn));
                                    } catch (\Exception $e) {
                                        $time = new DateTime($pointage->getSortie()->format("H:i:s"));
                                        $diff = date_diff($horaire->getFinPauseMatinal(), $horaire->getDebutPauseMatinal());
                                        $time->sub(new DateInterval('PT' . $diff->h . 'H' . $diff->i . 'M' . $diff->s . 'S'));
                                        $diff = date_diff($horaire->getFinPauseDejeuner(), $horaire->getDebutPauseDejeuner());
                                        $time->sub(new DateInterval('PT' . $diff->h . 'H' . $diff->i . 'M' . $diff->s . 'S'));
                                        $diff = date_diff($horaire->getFinPauseMidi(), $horaire->getDebutPauseMidi());
                                        $time->sub(new DateInterval('PT' . $diff->h . 'H' . $diff->i . 'M' . $diff->s . 'S'));
                                        $diff = date_diff($time, $pointage->getEntrer());
                                        $pointage->setNbrHeurTravailler(new DateTime($diff->h . ":" . $diff->i . ":" . $diff->s));
                                    }
                                break;
                            case 'F':
                                if ($colomn)
                                    try {
                                        $pointage->setRetardEnMinute(new DateTime($colomn));
                                    } catch (\Exception $e) {
                                        $time = new DateTime($horaire->getHeurDebutTravaille()->format("H:i:s"));
                                        $time->add(new DateInterval('PT30M'));
                                        $diff = date_diff($pointage->getEntrer(), $time);
                                        $pointage->setRetardEnMinute(new DateTime($diff->h . ":" . $diff->i . ":" . $diff->s));
                                    }
                                break;
                            case 'G':
                                if ($colomn)
                                    $pointage->setDepartAnticiper(new DateTime($colomn));

                                break;
                            case 'H':
                                if ($colomn)
                                    $pointage->setRetardMidi(new DateTime($colomn));
                                break;
                            case 'I':
                                try {
                                    $pointage->setTotaleRetard(new DateTime($colomn));
                                } catch (\Exception $e) {
                                    $pointage->setTotaleRetard(new DateTime("00:00:00"));
                                    $time = new DateTime($pointage->getTotaleRetard()->format("H:i:s"));
                                    if ($pointage->getRetardEnMinute())
                                        $time->add(new DateInterval('PT' . $pointage->getRetardEnMinute()->format('H') . 'H' . $pointage->getRetardEnMinute()->format('i') . 'M' . $pointage->getRetardEnMinute()->format('s') . 'S'));
                                    if ($pointage->getDepartAnticiper())
                                        $time->add(new DateInterval('PT' . $pointage->getDepartAnticiper()->format('H') . 'H' . $pointage->getDepartAnticiper()->format('i') . 'M' . $pointage->getDepartAnticiper()->format('s') . 'S'));
                                    if ($pointage->getRetardMidi())
                                        $time->add(new DateInterval('PT' . $pointage->getRetardMidi()->format('H') . 'H' . $pointage->getRetardMidi()->format('i') . 'M' . $pointage->getRetardMidi()->format('s') . 'S'));
                                    $pointage->setTotaleRetard($time);
                                }
                            case 'J':
                                try {
                                    $pointage->setAutorisationSortie(new DateTime($colomn));
                                } catch (\Exception $e) {
                                    $pointage->setAutorisationSortie(new DateTime('00:00:00'));
                                }
                                break;
                            case 'K':
                                if ($colomn)
                                    $pointage->setCongerPayer($colomn);
                                break;
                            case 'L':
                                if ($colomn)
                                    $pointage->setAbscence($colomn);
                                break;
                            case 'M':
                                try {
                                    $pointage->setHeurNormalementTravailler(new DateTime($colomn));
                                } catch (\Exception $e) {
                                    $time = new DateTime($horaire->getHeurFinTravaille()->format("H:i:s"));
                                    if ($pointage->getAutorisationSortie())
                                        $time->sub(new DateInterval('PT' . $pointage->getAutorisationSortie()->format('H') . 'H' . $pointage->getAutorisationSortie()->format('i') . 'M' . $pointage->getAutorisationSortie()->format('s') . 'S'));
                                    $diff = date_diff($time, $horaire->getHeurDebutTravaille());
                                    $pointage->setHeurNormalementTravailler(new DateTime($diff->h . ":" . $diff->i . ":" . $diff->s));
                                    dd($pointage);
                                }
                                break;
                            case 'N':
                                if ($colomn)
                                    $pointage->setDiff(new DateTime($colomn));
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
        $this->em->persist($user);
        $this->em->flush();
    }
}
