<?php

namespace App\Entity;

use App\Repository\WorkTimeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=WorkTimeRepository::class)
 */
class WorkTime
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="workTimes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $employer;

    /**
     * @ORM\ManyToOne(targetEntity=Horaire::class, inversedBy="workTimes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $horaire;

    /**
     * @ORM\Column(type="date")
     */
    private $dateDebut;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateFin;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $heurDebutTravaille;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $heurFinTravaille;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $debutPauseMatinal;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $finPauseMatinal;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $debutPauseDejeuner;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $finPauseDejeuner;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $debutPauseMidi;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $finPauseMidi;

    /**
     * @ORM\OneToMany(targetEntity=Pointage::class, mappedBy="workTime")
     */
    private $pointages;

    public function __construct()
    {
        $this->pointages = new ArrayCollection();
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

    public function getHoraire(): ?Horaire
    {
        return $this->horaire;
    }

    public function setHoraire(?Horaire $horaire): self
    {
        $this->horaire = $horaire;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getHeurDebutTravaille(): ?\DateTimeInterface
    {
        return $this->heurDebutTravaille;
    }

    public function setHeurDebutTravaille(?\DateTimeInterface $heurDebutTravaille): self
    {
        $this->heurDebutTravaille = $heurDebutTravaille;

        return $this;
    }

    public function getHeurFinTravaille(): ?\DateTimeInterface
    {
        return $this->heurFinTravaille;
    }

    public function setHeurFinTravaille(?\DateTimeInterface $heurFinTravaille): self
    {
        $this->heurFinTravaille = $heurFinTravaille;

        return $this;
    }

    public function getDebutPauseMatinal(): ?\DateTimeInterface
    {
        return $this->debutPauseMatinal;
    }

    public function setDebutPauseMatinal(?\DateTimeInterface $debutPauseMatinal): self
    {
        $this->debutPauseMatinal = $debutPauseMatinal;

        return $this;
    }

    public function getFinPauseMatinal(): ?\DateTimeInterface
    {
        return $this->finPauseMatinal;
    }

    public function setFinPauseMatinal(?\DateTimeInterface $finPauseMatinal): self
    {
        $this->finPauseMatinal = $finPauseMatinal;

        return $this;
    }

    public function getDebutPauseDejeuner(): ?\DateTimeInterface
    {
        return $this->debutPauseDejeuner;
    }

    public function setDebutPauseDejeuner(?\DateTimeInterface $debutPauseDejeuner): self
    {
        $this->debutPauseDejeuner = $debutPauseDejeuner;

        return $this;
    }

    public function getFinPauseDejeuner(): ?\DateTimeInterface
    {
        return $this->finPauseDejeuner;
    }

    public function setFinPauseDejeuner(?\DateTimeInterface $finPauseDejeuner): self
    {
        $this->finPauseDejeuner = $finPauseDejeuner;

        return $this;
    }

    public function getDebutPauseMidi(): ?\DateTimeInterface
    {
        return $this->debutPauseMidi;
    }

    public function setDebutPauseMidi(?\DateTimeInterface $debutPauseMidi): self
    {
        $this->debutPauseMidi = $debutPauseMidi;

        return $this;
    }

    public function getFinPauseMidi(): ?\DateTimeInterface
    {
        return $this->finPauseMidi;
    }

    public function setFinPauseMidi(?\DateTimeInterface $finPauseMidi): self
    {
        $this->finPauseMidi = $finPauseMidi;

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
            $pointage->setWorkTime($this);
        }

        return $this;
    }

    public function removePointage(Pointage $pointage): self
    {
        if ($this->pointages->removeElement($pointage)) {
            // set the owning side to null (unless already changed)
            if ($pointage->getWorkTime() === $this) {
                $pointage->setWorkTime(null);
            }
        }

        return $this;
    }
}
