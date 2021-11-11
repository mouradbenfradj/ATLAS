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
     * dateService
     *
     * @var DateService
     */
    private $dateService;
    /**
     * pointage
     *
     * @var Pointage
     */
    private $pointage;

    private $initBilan;
    private $flash;
    private $abscenceService;

    /**
     *
     * @var int
     */
    private $nextYear;
    private $configService;
    private $congerService;
    private $autorisationSortieService;
    private $manager;









    /**
     * __construct
     *
     * @param HoraireService $horaireService
     */
    public function __construct(
        FlashBagInterface $flash,
        HoraireService $horaireService,
        DateService $dateService,
        TimeService $timeService,
        ConfigService $configService,
        EntityManagerInterface $manager,
        AbscenceService $abscenceService,
        CongerService $congerService,
        AutorisationSortieService $autorisationSortieService
    ) {
        $this->horaireService = $horaireService;
        $this->timeService = $timeService;
        $this->dateService = $dateService;
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


    public function constructFromDbf(Dbf $dbf)
    {
        /*
        $abscence =  $this->abscenceService->getAbscence($this->employer, $this->date);
        $conger = $this->congerService->getConger($this->employer, $this->date);
        $autorisationSortie = */
        $this->date = $dbf->getAttdate();
        $this->employer = $dbf->getEmployer();
        $this->horaire = $this->horaireService->getHoraireForDate($this->date);
        $this->congerPayer = $this->estUnConger();
        $this->abscence = $this->estAbscent();
        $this->entrer = $dbf->getStarttime();
        $this->sortie = $dbf->getEndtime();
        $this->nbrHeurTravailler = $this->nbrHeurTravailler(); // $dbf->getWorktime();
        $this->retardEnMinute = $this->retardEnMinute(); //$dbf->getLate();
        if ($dbf->getLate()) {
            $late = new DateTime($dbf->getLate()->format('H:i:s'));
            $late->sub($this->timeService->dateTimeToDateInterval($this->horaire->getMargeDuRetard()));

            if ($this->retardEnMinute()->format('H:i:s') != $late->format('H:i:s')) {
                dump($dbf);
                dump($this->retardEnMinute());
                dump($late);
                $debutHeurDeTravaille = new DateTime($this->horaire->getHeurDebutTravaille()->format("H:i:s"));
                dump($debutHeurDeTravaille);
                if (!$this->congerPayer and $this->autorisationSortie)
                    dd($this->autorisationSortie);

                if ($this->entrer)
                    $entrer = $this->entrer;
                else
                    $entrer = $debutHeurDeTravaille;

                if ($this->congerPayer and $this->congerPayer->getDemiJourner()) {
                    dump('CP');
                    dump($entrer);
                    dd($debutHeurDeTravaille);
                }

                $debutHeurDeTravaille->add($this->timeService->dateTimeToDateInterval($this->horaire->getMargeDuRetard()));
                if ($debutHeurDeTravaille >= $entrer)
                    dump($debutHeurDeTravaille);
                else                dump($debutHeurDeTravaille);

                $debutHeurDeTravaille = $this->timeService->diffTime($debutHeurDeTravaille, $entrer);
                dd($this->timeService->dateIntervalToDateTime($debutHeurDeTravaille));
            }
        }
        $this->departAnticiper = $this->departAnticiper(); //$dbf->getEarly();
        $this->retardMidi = null;
        $this->totalRetard = $this->totalRetard();
        $this->heurNormalementTravailler = $this->heurNormalementTravailler();
        if ($dbf->getWorktime()) {

            if ($this->heurNormalementTravailler()->format('H:i:s') != $dbf->getWorktime()->format('H:i:s')) {
                dump($dbf);
                dump($this->heurNormalementTravailler());
                dd($dbf->getWorktime());
            }
        }
        $this->diff = $this->diff();
        $this->workTime = null;
    }

    public function constructFromPointage(Pointage $pointage)
    {
        /*
        $abscence =  $this->abscenceService->getAbscence($this->employer, $this->date);
        $conger = $this->congerService->getConger($this->employer, $this->date);
        $autorisationSortie = */

        $this->date = $pointage->getDate();
        $this->employer = $pointage->getEmployer();
        $this->horaire = $this->horaireService->getHoraireForDate($this->date);
        $this->autorisationSortie =  $this->autorisationSortieService->getAutorisation($this->employer, $this->date);
        $this->entrer = $pointage->getEntrer();
        $this->sortie = $pointage->getSortie();
        $this->abscence = $pointage->getAbscence();
        $this->congerPayer = $pointage->getCongerPayer();
        $this->nbrHeurTravailler = $pointage->getNbrHeurTravailler();
        $this->retardEnMinute = $pointage->getRetardEnMinute();
        $this->departAnticiper = $pointage->getDepartAnticiper();
        $this->retardMidi = $pointage->getRetardMidi();;
        $this->totalRetard = $pointage->getTotaleRetard();
        $this->heurNormalementTravailler = $pointage->getHeurNormalementTravailler();
        $this->diff = $pointage->getDiff();
        $this->workTime = $pointage->getWorkTime();
    }

    public function createEntity(): Pointage
    {
        $this->pointage = new Pointage();
        $this->pointage->setDate($this->date);
        $this->pointage->setHoraire($this->horaire);
        $this->pointage->setAbscence($this->abscence);
        /* if (!$this->entrer and !$this->sortie and !$conger and !$abscence) {
            $abscence = new Abscence();
            $abscence->setDebut($this->date);
            $abscence->setFin($this->date);
            $this->pointage->setAbscence($abscence);
            $this->pointage->setRetardEnMinute($this->pointageService->retardEnMinute());
            $this->pointage->setTotaleRetard($this->pointageService->totalRetard());
            $this->pointage->setHeurNormalementTravailler($this->pointageService->heurNormalementTravailler());
            $this->pointage->setDiff($this->pointageService->diff());
            //dd($this->pointage);
            $this->manager->remove($dbf);
            $this->employer->addAbscence($abscence);
            $this->employer->addPointage($this->pointage);
        } else  */
        /* if (!$this->entrer and !$this->sortie and $conger and !$conger->getDemiJourner()) {
            $this->pointage->setCongerPayer($conger ? $conger : null);
            dd($this->pointage);
        } else  */
        // if ($this->entrer and $this->sortie /* and !$conger and !$autorisationSortie */) {
        $this->pointage->setCongerPayer($this->congerPayer);
        $this->pointage->setAutorisationSortie($this->autorisationSortie);
        $this->pointage->setEntrer($this->entrer);
        $this->pointage->setSortie($this->sortie);
        $this->pointage->setNbrHeurTravailler($this->nbrHeurTravailler);
        $this->pointage->setRetardEnMinute($this->retardEnMinute);
        $this->pointage->setTotaleRetard($this->totalRetard);
        $this->pointage->setHeurNormalementTravailler($this->heurNormalementTravailler);
        $this->pointage->setDiff($this->diff);
        $this->pointage->setEmployer($this->employer);
        //   dd($this->pointage);
        /*$this->pointage->setDepartAnticiper(null);
                        $this->pointage->setRetardMidi(null);*/
        // $this->manager->remove($dbf);
        // $this->employer->addPointage($this->pointage);
        // }
        return $this->pointage;
    }


    public function estAbscent()
    {
        $abscence =  $this->abscenceService->getAbscence($this->employer, $this->date);
        $conger = $this->congerService->getConger($this->employer, $this->date);
        if (!$this->entrer and !$this->sortie and !$conger and !$abscence) {
            $abscence = new Abscence();
            $abscence->setDebut($this->date);
            $abscence->setFin($this->date);
            $this->employer->addAbscence($abscence);
            /*  $this->pointage->setAbscence($abscence);
            $this->pointage->setRetardEnMinute($this->retardEnMinute);
            $this->pointage->setTotaleRetard($this->totalRetard());
            $this->pointage->setHeurNormalementTravailler($this->heurNormalementTravailler());
            $this->pointage->setDiff($this->diff()); */
            //dd($this->pointage);
            //$this->manager->remove($dbf);
            return $abscence;
        } else
            return $abscence;
    }
    public function estUnConger()
    {
        $abscence =  $this->abscenceService->getAbscence($this->employer, $this->date);
        $conger = $this->congerService->getConger($this->employer, $this->date);
        if (!$this->entrer and !$this->sortie and !$abscence and $conger and !$conger->getDemiJourner()) {
            dd($conger);
            $this->pointage->setCongerPayer($conger ? $conger : null);
            dd($this->pointage);
        } else if ((!$this->entrer or !$this->sortie) and !$abscence and $conger and $conger->getDemiJourner()) {
            dd($this->pointage);
        } else
            return $conger;
    }

    public function dbfUpdated(Dbf $dbf)
    {
        $user = $dbf->getEmployer();
        $abscence =  $this->abscenceService->getAbscence($user, $this->date);
        $conger = $this->congerService->getConger($user, $this->date);
        $autorisationSortie = $this->autorisationSortieService->getAutorisation($user, $this->date);
        $this->pointage = new Pointage();
        $this->pointage->setDate($this->date);
        $this->pointage->setHoraire($this->horaireService->getHoraireForDate($this->date));
        $this->pointage->setCongerPayer($conger ? $conger : null);
        $this->pointage->setAutorisationSortie($autorisationSortie ? $autorisationSortie : null);
        $this->pointage->setEntrer($this->entrer);
        $this->pointage->setSortie($this->sortie);
        $this->setPointage($this->pointage);
        $this->pointage->setNbrHeurTravailler($this->nbrHeurTravailler());
        $this->pointage->setRetardEnMinute($this->retardEnMinute);
        $this->pointage->setTotaleRetard($this->totalRetard());
        $this->pointage->setHeurNormalementTravailler($this->heurNormalementTravailler());
        $this->pointage->setDiff($this->diff());
        $this->pointage->setEmployer($user);
        $this->manager->persist($this->pointage);
        $this->manager->remove($dbf);
        $this->manager->flush();
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
            $user->getPointages()->toArray()
        );
    }


    public function bilan(?DateTimeInterface $time, int $total)
    {
        if (!$time)
            return $total;
        $total += $time->format('H') * 3600; // Convert the hours to seconds and add to our total
        $total += $time->format('i') * 60;  // Convert the minutes to seconds and add to our total
        $total += $time->format('s'); // Add the seconds to our total
        return $total;
    }

    public function calculateurBilan(Pointage $pointage, array $bilan)
    {
        $bilan["nbrHeurTravailler"] = $this->bilan($this->nbrHeurTravailler, $bilan["nbrHeurTravailler"]);
        if ($this->retardEnMinute)
            $bilan["retardEnMinute"] = $this->bilan($this->retardEnMinute, $bilan["retardEnMinute"]);
        if ($this->departAnticiper)
            $bilan["departAnticiper"] = $this->bilan($this->departAnticiper, $bilan["departAnticiper"]);
        if ($this->retardMidi)
            $bilan["retardMidi"] = $this->bilan($this->getRetardMidi, $bilan["retardMidi"]);
        $bilan["totalRetard"] = $this->bilan($this->totalRetard, $bilan["totalRetard"]);
        if ($this->autorisationSortie)
            $bilan["autorisationSortie"] = $this->bilan($this->autorisationSortie->getTime(), $bilan["autorisationSortie"]);
        if ($this->congerPayer) {
            if ($this->congerPayer->getDemiJourner())
                $bilan["congerPayer"] += 0.5;
            else
                $bilan["congerPayer"] += 1;
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
                if ($thisYear and $thisMonth)
                    array_push($collectMensuel, $bilan);
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
                if ($thisYear)
                    array_push($collectAnnuel, $bilan);
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
     * @param Collection $this->pointages
     * @return array
     */
    public function getBilanGeneral(Collection $pointages): array
    {
        $this->pointages = $pointages->toArray();
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
                if ($thisYear and  $thisMonth)
                    array_push($collectGeneral,  $bilanMonth);
                $bilanMonth = $this->initBilan;
            }
            if ($thisYear != $this->pointageDate->format('Y') and $index) {
                $bilanYear["date"] =     $thisYear;
                $bilanYear["background"] = "MediumSeaGreen";
                $bilanMonth["colspan"] = 4;
                if ($thisYear)
                    array_push($collectGeneral, $bilanYear);
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
                "autorisationSortie" => $this->autorisationSortie ? $this->autorisationSortie->getTime()->format('H:i:s') : "",
                "congerPayer" =>  $this->congerPayer,
                "abscence" => $this->abscence,
                "heurNormalementTravailler" => $this->heurNormalementTravailler() ? $this->heurNormalementTravailler()->format('H:i:s') : "",
                "diff" => $this->diff ? $this->diff->format('H:i:s') : "",
            ]);
            $thisMonth =  $this->date->format('m');
            $thisYear =  $this->date->format('Y');
            $nextWeek = $this->date->setISODate($this->date->format('o'), $this->date->format('W') + 1);
        }

        /*
        $bilanMonth = $this->initBilan;
        $bilanYear = $this->initBilan;
        $thisWeek = 0;
        $nextWeek = 0;
        $thisMonth = 0;
        $thisYear = 0;
        $countWeek = 1;
        foreach ($this->pointages as  $this->pointage) {
            $this->setPointage($this->pointage);
            $this->setHoraireServiceHoraire();
            $thisWeek = $this->date->format('W');
            if ($thisWeek >=  $nextWeek) {
                $bilan["date"] = $countWeek;
                if ($nextWeek) {
                    $bilan["background"] = "Orange";
                    $bilan["colspan"] = 4;
                    $bilan["date"] = "Semaine " . $bilan["date"];
                    array_push($collectGeneral, $bilan);
                    $bilan = $this->initBilan;
                    $countWeek++;
                }

                $nextWeek = $this->date->format('W') + 1;
            }
            if ($thisYear . '-' . $thisMonth != $this->date->format('Y-m')) {
                $bilanMonth["date"] =   $thisYear . '-' . $thisMonth;
                $bilanMonth["background"] = "DodgerBlue";
                $bilanMonth["colspan"] = 4;
                if ($thisYear and  $thisMonth)
                    array_push($collectGeneral,  $bilanMonth);
                $bilanMonth = $this->initBilan;
            }
            if ($thisYear != $this->date->format('Y')) {
                $bilanYear["date"] =     $thisYear;
                $bilanYear["background"] = "MediumSeaGreen";
                $bilanMonth["colspan"] = 4;
                if ($thisYear)
                    array_push($collectGeneral, $bilanYear);
                $bilanYear = $this->initBilan;
            }
            $bilan = $this->calculateurBilan($this->pointage, $bilan);
            $bilanMonth = $this->calculateurBilan($this->pointage, $bilanMonth);
            $bilanYear = $this->calculateurBilan($this->pointage, $bilanYear);

            $thisMonth =  $this->date->format('m');
            $thisYear =  $this->date->format('Y');
            
        }
        if (!empty($collectGeneral))
            array_push($collectGeneral, $bilan); */
        return $collectGeneral;
    }




    public function setHoraireServiceHoraire()
    {
        $this->horaireService->setHoraire($this->horaire);
    }

    public function nbrHeurTravailler()
    {
        $entrer =  $this->entrer;
        $sortie = $this->sortie;
        if (!$entrer or !$sortie)
            return new DateTime("00:00:00");
        $time = new DateTime($sortie->format("H:i:s"));
        $time->sub($this->timeService->dateTimeToDateInterval($this->horaireService->sumPause()));
        $time = $this->timeService->diffTime($time,  $entrer);
        return $this->timeService->dateIntervalToDateTime($time);
    }

    public function departAnticiper()
    {
        $heurFinTravaille = new DateTime($this->horaireService->getHoraire()->getHeurFinTravaille()->format("H:i:s"));
        if ($this->sortie)
            $sortie = new DateTime(date('H:i:s', strtotime($this->sortie->format("H:i:s"))));
        else
            $sortie = $heurFinTravaille;
        if ($this->congerPayer and $this->congerPayer->getDemiJourner()) {
            dump('CP');
            dump($sortie);
            dd($heurFinTravaille);
        } elseif (!$this->congerPayer and $this->autorisationSortie) {
            dump('AS');
            $as = $this->timeService->dateIntervalToDateTime($this->timeService->diffTime($this->autorisationSortie->getDe(), $this->autorisationSortie->getA()));
            dd($as);
            $heurFinTravaille = $this->timeService->diffTime(new DateTime(date('H:i:s', strtotime($heurFinTravaille->format("H:i:s")))), $sortie);
            return $this->timeService->dateIntervalToDateTime($heurFinTravaille);
            dump($sortie);
            dd($heurFinTravaille);
        } else {
            $heurFinTravaille->add($this->timeService->margeDuRetard());
            if ($heurFinTravaille >= $sortie) {
                $heurFinTravaille = $this->timeService->diffTime(new DateTime(date('H:i:s', strtotime($heurFinTravaille->format("H:i:s")))), $sortie);
                return $this->timeService->dateIntervalToDateTime($heurFinTravaille);
            }
            return null;
        }
    }
    public function retardEnMinute()
    {

        $debutHeurDeTravaille = new DateTime($this->horaire->getHeurDebutTravaille()->format("H:i:s"));
        if (!$this->congerPayer and $this->autorisationSortie)
            dd($this->autorisationSortie);

        if ($this->entrer)
            $entrer = $this->entrer;
        else
            $entrer = $debutHeurDeTravaille;

        if ($this->congerPayer and $this->congerPayer->getDemiJourner()) {
            dump('CP');
            dump($entrer);
            dd($debutHeurDeTravaille);
        }

        $debutHeurDeTravaille->add($this->timeService->dateTimeToDateInterval($this->horaire->getMargeDuRetard()));
        if ($debutHeurDeTravaille >= $entrer)
            return new DateTime("00:00:00");
        $debutHeurDeTravaille = $this->timeService->diffTime($debutHeurDeTravaille, $entrer);
        return $this->timeService->dateIntervalToDateTime($debutHeurDeTravaille);
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
        if ($this->congerPayer and !$this->congerPayer->getDemiJourner())
            return new DateTime('00:00:00');
        elseif ($this->congerPayer and $this->congerPayer->getDemiJourner())
            return $this->horaireService->getHeursDemiJournerDeTravaille();
        elseif (!$this->congerPayer and $this->autorisationSortie) {
            dd($this->horaireService->getHeursJournerDeTravaille());
            return $this->horaireService->getHeursDemiJournerDeTravaille();
        } else
            return $this->horaireService->getHeursJournerDeTravaille();
        /*  $heurFinTravaille = new DateTime($this->horaireService->getHoraire()->getHeurFinTravaille()->format("H:i:s"));
        $heurDebutTravaille = $this->horaireService->getHoraire()->getHeurDebutTravaille();
        if ($this->congerPayer and $this->congerPayer->getDemiJourner()) {
            dd('demijourner heur normalement travailer');
        } else {
            if ($this->autorisationSortie)
                $heurFinTravaille->sub(
                    $this->timeService->diffTime(
                        $this->autorisationSortie->getDe(),
                        $this->autorisationSortie->getA()
                    )
                );
            $e = $this->horaireService->sumPause();
            $heurFinTravaille->sub($this->timeService->dateTimeToDateInterval($e));
            $heurFinTravaille = $this->timeService->diffTime($heurFinTravaille, $heurDebutTravaille);
            return $this->timeService->dateIntervalToDateTime($heurFinTravaille); */
    }

    public function diff(): DateTime
    {
        if ($this->nbrHeurTravailler)

            return $this->timeService->dateIntervalToDateTime(
                $this->timeService->diffTime(
                    $this->nbrHeurTravailler,
                    $this->heurNormalementTravailler()
                )
            );
        else
            return $this->heurNormalementTravailler();
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
     * Get the value of nextYear
     *
     * @return  int
     */
    public function getNextYear()
    {
        $this->nextYear = new DateTime($this->date->format("Y-m-d"));
        $this->nextYear->modify('+1 year');
        return $this->nextYear;
    }

    /**
     * Set the value of nextYear
     *
     * @param  int  $nextYear
     *
     * @return  self
     */
    public function setNextYear(int $nextYear)
    {

        $this->nextYear = $nextYear + 1;
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

    public function addLigne(array $ligne, User $user)
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
    }
}
