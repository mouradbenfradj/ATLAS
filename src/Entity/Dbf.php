<?php

namespace App\Entity;

use App\Repository\DbfRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DbfRepository::class)
 */
class Dbf
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $userid;

    /**
     * @ORM\Column(type="integer")
     */
    private $badgenumbe;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ssn;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $autosch;

    /**
     * @ORM\Column(type="date")
     */
    private $attdate;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $schid;

    /**
     * @ORM\Column(type="time", nullable=true)
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

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="dbfs")
     */
    private $employer;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserid(): ?float
    {
        return $this->userid;
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
