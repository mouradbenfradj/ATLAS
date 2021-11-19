<?php

namespace App\Service;

use App\Entity\Abscence;
use DateTime;
use App\Entity\User;
use App\Entity\Conger;
use DateTimeInterface;
use App\Entity\Pointage;
use App\Service\TimeService;
use App\Service\HoraireService;
use App\Entity\AutorisationSortie;
use App\Entity\Dbf;
use App\Entity\Horaire;
use App\Entity\WorkTime;
use DateInterval;
use Doctrine\Common\Collections\Collection;
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
     * abscence
     *
     * @var Abscence|null
     */
    private $abscence;

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
     * initBilan
     *
     * @var array
     */
    private $initBilan;

    /**
     * abscenceService
     *
     * @var AbscenceService
     */
    private $abscenceService;
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








    /**
     * __construct
     *
     * @param FlashBagInterface $flash
     * @param HoraireService $horaireService
     * @param TimeService $timeService
     * @param ConfigService $configService
     * @param EntityManagerInterface $manager
     * @param AbscenceService $abscenceService
     * @param CongerService $congerService
     * @param AutorisationSortieService $autorisationSortieService
     */
    public function __construct(
        FlashBagInterface $flash,
        HoraireService $horaireService,
        TimeService $timeService,
        ConfigService $configService,
        EntityManagerInterface $manager,
        AbscenceService $abscenceService,
        CongerService $congerService,
        AutorisationSortieService $autorisationSortieService
    ) {
        $this->horaireService = $horaireService;
        $this->timeService = $timeService;
        $this->flash = $flash;
        $this->configService = $configService;
        $this->manager = $manager;
        $this->abscenceService = $abscenceService;
        $this->congerService = $congerService;
        $this->autorisationSortieService = $autorisationSortieService;

        $this->initBilan = [
            "colspan" => 1,
            "background" => null,
            "date" => null,
            "horaire" => null,
            "entrer" => null,
            "sortie" => null,
            "nbrHeurTravailler" => 0,
            "retardEnMinute" => 0,
            "departAnticiper" => 0,
            "retardMidi" => 0,
            "totalRetard" => 0,
            "autorisationSortie" => 0,
            "congerPayer" =>  0,
            "abscence" =>  0,
            "heurNormalementTravailler" => 0,
            "diff" => 0,
        ];
    }

    /**
     * constructFromDbf
     *
     * @param Dbf $dbf
     * @return void
     */
    public function constructFromDbf(Dbf $dbf): void
    {
        $this->date = $dbf->getAttdate();
        $this->employer = $dbf->getEmployer();
        $this->horaire = $this->horaireService->getHoraireForDate($this->date);
        $this->entrer = $dbf->getStarttime() ? $dbf->getStarttime() : $this->entrer($dbf->getAttchktime());
        $this->sortie = $dbf->getEndtime() ? $dbf->getEndtime() : $this->sortie($dbf->getAttchktime());
        $this->abscenceService->partielConstruct($this->employer, $this->date, $this->date);
        $this->abscence = $this->abscenceService->findOrCreate($this->entrer, $this->sortie);
       
        //$this->abscence = $this->abscenceService->estAbscent($this->date);
        
        if (!$this->abscence) {
            $this->congerService->partielConstruct($this->employer, $this->date, $this->date);
            $this->congerPayer = $this->congerService->findOrCreate($this->entrer, $this->sortie);
            if (!$this->congerPayer) {
                $this->autorisationSortieService->partielConstruct($this->employer, $this->date);
                $this->autorisationSortie = $this->autorisationSortieService->getAutorisation();
            }
        }
           
        //$this->congerPayer = $this->congerService->estUnConger();
      
       

        /*  $heurDebutTravaille = $this->timeService->generateTime($this->horaire->getHeurDebutTravaille()->format('H:i:s'));
         $debutPauseMatinal = $this->timeService->generateTime($this->horaire->getDebutPauseMatinal()->format('H:i:s'));
         $finPauseMatinal = $this->timeService->generateTime($this->horaire->getFinPauseMatinal()->format('H:i:s'));
         $debutPauseDejeuner = $this->timeService->generateTime($this->horaire->getDebutPauseDejeuner()->format('H:i:s'));
         $finPauseDejeuner = $this->timeService->generateTime($this->horaire->getFinPauseDejeuner()->format('H:i:s'));
         $finPauseDejeuner->add($this->timeService->dateTimeToDateInterval($this->horaire->getMargeDuRetard()));
         $debutPauseMidi = $this->timeService->generateTime($this->horaire->getDebutPauseMidi()->format('H:i:s'));
         $finPauseMidi = $this->timeService->generateTime($this->horaire->getFinPauseMidi()->format('H:i:s'));
         $heurFinTravaille = $this->timeService->generateTime($this->horaire->getHeurFinTravaille()->format('H:i:s'));



         if ($this->entrer or $this->sortie) {
             switch (count($dbf->getAttchktime())) {
                     case 1:
                         if (!$this->congerPayer) {
                             $atttime = new DateTime($dbf->getAttchktime()[0]);
                             if (!$this->congerPayer and (($this->timeService->generateTime($this->horaire->getDebutPauseDejeuner()->format('H:i:s')) <= $atttime and $atttime <= $this->timeService->generateTime($this->horaire->getHeurFinTravaille()->format('H:i:s'))) or (($this->timeService->generateTime($this->horaire->getHeurDebutTravaille()->format('H:i:s')) <= $atttime and $atttime <= $this->timeService->generateTime($this->horaire->getFinPauseDejeuner()->format('H:i:s'))) and $this->horaire->getFinPauseDejeuner() <= $this->horaire->getHeurFinTravaille())) and $this->horaire->getFinPauseDejeuner() <= $this->horaire->getHeurFinTravaille()) {
                                 $this->congerService->partielConstruct($this->employer, $this->date, $this->date, "CP", true, false, true);
                                 $this->congerPayer = $this->congerService->ConstructEntity();
                                 $this->employer->addConger($this->congerPayer);
                             }
                         }
                         break;
                     case 2:
                         if (!$this->congerPayer) {
                             $atttime = new DateTime($dbf->getAttchktime()[0]);
                             $atttims = new DateTime($dbf->getAttchktime()[1]);

                             if (
                                 !$this->congerPayer
                                 and (
                                     ($heurDebutTravaille <= $atttime and $atttime <= $finPauseMatinal and $debutPauseMatinal <= $atttims and $atttims <= $finPauseDejeuner)
                                     or
                                     ($debutPauseMatinal <= $atttime and $atttime <= $finPauseDejeuner and $debutPauseDejeuner <= $atttims and $atttims <= $finPauseMidi)
                                     or
                                     ($debutPauseDejeuner <= $atttime and $atttime <= $finPauseMidi and $debutPauseMidi <= $atttims and $atttims <= $heurFinTravaille))
                                 and $finPauseDejeuner <= $heurFinTravaille
                             ) {
                                 $this->congerService->partielConstruct($this->employer, $this->date, $this->date, "CP", true, false, true);
                                 $this->congerPayer = $this->congerService->ConstructEntity();
                                 $this->employer->addConger($this->congerPayer);
                             }
                         }
                         break;
                     case 3:
                         if (!$this->autorisationSortie) {
                             $atttime = new DateTime($dbf->getAttchktime()[0]);
                             $atttims = new DateTime($dbf->getAttchktime()[1]);
                             $atttim3 = new DateTime($dbf->getAttchktime()[2]);

                             if (!$this->autorisationSortie and $debutPauseMatinal <= $atttime and $atttime <= $finPauseDejeuner and $debutPauseDejeuner <= $atttims and $atttims <= $finPauseMidi and $debutPauseMidi <= $atttim3 and ($atttim3 <= $heurFinTravaille or $atttim3 >= $heurFinTravaille)) {
                                 $this->autorisationSortieService->partielConstruct($this->employer, $this->date, $heurDebutTravaille, $debutPauseMatinal, true, false);
                                 $this->autorisationSortie = $this->autorisationSortieService->ConstructEntity();
                                 $this->employer->addAutorisationSorties($this->autorisationSortie);
                             } elseif (!$this->autorisationSortie and $heurDebutTravaille <= $atttime and $atttime <= $finPauseMatinal and $debutPauseDejeuner <= $atttims and $atttims <= $finPauseMidi and $debutPauseMidi <= $atttim3 and ($atttim3 <= $heurFinTravaille or $atttim3 >= $heurFinTravaille)) {
                                 $this->autorisationSortieService->partielConstruct($this->employer, $this->date, $debutPauseMatinal, $debutPauseDejeuner, true, false);
                                 $this->autorisationSortie = $this->autorisationSortieService->ConstructEntity();
                                 $this->employer->addAutorisationSorties($this->autorisationSortie);
                             } elseif (!$this->autorisationSortie and $heurDebutTravaille <= $atttime and $atttime <= $finPauseMatinal and $debutPauseMatinal <= $atttims and $atttims <= $finPauseDejeuner and $debutPauseMidi <= $atttim3 and ($atttim3 <= $heurFinTravaille or $atttim3 >= $heurFinTravaille)) {
                                 $this->autorisationSortieService->partielConstruct($this->employer, $this->date, $finPauseDejeuner, $debutPauseMidi, true, false);
                                 $this->autorisationSortie = $this->autorisationSortieService->ConstructEntity();
                                 $this->employer->addAutorisationSorties($this->autorisationSortie);
                             } elseif (!$this->autorisationSortie and $heurDebutTravaille <= $atttime and $atttime <= $finPauseMatinal and $debutPauseMatinal <= $atttims and $atttims <= $finPauseDejeuner and $debutPauseDejeuner <= $atttim3 and $atttim3 <= $finPauseMidi) {
                                 $this->autorisationSortieService->partielConstruct($this->employer, $this->date, $debutPauseMidi, $heurFinTravaille, true, false);
                                 $this->autorisationSortie = $this->autorisationSortieService->ConstructEntity();
                                 $this->employer->addAutorisationSorties($this->autorisationSortie);
                             }
                             //$this->retardMidi = $this->retardMidi($dbf->getAttchktime());
                         }
                         break;
                     default:
                         break;
                 }
         } else {
             if ($this->congerPayer) {
                 dump($this->entrer);
                 dump($this->sortie);
                 dump($this->autorisationSortie);
                 dd($dbf);
             //$this->autorisationSortie = $this->autorisationSortieService->getAutorisation();
             } else {
                 if (!$this->abscence) {
                     $this->abscenceService->partielConstruct($this->employer, $this->date, $this->date);
                     $this->abscence = $this->abscenceService->constructEntity();
                 }
                 $this->employer->addAbscence($this->abscence);
             }
         } */
        $this->retardMidi = $this->retardMidi($dbf->getAttchktime());
       
        $this->retardEnMinute = $this->retardEnMinute(); //$dbf->getLate();
        $this->departAnticiper = $this->departAnticiper(); //$dbf->getEarly();
        $this->workTime = null;
        $this->heurNormalementTravailler = $this->heurNormalementTravailler();
        $this->nbrHeurTravailler = $this->nbrHeurTravailler(); // $dbf->getWorktime();
        $this->totalRetard = $this->totalRetard();
        $this->diff = $this->diff();
        if (!$this->abscence) {
            dump($this->entrer);
            dump($this->sortie);
            dump($this->abscence);
            dump($this->congerPayer);
            dump($this->autorisationSortie);
            dump($this->retardMidi);
            dump($this->departAnticiper);
            dump($this->workTime);
            dump($this->heurNormalementTravailler);
            dump($this->nbrHeurTravailler);
            dump($this->totalRetard);
            dd($this->diff);
        }
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
        $this->horaire = $this->horaireService->getHoraireForDate($this->date);
        $this->autorisationSortieService->partielConstruct($this->employer);
        $this->autorisationSortie =  $this->autorisationSortieService->getAutorisation($this->date);
        $this->entrer = $pointage->getEntrer();
        $this->sortie = $pointage->getSortie();
        $this->abscence = $pointage->getAbscence();
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
        $this->pointage->setAbscence($this->abscence);
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
        $this->pointage->setAbscence($this->abscence);
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
     * entrer
     *
     * @param array $attchktime
     * @return void
     */
    public function entrer(array $attchktime)
    {
        $heurDebutTravaille = $this->timeService->generateTime($this->horaire->getHeurDebutTravaille()->format('H:i:s'));
        $debutPauseMatinal = $this->timeService->generateTime($this->horaire->getDebutPauseMatinal()->format('H:i:s'));
        $finPauseMatinal = $this->timeService->generateTime($this->horaire->getFinPauseMatinal()->format('H:i:s'));
        $debutPauseDejeuner = $this->timeService->generateTime($this->horaire->getDebutPauseDejeuner()->format('H:i:s'));
        $finPauseDejeuner = $this->timeService->generateTime($this->horaire->getFinPauseDejeuner()->format('H:i:s'));
        $debutPauseMidi = $this->timeService->generateTime($this->horaire->getDebutPauseMidi()->format('H:i:s'));
        $finPauseMidi = $this->timeService->generateTime($this->horaire->getFinPauseMidi()->format('H:i:s'));
        $heurFinTravaille = $this->timeService->generateTime($this->horaire->getHeurFinTravaille()->format('H:i:s'));
        if ($attchktime[0] != "") {
            switch (count($attchktime)) {
                case 1:
                    $atttime = $this->timeService->generateTime($attchktime[0]);
                    if ($attchktime < $finPauseMatinal or ($attchktime >= $debutPauseMidi and $attchktime < $finPauseMidi)) {
                        dd($atttime);
                        return $atttime;
                    }
                    $a = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $heurDebutTravaille));
                    $b = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $debutPauseDejeuner));
                    $c = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $finPauseDejeuner));
                    $d = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $heurFinTravaille));
                    dump($attchktime);
                    dump($atttime);
                    dump($a);
                    dump($b);
                    dump($c);
                    dd($d);
                    if (($heurDebutTravaille <= $atttime and $atttime <= $debutPauseDejeuner or
                            $finPauseDejeuner <= $atttime and $atttime <= $heurFinTravaille)
                        and
                        $finPauseDejeuner <= $heurFinTravaille
                    ) {
                        return   $atttime;
                    } else {
                        return null;
                    }
                    break;
                case 2:
                    if (!$this->congerPayer) {
                        $atttime = $this->timeService->generateTime($attchktime[0]);
                        $atttims = $this->timeService->generateTime($attchktime[1]);
                        $a = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $heurDebutTravaille));
                        $b = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $debutPauseDejeuner));
                        $c = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $finPauseDejeuner));
                        $d = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $heurFinTravaille));
                        dump($atttime);
                        dump($atttims);
                        dump($a);
                        dump($b);
                        dump($c);
                        dd($d);
                        if (
                            !$this->congerPayer
                            and (
                                ($heurDebutTravaille <= $atttime and $atttime <= $finPauseMatinal and $debutPauseMatinal <= $atttims and $atttims <= $finPauseDejeuner)
                                or
                                ($debutPauseMatinal <= $atttime and $atttime <= $finPauseDejeuner and $debutPauseDejeuner <= $atttims and $atttims <= $finPauseMidi)
                                or
                                ($debutPauseDejeuner <= $atttime and $atttime <= $finPauseMidi and $debutPauseMidi <= $atttims and $atttims <= $heurFinTravaille))
                            and $finPauseDejeuner <= $heurFinTravaille
                        ) {
                            $this->congerService->partielConstruct($this->employer, $this->date, $this->date, "CP", true, false, true);
                            $this->congerPayer = $this->congerService->ConstructEntity();
                            $this->employer->addConger($this->congerPayer);
                        }
                    }
                    break;
                case 3:
                    if (!$this->autorisationSortie) {
                        $atttime = new DateTime($attchktime[0]);
                        $atttims = new DateTime($attchktime[1]);
                        $atttim3 = new DateTime($attchktime[2]);
                        $a = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $heurDebutTravaille));
                        $b = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $debutPauseDejeuner));
                        $c = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $finPauseDejeuner));
                        $d = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $heurFinTravaille));
                        dump($atttime);
                        dump($atttims);
                        dump($atttim3);
                        dump($a);
                        dump($b);
                        dump($c);
                        dd($d);
                        if (!$this->autorisationSortie and $debutPauseMatinal <= $atttime and $atttime <= $finPauseDejeuner and $debutPauseDejeuner <= $atttims and $atttims <= $finPauseMidi and $debutPauseMidi <= $atttim3 and ($atttim3 <= $heurFinTravaille or $atttim3 >= $heurFinTravaille)) {
                            $this->autorisationSortieService->partielConstruct($this->employer, $this->date, $heurDebutTravaille, $debutPauseMatinal, true, false);
                            $this->autorisationSortie = $this->autorisationSortieService->ConstructEntity();
                            $this->employer->addAutorisationSorties($this->autorisationSortie);
                        } elseif (!$this->autorisationSortie and $heurDebutTravaille <= $atttime and $atttime <= $finPauseMatinal and $debutPauseDejeuner <= $atttims and $atttims <= $finPauseMidi and $debutPauseMidi <= $atttim3 and ($atttim3 <= $heurFinTravaille or $atttim3 >= $heurFinTravaille)) {
                            $this->autorisationSortieService->partielConstruct($this->employer, $this->date, $debutPauseMatinal, $debutPauseDejeuner, true, false);
                            $this->autorisationSortie = $this->autorisationSortieService->ConstructEntity();
                            $this->employer->addAutorisationSorties($this->autorisationSortie);
                        } elseif (!$this->autorisationSortie and $heurDebutTravaille <= $atttime and $atttime <= $finPauseMatinal and $debutPauseMatinal <= $atttims and $atttims <= $finPauseDejeuner and $debutPauseMidi <= $atttim3 and ($atttim3 <= $heurFinTravaille or $atttim3 >= $heurFinTravaille)) {
                            $this->autorisationSortieService->partielConstruct($this->employer, $this->date, $finPauseDejeuner, $debutPauseMidi, true, false);
                            $this->autorisationSortie = $this->autorisationSortieService->ConstructEntity();
                            $this->employer->addAutorisationSorties($this->autorisationSortie);
                        } elseif (!$this->autorisationSortie and $heurDebutTravaille <= $atttime and $atttime <= $finPauseMatinal and $debutPauseMatinal <= $atttims and $atttims <= $finPauseDejeuner and $debutPauseDejeuner <= $atttim3 and $atttim3 <= $finPauseMidi) {
                            $this->autorisationSortieService->partielConstruct($this->employer, $this->date, $debutPauseMidi, $heurFinTravaille, true, false);
                            $this->autorisationSortie = $this->autorisationSortieService->ConstructEntity();
                            $this->employer->addAutorisationSorties($this->autorisationSortie);
                        }
                        //$this->retardMidi = $this->retardMidi($attchktime);
                    }
                    break;
                default:
                    $atttime = new DateTime($attchktime[0]);
                    $atttims = new DateTime($attchktime[1]);
                    $atttim3 = new DateTime($attchktime[2]);
                    $atttim4 = new DateTime($attchktime[3]);
                    $a = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $heurDebutTravaille));
                    $b = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $debutPauseDejeuner));
                    $c = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $finPauseDejeuner));
                    $d = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $heurFinTravaille));
                    dump($atttime);
                    dump($atttims);
                    dump($atttim3);
                    dump($atttim4);
                    dump($a);
                    dump($b);
                    dump($c);
                    dd($d);
                    break;
            }
        } else {
            return null;
        }
    }
    /**
     * sortie
     *
     * @param array $attchktime
     * @return void
     */
    public function sortie(array $attchktime)
    {
        $heurDebutTravaille = $this->timeService->generateTime($this->horaire->getHeurDebutTravaille()->format('H:i:s'));
        $debutPauseMatinal = $this->timeService->generateTime($this->horaire->getDebutPauseMatinal()->format('H:i:s'));
        $finPauseMatinal = $this->timeService->generateTime($this->horaire->getFinPauseMatinal()->format('H:i:s'));
        $debutPauseDejeuner = $this->timeService->generateTime($this->horaire->getDebutPauseDejeuner()->format('H:i:s'));
        $finPauseDejeuner = $this->timeService->generateTime($this->horaire->getFinPauseDejeuner()->format('H:i:s'));
        $debutPauseMidi = $this->timeService->generateTime($this->horaire->getDebutPauseMidi()->format('H:i:s'));
        $finPauseMidi = $this->timeService->generateTime($this->horaire->getFinPauseMidi()->format('H:i:s'));
        $heurFinTravaille = $this->timeService->generateTime($this->horaire->getHeurFinTravaille()->format('H:i:s'));

        if ($attchktime[0] != "") {
            switch (count($attchktime)) {
                case 1:
                    $atttime = $this->timeService->generateTime($attchktime[0]);
                    if (($attchktime >= $finPauseMatinal and $attchktime < $finPauseDejeuner) or $attchktime >= $debutPauseMidi) {
                        dd($atttime);
                        return $atttime;
                    }
                    $atttime->add($this->timeService->dateTimeToDateInterval($this->horaireService->getHeursDemiJournerDeTravaille()));
                    return $atttime;
                    break;
                case 2:
                    if (!$this->congerPayer) {
                        $atttime = $this->timeService->generateTime($attchktime[0]);
                        $atttims = $this->timeService->generateTime($attchktime[1]);
                        $a = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $heurDebutTravaille));
                        $b = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $debutPauseDejeuner));
                        $c = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $finPauseDejeuner));
                        $d = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $heurFinTravaille));
                        dump($a);
                        dump($b);
                        dump($c);
                        dd($d);
                        if (
                            !$this->congerPayer
                            and (
                                ($heurDebutTravaille <= $atttime and $atttime <= $finPauseMatinal and $debutPauseMatinal <= $atttims and $atttims <= $finPauseDejeuner)
                                or
                                ($debutPauseMatinal <= $atttime and $atttime <= $finPauseDejeuner and $debutPauseDejeuner <= $atttims and $atttims <= $finPauseMidi)
                                or
                                ($debutPauseDejeuner <= $atttime and $atttime <= $finPauseMidi and $debutPauseMidi <= $atttims and $atttims <= $heurFinTravaille))
                            and $finPauseDejeuner <= $heurFinTravaille
                        ) {
                            $this->congerService->partielConstruct($this->employer, $this->date, $this->date, "CP", true, false, true);
                            $this->congerPayer = $this->congerService->ConstructEntity();
                            $this->employer->addConger($this->congerPayer);
                        }
                    }
                    break;
                case 3:
                    if (!$this->autorisationSortie) {
                        $atttime = new DateTime($attchktime[0]);
                        $atttims = new DateTime($attchktime[1]);
                        $atttim3 = new DateTime($attchktime[2]);
                        $a = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $heurDebutTravaille));
                        $b = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $debutPauseDejeuner));
                        $c = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $finPauseDejeuner));
                        $d = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $heurFinTravaille));
                        dump($a);
                        dump($b);
                        dump($c);
                        dd($d);
                        if (!$this->autorisationSortie and $debutPauseMatinal <= $atttime and $atttime <= $finPauseDejeuner and $debutPauseDejeuner <= $atttims and $atttims <= $finPauseMidi and $debutPauseMidi <= $atttim3 and ($atttim3 <= $heurFinTravaille or $atttim3 >= $heurFinTravaille)) {
                            $this->autorisationSortieService->partielConstruct($this->employer, $this->date, $heurDebutTravaille, $debutPauseMatinal, true, false);
                            $this->autorisationSortie = $this->autorisationSortieService->ConstructEntity();
                            $this->employer->addAutorisationSorties($this->autorisationSortie);
                        } elseif (!$this->autorisationSortie and $heurDebutTravaille <= $atttime and $atttime <= $finPauseMatinal and $debutPauseDejeuner <= $atttims and $atttims <= $finPauseMidi and $debutPauseMidi <= $atttim3 and ($atttim3 <= $heurFinTravaille or $atttim3 >= $heurFinTravaille)) {
                            $this->autorisationSortieService->partielConstruct($this->employer, $this->date, $debutPauseMatinal, $debutPauseDejeuner, true, false);
                            $this->autorisationSortie = $this->autorisationSortieService->ConstructEntity();
                            $this->employer->addAutorisationSorties($this->autorisationSortie);
                        } elseif (!$this->autorisationSortie and $heurDebutTravaille <= $atttime and $atttime <= $finPauseMatinal and $debutPauseMatinal <= $atttims and $atttims <= $finPauseDejeuner and $debutPauseMidi <= $atttim3 and ($atttim3 <= $heurFinTravaille or $atttim3 >= $heurFinTravaille)) {
                            $this->autorisationSortieService->partielConstruct($this->employer, $this->date, $finPauseDejeuner, $debutPauseMidi, true, false);
                            $this->autorisationSortie = $this->autorisationSortieService->ConstructEntity();
                            $this->employer->addAutorisationSorties($this->autorisationSortie);
                        } elseif (!$this->autorisationSortie and $heurDebutTravaille <= $atttime and $atttime <= $finPauseMatinal and $debutPauseMatinal <= $atttims and $atttims <= $finPauseDejeuner and $debutPauseDejeuner <= $atttim3 and $atttim3 <= $finPauseMidi) {
                            $this->autorisationSortieService->partielConstruct($this->employer, $this->date, $debutPauseMidi, $heurFinTravaille, true, false);
                            $this->autorisationSortie = $this->autorisationSortieService->ConstructEntity();
                            $this->employer->addAutorisationSorties($this->autorisationSortie);
                        }
                        //$this->retardMidi = $this->retardMidi($attchktime);
                    }
                    break;
                default:
                    $atttime = new DateTime($attchktime[0]);
                    $atttims = new DateTime($attchktime[1]);
                    $atttim3 = new DateTime($attchktime[2]);
                    $a = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $heurDebutTravaille));
                    $b = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $debutPauseDejeuner));
                    $c = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $finPauseDejeuner));
                    $d = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $heurFinTravaille));
                    dump($a);
                    dump($b);
                    dump($c);
                    dd($d);
                    break;
            }
        } else {
            return null;
        }
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
     * nbrHeurTravailler
     *
     * @return DateTime
     */
    public function nbrHeurTravailler(): DateTime
    {
        $entrer = $this->entrer;
        $sortie = $this->sortie;
        if (!$entrer or !$sortie) {
            return new DateTime("00:00:00");
        }
        $time = new DateTime($sortie->format("H:i:s"));
        if ($this->congerPayer and $this->congerPayer->getDemiJourner()) {
            if ($this->horaire->getDebutPauseMidi() > $entrer) {
                $time->sub($this->timeService->diffTime(
                    $this->horaire->getDebutPauseMatinal(),
                    $this->horaire->getfinPauseMatinal()
                ));
            } else {
                $time->sub($this->timeService->diffTime(
                    $this->horaire->getDebutPauseMidi(),
                    $this->horaire->getFinPauseMidi()
                ));
            }
        } else {
            $time->sub($this->timeService->dateTimeToDateInterval($this->horaireService->sumPause()));
        }
        $time = $this->timeService->diffTime($time, $entrer);
        return $this->timeService->dateIntervalToDateTime($time);
    }

    public function retardMidi(array $attchktime): ?DateTime
    {
        if (count($attchktime) < 3) {
            return null;
        }
        $heurDebutTravaille = $this->timeService->generateTime($this->horaire->getHeurDebutTravaille()->format('H:i:s'));
        $debutPauseMatinal = $this->timeService->generateTime($this->horaire->getDebutPauseMatinal()->format('H:i:s'));
        $finPauseMatinal = $this->timeService->generateTime($this->horaire->getFinPauseMatinal()->format('H:i:s'));
        $debutPauseDejeuner = $this->timeService->generateTime($this->horaire->getDebutPauseDejeuner()->format('H:i:s'));
        $finPauseDejeuner = $this->timeService->generateTime($this->horaire->getFinPauseDejeuner()->format('H:i:s'));
        $debutPauseMidi = $this->timeService->generateTime($this->horaire->getDebutPauseMidi()->format('H:i:s'));
        $finPauseMidi = $this->timeService->generateTime($this->horaire->getFinPauseMidi()->format('H:i:s'));
        $heurFinTravaille = $this->timeService->generateTime($this->horaire->getHeurFinTravaille()->format('H:i:s'));
        $diffAS = new DateInterval('PT' . 1 . 'H' . 0 . 'M' . 0 . 'S');

        $diffSR = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($debutPauseDejeuner, $finPauseDejeuner));

        $atttime = new DateTime($attchktime[0]);
        $atttims = new DateTime($attchktime[1]);
        $atttim3 = new DateTime($attchktime[2]);


        if ($this->autorisationSortie) {
            $diffAS = $this->timeService->diffTime($this->autorisationSortie->getDe(), $this->autorisationSortie->getA());
        }
        if ($debutPauseMatinal <= $atttime and $atttime <= $finPauseDejeuner and $debutPauseDejeuner <= $atttims and $atttims <= $finPauseMidi and $debutPauseMidi <= $atttim3 and ($atttim3 <= $heurFinTravaille or $atttim3 >= $heurFinTravaille)) {
            $diff = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttime, $atttims));
            if ($diffSR < $diff) {
                return  $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($diff, $diffSR));
            }
            return null;
        //new DateInterval('PT' . $diff->h . 'H' . $diff->i . 'M' . $diff->s . 'S');
            /*     $this->autorisationSortieService->partielConstruct($this->employer, $this->date, $heurDebutTravaille, $debutPauseMatinal, false, false);
                    $this->autorisationSortie = $this->autorisationSortieService->ConstructEntity();
                    $this->employer->addAutorisationSorties($this->autorisationSortie);
                */
        } elseif ($heurDebutTravaille <= $atttime and $atttime <= $finPauseMatinal and $debutPauseDejeuner <= $atttims and $atttims <= $finPauseMidi and $debutPauseMidi <= $atttim3 and ($atttim3 <= $heurFinTravaille or $atttim3 >= $heurFinTravaille)) {
            dd($attchktime);
        /*      $this->autorisationSortieService->partielConstruct($this->employer, $this->date, $debutPauseMatinal, $debutPauseDejeuner, false, false);
                $this->autorisationSortie = $this->autorisationSortieService->ConstructEntity();
                $this->employer->addAutorisationSorties($this->autorisationSortie);
            */
        } elseif ($heurDebutTravaille <= $atttime and $atttime <= $finPauseMatinal and $debutPauseMatinal <= $atttims and $atttims <= $finPauseDejeuner and $debutPauseMidi <= $atttim3 and ($atttim3 <= $heurFinTravaille or $atttim3 >= $heurFinTravaille)) {
            dd($attchktime);
        /*        $this->autorisationSortieService->partielConstruct($this->employer, $this->date, $finPauseDejeuner, $debutPauseMidi, false, false);
                $this->autorisationSortie = $this->autorisationSortieService->ConstructEntity();
                $this->employer->addAutorisationSorties($this->autorisationSortie);
          */
        } elseif ($heurDebutTravaille <= $atttime and $atttime <= $finPauseMatinal and $debutPauseMatinal <= $atttims and $atttims <= $finPauseDejeuner and $debutPauseDejeuner <= $atttim3 and $atttim3 <= $finPauseMidi) {
            /*      $this->autorisationSortieService->partielConstruct($this->employer, $this->date, $debutPauseMidi, $heurFinTravaille, false, false);
                    $this->autorisationSortie = $this->autorisationSortieService->ConstructEntity();
                    $this->employer->addAutorisationSorties($this->autorisationSortie);
               */
        } else {
            $diff = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($atttim3, $atttims));
            if ($diffSR < $diff) {
                return  $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($diff, $diffSR));
            }
            return null;
        }





        if ($this->sortie) {
            $sortie = $this->timeService->generateTime($this->sortie->format("H:i:s"));
        } else {
            $sortie = $heurFinTravaille;
        }
        if ($this->congerPayer and $this->congerPayer->getValider() and $this->congerPayer->getDemiJourner()) {
            dump('CP');
            dump($sortie);
            dd($heurFinTravaille);
        } elseif (!$this->congerPayer and $this->autorisationSortie and $this->autorisationSortie->getValider()) {
            dump('AS');
            $as = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($this->autorisationSortie->getDe(), $this->autorisationSortie->getA()));
            dd($as);
            $heurFinTravaille = $this->timeService->diffTime(new DateTime(date('H:i:s', strtotime($heurFinTravaille->format("H:i:s")))), $sortie);
            return $this->timeService->dateIntervalToDateTime($heurFinTravaille);
            dump($sortie);
            dd($heurFinTravaille);
        } else {
            $heurFinTravaille->add($this->timeService->dateTimeToDateInterval($this->horaire->getMargeDuRetard()));
            if ($heurFinTravaille > $sortie) {
                $heurFinTravaille = $this->timeService->diffTime($heurFinTravaille, $sortie);
                return $this->timeService->dateIntervalToDateTime($heurFinTravaille);
            }
            return null;
        }
    }
    /**
     * departAnticiper
     *
     * @return DateTime|null
     */
    public function departAnticiper(): ?DateTime
    {
        $heurDebutTravaille = $this->timeService->generateTime($this->horaire->getHeurDebutTravaille()->format('H:i:s'));
        $finPauseDejeuner = $this->timeService->generateTime($this->horaire->getFinPauseDejeuner()->format('H:i:s'));
        $heurFinTravaille = $this->timeService->generateTime($this->horaire->getHeurFinTravaille()->format('H:i:s'));
        $debutPauseDejeuner = $this->timeService->generateTime($this->horaire->getDebutPauseDejeuner()->format('H:i:s'));
        $entrer= $this->entrer;
        $sortie= $this->sortie;
        if (!$entrer and !$sortie) {
            return null;
        }
        if ($this->congerPayer and $this->congerPayer->getValider() and $this->congerPayer->getDemiJourner()) {
            if (($entrer and ($entrer < $debutPauseDejeuner)) and ($sortie and ($sortie < $finPauseDejeuner))) {
                //$sortie->add($this->timeService->dateTimeToDateInterval($this->horaire->getMargeDuRetard()));
                $heurFinTravaille = $debutPauseDejeuner;
                $heurFinTravaille->add($this->timeService->dateTimeToDateInterval($this->horaire->getMargeDuRetard()));
                if ($heurFinTravaille > $sortie) {
                    $heurFinTravaille = $this->timeService->diffTime($heurFinTravaille, $sortie);
                    dump('CP');
                    dump($this->congerPayer);
                    dump($entrer);
    
                    dump($sortie);
                    dd($heurFinTravaille);
                    return $this->timeService->dateIntervalToDateTime($heurFinTravaille);
                }
                return null;
            }
        } elseif (!$this->congerPayer and $this->autorisationSortie and $this->autorisationSortie->getValider()) {
            dump('AS');
            $as = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($this->autorisationSortie->getDe(), $this->autorisationSortie->getA()));
            dd($as);
            $heurFinTravaille = $this->timeService->diffTime(new DateTime(date('H:i:s', strtotime($heurFinTravaille->format("H:i:s")))), $sortie);
            return $this->timeService->dateIntervalToDateTime($heurFinTravaille);
            dump($sortie);
            dd($heurFinTravaille);
        } else {
            $heurFinTravaille->add($this->timeService->dateTimeToDateInterval($this->horaire->getMargeDuRetard()));
            $sortie->add($this->timeService->dateTimeToDateInterval($this->horaire->getMargeDuRetard()));
            dd($sortie);
            if ($heurFinTravaille > $sortie) {
                $heurFinTravaille = $this->timeService->diffTime($heurFinTravaille, $sortie);
                dd($heurFinTravaille);
                return $this->timeService->dateIntervalToDateTime($heurFinTravaille);
            }
            return null;
        }
    }

    /**
     * retardEnMinute
     *
     * @return DateTime
     */
    public function retardEnMinute(): DateTime
    {
        $heurDebutTravaille = $this->timeService->generateTime($this->horaire->getHeurDebutTravaille()->format('H:i:s'));
        $finPauseDejeuner = $this->timeService->generateTime($this->horaire->getFinPauseDejeuner()->format('H:i:s'));
        $debutPauseDejeuner = $this->timeService->generateTime($this->horaire->getDebutPauseDejeuner()->format('H:i:s'));
        if ($this->entrer) {
            $entrer = $this->timeService->generateTime($this->entrer->format("H:i:s"));
            if (!$this->sortie) {
                $sortie = $entrer;
            } else {
                $sortie = $this->timeService->generateTime($this->sortie->format("H:i:s"));
            }
        } elseif ($this->sortie) {
            $sortie = $this->timeService->generateTime($this->sortie->format("H:i:s"));
            $entrer = $sortie;
        } else {
            $sortie = $heurDebutTravaille;
            $entrer = $heurDebutTravaille;
        }


        if (!$this->congerPayer and $this->autorisationSortie and $this->autorisationSortie->getValider()) {
            dd($this->autorisationSortie);
        }

        if ($this->congerPayer and $this->congerPayer->getValider() and $this->congerPayer->getDemiJourner()) {
            if (($entrer and ($entrer > $debutPauseDejeuner)) and ($sortie and ($sortie > $finPauseDejeuner))) {
                $entrer = $finPauseDejeuner;
                dump('CP');
                dump($this->congerPayer);
                dump($entrer);

                dump($sortie);
                dd($heurDebutTravaille);
                $heurDebutTravaille  = $finPauseDejeuner;
            }
        }

        $heurDebutTravaille->add($this->timeService->dateTimeToDateInterval($this->horaire->getMargeDuRetard()));
        if ($heurDebutTravaille >= $entrer) {
            return new DateTime("00:00:00");
        }
        $heurDebutTravaille = $this->timeService->diffTime($heurDebutTravaille, $entrer);
        return $this->timeService->dateIntervalToDateTime($heurDebutTravaille);
    }


    /**
     * totalRetard
     *
     * @return DateTime
     */
    public function totalRetard(): DateTime
    {
        $e = new DateTime('00:00:00');
        if ($this->retardEnMinute) {
            $e->add($this->timeService->dateTimeToDateInterval($this->retardEnMinute));
        }
        if ($this->departAnticiper) {
            $e->add($this->timeService->dateTimeToDateInterval($this->departAnticiper));
        }
        if ($this->retardMidi) {
            $e->add($this->timeService->dateTimeToDateInterval($this->retardMidi));
        }
        return $e;
    }

    /**
     * heurNormalementTravailler
     *
     * @return DateTime
     */
    public function heurNormalementTravailler(): DateTime
    {
        if ($this->congerPayer and !$this->congerPayer->getDemiJourner()) {
            return new DateTime('00:00:00');
        } elseif ($this->congerPayer and $this->congerPayer->getDemiJourner()) {
            return $this->horaireService->getHeursDemiJournerDeTravaille();
        } elseif (!$this->congerPayer and $this->autorisationSortie) {
            $heursJournerDeTravaille = $this->horaireService->getHeursJournerDeTravaille();
            if ($heursJournerDeTravaille) {
                $heursJournerDeTravaille->sub($this->timeService->diffTime($this->autorisationSortie->getDe(), $this->autorisationSortie->getA()));
            }
            return $heursJournerDeTravaille;
        } else {
            return $this->horaireService->getHeursJournerDeTravaille();
        }
    }

    public function diff(): DateTime
    {
        if ($this->nbrHeurTravailler) {
            return $this->timeService->dateIntervalToDateTime(
                $this->timeService->diffTime(
                    $this->nbrHeurTravailler,
                    $this->heurNormalementTravailler()
                )
            );
        } else {
            return $this->heurNormalementTravailler();
        }
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
    public function bilan(?DateTimeInterface $time, int $total)
    {
        if (!$time) {
            return $total;
        }
        $total += $time->format('H') * 3600; // Convert the hours to seconds and add to our total
        $total += $time->format('i') * 60;  // Convert the minutes to seconds and add to our total
        $total += $time->format('s'); // Add the seconds to our total
        return $total;
    }

    public function calculateurBilan(Pointage $pointage, array $bilan)
    {
        $bilan["nbrHeurTravailler"] = $this->bilan($this->nbrHeurTravailler, $bilan["nbrHeurTravailler"]);
        if ($this->retardEnMinute) {
            $bilan["retardEnMinute"] = $this->bilan($this->retardEnMinute, $bilan["retardEnMinute"]);
        }
        if ($this->departAnticiper) {
            $bilan["departAnticiper"] = $this->bilan($this->departAnticiper, $bilan["departAnticiper"]);
        }
        if ($this->retardMidi) {
            $bilan["retardMidi"] = $this->bilan($this->retardMidi, $bilan["retardMidi"]);
        }
        $bilan["totalRetard"] = $this->bilan($this->totalRetard, $bilan["totalRetard"]);
        if ($this->autorisationSortie) {
            $bilan["autorisationSortie"] = $this->bilan($this->autorisationSortie->getTime(), $bilan["autorisationSortie"]);
        }
        if ($this->congerPayer) {
            if ($this->congerPayer->getDemiJourner()) {
                $bilan["congerPayer"] += 0.5;
            } else {
                $bilan["congerPayer"] += 1;
            }
        }
        $bilan["abscence"] = $this->abscence ? $bilan["abscence"] + 1 : $bilan["abscence"];
        $bilan["heurNormalementTravailler"] = $this->bilan($this->heurNormalementTravailler(), $bilan["heurNormalementTravailler"]);
        $bilan["diff"] = $this->bilan($this->diff, $bilan["diff"]);
        return $bilan;
    }

    public function getBilanSemestriel($pointages)
    {
        $bilan = $this->initBilan;
        $thisWeek = 0;
        $countWeek = 1;
        $collectSemaine = [];
        foreach ($pointages as $this->pointage) {
            $this->constructFromPointage($this->pointage);
            if ($thisWeek != $this->date->format('W')) {
                if ($thisWeek) {
                    array_push($collectSemaine, $bilan);
                    $countWeek++;
                }
                $thisWeek = $this->date->format('W');
                $bilan = $this->initBilan;
                $bilan["date"] = $countWeek;
            }
            $bilan = $this->calculateurBilan($this->pointage, $bilan);
        }
        array_push($collectSemaine, $bilan);
        return $collectSemaine;
    }
    public function getBilanMensuel($pointages)
    {
        $bilan = $this->initBilan;
        $thisYear = 0;
        $thisMonth = 0;
        $collectMensuel = [];
        foreach ($pointages as $this->pointage) {
            $this->constructFromPointage($this->pointage);
            if ($thisYear . '-' . $thisMonth != $this->date->format('Y-m')) {
                if ($thisYear and $thisMonth) {
                    array_push($collectMensuel, $bilan);
                }
                $thisYear =  $this->date->format('Y');
                $thisMonth =  $this->date->format('m');
                $bilan = $this->initBilan;
                $bilan["date"] =  $this->date->format('Y-m');
            }
            $bilan = $this->calculateurBilan($this->pointage, $bilan);
        }
        array_push($collectMensuel, $bilan);
        return $collectMensuel;
    }
    public function getBilanAnnuel($pointages)
    {
        $bilan = $this->initBilan;
        $thisYear = 0;
        $collectAnnuel = [];
        foreach ($pointages as $this->pointage) {
            $this->constructFromPointage($this->pointage);
            if ($thisYear != $this->date->format('Y')) {
                if ($thisYear) {
                    array_push($collectAnnuel, $bilan);
                }
                $thisYear =  $this->date->format('Y');
                $bilan = $this->initBilan;
                $bilan["date"] =  $this->date->format('Y');
            }
            $bilan = $this->calculateurBilan($this->pointage, $bilan);
        }
        array_push($collectAnnuel, $bilan);
        return $collectAnnuel;
    }

    /**
     * getBilanGeneral
     *
     * @param array $pointages
     * @return array
     */
    public function getBilanGeneral(array $pointages): array
    {
        $this->pointages = $pointages;
        usort($this->pointages, fn ($a, $b) => $a->getDate() > $b->getDate());
        $collectGeneral = [];
        $bilanWeek = $this->initBilan;
        $bilanMonth = $this->initBilan;
        $bilanYear = $this->initBilan;
        $thisMonth = 0;
        $thisYear = 0;
        $countWeek = 1;
        $nextWeek = new DateTime("0000-00-00");
        foreach ($this->pointages as $index => $this->pointage) {
            $this->constructFromPointage($this->pointage);
            $this->setPointage($this->pointage);
            $this->pointageDate = $this->date;
            if ($this->date >=  $nextWeek and $index) {
                $bilanWeek["date"] = $countWeek;
                $bilanWeek["background"] = "Orange";
                $bilanWeek["colspan"] = 4;
                $bilanWeek["date"] = "Semaine " . $bilanWeek["date"];
                array_push($collectGeneral, $bilanWeek);
                $bilanWeek = $this->initBilan;
                $countWeek++;
            }
            if ($thisYear . '-' . $thisMonth != $this->pointageDate->format('Y-m') and $index) {
                $bilanMonth["date"] =   $thisYear . '-' . $thisMonth;
                $bilanMonth["background"] = "DodgerBlue";
                $bilanMonth["colspan"] = 4;
                if ($thisYear and  $thisMonth) {
                    array_push($collectGeneral, $bilanMonth);
                }
                $bilanMonth = $this->initBilan;
            }
            if ($thisYear != $this->pointageDate->format('Y') and $index) {
                $bilanYear["date"] =     $thisYear;
                $bilanYear["background"] = "MediumSeaGreen";
                $bilanMonth["colspan"] = 4;
                if ($thisYear) {
                    array_push($collectGeneral, $bilanYear);
                }
                $bilanYear = $this->initBilan;
            }

            $bilanWeek = $this->calculateurBilan($this->pointage, $bilanWeek);
            $bilanMonth = $this->calculateurBilan($this->pointage, $bilanMonth);
            $bilanYear = $this->calculateurBilan($this->pointage, $bilanYear);
            //if (!($this->pointageDate->format("W") == 0) and  !($this->pointageDate->format("W") == 6))
            array_push($collectGeneral, [
                "colspan" => 1,
                "date" =>  $this->pointageDate->format('Y-m-d'),
                "horaire" =>  $this->horaire,
                "entrer" =>  $this->entrer ? $this->entrer->format('H:i:s') : "",
                "sortie" =>  $this->sortie ? $this->sortie->format('H:i:s') : "",
                "nbrHeurTravailler" => $this->nbrHeurTravailler ? $this->nbrHeurTravailler->format('H:i:s') : "",
                "retardEnMinute" => $this->retardEnMinute ? $this->retardEnMinute->format('H:i:s') : "",
                "departAnticiper" => $this->departAnticiper ? $this->departAnticiper->format('H:i:s') : "",
                "retardMidi" => $this->retardMidi ? $this->retardMidi->format('H:i:s') : "",
                "totalRetard" => $this->totalRetard ? $this->totalRetard->format('H:i:s') : "",
                "autorisationSortie" => $this->autorisationSortie ? $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($this->autorisationSortie->getDe(), $this->autorisationSortie->getA()))->format('H:i:s') : "",
                "congerPayer" =>  $this->congerPayer,
                "abscence" => $this->abscence,
                "heurNormalementTravailler" => $this->heurNormalementTravailler() ? $this->heurNormalementTravailler()->format('H:i:s') : "",
                "diff" => $this->diff ? $this->diff->format('H:i:s') : "",
            ]);
            $thisMonth =  $this->date->format('m');
            $thisYear =  $this->date->format('Y');
            $nextWeek = $this->date->setISODate($this->date->format('o'), $this->date->format('W') + 1);
        }
        return $collectGeneral;
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
                        $this->pointage->setAbscence($colomn);
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
