<?php

namespace App\Entity;

use App\Repository\ConfigRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ConfigRepository::class)
 */
class Config
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
    private $debutSoldConger;

    /**
     * @ORM\Column(type="float")
     */
    private $incSoldConger;

    /**
     * @ORM\Column(type="time")
     */
    private $debutSoldAS;

    /**
     * @ORM\Column(type="time")
     */
    private $incAutorisationSortie;

    /**
     * @ORM\Column(type="boolean")
     */
    private $reinitialisationC;

    /**
     * @ORM\Column(type="boolean")
     */
    private $reinitialisationAS;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDebutSoldConger(): ?float
    {
        return $this->debutSoldConger;
    }

    public function setDebutSoldConger(float $debutSoldConger): self
    {
        $this->debutSoldConger = $debutSoldConger;

        return $this;
    }

    public function getIncSoldConger(): ?float
    {
        return $this->incSoldConger;
    }

    public function setIncSoldConger(float $incSoldConger): self
    {
        $this->incSoldConger = $incSoldConger;

        return $this;
    }

    public function getDebutSoldAS(): ?\DateTimeInterface
    {
        return $this->debutSoldAS;
    }

    public function setDebutSoldAS(\DateTimeInterface $debutSoldAS): self
    {
        $this->debutSoldAS = $debutSoldAS;

        return $this;
    }

    public function getIncAutorisationSortie(): ?\DateTimeInterface
    {
        return $this->incAutorisationSortie;
    }

    public function setIncAutorisationSortie(\DateTimeInterface $incAutorisationSortie): self
    {
        $this->incAutorisationSortie = $incAutorisationSortie;

        return $this;
    }

    public function getReinitialisationC(): ?bool
    {
        return $this->reinitialisationC;
    }

    public function setReinitialisationC(bool $reinitialisationC): self
    {
        $this->reinitialisationC = $reinitialisationC;

        return $this;
    }

    public function getReinitialisationAS(): ?bool
    {
        return $this->reinitialisationAS;
    }

    public function setReinitialisationAS(bool $reinitialisationAS): self
    {
        $this->reinitialisationAS = $reinitialisationAS;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }
}
