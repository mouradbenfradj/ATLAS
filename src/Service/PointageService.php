<?php

namespace App\Service;

use DateTime;
use App\Entity\User;
use App\Entity\Conger;
use DateTimeInterface;
use App\Entity\Pointage;
use App\Service\TimeService;
use App\Service\HoraireService;
use App\Entity\AutorisationSortie;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class PointageService
{
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

    /**
     *
     * @var int
     */
    private $nextYear;
    private $configService;
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
        ConfigService $configService
    ) {
        $this->horaireService = $horaireService;
        $this->timeService = $timeService;
        $this->dateService = $dateService;
        $this->flash = $flash;
        $this->configService = $configService;

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
        $bilan["nbrHeurTravailler"] = $this->bilan($pointage->getNbrHeurTravailler(), $bilan["nbrHeurTravailler"]);
        if ($pointage->getRetardEnMinute())
            $bilan["retardEnMinute"] = $this->bilan($pointage->getRetardEnMinute(), $bilan["retardEnMinute"]);
        if ($pointage->getDepartAnticiper())
            $bilan["departAnticiper"] = $this->bilan($pointage->getDepartAnticiper(), $bilan["departAnticiper"]);
        if ($pointage->getRetardMidi())
            $bilan["retardMidi"] = $this->bilan($pointage->getRetardMidi(), $bilan["retardMidi"]);
        $bilan["totaleRetard"] = $this->bilan($pointage->getTotaleRetard(), $bilan["totaleRetard"]);
        if ($pointage->getAutorisationSortie())
            $bilan["autorisationSortie"] = $this->bilan($pointage->getAutorisationSortie()->getTime(), $bilan["autorisationSortie"]);
        if ($pointage->getCongerPayer()) {
            if ($pointage->getCongerPayer()->getDemiJourner())
                $bilan["congerPayer"] += 0.5;
            else
                $bilan["congerPayer"] += 1;
        }
        $bilan["abscence"] = $pointage->getAbscence() ? $bilan["abscence"] + 1 : $bilan["abscence"];
        $bilan["heurNormalementTravailler"] = $this->bilan($pointage->getHeurNormalementTravailler(), $bilan["heurNormalementTravailler"]);
        $bilan["diff"] = $this->bilan($pointage->getDiff(), $bilan["diff"]);
        return $bilan;
    }

    public function getBilanSemestriel($pointages)
    {
        $bilan = $this->initBilan;
        $thisWeek = 0;
        $countWeek = 1;
        $collectSemaine = [];
        foreach ($pointages as $pointage) {
            if ($thisWeek != $pointage->getDate()->format('W')) {
                if ($thisWeek) {
                    array_push($collectSemaine, $bilan);
                    $countWeek++;
                }
                $thisWeek = $pointage->getDate()->format('W');
                $bilan = $this->initBilan;
                $bilan["date"] = $countWeek;
            }
            $bilan = $this->calculateurBilan($pointage, $bilan);
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
        foreach ($pointages as $pointage) {
            if ($thisYear . '-' . $thisMonth != $pointage->getDate()->format('Y-m')) {
                if ($thisYear and $thisMonth)
                    array_push($collectMensuel, $bilan);
                $thisYear =  $pointage->getDate()->format('Y');
                $thisMonth =  $pointage->getDate()->format('m');
                $bilan = $this->initBilan;
                $bilan["date"] =  $pointage->getDate()->format('Y-m');
            }
            $bilan = $this->calculateurBilan($pointage, $bilan);
        }
        array_push($collectMensuel, $bilan);
        return $collectMensuel;
    }
    public function getBilanAnnuel($pointages)
    {
        $bilan = $this->initBilan;
        $thisYear = 0;
        $collectAnnuel = [];
        foreach ($pointages as $pointage) {
            if ($thisYear != $pointage->getDate()->format('Y')) {
                if ($thisYear)
                    array_push($collectAnnuel, $bilan);
                $thisYear =  $pointage->getDate()->format('Y');
                $bilan = $this->initBilan;
                $bilan["date"] =  $pointage->getDate()->format('Y');
            }
            $bilan = $this->calculateurBilan($pointage, $bilan);
        }
        array_push($collectAnnuel, $bilan);
        return $collectAnnuel;
    }

    /**
     * getBilanGeneral
     *
     * @param Collection $pointages
     * @return array
     */
    public function getBilanGeneral(Collection $pointages): array
    {
        $pointages = $pointages->toArray();
        usort($pointages, fn ($a, $b) => $a->getDate() > $b->getDate());

        $collectGeneral = [];
        $bilan = $this->initBilan;
        foreach ($pointages as  $pointage) {
            $this->setPointage($pointage);

            array_push($collectGeneral, [
                "colspan" => 1,
                "date" =>  $pointage->getdate()->format('Y-m-d'),
                "horaire" =>  $pointage->getHoraire(),
                "entrer" =>  $pointage->getEntrer() ? $pointage->getEntrer()->format('H:i:s') : "",
                "sortie" =>  $pointage->getSortie() ? $pointage->getSortie()->format('H:i:s') : "",
                "nbrHeurTravailler" => $pointage->getNbrHeurTravailler() ? $pointage->getNbrHeurTravailler()->format('H:i:s') : "",
                "retardEnMinute" => $pointage->getRetardEnMinute() ? $pointage->getRetardEnMinute()->format('H:i:s') : "",
                "departAnticiper" => $pointage->getDepartAnticiper() ? $pointage->getDepartAnticiper()->format('H:i:s') : "",
                "retardMidi" => $pointage->getRetardMidi() ? $pointage->getRetardMidi()->format('H:i:s') : "",
                "totaleRetard" => $pointage->getTotaleRetard() ? $pointage->getTotaleRetard()->format('H:i:s') : "",
                "autorisationSortie" => $pointage->getAutorisationSortie() ? $pointage->getAutorisationSortie()->getTime()->format('H:i:s') : "",
                "congerPayer" =>  $pointage->getCongerPayer(),
                "abscence" => $pointage->getAbscence(),
                "heurNormalementTravailler" => $pointage->getHeurNormalementTravailler() ? $pointage->getHeurNormalementTravailler()->format('H:i:s') : "",
                "diff" => $pointage->getDiff() ? $pointage->getDiff()->format('H:i:s') : "",
            ]);
        }
        /*
        $bilanMonth = $this->initBilan;
        $bilanYear = $this->initBilan;
        $thisWeek = 0;
        $nextWeek = 0;
        $thisMonth = 0;
        $thisYear = 0;
        $countWeek = 1;
        foreach ($pointages as  $pointage) {
            $this->setPointage($pointage);
            $this->setHoraireServiceHoraire();
            $thisWeek = $pointage->getDate()->format('W');
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

                $nextWeek = $pointage->getDate()->format('W') + 1;
            }
            if ($thisYear . '-' . $thisMonth != $pointage->getDate()->format('Y-m')) {
                $bilanMonth["date"] =   $thisYear . '-' . $thisMonth;
                $bilanMonth["background"] = "DodgerBlue";
                $bilanMonth["colspan"] = 4;
                if ($thisYear and  $thisMonth)
                    array_push($collectGeneral,  $bilanMonth);
                $bilanMonth = $this->initBilan;
            }
            if ($thisYear != $pointage->getDate()->format('Y')) {
                $bilanYear["date"] =     $thisYear;
                $bilanYear["background"] = "MediumSeaGreen";
                $bilanMonth["colspan"] = 4;
                if ($thisYear)
                    array_push($collectGeneral, $bilanYear);
                $bilanYear = $this->initBilan;
            }
            $bilan = $this->calculateurBilan($pointage, $bilan);
            $bilanMonth = $this->calculateurBilan($pointage, $bilanMonth);
            $bilanYear = $this->calculateurBilan($pointage, $bilanYear);

            $thisMonth =  $pointage->getDate()->format('m');
            $thisYear =  $pointage->getDate()->format('Y');
            
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
     * @param  Pointage  $pointage  pointage
     *
     * @return  self
     */
    public function setPointage(Pointage $pointage)
    {
        $this->pointage = $pointage;

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
