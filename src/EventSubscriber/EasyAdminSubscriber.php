<?php

namespace App\EventSubscriber;

use App\Entity\Dbf;
use App\Entity\User;
use App\Entity\Absence;
use App\Entity\AutorisationSortie;
use App\Entity\Conger;
use App\Entity\Pointage;
use App\Service\ConfigService;
use App\Service\PointageService;
use App\Repository\DbfRepository;
use App\Service\AbsenceService;
use App\Service\CongerService;
use App\Service\HoraireService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityDeletedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    private $manager;
    private $passwordHasher;
    private $pointageService;
    private $configService;
    private $horaireService;
    private $absenceService;
    private $congerService;

    /**
     * __construct
     * @param UserPasswordHasherInterface $passwordHasher
     * @param ConfigService $configService
     * @param PointageService $pointageService
     * @param HoraireService $horaireService
     * @param AbsenceService $absenceService
     * @param CongerService $congerService
     */
    public function __construct(
        UserPasswordHasherInterface $passwordHasher,
        ConfigService $configService,
        PointageService $pointageService,
        HoraireService $horaireService,
        AbsenceService $absenceService,
        CongerService $congerService,
        EntityManagerInterface $manager
    ) {
        $this->passwordHasher = $passwordHasher;
        $this->pointageService = $pointageService;
        $this->configService = $configService;
        $this->horaireService = $horaireService;
        $this->absenceService = $absenceService;
        $this->congerService = $congerService;
        $this->manager = $manager;
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
            BeforeEntityDeletedEvent::class => ['absenceDeleter'],
        ];
    }

    public function absenceDeleter(BeforeEntityDeletedEvent $event)
    {
        $absence = $event->getEntityInstance();
        if (!($absence instanceof Absence)) {
            return;
        }
        $pointage = current(array_filter(array_map(
            fn ($pointage): ?Pointage => ($absence->getDebut() <= $pointage->getDate() and $pointage->getDate() <= $absence->getFin()) ? $pointage : null,
            $absence->getPointages()->toArray()
        )));
        $pointage->setAbsence(null);
        $this->congerService->constructFromAbsence($absence);
        //$this->manager->persist($pointage);
        $this->manager->persist($this->congerService->createEntity());
        $this->manager->flush();
        /*  if ($absence->getStarttime() and $absence->getEndtime() /* and !$conger and !$autorisationSortie ) {
            $this->pointageService->constructFromDbf($absence);
            $this->pointageService->dbfUpdated($absence);
        } */
        return;
        /*
        $slug = $this->slugger->slugify($entity->getTitle());
        $entity->setSlug($slug); */
    }
    /**
     * dbfUpdated
     *
     * @param AfterEntityUpdatedEvent $event
     * @return void
     */
    public function dbfUpdated(AfterEntityUpdatedEvent $event)
    {
        $dbf = $event->getEntityInstance();
        if (!($dbf instanceof Dbf)) {
            return;
        }
        if (($dbf->getStarttime() and $dbf->getEndtime()) or count($dbf->getAttchktime())==0) {
            $this->pointageService->constructFromDbf($dbf);
            $pointage = $this->pointageService->createEntity();
            $this->pointageService->dbfUpdated($pointage, $dbf);
        }
        return;
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
