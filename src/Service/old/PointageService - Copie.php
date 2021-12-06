<?php



use App\Entity\User;
use DateTime;

class PointageService extends EmployerService
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
     * @var DateTime|null
     */
    private $entrer;

    /**
     * sortie
     *
     * @var DateTime|null
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
     * totaleRetard
     *
     * @var DateTime
     */
    private $totaleRetard;

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
     * congerPayer
     *
     * @var Conger
     */
    private $congerPayer;

    /**
     * autorisationSortie
     *
     * @var AutorisationSortie
     */
    private $autorisationSortie;

    /**
     * workTime
     *
     * @var WorkTime
     */
    private $workTime;

    /**
     * absence
     *
     * @var Absence
     */
    private $absence;

    /**
     * dateInDB
     *
     * @return array
     */
    public function getPointageDateInDB(): array
    {
        return array_map(
            fn ($date): string => $date->getDate()->format('Y-m-d'),
            $this->getEmployer()->getPointages()->toArray()
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
}
