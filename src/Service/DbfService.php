<?php
namespace App\Service;

use App\Entity\Dbf;
use DateTime;

class DbfService extends PointageService
{
    /**
     * dbf
     *
     * @var Dbf
     */
    private $dbf;
    
    /**
     * userid
     *
     * @var float
     */
    private $userid;
    /**
     * badgenumbe
     *
     * @var integer
     */
    private $badgenumbe;
    /**
     * ssn
     *
     * @var string
     */
    private $ssn;

    /**
     * username
     *
     * @var string
     */
    private $username;
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
     * clockoutti
     *
     * @var DateTime|null
     */
    private $clockoutti;

    /**
     * workday
     *
     * @var float|null
     */
    private $workday;

    /**
     * realworkda
     *
     * @var float|null
     */
    private $realworkda;

    /**
     * late
     *
     * @var DateTime|null
     */
    private $late;

    /**
     * early
     *
     * @var DateTime|null
     */
    private $early;

    /**
     * absent
     *
     * @var float|null
     */
    private $absent;

    /**
     * overtime
     *
     * @var DateTime|null
     */
    private $overtime;

    /**
     * worktime
     *
     * @var DateTime|null
     */
    private $worktime;

    /**
     * exceptioni
     *
     * @var string|null
     */
    private $exceptioni;

    /**
     * mustin
     *
     * @var string|null
     */
    private $mustin;

    /**
     * mustout
     *
     * @var string|null
     */
    private $mustout;

    /**
     * deptid
     *
     * @var float|true
     */
    private $deptid;

    /**
     * sspedaynor
     *
     * @var float|true
     */
    private $sspedaynor;

    /**
     * sspedaywee
     *
     * @var float|true
     */
    private $sspedaywee;

    /**
     * sspedayhol
     *
     * @var float|true
     */
    private $sspedayhol;

    /**
     * atttime
     *
     * @var DateTime
     */
    private $atttime;

    /**
     * attchktime
     *
     * @var string[]
     */
    private $attchktime = [];

    public function getDbfDateInDB()
    {
        return array_map(
            fn ($date): string => $date->getAttdate()->format('Y-m-d'),
            $this->getEmployer()->getDbfs()->toArray()
        );
    }

    /**
     * createEntity
     *
     * @return Dbf
     */
    public function createDbfEntity(): Dbf
    {
        $this->dbf = new Dbf();
        $this->dbf->setUserid($this->userid);
        $this->dbf->setBadgenumbe($this->badgenumbe);
        $this->dbf->setSsn($this->ssn);
        $this->dbf->setUsername($this->username);
        $this->dbf->setAutosch($this->autosch);
        $this->dbf->setAttdate($this->getDate());
        $this->dbf->setSchid($this->schid);
        $this->dbf->setClockintim($this->clockintim);
        $this->dbf->setClockoutti($this->clockoutti);
        $this->dbf->setStarttime($this->getEntrer());
        $this->dbf->setEndtime($this->getSortie());
        $this->dbf->setWorkday($this->workday);
        $this->dbf->setRealworkda($this->realworkda);
        $this->dbf->setLate($this->late);
        $this->dbf->setEarly($this->early);
        $this->dbf->setAbsent($this->absent);
        $this->dbf->setOvertime($this->overtime);
        $this->dbf->setWorktime($this->worktime);
        $this->dbf->setExceptioni($this->exceptioni);
        $this->dbf->setMustin($this->mustin);
        $this->dbf->setMustout($this->mustout);
        $this->dbf->setDeptid($this->deptid);
        $this->dbf->setSspedaynor($this->sspedaynor);
        $this->dbf->setSspedaywee($this->sspedaywee);
        $this->dbf->setSspedayhol($this->sspedayhol);
        $this->dbf->setAtttime($this->atttime);
        $this->dbf->setAttchktime($this->attchktime);
        $this->dbf->setEmployer($this->getEmployer());
        return $this->dbf;
    }

