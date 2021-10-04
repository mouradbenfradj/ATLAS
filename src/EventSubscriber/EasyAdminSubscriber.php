<?php

namespace App\EventSubscriber;

use App\Entity\Pointage;
use App\Entity\User;
use App\Service\PointageService;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    private $passwordHasher;
    private $pointageService;

    public function __construct(UserPasswordHasherInterface $passwordHasher, PointageService $pointageService)
    {
        $this->passwordHasher = $passwordHasher;
        $this->pointageService = $pointageService;
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => ['hashPassword'],
            BeforeEntityUpdatedEvent::class => ['totaleRetard'],
        ];
    }

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
        /* 
        $slug = $this->slugger->slugify($entity->getTitle());
        $entity->setSlug($slug); */
    }
    public function totaleRetard(BeforeEntityUpdatedEvent $event)
    {
        $entity = $event->getEntityInstance();
        if (!($entity instanceof Pointage)) {
            return;
        }
        $this->pointageService->setPointage($entity);
        $entity->setTotaleRetard($this->pointageService->totalRetard());
        /* 
        $slug = $this->slugger->slugify($entity->getTitle());
        $entity->setSlug($slug); */
    }
}
