<?php

namespace App\Service;

use App\Entity\AutorisationSortie;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class AutorisationSortieService
{
    /**
     * employer
     *
     * @var User
     */
    private $employer;

    /**
     * dateAutorisation
     *
     * @var DateTime
     */
    private $dateAutorisation;
    /**
     * pointages
     *
     * @var array
     */
    private $pointages;

    /**
     * valider
     *
     * @var bool
     */
    private $valider;

    /**
     * refuser
     *
     * @var bool
     */
    private $refuser;

    /**
     * de
     *
     * @var DateTime
     */
    private $de;

    /**
     * a
     *
     * @var DateTime
     */
    private $a;



    /**
     * em
     *
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * __construct
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function partielConstruct(
        ?User $employer = null,
        ?DateTime $dateAutorisation = null,
        ?DateTime $de = null,
        ?DateTime $a = null,
        ?bool $valider = null,
        ?bool $refuser = null,
        ?array $pointages = null
    ) {
        $this->employer = $employer;
        $this->dateAutorisation = $dateAutorisation;
        $this->de = $de;
        $this->a = $a;
        $this->pointages = $pointages;
        $this->valider = $valider;
        $this->refuser = $refuser;
    }


    public function ConstructEntity(): AutorisationSortie
    {
        $autorisationSortie = new AutorisationSortie();
        $autorisationSortie->setDateAutorisation($this->dateAutorisation);
        $autorisationSortie->setDe($this->de);
        $autorisationSortie->setA($this->a);
        $autorisationSortie->setValider($this->valider);
        $autorisationSortie->setRefuser($this->refuser);
        $autorisationSortie->setEmployer($this->employer);
        return $autorisationSortie;
    }



    /**
     * getIfAutorisationSortie
     *
     * @param string $date
     * @param User $employer
     * @return AutorisationSortie|null
     */
    public function getIfAutorisationSortie(string $date, User $employer): ?AutorisationSortie
    {
        return $this->em->getRepository(AutorisationSortie::class)->findOneByEmployerAndDate($date, $employer);
    }


    public function getAutorisation(DateTime $date): ?AutorisationSortie
    {
        $autorisationSortie =  current(array_filter(array_map(
            fn ($autorisationSortie): ?AutorisationSortie => ($autorisationSortie->getDateAutorisation() <= $date and $date <= $autorisationSortie->getDateAutorisation()) ? $autorisationSortie : null,
            $this->employer->getAutorisationSorties()->toArray()
        )));
        if ($autorisationSortie)
            return $autorisationSortie;
        return null;
    }
}
