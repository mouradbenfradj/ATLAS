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
                dd($pointage);
                $pointage->setRetardEnMinute();
                $pointage->setDepartAnticiper();
                $pointage->setRetardMidi();
                $pointage->setTotaleRetard();
                $pointage->setAutorisationSortie();
                $pointage->setCongerPayer();
                $pointage->setAbscence();
                $pointage->setHeurNormalementTravailler(new DateTime($diff->h . ":" . $diff->i . ":" . $diff->s));
                $pointage->diff();


                dump($horaire);
                $diff = date_diff($horaire->getHeurFinTravaille(), $horaire->getHeurDebutTravaille());
                dump($diff);











                $diff = date_diff($pointage->getEntrer(), $pointage->getSortie());
                dump($diff);
                $pointage->setTotaleRetard(new DateTime($diff->h . ":" . $diff->i . ":" . $diff->s));

                dd($pointage);



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

        for ($i = 0; $i < $sheetCount; $i++) {
            $sheet = $spreadsheet->getSheet($i);
            $sheetData = $sheet->toArray(null, true, true, true);
            foreach ($sheetData as $ligne) {
                $horaire = $this->em->getRepository(Horaire::class)->findOneBy(["horaire" => $ligne['B']]);
                if (
                    DateTime::createFromFormat('d/m/Y', $ligne['A']) !== false
                    &&
                    $horaire
                ) {
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
                                        $pointage->setNbrHeurTravailler(new DateTime("00:00:00"));
                                    }
                                break;
                            case 'F':
                                if ($colomn)
                                    try {
                                        $pointage->setRetardEnMinute(new DateTime($colomn));
                                    } catch (\Exception $e) {
                                        dd($ligne);
                                        $pointage->setRetardEnMinute(new DateTime("00:00:00"));
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
                                if ($colomn)
                                    $pointage->setTotaleRetard(new DateTime($colomn));
                            case 'J':

                                if ($colomn)
                                    $pointage->setAutorisationSortie(new DateTime($colomn));
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
                                if ($colomn)
                                    $pointage->setHeurNormalementTravailler(new DateTime($colomn));
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
