<?php

namespace App\Service;

use App\Entity\Absence;
use App\Entity\AutorisationSortie;
use App\Entity\Conger;
use DateTime;
use App\Entity\User;
use App\Entity\Horaire;
use App\Entity\Pointage;
use App\Entity\Xlsx;
use App\Service\DateService;
use App\Service\JourFerierService;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Worksheet\Col;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class XlsxService
{

    /**
     * date variable
     *
     * @var DateTime
     */
    private $date;
    /**
     * horaire variable
     *
     * @var Horaire
     */
    private $horaire;

    /**
     * entrer variable
     *
     * @var DateTime
     */
    private $entrer;

    /**
     * sortie variable
     *
     * @var DateTime
     */
    private $sortie;

    /**
     * nbrHeursTravailler variable
     *
     * @var DateTime
     */
    private $nbrHeursTravailler;

    /**
    * retardEnMinute variable
    *
    * @var DateTime
    */
    private $retardEnMinute;

    /**
    * departAnticiper variable
    *
    * @var DateTime
    */
    private $departAnticiper;

    /**
    * retardMidi variable
    *
    * @var DateTime
    */
    private $retardMidi;

    /**
    * totalRetard variable
    *
    * @var DateTime
    */
    private $totalRetard;

    /**
     * autorisationSortie variable
     *
     * @var AutorisationSortie
     */
    private $autorisationSortie;

    /**
     * congerPayer variable
     *
     * @var Conger
     */
    private $congerPayer;

    /**
     * absence variable
     *
     * @var Absence
     */
    private $absence;

    /**
    * heurNormalementTravailler variable
    *
    * @var DateTime
    */
    private $heurNormalementTravailler;

    /**
    * diff variable
    *
    * @var DateTime
    */
    private $diff;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="xlsxes")
     */
    private $employer;



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
     * dateService
     *
     * @var DateService
     */
    private $dateService;

    /**
     * timeService
     *
     * @var TimeService
     */
    private $timeService;

    /**
     * horaireService
     *
     * @var HoraireService
     */
    private $horaireService;

    /**
     * pointageService
     *
     * @var PointageService
     */
    private $pointageService;

    /**
     * congerService variable
     *
     * @var CongerService
     */
    private $congerService;
    /**
     * workHourService variable
     *
     * @var WorkHourService
     */
    private $workHourService;
    /**
     * retardService variable
     *
     * @var RetardService
     */
    private $retardService;
    /**
     * flash
     *
     * @var FlashBagInterface
     */
    private $flash;

    
    /**
     * autorisationSortieService variable
     *
     * @var AutorisationSortieService
     */
    private $autorisationSortieService;
   
    public function __construct(
        EntityManagerInterface $em,
        PointageService $pointageService,
        JourFerierService $jourFerierService,
        DateService $dateService,
        TimeService $timeService,
        HoraireService $horaireService,
        FlashBagInterface $flash,
        AutorisationSortieService $autorisationSortieService,
        CongerService $congerService,
        WorkHourService $workHourService,
        RetardService $retardService
    ) {
        $this->em = $em;
        $this->jourFerierService = $jourFerierService;
        $this->dateService = $dateService;
        $this->horaireService = $horaireService;
        $this->timeService = $timeService;
        $this->congerService = $congerService;
        $this->pointageService = $pointageService;
        $this->flash = $flash;
        $this->autorisationSortieService = $autorisationSortieService;
        $this->workHourService = $workHourService;
        $this->retardService = $retardService;
    }
    
    public function construct(array $col, ?User $employer)
    {
        $this->employer = $employer;
        $this->date = $this->dateService->dateString_d_m_Y_ToDateTime($col['A']);
        $this->horaire = $this->horaireService->getHoraireForDate($this->date, $this->employer);
        if (!$this->horaire) {
            $otherHoraire = $this->horaireService->getHoraireByHoraireName($col['B'], $this->employer);
            $this->horaire = new Horaire();
            $this->horaire->setDateDebut($this->date);
            $this->horaire->setDateFin($this->date);
            $this->horaire->setHoraire($col['B']);
            $this->horaire->setDebutPauseMatinal($otherHoraire->getDebutPauseMatinal());
            $this->horaire->setDebutPauseDejeuner($otherHoraire->getDebutPauseDejeuner());
            $this->horaire->setDebutPauseMidi($otherHoraire->getDebutPauseMidi());
            $this->horaire->setHeurDebutTravaille($otherHoraire->getHeurDebutTravaille());
            $this->horaire->setFinPauseDejeuner($otherHoraire->getFinPauseDejeuner());
            $this->horaire->setFinPauseMatinal($otherHoraire->getFinPauseMatinal());
            $this->horaire->setFinPauseMidi($otherHoraire->getFinPauseMidi());
            $this->horaire->setHeurFinTravaille($otherHoraire->getHeurFinTravaille());
            $this->horaire->setMargeDuRetard($otherHoraire->getMargeDuRetard());
            //$this->em->persist($this->horaire);
        }
        $this->entrer = $col['C']?$this->timeService->generateTime($col['C']):null;
        $this->sortie = $col['D']?$this->timeService->generateTime($col['D']):null;
        if ($col['E']) {
            $this->nbrHeurTravailler = $this->timeService->generateTime($col['E']);
        } else {
            $this->workHourService->requirement([], $this->horaire, $this->employer, $this->date, $this->entrer, $this->sortie);
            $this->nbrHeurTravailler =$this->workHourService->nbrHeurTravailler();
        }
        if ($col['F']) {
            $this->retardEnMinute = $this->timeService->generateTime($col['F']);
        } else {
            $this->retardService->requirement([], $this->horaire, $this->entrer, $this->sortie, $this->congerPayer, $this->autorisationSortie);
            $this->retardEnMinute = $this->retardService->retardEnMinute(); //$dbf->getLate();
        }
        if ($col['G']) {
            $this->departAnticiper = $this->timeService->generateTime($col['G']);
        } else {
            $this->retardService->requirement([], $this->horaire, $this->entrer, $this->sortie, $this->congerPayer, $this->autorisationSortie);
            $this->departAnticiper =$this->retardService->departAnticiper();
        }
        if ($col['H']) {
            $this->retardMidi =  $this->timeService->generateTime($col['H']);
        } else {
            $this->retardService->requirement([], $this->horaire, $this->entrer, $this->sortie, $this->congerPayer, $this->autorisationSortie);
            $this->retardMidi  =$this->retardService->retardMidi();
        }
        $this->totalRetard =$col['I']?  $this->timeService->generateTime($col['I']):null;
        $this->autorisationSortieService->partielConstruct($this->employer);
        $this->autorisationSortie =  $this->autorisationSortieService->getAutorisation($this->date);
        if ($col['J'] and !$this->autorisationSortie) {
            $this->autorisationSortie = $col['J']?$this->timeService->generateTime($col['J']):null;
            $this->autorisationSortieService->partielConstruct($this->employer, $this->date, $this->autorisationSortie, true, false);
            $this->autorisationSortie =  $this->autorisationSortieService->ConstructEntity();
        }
        if ($col['K']) {
            if (floatval($col['K']) < 1) {
                $this->congerService->partielConstruct($this->employer, $this->date, $this->date, 'CP', true, false, true);
            } elseif (floatval($col['K']) == 1) {
                $this->congerService->partielConstruct($this->employer, $this->date, $this->date, 'CP', true, false, false);
            } else {
                dd($col['K']);
            }
            $this->congerPayer = $this->congerService->ConstructEntity();
        }
        if ($col['L']) {
            dd($col['L']);
            $this->absence = $col['L'];
        }
        $this->heurNormalementTravailler =  $col['M']?  $this->timeService->generateTime($col['M']):null;
        $this->diff = $col['N']?  $this->timeService->generateTime($col['N']):null;
    }

    public function createEntity(): Xlsx
    {
        $xlsx = new Xlsx();
        $xlsx->setDate($this->date);
        $xlsx->setHoraire($this->horaire);
        $xlsx->setEntrer($this->entrer);
        $xlsx->setSortie($this->sortie);
        $xlsx->setNbrHeursTravailler($this->nbrHeursTravailler);
        $xlsx->setRetardEnMinute($this->retardEnMinute);
        $xlsx->setDepartAnticiper($this->departAnticiper);
        $xlsx->setRetardMidi($this->retardMidi);
        $xlsx->setTotalRetard($this->totalRetard);
        $xlsx->setAutorisationSortie($this->autorisationSortie);
        $xlsx->setCongerPayer($this->congerPayer);
        $xlsx->setAbsence($this->absence);
        $xlsx->setHeurNormalementTravailler($this->heurNormalementTravailler);
        $xlsx->setDiff($this->diff);
        $xlsx->setEmployer($this->employer);
        return $xlsx;
    }

    /**
     * dateInDB
     *
     * @param User $user
     * @return array
     */
    public function dateInDB(User $user): array
    {
        return array_map(
            fn ($date): string => $date->getDate()->format('Y-m-d'),
            $user->getXlsxes()->toArray()
        );
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
                    } else {
                        $this->flash->add('danger ', 'ignored ligne ' . implode(" | ", $ligne));
                    }
                }
            }
        }
        return $user;
    }
}