    /**
     * Get userid
     *
     * @return  float
     */
    public function getUserid()
    {
        return $this->userid;
    }

    /**
     * Set userid
     *
     * @param  float  $userid  userid
     *
     * @return  self
     */
    public function setUserid(float $userid)
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
     * Get ssn
     *
     * @return  string
     */
    public function getSsn()
    {
        return $this->ssn;
    }

    /**
     * Set ssn
     *
     * @param  string  $ssn  ssn
     *
     * @return  self
     */
    public function setSsn(string $ssn)
    {
        $this->ssn = $ssn;

        return $this;
    }

    /**
     * Get username
     *
     * @return  string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set username
     *
     * @param  string  $username  username
     *
     * @return  self
     */
    public function setUsername(string $username)
    {
        $this->username = $username;

        return $this;
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

    /**
     * Get clockintim
     *
     * @return  DateTime|null
     */
    public function getClockintim()
    {
        return $this->clockintim;
    }

    /**
     * Set clockintim
     *
     * @param  DateTime|null  $clockintim  clockintim
     *
     * @return  self
     */
    public function setClockintim($clockintim)
    {
        $this->clockintim = $clockintim;

        return $this;
    }

    /**
     * Get clockoutti
     *
     * @return  DateTime|null
     */
    public function getClockoutti()
    {
        return $this->clockoutti;
    }

    /**
     * Set clockoutti
     *
     * @param  DateTime|null  $clockoutti  clockoutti
     *
     * @return  self
     */
    public function setClockoutti($clockoutti)
    {
        $this->clockoutti = $clockoutti;

        return $this;
    }

    /**
     * Get workday
     *
     * @return  float|null
     */
    public function getWorkday()
    {
        return $this->workday;
    }

    /**
     * Set workday
     *
     * @param  float|null  $workday  workday
     *
     * @return  self
     */
    public function setWorkday($workday)
    {
        $this->workday = $workday;

        return $this;
    }

    /**
     * Get realworkda
     *
     * @return  float|null
     */
    public function getRealworkda()
    {
        return $this->realworkda;
    }

    /**
     * Set realworkda
     *
     * @param  float|null  $realworkda  realworkda
     *
     * @return  self
     */
    public function setRealworkda($realworkda)
    {
        $this->realworkda = $realworkda;

        return $this;
    }

    /**
     * Get late
     *
     * @return  DateTime|null
     */
    public function getLate()
    {
        return $this->late;
    }

    /**
     * Set late
     *
     * @param  DateTime|null  $late  late
     *
     * @return  self
     */
    public function setLate($late)
    {
        $this->late = $late;

        return $this;
    }

    /**
     * Get early
     *
     * @return  DateTime|null
     */
    public function getEarly()
    {
        return $this->early;
    }

    /**
     * Set early
     *
     * @param  DateTime|null  $early  early
     *
     * @return  self
     */
    public function setEarly($early)
    {
        $this->early = $early;

        return $this;
    }

    /**
     * Get absent
     *
     * @return  float|null
     */
    public function getAbsent()
    {
        return $this->absent;
    }

    /**
     * Set absent
     *
     * @param  float|null  $absent  absent
     *
     * @return  self
     */
    public function setAbsent($absent)
    {
        $this->absent = $absent;

        return $this;
    }

    /**
     * Get overtime
     *
     * @return  DateTime|null
     */
    public function getOvertime()
    {
        return $this->overtime;
    }

    /**
     * Set overtime
     *
     * @param  DateTime|null  $overtime  overtime
     *
     * @return  self
     */
    public function setOvertime($overtime)
    {
        $this->overtime = $overtime;

        return $this;
    }

    /**
     * Get worktime
     *
     * @return  DateTime|null
     */
    public function getWorktime()
    {
        return $this->worktime;
    }

    /**
     * Set worktime
     *
     * @param  DateTime|null  $worktime  worktime
     *
     * @return  self
     */
    public function setWorktime($worktime)
    {
        $this->worktime = $worktime;

        return $this;
    }

    /**
     * Get exceptioni
     *
     * @return  string|null
     */
    public function getExceptioni()
    {
        return $this->exceptioni;
    }

    /**
     * Set exceptioni
     *
     * @param  string|null  $exceptioni  exceptioni
     *
     * @return  self
     */
    public function setExceptioni($exceptioni)
    {
        $this->exceptioni = $exceptioni;

        return $this;
    }

    /**
     * Get mustin
     *
     * @return  string|null
     */
    public function getMustin()
    {
        return $this->mustin;
    }

    /**
     * Set mustin
     *
     * @param  string|null  $mustin  mustin
     *
     * @return  self
     */
    public function setMustin($mustin)
    {
        $this->mustin = $mustin;

        return $this;
    }

    /**
     * Get mustout
     *
     * @return  string|null
     */
    public function getMustout()
    {
        return $this->mustout;
    }

    /**
     * Set mustout
     *
     * @param  string|null  $mustout  mustout
     *
     * @return  self
     */
    public function setMustout($mustout)
    {
        $this->mustout = $mustout;

        return $this;
    }

    /**
     * Get deptid
     *
     * @return  float|true
     */
    public function getDeptid()
    {
        return $this->deptid;
    }

    /**
     * Set deptid
     *
     * @param  float|true  $deptid  deptid
     *
     * @return  self
     */
    public function setDeptid($deptid)
    {
        $this->deptid = $deptid;

        return $this;
    }

    /**
     * Get sspedaynor
     *
     * @return  float|true
     */
    public function getSspedaynor()
    {
        return $this->sspedaynor;
    }

    /**
     * Set sspedaynor
     *
     * @param  float|true  $sspedaynor  sspedaynor
     *
     * @return  self
     */
    public function setSspedaynor($sspedaynor)
    {
        $this->sspedaynor = $sspedaynor;

        return $this;
    }

    /**
     * Get sspedaywee
     *
     * @return  float|true
     */
    public function getSspedaywee()
    {
        return $this->sspedaywee;
    }

    /**
     * Set sspedaywee
     *
     * @param  float|true  $sspedaywee  sspedaywee
     *
     * @return  self
     */
    public function setSspedaywee($sspedaywee)
    {
        $this->sspedaywee = $sspedaywee;

        return $this;
    }

    /**
     * Get sspedayhol
     *
     * @return  float|true
     */
    public function getSspedayhol()
    {
        return $this->sspedayhol;
    }

    /**
     * Set sspedayhol
     *
     * @param  float|true  $sspedayhol  sspedayhol
     *
     * @return  self
     */
    public function setSspedayhol($sspedayhol)
    {
        $this->sspedayhol = $sspedayhol;

        return $this;
    }

    /**
     * Get atttime
     *
     * @return  DateTime
     */
    public function getAtttime()
    {
        return $this->atttime;
    }

    /**
     * Set atttime
     *
     * @param  DateTime  $atttime  atttime
     *
     * @return  self
     */
    public function setAtttime(DateTime $atttime)
    {
        $this->atttime = $atttime;

        return $this;
    }

    /**
     * Get attchktime
     *
     * @return  string[]
     */
    public function getAttchktime()
    {
        return $this->attchktime;
    }

    /**
     * Set attchktime
     *
     * @param  string[]  $attchktime  attchktime
     *
     * @return  self
     */
    public function setAttchktime(array $attchktime)
    {
        $this->attchktime = $attchktime;

        return $this;
    }

    /**
     * Get dbf
     *
     * @return  Dbf
     */
    public function getDbf()
    {
        return $this->dbf;
    }

    /**
     * Set dbf
     *
     * @param  Dbf  $dbf  dbf
     *
     * @return  self
     */
    public function setDbf(Dbf $dbf)
    {
        $this->dbf = $dbf;

        return $this;
    }
}
