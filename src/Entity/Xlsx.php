<?php

namespace App\Entity;

use App\Repository\XlsxRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=XlsxRepository::class)
 */
class Xlsx
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
     * @ORM\ManyToOne(targetEntity=Horaire::class, cascade={"persist"}, inversedBy="xlsxes")
     * @ORM\JoinColumn(nullable=false)
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
     * @ORM\Column(type="time", nullable=true)
     */
    private $totalRetard;

    /**
     * @ORM\ManyToOne(targetEntity=AutorisationSortie::class, cascade={"persist"}, inversedBy="xlsxes")
     */
    private $autorisationSortie;

    /**
     * @ORM\ManyToOne(targetEntity=Conger::class, cascade={"persist"}, inversedBy="xlsxes")
     */
    private $congerPayer;

    /**
     * @ORM\ManyToOne(targetEntity=Absence::class, cascade={"persist"}, inversedBy="xlsxes")
     */
    private $absence;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $heurNormalementTravailler;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $diff;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="xlsxes")
     */
    private $employer;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getHoraire(): ?Horaire
    {
        return $this->horaire;
    }

    public function setHoraire(?Horaire $horaire): self
    {
        $this->horaire = $horaire;

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

    public function getTotalRetard(): ?\DateTimeInterface
    {
        return $this->totalRetard;
    }

    public function setTotalRetard(?\DateTimeInterface $totalRetard): self
    {
        $this->totalRetard = $totalRetard;

        return $this;
    }

    public function getAutorisationSortie(): ?AutorisationSortie
    {
        return $this->autorisationSortie;
    }

    public function setAutorisationSortie(?AutorisationSortie $autorisationSortie): self
    {
        $this->autorisationSortie = $autorisationSortie;

        return $this;
    }

    public function getCongerPayer(): ?Conger
    {
        return $this->congerPayer;
    }

    public function setCongerPayer(?Conger $congerPayer): self
    {
        $this->congerPayer = $congerPayer;

        return $this;
    }

    public function getAbsence(): ?Absence
    {
        return $this->absence;
    }

    public function setAbsence(?Absence $absence): self
    {
        $this->absence = $absence;

        return $this;
    }

    public function getHeurNormalementTravailler(): ?\DateTimeInterface
    {
        return $this->heurNormalementTravailler;
    }

    public function setHeurNormalementTravailler(?\DateTimeInterface $heurNormalementTravailler): self
    {
        $this->heurNormalementTravailler = $heurNormalementTravailler;

        return $this;
    }

    public function getDiff(): ?\DateTimeInterface
    {
        return $this->diff;
    }

    public function setDiff(?\DateTimeInterface $diff): self
    {
        $this->diff = $diff;

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
