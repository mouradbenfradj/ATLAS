<?php
namespace App\Service;

use App\Entity\Absence;
use App\Entity\AutorisationSortie;
use App\Entity\Conger;
use App\Entity\User;
use DateTime;

class EmployerService extends DateTimeService
{
    /**
     * employer
     *
     * @var User
     */
    private $employer;

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

    public function __construct(AbsenceService $absenceService, CongerService $congerService, AutorisationSortieService $autorisationSortieService, JourFerierService $jourFerierService, HoraireService $horaireService)
    {
        parent::__construct($jourFerierService, $horaireService);
        $this->absenceService = $absenceService;
        $this->congerService = $congerService;
        $this->autorisationSortieService = $autorisationSortieService;
    }
    public function estAbsent(DateTime $date):bool
    {
        return $this->absenceService->matchAvecUneAbsence($this->employer->getAbsences()->toArray(), $date);
    }
    public function aPrisUnConger(DateTime $date):bool
    {
        return $this->congerService->matchAvecUnConger($this->employer->getCongers()->toArray(), $date);
    }
    public function aPrisUneAutorisationDeSortie(DateTime $date):bool
    {
        return $this->autorisationSortieService->matchAvecUneAutorisationDeSortie($this->employer->getAutorisationSorties()->toArray(), $date);
    }
    public function getAbsence(DateTime $date):?Absence
    {
        return $this->absenceService->getAbsence($this->employer->getAbsences()->toArray(), $date);
    }
    public function getConger(DateTime $date):?Conger
    {
        return $this->congerService->getConger($this->employer->getCongers()->toArray(), $date);
    }
    public function getAutorisationSortie(DateTime $date):?AutorisationSortie
    {
        return $this->autorisationSortieService->getAutorisation($this->employer->getAutorisationSorties()->toArray(), $date);
    }



    /**
     * getEmployer
     *
     * @return  User
     */
    public function getEmployer()
    {
        return $this->employer;
    }

    /**
     * setEmployer
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
}
