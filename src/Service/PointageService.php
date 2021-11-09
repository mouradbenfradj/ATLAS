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
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class PointageService
{
    private $date;
    private $entrer;
    private $sortie;
    private $nbrHeurTravailler;
    private $retardEnMinute;
    private $departAnticiper;
    private $retardMidi;
    private $totaleRetard;
    private $heurNormalementTravailler;
    private $diff;
    private $employer;
    private $horaire;
    private $congerPayer;
    private $autorisationSortie;
    private $workTime;
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
            "totaleRetard" => 0,
            "autorisationSortie" => 0,
            "congerPayer" =>  0,
            "abscence" =>  0,
            "heurNormalementTravailler" => 0,
            "diff" => 0,
        ];
    }


    public function constructFromDbf(Dbf $dbf)
    {
        $this->date = $dbf->getAttdate();
        $this->entrer = $dbf->getStarttime();
        $this->sortie = $dbf->getEndtime();
        $this->nbrHeurTravailler = $this->nbrHeurTravailler(); // $dbf->getWorktime();
        $this->retardEnMinute = $this->retardEnMinute(); //$dbf->getLate();
        $this->departAnticiper = null; //$dbf->getEarly();
        $this->retardMidi = null;
        $this->totaleRetard = $this->totalRetard();
        $this->heurNormalementTravailler = null;
        $this->diff = null;
        $this->employer = $dbf->getEmployer();
        $this->horaire = null;
        $this->congerPayer = null;
        $this->autorisationSortie = null;
        $this->workTime = null;
        $this->abscence = null;
    }

    public function createEntity()
    {
        $abscence =  $this->abscenceService->getAbscence($this->employer, $this->date);
        $conger = $this->congerService->getConger($this->employer, $this->date);
        $autorisationSortie = $this->autorisationSortieService->getAutorisation($this->employer, $this->date);
        dd($autorisationSortie);
        $this->pointage = new Pointage();
        $this->pointage->setDate($this->date);
        $this->pointage->setHoraire($this->horaireService->getHoraireForDate($this->date));
        if (!$this->entrer and !$this->sortie and !$conger and !$abscence) {
            $abscence = new Abscence();
            $abscence->setDebut($this->date);
            $abscence->setFin($this->date);
            $this->pointage->setAbscence($abscence);
            $this->setPointage($this->pointage);
            $this->pointage->setRetardEnMinute($this->pointageService->retardEnMinute());
            $this->pointage->setTotaleRetard($this->pointageService->totalRetard());
            $this->pointage->setHeurNormalementTravailler($this->pointageService->heurNormalementTravailler());
            $this->pointage->setDiff($this->pointageService->diff());
            //dd($this->pointage);
            $this->manager->remove($dbf);
            $this->employer->addAbscence($abscence);
            $this->employer->addPointage($this->pointage);
        } else if (!$this->entrer and !$this->sortie and $conger and !$conger->getDemiJourner()) {
            $this->pointage->setCongerPayer($conger ? $conger : null);
            dd($this->pointage);
            $this->manager->remove($dbf);
            $this->employer->addPointage($this->pointage);
        } else if ($this->entrer and $this->sortie /* and !$conger and !$autorisationSortie */) {
            $this->pointage->setCongerPayer($conger ? $conger : null);
            $this->pointage->setAutorisationSortie($autorisationSortie ? $autorisationSortie : null);
            $this->pointage->setEntrer($this->entrer);
            $this->pointage->setSortie($this->sortie);
            $this->pointageService->setPointage($this->pointage);
            $this->pointage->setNbrHeurTravailler($this->pointageService->nbrHeurTravailler());
            $this->pointage->setRetardEnMinute($this->pointageService->retardEnMinute());
            $this->pointage->setTotaleRetard($this->pointageService->totalRetard());
            $this->pointage->setHeurNormalementTravailler($this->pointageService->heurNormalementTravailler());
            $this->pointage->setDiff($this->pointageService->diff());
            /*$this->pointage->setDepartAnticiper(null);
                        $this->pointage->setRetardMidi(null);*/
            $this->manager->remove($dbf);
            $this->employer->addPointage($this->pointage);
        }
        return $this->pointage;
    }



    public function dbfUpdated(Dbf $dbf)
    {
        $user = $dbf->getEmployer();
        $abscence =  $this->abscenceService->getAbscence($user, $this->date);
        $conger = $this->congerService->getConger($user, $this->date);
        $autorisationSortie = $this->autorisationSortieService->getAutorisation($user, $this->date);
        $this->pointage = new Pointage();
        $this->pointage->setDate($this->date);
        $this->pointage->setHoraire($this->horaireService->getHoraireForDate($this->pointage->getDate()));
        $this->pointage->setCongerPayer($conger ? $conger : null);
        $this->pointage->setAutorisationSortie($autorisationSortie ? $autorisationSortie : null);
        $this->pointage->setEntrer($this->entrer);
        $this->pointage->setSortie($this->sortie);
        $this->setPointage($this->pointage);
        $this->pointage->setNbrHeurTravailler($this->nbrHeurTravailler());
        $this->pointage->setRetardEnMinute($this->retardEnMinute());
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

    public function calculateurBilan(Pointage $this->pointage, array $bilan)
    {
        $bilan["nbrHeurTravailler"] = $this->bilan($this->pointage->getNbrHeurTravailler(), $bilan["nbrHeurTravailler"]);
        if ($this->pointage->getRetardEnMinute())
            $bilan["retardEnMinute"] = $this->bilan($this->pointage->getRetardEnMinute(), $bilan["retardEnMinute"]);
        if ($this->pointage->getDepartAnticiper())
            $bilan["departAnticiper"] = $this->bilan($this->pointage->getDepartAnticiper(), $bilan["departAnticiper"]);
        if ($this->pointage->getRetardMidi())
            $bilan["retardMidi"] = $this->bilan($this->pointage->getRetardMidi(), $bilan["retardMidi"]);
        $bilan["totaleRetard"] = $this->bilan($this->pointage->getTotaleRetard(), $bilan["totaleRetard"]);
        if ($this->pointage->getAutorisationSortie())
            $bilan["autorisationSortie"] = $this->bilan($this->pointage->getAutorisationSortie()->getTime(), $bilan["autorisationSortie"]);
        if ($this->pointage->getCongerPayer()) {
            if ($this->pointage->getCongerPayer()->getDemiJourner())
                $bilan["congerPayer"] += 0.5;
            else
                $bilan["congerPayer"] += 1;
        }
        $bilan["abscence"] = $this->pointage->getAbscence() ? $bilan["abscence"] + 1 : $bilan["abscence"];
        $bilan["heurNormalementTravailler"] = $this->bilan($this->pointage->getHeurNormalementTravailler(), $bilan["heurNormalementTravailler"]);
        $bilan["diff"] = $this->bilan($this->pointage->getDiff(), $bilan["diff"]);
        return $bilan;
    }

    public function getBilanSemestriel($this->pointages)
    {
        $bilan = $this->initBilan;
        $thisWeek = 0;
        $countWeek = 1;
        $collectSemaine = [];
        foreach ($this->pointages as $this->pointage) {
            if ($thisWeek != $this->pointage->getDate()->format('W')) {
                if ($thisWeek) {
                    array_push($collectSemaine, $bilan);
                    $countWeek++;
                }
                $thisWeek = $this->pointage->getDate()->format('W');
                $bilan = $this->initBilan;
                $bilan["date"] = $countWeek;
            }
            $bilan = $this->calculateurBilan($this->pointage, $bilan);
        }
        array_push($collectSemaine, $bilan);
        return $collectSemaine;
    }
    public function getBilanMensuel($this->pointages)
    {
        $bilan = $this->initBilan;
        $thisYear = 0;
        $thisMonth = 0;
        $collectMensuel = [];
        foreach ($this->pointages as $this->pointage) {
            if ($thisYear . '-' . $thisMonth != $this->pointage->getDate()->format('Y-m')) {
                if ($thisYear and $thisMonth)
                    array_push($collectMensuel, $bilan);
                $thisYear =  $this->pointage->getDate()->format('Y');
                $thisMonth =  $this->pointage->getDate()->format('m');
                $bilan = $this->initBilan;
                $bilan["date"] =  $this->pointage->getDate()->format('Y-m');
            }
            $bilan = $this->calculateurBilan($this->pointage, $bilan);
        }
        array_push($collectMensuel, $bilan);
        return $collectMensuel;
    }
    public function getBilanAnnuel($this->pointages)
    {
        $bilan = $this->initBilan;
        $thisYear = 0;
        $collectAnnuel = [];
        foreach ($this->pointages as $this->pointage) {
            if ($thisYear != $this->pointage->getDate()->format('Y')) {
                if ($thisYear)
                    array_push($collectAnnuel, $bilan);
                $thisYear =  $this->pointage->getDate()->format('Y');
                $bilan = $this->initBilan;
                $bilan["date"] =  $this->pointage->getDate()->format('Y');
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
    public function getBilanGeneral(Collection $this->pointages): array
    {
        $this->pointages = $this->pointages->toArray();
        usort($this->pointages, fn ($a, $b) => $a->getDate() > $b->getDate());
        $collectGeneral = [];
        $bilanWeek = $this->initBilan;
        $countWeek = 1;
        $nextWeek = new DateTime("0000-00-00");
        foreach ($this->pointages as $index => $this->pointage) {
            $this->setPointage($this->pointage);
            $this->pointageDate = $this->pointage->getdate();
            if ($this->pointage->getdate() >=  $nextWeek and $index) {
                $bilanWeek["date"] = $countWeek;
                $bilanWeek["background"] = "Orange";
                $bilanWeek["colspan"] = 4;
                $bilanWeek["date"] = "Semaine " . $bilanWeek["date"];
                array_push($collectGeneral, $bilanWeek);
                $bilanWeek = $this->initBilan;
                $countWeek++;
            }
            $bilanWeek = $this->calculateurBilan($this->pointage, $bilanWeek);

            //if (!($this->pointageDate->format("W") == 0) and  !($this->pointageDate->format("W") == 6))
            array_push($collectGeneral, [
                "colspan" => 1,
                "date" =>  $this->pointageDate->format('Y-m-d'),
                "horaire" =>  $this->pointage->getHoraire(),
                "entrer" =>  $this->pointage->getEntrer() ? $this->pointage->getEntrer()->format('H:i:s') : "",
                "sortie" =>  $this->pointage->getSortie() ? $this->pointage->getSortie()->format('H:i:s') : "",
                "nbrHeurTravailler" => $this->pointage->getNbrHeurTravailler() ? $this->pointage->getNbrHeurTravailler()->format('H:i:s') : "",
                "retardEnMinute" => $this->pointage->getRetardEnMinute() ? $this->pointage->getRetardEnMinute()->format('H:i:s') : "",
                "departAnticiper" => $this->pointage->getDepartAnticiper() ? $this->pointage->getDepartAnticiper()->format('H:i:s') : "",
                "retardMidi" => $this->pointage->getRetardMidi() ? $this->pointage->getRetardMidi()->format('H:i:s') : "",
                "totaleRetard" => $this->pointage->getTotaleRetard() ? $this->pointage->getTotaleRetard()->format('H:i:s') : "",
                "autorisationSortie" => $this->pointage->getAutorisationSortie() ? $this->pointage->getAutorisationSortie()->getTime()->format('H:i:s') : "",
                "congerPayer" =>  $this->pointage->getCongerPayer(),
                "abscence" => $this->pointage->getAbscence(),
                "heurNormalementTravailler" => $this->pointage->getHeurNormalementTravailler() ? $this->pointage->getHeurNormalementTravailler()->format('H:i:s') : "",
                "diff" => $this->pointage->getDiff() ? $this->pointage->getDiff()->format('H:i:s') : "",
            ]);
            $nextWeek = $this->pointage->getdate()->setISODate($this->pointage->getdate()->format('o'), $this->pointage->getdate()->format('W') + 1);
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
            $thisWeek = $this->pointage->getDate()->format('W');
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

                $nextWeek = $this->pointage->getDate()->format('W') + 1;
            }
            if ($thisYear . '-' . $thisMonth != $this->pointage->getDate()->format('Y-m')) {
                $bilanMonth["date"] =   $thisYear . '-' . $thisMonth;
                $bilanMonth["background"] = "DodgerBlue";
                $bilanMonth["colspan"] = 4;
                if ($thisYear and  $thisMonth)
                    array_push($collectGeneral,  $bilanMonth);
                $bilanMonth = $this->initBilan;
            }
            if ($thisYear != $this->pointage->getDate()->format('Y')) {
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

            $thisMonth =  $this->pointage->getDate()->format('m');
            $thisYear =  $this->pointage->getDate()->format('Y');
            
        }
        if (!empty($collectGeneral))
            array_push($collectGeneral, $bilan); */
        return $collectGeneral;
    }




    public function setHoraireServiceHoraire()
    {
        $this->horaireService->setHoraire($this->pointage->getHoraire());
    }

    public function nbrHeurTravailler()
    {
        $entrer =  $this->pointage->getEntrer();
        $sortie = $this->pointage->getSortie();
        $time = new DateTime($sortie->format("H:i:s"));
        $time->sub($this->timeService->dateTimeToDateInterval($this->horaireService->sumPause()));
        $time = $this->timeService->diffTime($time,  $entrer);
        return $this->timeService->dateIntervalToDateTime($time);
    }

    public function retardEnMinute()
    {
        $debutHeurDeTravaille = new DateTime($this->horaireService->getHoraire()->getHeurDebutTravaille()->format("H:i:s"));
        if ($this->pointage->getEntrer())
            $entrer = new DateTime(date('H:i:s', strtotime($this->pointage->getEntrer()->format("H:i:s"))));
        else
            $entrer = $debutHeurDeTravaille;
        if ($this->pointage->getCongerPayer() and $this->pointage->getCongerPayer()->getDemiJourner()) {
            dump('CP');
            dump($entrer);
            dd($debutHeurDeTravaille);
        } elseif ($this->pointage->getCongerPayer() and $this->pointage->getAutorisationSortie()) {
            dump('AS');
            dump($entrer);
            dd($debutHeurDeTravaille);
        } else {
            $debutHeurDeTravaille->add($this->timeService->margeDuRetard());
            if ($debutHeurDeTravaille >= $entrer)
                return new DateTime("00:00:00");
            $debutHeurDeTravaille = $this->timeService->diffTime(new DateTime(date('H:i:s', strtotime($debutHeurDeTravaille->format("H:i:s")))), $entrer);
            return $this->timeService->dateIntervalToDateTime($debutHeurDeTravaille);
        }
    }


    /**
     * totalRetard
     *
     * @return DateTime
     */
    public function totalRetard(): DateTime
    {
        $e = new DateTime('00:00:00');
        if ($this->pointage->getRetardEnMinute()) {
            $e->add($this->timeService->dateTimeToDateInterval($this->pointage->getRetardEnMinute()));
        }
        if ($this->pointage->getDepartAnticiper()) {
            $e->add($this->timeService->dateTimeToDateInterval($this->pointage->getDepartAnticiper()));
        }
        if ($this->pointage->getRetardMidi()) {
            $e->add($this->timeService->dateTimeToDateInterval($this->pointage->getRetardMidi()));
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
        $heurFinTravaille = new DateTime($this->horaireService->getHoraire()->getHeurFinTravaille()->format("H:i:s"));
        $heurDebutTravaille = $this->horaireService->getHoraire()->getHeurDebutTravaille();
        if ($this->pointage->getCongerPayer() and $this->pointage->getCongerPayer()->getDemiJourner()) {
            dd('demijourner heur normalement travailer');
        } else {
            if ($this->pointage->getAutorisationSortie())
                $heurFinTravaille->sub($this->timeService->dateTimeToDateInterval($this->pointage->getAutorisationSortie()->getTime()));
            $e = $this->horaireService->sumPause();
            $heurFinTravaille->sub($this->timeService->dateTimeToDateInterval($e));
            $heurFinTravaille = $this->timeService->diffTime($heurFinTravaille, $heurDebutTravaille);
            return $this->timeService->dateIntervalToDateTime($heurFinTravaille);
        }
    }

    public function diff(): DateTime
    {
        if ($this->pointage->getNbrHeurTravailler())

            return $this->timeService->dateIntervalToDateTime(
                $this->timeService->diffTime(
                    $this->pointage->getNbrHeurTravailler(),
                    $this->pointage->getHeurNormalementTravailler()
                )
            );
        else
            return $this->pointage->getHeurNormalementTravailler();
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
        $this->nextYear = new DateTime($this->pointage->getDate()->format("Y-m-d"));
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
    public function setPointage(Pointage $this->pointage)
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
                    $this->pointage->setHoraire($this->horaireService->getHoraireForDate($this->pointage->getDate()));
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
                    if ($this->pointage->getSortie() and $this->pointage->getEntrer())
                        $this->pointage->setNbrHeurTravailler($this->nbrHeurTravailler());
                    else {
                        $this->pointage->setNbrHeurTravailler(new DateTime('00:00:00'));
                        if (!in_array($ligne['K'], ['1']) and $ligne['C'] != 'CP')
                            $this->flash->add('warning', 'set to 0 nbrHeurTravailler, entrer ou sortie non saisie ' . $colomn . ' of ligne ' . implode(" | ", $ligne));
                    }
                    break;
                case 'F':
                    if ($this->pointage->getSortie() and $this->pointage->getEntrer())
                        $this->pointage->setRetardEnMinute($this->retardEnMinute());
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
                        $autrisationSotie->setDateAutorisation($this->pointage->getDate());
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
                            $conger->setDebut($this->pointage->getDate());
                            $conger->setFin($this->pointage->getDate());
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
                            $conger->setDebut($this->pointage->getDate());
                            $conger->setFin($this->pointage->getDate());
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
                                $conger->setDebut($this->pointage->getDate());
                                $conger->setFin($this->pointage->getDate());
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
