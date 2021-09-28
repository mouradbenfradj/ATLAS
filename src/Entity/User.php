<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public function __toString()
    {
        return $this->getBadgenumbe() . " " . $this->getLastName() . " " . $this->getFirstName();
    }
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $userID;

    /**
     * @ORM\Column(type="integer")
     */
    private $badgenumbe;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $qualification;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $matricule;

    /**
     * @ORM\Column(type="date")
     */
    private $debutTravaille;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $demission;

    /**
     * @ORM\OneToMany(targetEntity=Pointage::class,cascade={"persist"}, mappedBy="employer")
     */
    private $pointages;

    /**
     * @ORM\OneToMany(targetEntity=Conger::class, mappedBy="employer")
     */
    private $congers;

    /**
     * @ORM\OneToMany(targetEntity=AutorisationSortie::class, mappedBy="employer")
     */
    private $autorisationSorties;

    public function __construct()
    {
        $this->pointages = new ArrayCollection();
        $this->congers = new ArrayCollection();
        $this->autorisationSorties = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUserID(): ?int
    {
        return $this->userID;
    }

    public function setUserID(?int $userID): self
    {
        $this->userID = $userID;

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

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getQualification(): ?string
    {
        return $this->qualification;
    }

    public function setQualification(?string $qualification): self
    {
        $this->qualification = $qualification;

        return $this;
    }

    public function getMatricule(): ?int
    {
        return $this->matricule;
    }

    public function setMatricule(?int $matricule): self
    {
        $this->matricule = $matricule;

        return $this;
    }

    public function getDebutTravaille(): ?\DateTimeInterface
    {
        return $this->debutTravaille;
    }

    public function setDebutTravaille(\DateTimeInterface $debutTravaille): self
    {
        $this->debutTravaille = $debutTravaille;

        return $this;
    }

    public function getDemission(): ?\DateTimeInterface
    {
        return $this->demission;
    }

    public function setDemission(?\DateTimeInterface $demission): self
    {
        $this->demission = $demission;

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
            $pointage->setEmployer($this);
        }

        return $this;
    }

    public function removePointage(Pointage $pointage): self
    {
        if ($this->pointages->removeElement($pointage)) {
            // set the owning side to null (unless already changed)
            if ($pointage->getEmployer() === $this) {
                $pointage->setEmployer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Conger[]
     */
    public function getCongers(): Collection
    {
        return $this->congers;
    }

    public function addConger(Conger $conger): self
    {
        if (!$this->congers->contains($conger)) {
            $this->congers[] = $conger;
            $conger->setEmployer($this);
        }

        return $this;
    }

    public function removeConger(Conger $conger): self
    {
        if ($this->congers->removeElement($conger)) {
            // set the owning side to null (unless already changed)
            if ($conger->getEmployer() === $this) {
                $conger->setEmployer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AutorisationSortie[]
     */
    public function getAutorisationSorties(): Collection
    {
        return $this->autorisationSorties;
    }

    public function addAutorisationSorty(AutorisationSortie $autorisationSorty): self
    {
        if (!$this->autorisationSorties->contains($autorisationSorty)) {
            $this->autorisationSorties[] = $autorisationSorty;
            $autorisationSorty->setEmployer($this);
        }

        return $this;
    }

    public function removeAutorisationSorty(AutorisationSortie $autorisationSorty): self
    {
        if ($this->autorisationSorties->removeElement($autorisationSorty)) {
            // set the owning side to null (unless already changed)
            if ($autorisationSorty->getEmployer() === $this) {
                $autorisationSorty->setEmployer(null);
            }
        }

        return $this;
    }
}
