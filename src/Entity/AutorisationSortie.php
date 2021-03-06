<?php

namespace App\Entity;

use App\Repository\AutorisationSortieRepository;
use App\Service\TimeService;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AutorisationSortieRepository::class)
 */
class AutorisationSortie
{


    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="autorisationSorties")
     */
    private $employer;

    /**
     * @ORM\Column(type="date")
     */
    private $dateAutorisation;

    /**
     * @ORM\OneToMany(targetEntity=Pointage::class, mappedBy="autorisationSortie")
     */
    private $pointages;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $valider;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $refuser;

    /**
     * @ORM\OneToMany(targetEntity=Xlsx::class, mappedBy="autorisationSortie")
     */
    private $xlsxes;

    /**
     * @ORM\Column(type="time")
     */
    private $heurAutoriser;


    public function __construct()
    {
        $this->pointages = new ArrayCollection();
        $this->xlsxes = new ArrayCollection();
    }
    public function __toString()
    {
        return  $this->heurAutoriser->format("H:i:s");
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDateAutorisation(): ?\DateTimeInterface
    {
        return $this->dateAutorisation;
    }

    public function setDateAutorisation(\DateTimeInterface $dateAutorisation): self
    {
        $this->dateAutorisation = $dateAutorisation;

        return $this;
    }

    /**
     * @return Collection|Pointage[]
     */
    public function getPointages(): Collection
    {
        return $this->pointages;
    }

    public function addPointage(Pointage $pointage): self
    {
        if (!$this->pointages->contains($pointage)) {
            $this->pointages[] = $pointage;
            $pointage->setAutorisationSortie($this);
        }

        return $this;
    }

    public function removePointage(Pointage $pointage): self
    {
        if ($this->pointages->removeElement($pointage)) {
            // set the owning side to null (unless already changed)
            if ($pointage->getAutorisationSortie() === $this) {
                $pointage->setAutorisationSortie(null);
            }
        }

        return $this;
    }

    public function getValider(): ?bool
    {
        return $this->valider;
    }

    public function setValider(?bool $valider): self
    {
        $this->valider = $valider;

        return $this;
    }

    public function getRefuser(): ?bool
    {
        return $this->refuser;
    }

    public function setRefuser(?bool $refuser): self
    {
        $this->refuser = $refuser;

        return $this;
    }

    /**
     * @return Collection|Xlsx[]
     */
    public function getXlsxes(): Collection
    {
        return $this->xlsxes;
    }

    public function addXlsx(Xlsx $xlsx): self
    {
        if (!$this->xlsxes->contains($xlsx)) {
            $this->xlsxes[] = $xlsx;
            $xlsx->setAutorisationSortie($this);
        }

        return $this;
    }

    public function removeXlsx(Xlsx $xlsx): self
    {
        if ($this->xlsxes->removeElement($xlsx)) {
            // set the owning side to null (unless already changed)
            if ($xlsx->getAutorisationSortie() === $this) {
                $xlsx->setAutorisationSortie(null);
            }
        }

        return $this;
    }

    public function getHeurAutoriser(): ?\DateTimeInterface
    {
        return $this->heurAutoriser;
    }

    public function setHeurAutoriser(\DateTimeInterface $heurAutoriser): self
    {
        $this->heurAutoriser = $heurAutoriser;

        return $this;
    }
}
