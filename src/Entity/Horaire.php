<?php

namespace App\Entity;

use App\Repository\HoraireRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=HoraireRepository::class)
 */
class Horaire
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $horaire;

    /**
     * @ORM\Column(type="date")
     */
    private $dateDebut;

    /**
     * @ORM\Column(type="date")
     */
    private $dateFin;

    /**
     * @ORM\Column(type="time")
     */
    private $heurDebutTravaille;

    /**
     * @ORM\Column(type="time")
     */
    private $heurFinTravaille;

    /**
     * @ORM\Column(type="time")
     */
    private $debutPauseMatinal;

    /**
     * @ORM\Column(type="time")
     */
    private $finPauseMatinal;

    /**
     * @ORM\Column(type="time")
     */
    private $debutPauseDejeuner;

    /**
     * @ORM\Column(type="time")
     */
    private $finPauseDejeuner;

    /**
     * @ORM\Column(type="time")
     */
    private $debutPauseMidi;

    /**
     * @ORM\Column(type="time")
     */
    private $finPauseMidi;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHoraire(): ?string
    {
        return $this->horaire;
    }

    public function setHoraire(string $horaire): self
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

    public function setDateFin(\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getHeurDebutTravaille(): ?\DateTimeInterface
    {
        return $this->heurDebutTravaille;
    }

    public function setHeurDebutTravaille(\DateTimeInterface $heurDebutTravaille): self
    {
        $this->heurDebutTravaille = $heurDebutTravaille;

        return $this;
    }

    public function getHeurFinTravaille(): ?\DateTimeInterface
    {
        return $this->heurFinTravaille;
    }

    public function setHeurFinTravaille(\DateTimeInterface $heurFinTravaille): self
    {
        $this->heurFinTravaille = $heurFinTravaille;

        return $this;
    }

    public function getDebutPauseMatinal(): ?\DateTimeInterface
    {
        return $this->debutPauseMatinal;
    }

    public function setDebutPauseMatinal(\DateTimeInterface $debutPauseMatinal): self
    {
        $this->debutPauseMatinal = $debutPauseMatinal;

        return $this;
    }

    public function getFinPauseMatinal(): ?\DateTimeInterface
    {
        return $this->finPauseMatinal;
    }

    public function setFinPauseMatinal(\DateTimeInterface $finPauseMatinal): self
    {
        $this->finPauseMatinal = $finPauseMatinal;

        return $this;
    }

    public function getDebutPauseDejeuner(): ?\DateTimeInterface
    {
        return $this->debutPauseDejeuner;
    }

    public function setDebutPauseDejeuner(\DateTimeInterface $debutPauseDejeuner): self
    {
        $this->debutPauseDejeuner = $debutPauseDejeuner;

        return $this;
    }

    public function getFinPauseDejeuner(): ?\DateTimeInterface
    {
        return $this->finPauseDejeuner;
    }

    public function setFinPauseDejeuner(\DateTimeInterface $finPauseDejeuner): self
    {
        $this->finPauseDejeuner = $finPauseDejeuner;

        return $this;
    }

    public function getDebutPauseMidi(): ?\DateTimeInterface
    {
        return $this->debutPauseMidi;
    }

    public function setDebutPauseMidi(\DateTimeInterface $debutPauseMidi): self
    {
        $this->debutPauseMidi = $debutPauseMidi;

        return $this;
    }

    public function getFinPauseMidi(): ?\DateTimeInterface
    {
        return $this->finPauseMidi;
    }

    public function setFinPauseMidi(\DateTimeInterface $finPauseMidi): self
    {
        $this->finPauseMidi = $finPauseMidi;

        return $this;
    }
}
