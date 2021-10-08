<?php

namespace App\EventSubscriber;

use App\Entity\Pointage;
use App\Entity\User;
use App\Service\ConfigService;
use App\Service\PointageService;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    private $passwordHasher;
    private $pointageService;
    private $configService;

    /**
     * __construct
     *
     * @param UserPasswordHasherInterface $passwordHasher
     * @param ConfigService $configService
     * @param PointageService $pointageService
     */
    public function __construct(UserPasswordHasherInterface $passwordHasher, ConfigService $configService, PointageService $pointageService)
    {
        $this->passwordHasher = $passwordHasher;
        $this->pointageService = $pointageService;
        $this->configService = $configService;
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
        ];
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
        $entity->setSoldConger($this->configService->getConfig()->getDebutSoldConger());
        $entity->setSoldAutorisationSortie($this->configService->getConfig()->getDebutSoldAS());

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

        dd($entity);
        $this->pointageService->setPointage($entity);
        $entity->setTotaleRetard($this->pointageService->totalRetard());
        /* 
        $slug = $this->slugger->slugify($entity->getTitle());
        $entity->setSlug($slug); */
    }
}
