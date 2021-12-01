<?php

namespace App\Service;

use App\Entity\Absence;
use DateTime;
use App\Entity\User;
use App\Entity\Conger;
use App\Entity\Pointage;
use App\Service\TimeService;
use App\Service\HoraireService;
use App\Entity\AutorisationSortie;
use App\Entity\Dbf;
use App\Entity\Horaire;
use App\Entity\WorkTime;
use DateInterval;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class PointageService
{
    /**
     * date
     *
     * @var DateTime
     */
    private $date;

    /**
     * entrer
     *
     * @var DateTime
     */
    private $entrer;

    /**
     * sortie
     *
     * @var DateTime
     */
    private $sortie;

    /**
     * nbrHeurTravailler
     *
     * @var DateTime|null
     */
    private $nbrHeurTravailler;

    /**
     * retardEnMinute
     *
     * @var DateTime|null
     */
    private $retardEnMinute;

    /**
     * departAnticiper
     *
     * @var DateTime|null
     */
    private $departAnticiper;

    /**
     * retardMidi
     *
     * @var DateTime|null
     */
    private $retardMidi;

    /**
     * totalRetard
     *
     * @var DateTime
     */
    private $totalRetard;

    /**
     * heurNormalementTravailler
     *
     * @var DateTime
     */
    private $heurNormalementTravailler;

    /**
     * diff
     *
     * @var DateTime
     */
    private $diff;

    /**
     * employer
     *
     * @var User
     */
    private $employer;

    /**
     * horaire
     *
     * @var Horaire
     */
    private $horaire;

    /**
     * congerPayer
     *
     * @var Conger|null
     */
    private $congerPayer;

    /**
     * autorisationSortie
     *
     * @var AutorisationSortie|null
     */
    private $autorisationSortie;

    /**
     * workTime
     *
     * @var WorkTime|null
     */
    private $workTime;

    /**
     * absence
     *
     * @var Absence|null
     */
    private $absence;

    /**
     * horaireService
     *
     * @var HoraireService
     */
    private $horaireService;

    /**
     * timeService
     *
     * @var TimeService
     */
    private $timeService;

    /**
     * pointage
     *
     * @var Pointage
     */
    private $pointage;

    /**
     * absenceService
     *
     * @var AbsenceService
     */
    private $absenceService;
    /**
     * congerService
     *
     * @var CongerService
     */
    private $congerService;
    /**
     * autorisationSortieService
     *
     * @var AutorisationSortieService
     */
    private $autorisationSortieService;

    /**
     * retardService variable
     *
     * @var RetardService
     */
    private $retardService;
    /**
     * workHourService variable
     *
     * @var WorkHourService
     */
    private $workHourService;

    /**
     * manager
     *
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * flash
     *
     * @var FlashBagInterface
     */
    private $flash;
    public function __construct(
        FlashBagInterface $flash,
        HoraireService $horaireService,
        TimeService $timeService,
        ConfigService $configService,
        EntityManagerInterface $manager,
        AbsenceService $absenceService,
        CongerService $congerService,
        AutorisationSortieService $autorisationSortieService,
        RetardService $retardService,
        WorkHourService $workHourService
    ) {
        $this->horaireService = $horaireService;
        $this->timeService = $timeService;
        $this->flash = $flash;
        $this->configService = $configService;
        $this->manager = $manager;
        $this->absenceService = $absenceService;
        $this->congerService = $congerService;
        $this->autorisationSortieService = $autorisationSortieService;
        $this->retardService = $retardService;
        $this->workHourService = $workHourService;
    }
    /**
     * initAttribute function
     *
     * @return void
     */
    public function initAttribute(): void
    {
        $this->date =null;
        $this->entrer =null;
        $this->sortie =null;
        $this->nbrHeurTravailler =null;
        $this->retardEnMinute =null;
        $this->departAnticiper =null;
        $this->retardMidi =null;
        $this->totaleRetard =null;
        $this->heurNormalementTravailler =null;
        $this->diff =null;
        $this->employer =null;
        $this->horaire =null;
        $this->congerPayer =null;
        $this->autorisationSortie =null;
        $this->workTime =null;
        $this->absence =null;
    }

    /**
     * constructFromDbf
     *
     * @param Dbf $dbf
     * @return void
     */
    public function constructFromDbf(Dbf $dbf): void
    {
        $this->initAttribute();
        $attchktime = ($dbf->getAttchktime()[0] =="")?[]:$dbf->getAttchktime();
        $this->date = $dbf->getAttdate();
        $this->employer = $dbf->getEmployer();
        $this->horaireService->setWorkTime($this->employer->getWorkTimes()->toArray());
        $this->horaire = $this->horaireService->getHoraireForDate($this->date, $this->employer);
        //$this->workTime = null;
        $this->workHourService->requirement($attchktime, $this->horaire, $this->employer, $this->date, $dbf->getStarttime(), $dbf->getEndtime());
        $this->entrer = $this->workHourService->getEntrerFromArray();
        //$this->entrer = $dbf->getStarttime() ? $dbf->getStarttime() : $this->entrerService->getEntrerFromArray($attchktime, $this->horaire, $this->employer, $this->date);
        $this->sortie = $this->workHourService->getSortieFromArray();
        //$this->sortie = $dbf->getEndtime() ? $dbf->getEndtime() : $this->sortieService->getSortieFromArray($attchktime, $this->horaire, $this->employer, $this->date);
        $this->absenceService->partielConstruct($this->employer, $this->date, $this->date);
        $this->absence = $this->absenceService->findOrCreate($this->entrer, $this->sortie);
        //$this->absence = $this->absenceService->estAbscent($this->date);
        if (!$this->absence) {
            $this->congerService->partielConstruct($this->employer, $this->date, $this->date);
            $this->congerPayer = $this->congerService->findOrCreate($this->entrer, $this->sortie);
            $this->workHourService->setCongerPayer($this->congerPayer);
            if (!$this->congerPayer) {
                $this->autorisationSortieService->requirement($attchktime, $this->horaire, $this->entrer, $this->sortie);
                $this->autorisationSortieService->partielConstruct($this->employer, $this->date);
                $this->autorisationSortie = $this->autorisationSortieService->getAutorisation();
                /* if (!$this->autorisationSortie and count($attchktime)<4) {
                    dump($this->horaire);
                    dump($this->horaireService->getHeursQuardJournerDeTravaille());
                    dump($this->horaireService->getHeursDemiJournerDeTravaille());
                    dump($this->horaireService->getHeursTroisQuardJournerDeTravaille());
                    dump($this->horaireService->getHeursJournerDeTravaille());
                    dump($dbf->getAttchktime());
                    dump($this->entrer);
                    dump($this->sortie);
                    dd($this->timeService->diffTime($this->entrer, $this->sortie));
                    
                    $this->autorisationSortieService->partielConstruct($this->employer, $this->date, $this->autorisationSortieService->de(), $this->autorisationSortieService->a(), true);
                    $this->autorisationSortie = $this->autorisationSortie?$this->autorisationSortie:$this->autorisationSortieService->ConstructEntity();
                } */
                $this->workHourService->setAutorisationSortie($this->autorisationSortie);
            }
        }
        $this->heurNormalementTravailler = $this->workHourService->heurNormalementTravailler();
        $this->retardService->requirement($attchktime, $this->horaire, $this->entrer, $this->sortie, $this->congerPayer, $this->autorisationSortie);
        $this->retardEnMinute = $this->retardService->retardEnMinute(); //$dbf->getLate();
        $this->retardMidi = $this->retardService->retardMidi();
        $this->departAnticiper = $this->retardService->departAnticiper(); //$dbf->getEarly();
        $this->nbrHeurTravailler = $this->workHourService->nbrHeurTravailler(); // $dbf->getWorktime();
        $this->totalRetard = $this->retardService->totalRetard();
        $this->diff = $this->workHourService->diff();
    }

    /**
     * constructFromPointage
     *
     * @param Pointage $pointage
     * @return void
     */
    public function constructFromPointage(Pointage $pointage): void
    {
        $this->date = $pointage->getDate();
        $this->employer = $pointage->getEmployer();
        $this->horaire = $this->horaireService->getHoraireForDate($this->date, $this->employer);
        $this->autorisationSortieService->partielConstruct($this->employer);
        $this->autorisationSortie =  $this->autorisationSortieService->getAutorisation($this->date);
        $this->entrer = $pointage->getEntrer();
        $this->sortie = $pointage->getSortie();
        $this->absence = $pointage->getAbsence();
        $this->congerPayer = $pointage->getCongerPayer();
        $this->nbrHeurTravailler = $pointage->getNbrHeurTravailler();
        $this->retardEnMinute = $pointage->getRetardEnMinute();
        $this->departAnticiper = $pointage->getDepartAnticiper();
        $this->retardMidi = $pointage->getRetardMidi();
        ;
        $this->totalRetard = $pointage->getTotaleRetard();
        $this->heurNormalementTravailler = $pointage->getHeurNormalementTravailler();
        $this->diff = $pointage->getDiff();
        $this->workTime = $pointage->getWorkTime();
    }

    /**
     * createEntity
     *
     * @return Pointage
     */
    public function createEntity(): Pointage
    {
        $this->pointage = new Pointage();
        $this->pointage->setDate($this->date);
        $this->pointage->setHoraire($this->horaire);
        $this->pointage->setAbsence($this->absence);
        $this->pointage->setCongerPayer($this->congerPayer);
        $this->pointage->setAutorisationSortie($this->autorisationSortie);
        $this->pointage->setEntrer($this->entrer);
        $this->pointage->setSortie($this->sortie);
        $this->pointage->setNbrHeurTravailler($this->nbrHeurTravailler);
        $this->pointage->setRetardEnMinute($this->retardEnMinute);
        $this->pointage->setDepartAnticiper($this->departAnticiper);
        $this->pointage->setRetardMidi($this->retardMidi);
        $this->pointage->setTotaleRetard($this->totalRetard);
        $this->pointage->setHeurNormalementTravailler($this->heurNormalementTravailler);
        $this->pointage->setDiff($this->diff);
        $this->pointage->setEmployer($this->employer);
        return $this->pointage;
    }

    /**
     * dbfUpdated
     *
     * @param Dbf $dbf
     * @return void
     */
    public function dbfUpdated(Dbf $dbf): void
    {
        $this->pointage = new Pointage();
        $this->pointage->setDate($this->date);
        $this->pointage->setHoraire($this->horaire);
        $this->pointage->setAbsence($this->absence);
        $this->pointage->setCongerPayer($this->congerPayer);
        $this->pointage->setAutorisationSortie($this->autorisationSortie);
        $this->pointage->setEntrer($this->entrer);
        $this->pointage->setSortie($this->sortie);
        $this->pointage->setNbrHeurTravailler($this->nbrHeurTravailler);
        $this->pointage->setRetardEnMinute($this->retardEnMinute);
        $this->pointage->setDepartAnticiper($this->departAnticiper);
        $this->pointage->setRetardMidi($this->retardMidi);
        $this->pointage->setTotaleRetard($this->totalRetard);
        $this->pointage->setHeurNormalementTravailler($this->heurNormalementTravailler);
        $this->pointage->setDiff($this->diff);
        $this->pointage->setEmployer($this->employer);
        $this->manager->persist($this->pointage);
        $this->manager->remove($dbf);
        $this->manager->flush();
    }


    /**
     * dateInDB
     *
     * @param User $employer
     * @return array
     */
    public function dateInDB(User $employer): array
    {
        return array_map(
            fn ($date): string => $date->getDate()->format('Y-m-d'),
            $employer->getPointages()->toArray()
        );
    }





    /**
     * Set the value of initBilan
     *
     * @return  self
     */
    public function setInitBilan($initBilan)
    {
        $this->initBilan = $initBilan;

        return $this;
    }

    /**
     * Get pointage
     *
     * @return  Pointage
     */
    public function getPointage()
    {
        return $this->pointage;
    }

    /**
     * Set pointage
     *
     * @param  Pointage  $this->pointage  pointage
     *
     * @return  self
     */
    public function setPointage(Pointage $pointage)
    {
        $this->pointage = $this->pointage;

        return $this;
    }





    /* public function addLigne(array $ligne, User $user)
    {
        $this->pointage = new Pointage();
        foreach ($ligne as $char => $colomn) {
            switch ($char) {
                case 'A':
                    $this->pointage->setDate($this->dateService->dateString_d_m_Y_ToDateTime($colomn));
                    break;
                case 'B':
                    $this->pointage->setHoraire($this->horaireService->getHoraireForDate($this->date));
                    break;
                case 'C':
                    if ($this->timeService->isTimeHi($colomn))
                        $this->pointage->setEntrer($this->timeService->generateTime($colomn));
                    else {
                        if (!in_array($ligne['K'], ['1']) and $colomn != 'CP' and !$colomn)
                            $this->flash->add('warning', 'not set entrer ' . $colomn . ' of ligne ' . implode(" | ", $ligne));
                    }
                    break;
                case 'D':
                    if ($this->timeService->isTimeHi($colomn))
                        $this->pointage->setSortie($this->timeService->generateTime($colomn));
                    else {
                        if (!in_array($ligne['K'], ['1']) and $ligne['C'] != 'CP')
                            $this->flash->add('warning', 'not set sortie ' . $colomn . ' of ligne ' . implode(" | ", $ligne));
                    }
                    break;
                case 'E':
                    if ($this->sortie and $this->entrer)
                        $this->pointage->setNbrHeurTravailler($this->nbrHeurTravailler());
                    else {
                        $this->pointage->setNbrHeurTravailler(new DateTime('00:00:00'));
                        if (!in_array($ligne['K'], ['1']) and $ligne['C'] != 'CP')
                            $this->flash->add('warning', 'set to 0 nbrHeurTravailler, entrer ou sortie non saisie ' . $colomn . ' of ligne ' . implode(" | ", $ligne));
                    }
                    break;
                case 'F':
                    if ($this->sortie and $this->entrer)
                        $this->pointage->setRetardEnMinute($this->retardEnMinute);
                    break;
                case 'G':
                    if ($this->timeService->isTimeHi($colomn))
                        $this->pointage->setDepartAnticiper(new DateTime($colomn));
                    else {
                        if ($colomn)
                            $this->flash->add('warning', 'ignored departAnticiper' . $colomn . ' of ligne ' . implode(" | ", $ligne));
                    }
                    break;
                case 'H':
                    if ($this->timeService->isTimeHi($colomn))
                        $this->pointage->setRetardMidi($this->timeService->generateTime($colomn));
                    else {
                        if ($colomn)
                            $this->flash->add('warning', 'ignored retardMidi' . $colomn . ' of ligne ' . implode(" | ", $ligne));
                    }
                    break;
                case 'I':
                    $this->pointage->setTotaleRetard($this->totalRetard());
                case 'J':
                    if ($this->timeService->isTimeHi($colomn)) {
                        $autrisationSotie = new AutorisationSortie();
                        $autrisationSotie->setDateAutorisation($this->date);
                        $autrisationSotie->setTime(new DateTime($colomn));
                        $autrisationSotie->setEmployer($user);
                        //if ($this->configService->getConfig()->getReinitialisationC() and ($lastYear != $date->format('Y')))
                        //if ($this->configService->getConfig()->getReinitialisationAS() and ($lastYear != $date->format('Y')))
                        //$lastYear = $date->format('Y');
                    } else {
                        if ($colomn)
                            $this->flash->add('warning', 'ignored autorisationSortie' . $colomn . ' of ligne ' . implode(" | ", $ligne));
                    }
                    break;
                case 'K':
                    switch ($colomn) {
                        case '0.5':
                            $conger = new Conger();
                            $conger->setType("CP");
                            $conger->setEmployer($user);
                            $conger->setDebut($this->date);
                            $conger->setFin($this->date);
                            $conger->setDemiJourner(true);
                            $this->pointage->setCongerPayer($conger);
                            break;
                        case '1':
                            $conger = new Conger();
                            if ($ligne['C'] == 'CM')
                                $conger->setType("CM");
                            else
                                $conger->setType("CP");
                            $conger->setEmployer($user);
                            $conger->setDebut($this->date);
                            $conger->setFin($this->date);
                            $conger->setDemiJourner(false);
                            $this->pointage->setCongerPayer($conger);
                            break;
                        default:
                            if (($ligne['C'] == 'CP' or $ligne['C'] == 'CM') and !$colomn) {
                                $conger = new Conger();
                                if ($ligne['C'] == 'CM')
                                    $conger->setType("CM");
                                else
                                    $conger->setType("CP");
                                $conger->setEmployer($user);
                                $conger->setDebut($this->date);
                                $conger->setFin($this->date);
                                $conger->setDemiJourner(false);
                                $this->pointage->setCongerPayer($conger);
                                $this->flash->add('warning', 'cp added automatically of ligne ' . implode(" | ", $ligne));
                            }
                            if ($colomn)
                                $this->flash->add('warning', 'ignored congerPayer' . $colomn . ' of ligne ' . implode(" | ", $ligne));
                            break;
                    }
                    break;
                case 'L':
                    if ($colomn)
                        $this->pointage->setAbsence($colomn);
                    break;
                case 'M':
                    $this->pointage->setHeurNormalementTravailler($this->heurNormalementTravailler());
                    break;
                case 'N':
                    $this->pointage->setDiff($this->diff());
                    break;
                default:
                    //dump($ligne[$char]);
                    break;
            }
        }
        $user->addPointage($this->pointage);
        return $user;
    } */
}
