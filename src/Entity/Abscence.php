<?php

namespace App\Entity;

use App\Repository\AbscenceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AbscenceRepository::class)
 */
class Abscence
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    public function __toString()
    {
        return "1";
    }
    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="abscences")
     */
    private $employer;

    /**
     * @ORM\Column(type="date")
     */
    private $debut;

    /**
     * @ORM\Column(type="date")
     */
    private $fin;

    /**
     * @ORM\OneToMany(targetEntity=Pointage::class, mappedBy="abscence")
     */
    private $pointages;

    /**
     * @ORM\OneToMany(targetEntity=Xlsx::class, mappedBy="abcence")
     */
    private $xlsxes;

    public function __construct()
    {
        $this->pointages = new ArrayCollection();
        $this->xlsxes = new ArrayCollection();
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

    public function getDebut(): ?\DateTimeInterface
    {
        return $this->debut;
    }

    public function setDebut(\DateTimeInterface $debut): self
    {
        $this->debut = $debut;

        return $this;
    }

    public function getFin(): ?\DateTimeInterface
    {
        return $this->fin;
    }

    public function setFin(\DateTimeInterface $fin): self
    {
        $this->fin = $fin;

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
            $pointage->setAbscence($this);
        }

        return $this;
    }

    public function removePointage(Pointage $pointage): self
    {
        if ($this->pointages->removeElement($pointage)) {
            // set the owning side to null (unless already changed)
            if ($pointage->getAbscence() === $this) {
                $pointage->setAbscence(null);
            }
        }

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
            $xlsx->setAbcence($this);
        }

        return $this;
    }

    public function removeXlsx(Xlsx $xlsx): self
    {
        if ($this->xlsxes->removeElement($xlsx)) {
            // set the owning side to null (unless already changed)
            if ($xlsx->getAbcence() === $this) {
                $xlsx->setAbcence(null);
            }
        }

        return $this;
    }
}
