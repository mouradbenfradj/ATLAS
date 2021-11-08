<?php

namespace App\EventSubscriber;

use App\Entity\Dbf;
use App\Entity\User;
use App\Entity\Abscence;
use App\Entity\AutorisationSortie;
use App\Entity\Conger;
use App\Entity\Pointage;
use App\Service\ConfigService;
use App\Service\PointageService;
use App\Repository\DbfRepository;
use App\Service\HoraireService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    private $passwordHasher;
    private $pointageService;
    private $configService;
    private $horaireService;

    /**
     * __construct
     *
     * @param UserPasswordHasherInterface $passwordHasher
     * @param ConfigService $configService
     * @param PointageService $pointageService
     */
    public function __construct(
        UserPasswordHasherInterface $passwordHasher,
        ConfigService $configService,
        PointageService $pointageService,
        HoraireService $horaireService
    ) {
        $this->passwordHasher = $passwordHasher;
        $this->pointageService = $pointageService;
        $this->configService = $configService;
        $this->horaireService = $horaireService;
    }

    /**
     * getSubscribedEvents
     *
     * @return void
     */
    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => ['hashPassword'],
            BeforeEntityUpdatedEvent::class => ['totaleRetard',],
            AfterEntityUpdatedEvent::class => ['dbfUpdated'],
        ];
    }
    public function dbfUpdated(AfterEntityUpdatedEvent $event)
    {
        $dbf = $event->getEntityInstance();
        if (!($dbf instanceof Dbf)) {
            return;
        }
        if ($dbf->getStarttime() and $dbf->getEndtime() /* and !$conger and !$autorisationSortie */) {
            $this->pointageService->dbfUpdated($dbf);
        }
        return;
        /* 
        $slug = $this->slugger->slugify($entity->getTitle());
        $entity->setSlug($slug); */
    }

    /**
     * hashPassword
     *
     * @param BeforeEntityPersistedEvent $event
     * @return void
     */
    public function hashPassword(BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();
        if (!($entity instanceof User)) {
            return;
        }
        $entity->setPassword($this->passwordHasher->hashPassword(
            $entity,
            $entity->getPassword()
        ));
        //$entity->setSoldConger($this->configService->getConfig()->getDebutSoldConger());
        //$entity->setSoldAutorisationSortie($this->configService->getConfig()->getDebutSoldAS());

        /* 
        $slug = $this->slugger->slugify($entity->getTitle());
        $entity->setSlug($slug); */
    }
    /**
     * totaleRetard
     *
     * @param BeforeEntityUpdatedEvent $event
     * @return void
     */
    public function totaleRetard(BeforeEntityUpdatedEvent $event)
    {
        $entity = $event->getEntityInstance();
        if (!($entity instanceof Pointage)) {
            return;
        }

        /*   dd($entity);
        $this->pointageService->setPointage($entity);
        $entity->setTotaleRetard($this->pointageService->totalRetard()); */
        /* 
        $slug = $this->slugger->slugify($entity->getTitle());
        $entity->setSlug($slug); */
    }
}
