<?php


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

class PointageServiceCopy
{

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
        $this->date = null;
        $this->entrer = null;
        $this->sortie = null;
        $this->nbrHeurTravailler = null;
        $this->retardEnMinute = null;
        $this->departAnticiper = null;
        $this->retardMidi = null;
        $this->totaleRetard = null;
        $this->heurNormalementTravailler = null;
        $this->diff = null;
        $this->employer = null;
        $this->horaire = null;
        $this->congerPayer = null;
        $this->autorisationSortie = null;
        $this->workTime = null;
        $this->absence = null;
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
        $attchktime = (count($dbf->getAttchktime()) == 1) ? (($dbf->getAttchktime()[0] == "") ? [] : $dbf->getAttchktime()) : $dbf->getAttchktime();
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
        $this->totaleRetard = $this->retardService->totaleRetard();
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
        $this->retardMidi = $pointage->getRetardMidi();;
        $this->totaleRetard = $pointage->getTotaleRetard();
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
        $this->pointage->setTotaleRetard($this->totaleRetard);
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
    public function dbfUpdated(Pointage $pointage, Dbf $dbf): void
    {
        $this->pointage = $pointage;
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
     * Get date
     *
     * @return  DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set date
     *
     * @param  DateTime  $date  date
     *
     * @return  self
     */
    public function setDate(DateTime $date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get entrer
     *
     * @return  DateTime
     */
    public function getEntrer()
    {
        return $this->entrer;
    }

    /**
     * Set entrer
     *
     * @param  DateTime  $entrer  entrer
     *
     * @return  self
     */
    public function setEntrer(DateTime $entrer)
    {
        $this->entrer = $entrer;

        return $this;
    }

    /**
     * Get sortie
     *
     * @return  DateTime
     */
    public function getSortie()
    {
        return $this->sortie;
    }

    /**
     * Set sortie
     *
     * @param  DateTime  $sortie  sortie
     *
     * @return  self
     */
    public function setSortie(DateTime $sortie)
    {
        $this->sortie = $sortie;

        return $this;
    }

    /**
     * Get nbrHeurTravailler
     *
     * @return  DateTime|null
     */
    public function getNbrHeurTravailler()
    {
        return $this->nbrHeurTravailler;
    }

    /**
     * Set nbrHeurTravailler
     *
     * @param  DateTime|null  $nbrHeurTravailler  nbrHeurTravailler
     *
     * @return  self
     */
    public function setNbrHeurTravailler($nbrHeurTravailler)
    {
        $this->nbrHeurTravailler = $nbrHeurTravailler;

        return $this;
    }

    /**
     * Get retardEnMinute
     *
     * @return  DateTime|null
     */
    public function getRetardEnMinute()
    {
        return $this->retardEnMinute;
    }

    /**
     * Set retardEnMinute
     *
     * @param  DateTime|null  $retardEnMinute  retardEnMinute
     *
     * @return  self
     */
    public function setRetardEnMinute($retardEnMinute)
    {
        $this->retardEnMinute = $retardEnMinute;

        return $this;
    }

    /**
     * Get departAnticiper
     *
     * @return  DateTime|null
     */
    public function getDepartAnticiper()
    {
        return $this->departAnticiper;
    }

    /**
     * Set departAnticiper
     *
     * @param  DateTime|null  $departAnticiper  departAnticiper
     *
     * @return  self
     */
    public function setDepartAnticiper($departAnticiper)
    {
        $this->departAnticiper = $departAnticiper;

        return $this;
    }

    /**
     * Get retardMidi
     *
     * @return  DateTime|null
     */
    public function getRetardMidi()
    {
        return $this->retardMidi;
    }

    /**
     * Set retardMidi
     *
     * @param  DateTime|null  $retardMidi  retardMidi
     *
     * @return  self
     */
    public function setRetardMidi($retardMidi)
    {
        $this->retardMidi = $retardMidi;

        return $this;
    }

    /**
     * Get totaleRetard
     *
     * @return  DateTime
     */
    public function getTotaleRetard()
    {
        return $this->totaleRetard;
    }

    /**
     * Set totaleRetard
     *
     * @param  DateTime  $totaleRetard  totaleRetard
     *
     * @return  self
     */
    public function setTotaleRetard(DateTime $totaleRetard)
    {
        $this->totaleRetard = $totaleRetard;

        return $this;
    }

    /**
     * Get heurNormalementTravailler
     *
     * @return  DateTime
     */
    public function getHeurNormalementTravailler()
    {
        return $this->heurNormalementTravailler;
    }

    /**
     * Set heurNormalementTravailler
     *
     * @param  DateTime  $heurNormalementTravailler  heurNormalementTravailler
     *
     * @return  self
     */
    public function setHeurNormalementTravailler(DateTime $heurNormalementTravailler)
    {
        $this->heurNormalementTravailler = $heurNormalementTravailler;

        return $this;
    }

    /**
     * Get diff
     *
     * @return  DateTime
     */
    public function getDiff()
    {
        return $this->diff;
    }

    /**
     * Set diff
     *
     * @param  DateTime  $diff  diff
     *
     * @return  self
     */
    public function setDiff(DateTime $diff)
    {
        $this->diff = $diff;

        return $this;
    }

    /**
     * Get employer
     *
     * @return  User
     */
    public function getEmployer()
    {
        return $this->employer;
    }

    /**
     * Set employer
     *
     * @param  User  $employer  employer
     *
     * @return  self
     */
    public function setEmployer(User $employer)
    {
        $this->employer = $employer;

        return $this;
    }

    /**
     * Get horaire
     *
     * @return  Horaire
     */
    public function getHoraire()
    {
        return $this->horaire;
    }

    /**
     * Set horaire
     *
     * @param  Horaire  $horaire  horaire
     *
     * @return  self
     */
    public function setHoraire(Horaire $horaire)
    {
        $this->horaire = $horaire;

        return $this;
    }

    /**
     * Get congerPayer
     *
     * @return  Conger|null
     */
    public function getCongerPayer()
    {
        return $this->congerPayer;
    }

    /**
     * Set congerPayer
     *
     * @param  Conger|null  $congerPayer  congerPayer
     *
     * @return  self
     */
    public function setCongerPayer($congerPayer)
    {
        $this->congerPayer = $congerPayer;

        return $this;
    }

    /**
     * Get autorisationSortie
     *
     * @return  AutorisationSortie|null
     */
    public function getAutorisationSortie()
    {
        return $this->autorisationSortie;
    }

    /**
     * Set autorisationSortie
     *
     * @param  AutorisationSortie|null  $autorisationSortie  autorisationSortie
     *
     * @return  self
     */
    public function setAutorisationSortie($autorisationSortie)
    {
        $this->autorisationSortie = $autorisationSortie;

        return $this;
    }

    /**
     * Get workTime
     *
     * @return  WorkTime|null
     */
    public function getWorkTime()
    {
        return $this->workTime;
    }

    /**
     * Set workTime
     *
     * @param  WorkTime|null  $workTime  workTime
     *
     * @return  self
     */
    public function setWorkTime($workTime)
    {
        $this->workTime = $workTime;

        return $this;
    }

    /**
     * Get absence
     *
     * @return  Absence|null
     */
    public function getAbsence()
    {
        return $this->absence;
    }

    /**
     * Set absence
     *
     * @param  Absence|null  $absence  absence
     *
     * @return  self
     */
    public function setAbsence($absence)
    {
        $this->absence = $absence;

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
     * @param  Pointage  $pointage  pointage
     *
     * @return  self
     */
    public function setPointage(Pointage $pointage)
    {
        $this->pointage = $pointage;

        return $this;
    }
}
