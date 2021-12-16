<?php

namespace App\EventSubscriber;

use App\Entity\Dbf;
use App\Entity\User;
use App\Entity\Absence;
use App\Entity\Pointage;
use App\Service\PointageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EmployerService;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    private $manager;
    private $passwordHasher;
    private $pointageService;

    public function __construct(
        UserPasswordHasherInterface $passwordHasher,
        PointageService $pointageService,
        EntityManagerInterface $manager
    ) {
        $this->passwordHasher = $passwordHasher;
        $this->pointageService = $pointageService;
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
            fn ($pointage): ?Pointage => ($absence->getDebut() <= $pointage->getDate() && $pointage->getDate() <= $absence->getFin()) ? $pointage : null,
            $absence->getPointages()->toArray()
        )));
        $pointage->setAbsence(null);
        $this->congerService->constructFromAbsence($absence);
        die('tttt');
        $this->manager->flush();
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
