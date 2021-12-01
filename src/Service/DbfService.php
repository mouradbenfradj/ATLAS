<?php

namespace App\Service;

use App\Entity\Dbf;
use App\Entity\JourFerier;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class DbfService
{
    private $userid;
    private $badgenumbe;
    private $ssn;
    private $username;
    private $autosch;
    private $attdate;
    private $schid;
    private $clockintim;
    private $clockoutti;
    private $starttime;
    private $endtime;
    private $workday;
    private $realworkda;
    private $late;
    private $early;
    private $absent;
    private $overtime;
    private $worktime;
    private $exceptioni;
    private $mustin;
    private $mustout;
    private $deptid;
    private $sspedaynor;
    private $sspedaywee;
    private $sspedayhol;
    private $atttime;
    private $attchktime;
    private $user;

    /**
     * dateService
     *
     * @var DateService
     */
    private $dateService;

    /**
     * timeService
     *
     * @var TimeService
     */
    private $timeService;


    /**
     * adminUrlGenerator variable
     *
     * @var AdminUrlGenerator
     */
    private $adminUrlGenerator;
    /**
     * jourFerierService variable
     *
     * @var JourFerierService
     */
    private $jourFerierService;
    /**
     * congerService variable
     *
     * @var CongerService
     */
    private $congerService;
    /**
     * horaireService variable
     *
     * @var HoraireService
     */
    private $horaireService;
    /**
     * pointageService variable
     *
     * @var PointageService
     */
    private $pointageService;
    /**
     * flash variable
     *
     * @var FlashBagInterface
     */
    private $flash;
    /**
     * autorisationSortieService variable
     *
     * @var AutorisationSortieService
     */
    private $autorisationSortieService;

    /**
     * __construct
     *
     * @param AdminUrlGenerator $adminUrlGenerator
     * @param DateService $dateService
     * @param JourFerierService $jourFerierService
     * @param HoraireService $horaireService
     * @param PointageService $pointageService
     * @param FlashBagInterface $flash
     * @param TimeService $timeService
     * @param CongerService $congerService
     * @param AutorisationSortieService $autorisationSortieService
     */
    public function __construct(DateService $dateService, TimeService $timeService, AdminUrlGenerator $adminUrlGenerator, JourFerierService $jourFerierService, HoraireService $horaireService, PointageService $pointageService, FlashBagInterface $flash, CongerService $congerService, AutorisationSortieService $autorisationSortieService)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
        $this->dateService = $dateService;
        $this->jourFerierService = $jourFerierService;
        $this->congerService = $congerService;
        $this->horaireService = $horaireService;
        $this->pointageService = $pointageService;
        $this->timeService = $timeService;
        $this->autorisationSortieService = $autorisationSortieService;
        $this->flash = $flash;
    }

    /**
     * dateDbfInDb
     *
     * @param User $user
     * @return array
     */
    public function dateDbfInDb(User $user): array
    {
        return array_map(
            fn ($date): string => $date->getAttdate()->format('Y-m-d'),
            $user->getDbfs()->toArray()
        );
    }




    /**
     * construct
     *
     * @param float $userid
     * @param integer $badgenumbe
     * @param string $ssn
     * @param string $username
     * @param string|null $autosch
     * @param string $attdate
     * @param float|null $schid
     * @param string|null $clockintim
     * @param string|null $clockoutti
     * @param string|null $starttime
     * @param string|null $endtime
     * @param float|null $workday
     * @param float|null $realworkda
     * @param string|null $late
     * @param string|null $early
     * @param float|null $absent
     * @param string|null $overtime
     * @param string|null $worktime
     * @param string|null $exceptioni
     * @param string|null $mustin
     * @param string|null $mustout
     * @param float|null $deptid
     * @param float|null $sspedaynor
     * @param float|null $sspedaywee
     * @param float|null $sspedayhol
     * @param string|null $atttime
     * @param string|null $attchktime
     * @param User|null $user
     * @return void
     */
    public function construct(float $userid, int $badgenumbe, string $ssn, string $username, ?string $autosch, string $attdate, ?float $schid, ?string $clockintim, ?string $clockoutti, ?string $starttime, ?string $endtime, ?float $workday, ?float $realworkda, ?string $late, ?string $early, ?float $absent, ?string $overtime, ?string $worktime, ?string $exceptioni, ?string $mustin, ?string $mustout, ?float $deptid, ?float $sspedaynor, ?float $sspedaywee, ?float $sspedayhol, ?string $atttime, ?string $attchktime, ?User $user)
    {
        $this->userid = $userid;
        $this->badgenumbe = $badgenumbe;
        $this->ssn = $ssn;
        $this->username = $username;
        $this->autosch = $autosch;
        $this->attdate = $this->dateService->dateString_d_m_Y_ToDateTime($attdate);
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
        $this->user = $user;
    }

    /**
     * createEntity
     *
     * @return Dbf
     */
    public function createEntity(): Dbf
    {
        $dbf = new Dbf();
        $dbf->setUserid($this->userid);
        $dbf->setBadgenumbe(intval($this->badgenumbe));
        $dbf->setSsn($this->ssn);
        $dbf->setUsername($this->username);
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
        ;
        $dbf->setAttchktime($this->attchktime);
        $dbf->setEmployer($this->user);
        return $dbf;
    }




    public function setUserid(float $userid): self
    {
        $this->userid = $userid;

        return $this;
    }

    public function getBadgenumbe(): ?int
    {
        return $this->badgenumbe;
    }

    public function setBadgenumbe(int $badgenumbe): self
    {
        $this->badgenumbe = $badgenumbe;

        return $this;
    }

    public function getSsn(): ?string
    {
        return $this->ssn;
    }

    public function setSsn(string $ssn): self
    {
        $this->ssn = $ssn;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getAutosch(): ?string
    {
        return $this->autosch;
    }

    public function setAutosch(?string $autosch): self
    {
        $this->autosch = $autosch;

        return $this;
    }

    public function getAttdate(): ?\DateTimeInterface
    {
        return $this->attdate;
    }

    public function setAttdate(\DateTimeInterface $attdate): self
    {
        $this->attdate = $attdate;

        return $this;
    }

    public function getSchid(): ?float
    {
        return $this->schid;
    }

    public function setSchid(?float $schid): self
    {
        $this->schid = $schid;

        return $this;
    }

    public function getClockintim(): ?\DateTimeInterface
    {
        return $this->clockintim;
    }

    public function setClockintim(?\DateTimeInterface $clockintim): self
    {
        $this->clockintim = $clockintim;

        return $this;
    }

    public function getClockoutti(): ?\DateTimeInterface
    {
        return $this->clockoutti;
    }

    public function setClockoutti(?\DateTimeInterface $clockoutti): self
    {
        $this->clockoutti = $clockoutti;

        return $this;
    }

    public function getStarttime(): ?\DateTimeInterface
    {
        return $this->starttime;
    }

    public function setStarttime(?\DateTimeInterface $starttime): self
    {
        $this->starttime = $starttime;

        return $this;
    }

    public function getEndtime(): ?\DateTimeInterface
    {
        return $this->endtime;
    }

    public function setEndtime(?\DateTimeInterface $endtime): self
    {
        $this->endtime = $endtime;

        return $this;
    }

    public function getWorkday(): ?float
    {
        return $this->workday;
    }

    public function setWorkday(?float $workday): self
    {
        $this->workday = $workday;

        return $this;
    }

    public function getRealworkda(): ?float
    {
        return $this->realworkda;
    }

    public function setRealworkda(?float $realworkda): self
    {
        $this->realworkda = $realworkda;

        return $this;
    }

    public function getLate(): ?\DateTimeInterface
    {
        return $this->late;
    }

    public function setLate(?\DateTimeInterface $late): self
    {
        $this->late = $late;

        return $this;
    }

    public function getEarly(): ?\DateTimeInterface
    {
        return $this->early;
    }

    public function setEarly(?\DateTimeInterface $early): self
    {
        $this->early = $early;

        return $this;
    }

    public function getAbsent(): ?float
    {
        return $this->absent;
    }

    public function setAbsent(?float $absent): self
    {
        $this->absent = $absent;

        return $this;
    }

    public function getOvertime(): ?\DateTimeInterface
    {
        return $this->overtime;
    }

    public function setOvertime(?\DateTimeInterface $overtime): self
    {
        $this->overtime = $overtime;

        return $this;
    }

    public function getWorktime(): ?\DateTimeInterface
    {
        return $this->worktime;
    }

    public function setWorktime(?\DateTimeInterface $worktime): self
    {
        $this->worktime = $worktime;

        return $this;
    }

    public function getExceptioni(): ?string
    {
        return $this->exceptioni;
    }

    public function setExceptioni(?string $exceptioni): self
    {
        $this->exceptioni = $exceptioni;

        return $this;
    }

    public function getMustin(): ?string
    {
        return $this->mustin;
    }

    public function setMustin(?string $mustin): self
    {
        $this->mustin = $mustin;

        return $this;
    }

    public function getMustout(): ?string
    {
        return $this->mustout;
    }

    public function setMustout(?string $mustout): self
    {
        $this->mustout = $mustout;

        return $this;
    }

    public function getDeptid(): ?float
    {
        return $this->deptid;
    }

    public function setDeptid(?float $deptid): self
    {
        $this->deptid = $deptid;

        return $this;
    }

    public function getSspedaynor(): ?float
    {
        return $this->sspedaynor;
    }

    public function setSspedaynor(?float $sspedaynor): self
    {
        $this->sspedaynor = $sspedaynor;

        return $this;
    }

    public function getSspedaywee(): ?float
    {
        return $this->sspedaywee;
    }

    public function setSspedaywee(?float $sspedaywee): self
    {
        $this->sspedaywee = $sspedaywee;

        return $this;
    }

    public function getSspedayhol(): ?float
    {
        return $this->sspedayhol;
    }

    public function setSspedayhol(?float $sspedayhol): self
    {
        $this->sspedayhol = $sspedayhol;

        return $this;
    }

    public function getAtttime(): ?\DateTimeInterface
    {
        return $this->atttime;
    }

    public function setAtttime(?\DateTimeInterface $atttime): self
    {
        $this->atttime = $atttime;

        return $this;
    }

    public function getAttchktime(): ?array
    {
        return $this->attchktime;
    }

    public function setAttchktime(?array $attchktime): self
    {
        $this->attchktime = $attchktime;

        return $this;
    }

    public function getEmployer(): ?User
    {
        return $this->employer;
    }

    public function setEmployer(?User $employer): self
    {
        $this->employer = $employer;

        return $this;
    }
}
