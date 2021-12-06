<?php


use App\Entity\User;

class EmployerService extends DateTimeService implements EmployerInterface
{
    private $autorisationSortieService;
    private $congerService;
    private $absenceService;
    private $horaireService;
    public function __construct(HoraireService $horaireService, AbsenceService $absenceService, CongerService $congerService, AutorisationSortieService $autorisationSortieService)
    {
        $this->autorisationSortieService = $autorisationSortieService;
        $this->congerService = $congerService;
        $this->absenceService = $absenceService;
        $this->horaireService = $horaireService;
    }
    
    /**
     * userid
     *
     * @var integer
     */
    private $userid;

    /**
     * badgenumbe
     *
     * @var integer
     */
    private $badgenumbe;

    /**
     * firstName
     *
     * @var string
     */
    private $firstName;

    /**
     * lastName
     *
     * @var string
     */
    private $lastName;

    /**
     * employer
     *
     * @var User
     */
    private $employer;

    public function demanderUnConger()
    {
    }
    public function demanderUneAutorisationDeSortie()
    {
    }
    public function modifierWorkTime()
    {
    }
    public function demissionner()
    {
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
     * Get userid
     *
     * @return  integer
     */
    public function getUserid()
    {
        return $this->userid;
    }

    /**
     * Set userid
     *
     * @param  integer  $userid  userid
     *
     * @return  self
     */
    public function setUserid($userid)
    {
        $this->userid = $userid;

        return $this;
    }

    /**
     * Get badgenumbe
     *
     * @return  integer
     */
    public function getBadgenumbe()
    {
        return $this->badgenumbe;
    }

    /**
     * Set badgenumbe
     *
     * @param  integer  $badgenumbe  badgenumbe
     *
     * @return  self
     */
    public function setBadgenumbe($badgenumbe)
    {
        $this->badgenumbe = $badgenumbe;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return  string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set firstName
     *
     * @param  string  $firstName  firstName
     *
     * @return  self
     */
    public function setFirstName(string $firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return  string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set lastName
     *
     * @param  string  $lastName  lastName
     *
     * @return  self
     */
    public function setLastName(string $lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }
}
