<?php

namespace App\EventSubscriber;

use App\Entity\Dbf;
use App\Entity\User;
use App\Entity\Absence;
use App\Entity\Pointage;
use App\Service\PointageService;
use App\Service\CongerService;
use Doctrine\ORM\EntityManagerInterface;
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
    private $congerService;

    /**
     * __construct
     * @param UserPasswordHasherInterface $passwordHasher
     * @param PointageService $pointageService
     * @param CongerService $congerService
     */
    public function __construct(
        UserPasswordHasherInterface $passwordHasher,
        PointageService $pointageService,
        CongerService $congerService,
        EntityManagerInterface $manager
    ) {
        $this->passwordHasher = $passwordHasher;
        $this->pointageService = $pointageService;
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
        dd($absence);
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
        dd($dbf);
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
        $user = $event->getEntityInstance();
        if (!($user instanceof User)) {
            return;
        }
        dd($user);
        $user->setPassword($this->passwordHasher->hashPassword(
            $user,
            $user->getPassword()
        ));
      
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
        $pointage = $event->getEntityInstance();
        if (!($pointage instanceof Pointage)) {
            return;
        }
        dd($pointage);
    }
}
