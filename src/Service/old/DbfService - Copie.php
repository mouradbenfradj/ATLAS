<?php


use App\Entity\Dbf;
use DateTime;

class DbfService extends PointageService
{

    /**
     * autosch
     *
     * @var string|null
     */
    private $autosch;


    /**
     * schid
     *
     * @var float|null
     */
    private $schid;

    /**
     * clockintim
     *
     * @var DateTime|null
     */
    private $clockintim;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $clockoutti;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $starttime;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $endtime;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $workday;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $realworkda;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $late;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $early;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $absent;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $overtime;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $worktime;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $exceptioni;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mustin;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mustout;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $deptid;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $sspedaynor;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $sspedaywee;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $sspedayhol;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $atttime;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $attchktime = [];
    public function getDbfDateInDB()
    {
        return array_map(
            fn ($date): string => $date->getAttdate()->format('Y-m-d'),
            $this->getEmployer()->getDbfs()->toArray()
        );
    }
    public function constructDbf(?string $autosch, ?float $schid, ?string $clockintim, ?string $clockoutti, ?string $starttime, ?string $endtime, ?float $workday, ?float $realworkda, ?string $late, ?string $early, ?float $absent, ?string $overtime, ?string $worktime, ?string $exceptioni, ?string $mustin, ?string $mustout, ?float $deptid, ?float $sspedaynor, ?float $sspedaywee, ?float $sspedayhol, ?string $atttime, ?string $attchktime)
    {
        $this->autosch = $autosch;
        $this->attdate = $this->getDate();
        //$this->attdate = $this->dateService->dateString_d_m_Y_ToDateTime($attdate);
        $this->schid = $schid;
        $this->clockintim = $this->timeService->timeStringToDateTime($clockintim);
        $this->clockoutti = $this->timeService->timeStringToDateTime($clockoutti);
        $this->starttime = $this->timeService->timeStringToDateTime($starttime);
        $this->endtime = $this->timeService->timeStringToDateTime($endtime);
        $this->workday = $workday;
        $this->realworkda = $realworkda;
        $this->late = $this->timeService->timeStringToDateTime($late);
        $this->early = $this->timeService->timeStringToDateTime($early);
        $this->absent = $absent;
        $this->overtime = $this->timeService->timeStringToDateTime($overtime);
        $this->worktime = $this->timeService->timeStringToDateTime($worktime);
        $this->exceptioni = $exceptioni;
        $this->mustin = $mustin;
        $this->mustout = $mustout;
        $this->deptid = $deptid;
        $this->sspedaynor = $sspedaynor;
        $this->sspedaywee = $sspedaywee;
        $this->sspedayhol = $sspedayhol;
        $this->atttime = $this->timeService->timeStringToDateTime($atttime);
        $this->attchktime = explode(" ", $attchktime);
        $this->user = $this->getEmployer();
    }
    /**
     * createEntity
     *
     * @return Dbf
     */
    public function createEntity(): Dbf
    {
        $dbf = new Dbf();
        $dbf->setUserid($this->getUserid());
        $dbf->setBadgenumbe($this->getBadgenumbe());
        $dbf->setSsn($this->getFirstName());
        $dbf->setUsername($this->getLastName());
        $dbf->setAutosch($this->autosch);
        $dbf->setAttdate($this->attdate);
        $dbf->setSchid($this->schid);
        $dbf->setClockintim($this->clockintim);
        $dbf->setClockoutti($this->clockoutti);
        $dbf->setStarttime($this->starttime);
        $dbf->setEndtime($this->endtime);
        $dbf->setWorkday($this->workday);
        $dbf->setRealworkda($this->realworkda);
        $dbf->setLate($this->late);
        $dbf->setEarly($this->early);
        $dbf->setAbsent($this->absent);
        $dbf->setOvertime($this->overtime);
        $dbf->setWorktime($this->worktime);
        $dbf->setExceptioni($this->exceptioni);
        $dbf->setMustin($this->mustin);
        $dbf->setMustout($this->mustout);
        $dbf->setDeptid($this->deptid);
        $dbf->setSspedaynor($this->sspedaynor);
        $dbf->setSspedaywee($this->sspedaywee);
        $dbf->setSspedayhol($this->sspedayhol);
        $dbf->setAtttime($this->atttime);
        $dbf->setAttchktime($this->attchktime);
        $dbf->setEmployer($this->user);
        return $dbf;
    }

    /**
     * Get autosch
     *
     * @return  string|null
     */
    public function getAutosch()
    {
        return $this->autosch;
    }

    /**
     * Set autosch
     *
     * @param  string|null  $autosch  autosch
     *
     * @return  self
     */
    public function setAutosch($autosch)
    {
        $this->autosch = $autosch;

        return $this;
    }

    /**
     * Get schid
     *
     * @return  float|null
     */
    public function getSchid()
    {
        return $this->schid;
    }

    /**
     * Set schid
     *
     * @param  float|null  $schid  schid
     *
     * @return  self
     */
    public function setSchid($schid)
    {
        $this->schid = $schid;

        return $this;
    }
}
