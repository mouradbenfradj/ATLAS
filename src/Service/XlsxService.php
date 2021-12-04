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
     * nbrHeurTravailler variable
     *
     * @var DateTime
     */
    private $nbrHeurTravailler;

    /**
     * employer variable
     *
     * @var User
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
     * absenceService variable
     *
     * @var AbsenceService
     */
    private $absenceService;
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
        AbsenceService $absenceService,
        CongerService $congerService,
        WorkHourService $workHourService,
        RetardService $retardService
    ) {
        $this->em = $em;
        $this->jourFerierService = $jourFerierService;
        $this->dateService = $dateService;
        $this->horaireService = $horaireService;
        $this->timeService = $timeService;
        $this->absenceService = $absenceService;
        $this->congerService = $congerService;
        $this->pointageService = $pointageService;
        $this->flash = $flash;
        $this->autorisationSortieService = $autorisationSortieService;
        $this->workHourService = $workHourService;
        $this->retardService = $retardService;
    }

    public function construct(array $cols, User $employer)
    {
        $this->employer = $employer;
        $this->date = $this->dateService->dateString_d_m_Y_ToDateTime($cols['A']);
        $this->date = $this->date->setTime(0, 0);
        $this->horaire = $this->horaireService->getHoraireForDate($this->date, $this->employer, $cols['B']);
        $this->entrer = $cols['C'] ? $this->timeService->generateTime($cols['C']) : null;
        $this->sortie = $cols['D'] ? $this->timeService->generateTime($cols['D']) : null;
        $this->autorisationSortieService->partielConstruct($this->employer);
        $this->autorisationSortie =  $this->autorisationSortieService->getAutorisation($this->date);
        if ($cols['J'] and !$this->autorisationSortie) {
            $this->autorisationSortie = $cols['J'] ? $this->timeService->generateTime($cols['J']) : null;
            $this->autorisationSortieService->partielConstruct($this->employer, $this->date, $this->autorisationSortie, true, false);
            $this->autorisationSortie =  $this->autorisationSortieService->constructEntity();
        }
        $this->congerService->partielConstruct($this->employer, $this->date, $this->date);
        $this->congerPayer = $this->congerService->findOrCreate($this->entrer, $this->sortie);
        if ($cols['K'] and !$this->congerPayer) {
            if (floatval($cols['K']) < 1) {
                $this->congerService->partielConstruct($this->employer, $this->date, $this->date, 'CP', true, false, true);
            } elseif (floatval($cols['K']) == 1) {
                $this->congerService->partielConstruct($this->employer, $this->date, $this->date, 'CP', true, false, false);
            } else {
                dd($cols['K']);
            }
            $this->congerPayer = $this->congerService->constructEntity();
        }
        $this->absenceService->partielConstruct($this->employer, $this->date, $this->date);
        $this->absence = $this->absenceService->findOrCreate($this->entrer, $this->sortie);
        if ($cols['L']) {
            dd($cols['L'] and !$this->absence);
            $this->absence = $cols['L'];
        }
        if ($cols['E']) {
            $this->nbrHeurTravailler = $this->timeService->generateTime($cols['E']);
        } else {
            $this->workHourService->requirement([], $this->horaire, $this->employer, $this->date, $this->entrer, $this->sortie);
            $this->nbrHeurTravailler = $this->workHourService->nbrHeurTravailler();
        }
        if ($cols['F']) {
            $this->retardEnMinute = $this->timeService->generateTime($cols['F']);
        } else {
            $this->retardService->requirement([], $this->horaire, $this->entrer, $this->sortie, $this->congerPayer, $this->autorisationSortie);
            $this->retardEnMinute = $this->retardService->retardEnMinute(); //$dbf->getLate();
        }
        if ($cols['G']) {
            $this->departAnticiper = $this->timeService->generateTime($cols['G']);
        } else {
            $this->retardService->requirement([], $this->horaire, $this->entrer, $this->sortie, $this->congerPayer, $this->autorisationSortie);
            $this->departAnticiper = $this->retardService->departAnticiper();
        }
        if ($cols['H']) {
            $this->retardMidi =  $this->timeService->generateTime($cols['H']);
        } else {
            $this->retardService->requirement([], $this->horaire, $this->entrer, $this->sortie, $this->congerPayer, $this->autorisationSortie);
            $this->retardMidi  = $this->retardService->retardMidi();
        }
        $this->totalRetard = $cols['I'] ?  $this->timeService->generateTime($cols['I']) : null;

        $this->heurNormalementTravailler =  $cols['M'] ?  $this->timeService->generateTime($cols['M']) : null;
        $this->diff = $cols['N'] ?  $this->timeService->generateTime($cols['N']) : null;
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
}
