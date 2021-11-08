<?php

namespace App\Service;

use App\Entity\Dbf;
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





    private $adminUrlGenerator;
    private $dateService;
    private $jourFerierService;
    private $pointageGeneratorService;
    private $congerService;
    private $horaireService;
    private $pointageService;
    private $flash;
    private $autorisationSortieService;
    private $timeService;

    /**
     * __construct
     *
     * @param AdminUrlGenerator $adminUrlGenerator
     * @param DateService $dateService
     * @param JourFerierService $jourFerierService
     * @param PointageGeneratorService $pointageGeneratorService
     * @param HoraireService $horaireService
     * @param PointageService $pointageService
     * @param FlashBagInterface $flash
     * @param TimeService $timeService
     * @param CongerService $congerService
     * @param AutorisationSortieService $autorisationSortieService
     */
    public function __construct(
        AdminUrlGenerator $adminUrlGenerator,
        DateService $dateService,
        JourFerierService $jourFerierService,
        PointageGeneratorService $pointageGeneratorService,
        HoraireService $horaireService,
        PointageService $pointageService,
        FlashBagInterface $flash,
        TimeService $timeService,
        CongerService $congerService,
        AutorisationSortieService $autorisationSortieService
    ) {
        $this->adminUrlGenerator = $adminUrlGenerator;
        $this->dateService = $dateService;
        $this->jourFerierService = $jourFerierService;
        $this->pointageGeneratorService = $pointageGeneratorService;
        $this->congerService = $congerService;
        $this->horaireService = $horaireService;
        $this->pointageService = $pointageService;
        $this->timeService = $timeService;
        $this->autorisationSortieService = $autorisationSortieService;
        $this->flash = $flash;
    }




    public function construct($userid, $badgenumbe, $ssn, $username, $autosch, $attdate, $schid, $clockintim, $clockoutti, $starttime, $endtime, $workday, $realworkda, $late, $early, $absent, $overtime, $worktime, $exceptioni, $mustin, $mustout, $deptid, $sspedaynor, $sspedaywee, $sspedayhol, $atttime, $attchktime, $user)
    {
        $this->userid = $userid;
        $this->badgenumbe = $badgenumbe;
        $this->ssn = $ssn;
        $this->username = $username;
        $this->autosch = $autosch;
        $this->attdate = $attdate;
        $this->schid = $schid;
        $this->clockintim = $clockintim;
        $this->clockoutti = $clockoutti;
        $this->starttime = $starttime;
        $this->endtime = $endtime;
        $this->workday = $workday;
        $this->realworkda = $realworkda;
        $this->late = $late;
        $this->early = $early;
        $this->absent = $absent;
        $this->overtime = $overtime;
        $this->worktime = $worktime;
        $this->exceptioni = $exceptioni;
        $this->mustin = $mustin;
        $this->mustout = $mustout;
        $this->deptid = $deptid;
        $this->sspedaynor = $sspedaynor;
        $this->sspedaywee = $sspedaywee;
        $this->sspedayhol = $sspedayhol;
        $this->atttime = $atttime;
        $this->attchktime = $attchktime;
        $this->user = $user;
    }
    public function createEntity(User $user)
    {
        $dbf = new Dbf();
        $dbf->setUserid($this->userid);
        $dbf->setBadgenumbe(intval($this->badgenumbe));
        $dbf->setSsn($this->ssn);
        $dbf->setUsername($this->username);
        $dbf->setAutosch($this->autosch);
        $dbf->setAttdate($this->dateService->dateString_d_m_Y_ToDateTime($this->attdate));
        $dbf->setSchid($this->schid);
        $dbf->setClockintim($this->timeService->timeStringToDateTime($this->clockintim));
        $dbf->setClockoutti($this->timeService->timeStringToDateTime($this->clockoutti));
        $dbf->setStarttime($this->timeService->timeStringToDateTime($this->starttime));
        $dbf->setEndtime($this->timeService->timeStringToDateTime($this->endtime));
        $dbf->setWorkday($this->workday);
        $dbf->setRealworkda($this->realworkda);
        $dbf->setLate($this->timeService->timeStringToDateTime($this->late));
        $dbf->setEarly($this->timeService->timeStringToDateTime($this->early));
        $dbf->setAbsent($this->absent);
        $dbf->setOvertime($this->timeService->timeStringToDateTime($this->overtime));
        $dbf->setWorktime($this->timeService->timeStringToDateTime($this->worktime));
        $dbf->setExceptioni($this->exceptioni);
        $dbf->setMustin($this->mustin);
        $dbf->setMustout($this->mustout);
        $dbf->setDeptid($this->deptid);
        $dbf->setSspedaynor($this->sspedaynor);
        $dbf->setSspedaywee($this->sspedaywee);
        $dbf->setSspedayhol($this->sspedayhol);
        $dbf->setAtttime($this->timeService->timeStringToDateTime($this->atttime));;
        $dbf->setAttchktime(explode(" ", $this->attchktime));
        $dbf->setEmployer($user);
        return $dbf;
    }
    public function dateDbfInDb(User $user)
    {
        return array_map(
            fn ($date): string => $date->getAttdate()->format('Y-m-d'),
            $user->getDbfs()->toArray()
        );
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
