<?php

namespace App\Entity;

use App\Repository\PointageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PointageRepository::class)
 */
class Pointage
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\OneToMany(targetEntity=Horaire::class, mappedBy="pointage")
     */
    private $horaire;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $entrer;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $sortie;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $nbrHeurTravailler;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $retardEnMinute;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $departAnticiper;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $retardMidi;

    /**
     * @ORM\Column(type="time")
     */
    private $totaleRetard;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $autorisationSortie;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $congerPayer;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $abscence;

    /**
     * @ORM\Column(type="time")
     */
    private $heurNormalementTravailler;

    /**
     * @ORM\Column(type="time")
     */
    private $diff;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="pointage")
     */
    private $Employer;

    public function __construct()
    {
        $this->horaire = new ArrayCollection();
        $this->Employer = new ArrayCollection();
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return Collection|Horaire[]
     */
    public function getHoraire(): Collection
    {
        return $this->horaire;
    }

    public function addHoraire(Horaire $horaire): self
    {
        if (!$this->horaire->contains($horaire)) {
            $this->horaire[] = $horaire;
            $horaire->setPointage($this);
        }

        return $this;
    }

    public function removeHoraire(Horaire $horaire): self
    {
        if ($this->horaire->removeElement($horaire)) {
            // set the owning side to null (unless already changed)
            if ($horaire->getPointage() === $this) {
                $horaire->setPointage(null);
            }
        }

        return $this;
    }

    public function getEntrer(): ?\DateTimeInterface
    {
        return $this->entrer;
    }

    public function setEntrer(?\DateTimeInterface $entrer): self
    {
        $this->entrer = $entrer;

        return $this;
    }

    public function getSortie(): ?\DateTimeInterface
    {
        return $this->sortie;
    }

    public function setSortie(?\DateTimeInterface $sortie): self
    {
        $this->sortie = $sortie;

        return $this;
    }

    public function getNbrHeurTravailler(): ?\DateTimeInterface
    {
        return $this->nbrHeurTravailler;
    }

    public function setNbrHeurTravailler(?\DateTimeInterface $nbrHeurTravailler): self
    {
        $this->nbrHeurTravailler = $nbrHeurTravailler;

        return $this;
    }

    public function getRetardEnMinute(): ?\DateTimeInterface
    {
        return $this->retardEnMinute;
    }

    public function setRetardEnMinute(?\DateTimeInterface $retardEnMinute): self
    {
        $this->retardEnMinute = $retardEnMinute;

        return $this;
    }

    public function getDepartAnticiper(): ?\DateTimeInterface
    {
        return $this->departAnticiper;
    }

    public function setDepartAnticiper(?\DateTimeInterface $departAnticiper): self
    {
        $this->departAnticiper = $departAnticiper;

        return $this;
    }

    public function getRetardMidi(): ?\DateTimeInterface
    {
        return $this->retardMidi;
    }

    public function setRetardMidi(?\DateTimeInterface $retardMidi): self
    {
        $this->retardMidi = $retardMidi;

        return $this;
    }

    public function getTotaleRetard(): ?\DateTimeInterface
    {
        return $this->totaleRetard;
    }

    public function setTotaleRetard(\DateTimeInterface $totaleRetard): self
    {
        $this->totaleRetard = $totaleRetard;

        return $this;
    }

    public function getAutorisationSortie(): ?\DateTimeInterface
    {
        return $this->autorisationSortie;
    }

    public function setAutorisationSortie(?\DateTimeInterface $autorisationSortie): self
    {
        $this->autorisationSortie = $autorisationSortie;

        return $this;
    }

    public function getCongerPayer(): ?float
    {
        return $this->congerPayer;
    }

    public function setCongerPayer(?float $congerPayer): self
    {
        $this->congerPayer = $congerPayer;

        return $this;
    }

    public function getAbscence(): ?float
    {
        return $this->abscence;
    }

    public function setAbscence(?float $abscence): self
    {
        $this->abscence = $abscence;

        return $this;
    }

    public function getHeurNormalementTravailler(): ?\DateTimeInterface
    {
        return $this->heurNormalementTravailler;
    }

    public function setHeurNormalementTravailler(\DateTimeInterface $heurNormalementTravailler): self
    {
        $this->heurNormalementTravailler = $heurNormalementTravailler;

        return $this;
    }

    public function getDiff(): ?\DateTimeInterface
    {
        return $this->diff;
    }

    public function setDiff(\DateTimeInterface $diff): self
    {
        $this->diff = $diff;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getEmployer(): Collection
    {
        return $this->Employer;
    }

    public function addEmployer(User $employer): self
    {
        if (!$this->Employer->contains($employer)) {
            $this->Employer[] = $employer;
            $employer->setPointage($this);
        }

        return $this;
    }

    public function removeEmployer(User $employer): self
    {
        if ($this->Employer->removeElement($employer)) {
            // set the owning side to null (unless already changed)
            if ($employer->getPointage() === $this) {
                $employer->setPointage(null);
            }
        }

        return $this;
    }
}
