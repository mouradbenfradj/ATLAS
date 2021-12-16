<?php
namespace App\Service;

use App\Entity\Dbf;
use App\Traits\DbfEntityTrait;
use App\Traits\TableReaderTrait;
use DateTime;

class DbfService extends PointageService
{
    use TableReaderTrait;
    use DbfEntityTrait;
    /**
     * dbf
     *
     * @var Dbf
     */
    private $dbf;

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
