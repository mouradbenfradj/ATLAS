<?php

namespace App\Service;

use App\Entity\AutorisationSortie;
use App\Entity\Horaire;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class AutorisationSortieService
{
    /**
     * employer
     *
     * @var User
     */
    private $employer;

    /**
     * dateAutorisation
     *
     * @var DateTime
     */
    private $dateAutorisation;

    /**
     * pointages
     *
     * @var array
     */
    private $pointages;

    /**
     * valider
     *
     * @var bool
     */
    private $valider;

    /**
     * refuser
     *
     * @var bool
     */
    private $refuser;

    /**
     * de
     *
     * @var DateTime
     */
    private $heurAutoriser;

    /**
     * attchktime variable
     *
     * @var array
     */
    private $attchktime;

    /**
     * timeService variable
     *
     * @var TimeService
     */
    private $timeService;
    private $horaireService;
    private $horaire;
    private $entrer;
    private $entrer1;
    private $entrer2;
    private $sortie;
    private $heurDebutTravaille;
    private $debutPauseMatinal;
    private $finPauseMatinal;
    private $debutPauseDejeuner;
    private $finPauseDejeuner;
    private $debutPauseMidi;
    private $finPauseMidi;
    private $heurFinTravaille;



    public function __construct(TimeService $timeService, HoraireService $horaireService)
    {
        $this->timeService = $timeService;
        $this->horaireService = $horaireService;
    }
    public function requirement(array $attchktime, Horaire $horaire, DateTime $entrer, DateTime $sortie)
    {
        $this->attchktime = $attchktime;
        $this->horaire = $horaire;
        switch (count($this->attchktime)) {
            case 2:
                $this->entrer = $entrer;
                $this->sortie = $sortie;
                $this->entrer1 = null;
                $this->entrer2 = null;
                break;
            case 3:
                $this->entrer = $entrer;
                $this->sortie = $sortie;
                $this->entrer1 = $attchktime[1] ? $this->timeService->generateTime($attchktime[1]) : null;
                $this->entrer2 = $attchktime[2] ? $this->timeService->generateTime($attchktime[2]) : null;
                break;
            case 4:
                $this->entrer = $entrer;
                $this->sortie = $sortie;
                $this->entrer1 = $attchktime[1] ? $this->timeService->generateTime($attchktime[1]) : null;
                $this->entrer2 = $attchktime[2] ? $this->timeService->generateTime($attchktime[2]) : null;
                break;
            default:
                $this->entrer = $entrer;
                $this->sortie = $sortie;
                break;
        }

        $this->heurDebutTravaille = $this->timeService->generateTime($this->horaire->getHeurDebutTravaille()->format('H:i:s'));
        $this->debutPauseMatinal = $this->timeService->generateTime($this->horaire->getDebutPauseMatinal()->format('H:i:s'));
        $this->finPauseMatinal = $this->timeService->generateTime($this->horaire->getFinPauseMatinal()->format('H:i:s'));
        $this->debutPauseDejeuner = $this->timeService->generateTime($this->horaire->getDebutPauseDejeuner()->format('H:i:s'));
        $this->finPauseDejeuner = $this->timeService->generateTime($this->horaire->getFinPauseDejeuner()->format('H:i:s'));
        $this->debutPauseMidi = $this->timeService->generateTime($this->horaire->getDebutPauseMidi()->format('H:i:s'));
        $this->finPauseMidi = $this->timeService->generateTime($this->horaire->getFinPauseMidi()->format('H:i:s'));
        $this->heurFinTravaille = $this->timeService->generateTime($this->horaire->getHeurFinTravaille()->format('H:i:s'));
    }

    public function partielConstruct(
        ?User $employer = null,
        ?DateTime $dateAutorisation = null,
        ?DateTime $heurAutoriser = null,
        ?bool $valider = null,
        ?bool $refuser = null,
        ?array $pointages = null
    ) {
        $this->employer = $employer;
        $this->dateAutorisation = $dateAutorisation;
        $this->heurAutoriser = $heurAutoriser;
        $this->pointages = $pointages;
        $this->valider = $valider;
        $this->refuser = $refuser;
    }

    public function constructEntity(): AutorisationSortie
    {
        $autorisationSortie = new AutorisationSortie();
        $autorisationSortie->setDateAutorisation($this->dateAutorisation);
        $autorisationSortie->setHeurAutoriser($this->heurAutoriser);
        $autorisationSortie->setValider($this->valider);
        $autorisationSortie->setRefuser($this->refuser);
        $autorisationSortie->setEmployer($this->employer);
        return $autorisationSortie;
    }

    /*
        public function getIfAutorisationSortie(string $date, User $employer): ?AutorisationSortie
        {
            return $this->em->getRepository(AutorisationSortie::class)->findOneByEmployerAndDate($date, $employer);
        } */


    public function getAutorisation(): ?AutorisationSortie
    {
        $autorisationSortie =  current(array_filter(array_map(
            fn ($autorisationSortie): ?AutorisationSortie => ($autorisationSortie->getDateAutorisation() <= $this->dateAutorisation and $this->dateAutorisation <= $autorisationSortie->getDateAutorisation()) ? $autorisationSortie : null,
            $this->employer->getAutorisationSorties()->toArray()
        )));
        if ($autorisationSortie) {
            return $autorisationSortie;
        }
        return null;
    }

    /**
     * Set attchktime variable
     *
     * @param  array  $attchktime  attchktime variable
     *
     * @return  self
     */
    public function setAttchktime(array $attchktime)
    {
        $this->attchktime = $attchktime;

        return $this;
    }

    /**
     * Get the value of sortie
     */
    public function getSortie()
    {
        return $this->sortie;
    }

    /**
     * Set the value of sortie
     *
     * @return  self
     */
    public function setSortie($sortie)
    {
        $this->sortie = $sortie;

        return $this;
    }

    /**
     * Get the value of entrer
     */
    public function getEntrer()
    {
        return $this->entrer;
    }

    /**
     * Set the value of entrer
     *
     * @return  self
     */
    public function setEntrer($entrer)
    {
        $this->entrer = $entrer;

        return $this;
    }

    /**
     * Get de
     *
     * @return  DateTime
     */
    public function getHeurAutoriser()
    {
        return $this->heurAutoriser;
    }

    /**
     * Set de
     *
     * @param  DateTime  $heurAutoriser  de
     *
     * @return  self
     */
    public function setHeurAutoriser(DateTime $heurAutoriser)
    {
        $this->heurAutoriser = $heurAutoriser;

        return $this;
    }
}
