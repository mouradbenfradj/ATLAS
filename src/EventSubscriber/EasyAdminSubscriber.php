<?php

namespace App\EventSubscriber;

use App\Entity\Dbf;
use App\Entity\User;
use App\Entity\Absence;
use App\Entity\AutorisationSortie;
use App\Entity\Pointage;
use App\Service\PointageService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
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
            AfterEntityPersistedEvent::class => ['ajoutDeAS'],
            BeforeEntityPersistedEvent::class => ['hashPassword'],
            BeforeEntityUpdatedEvent::class => ['totaleRetard',],
            AfterEntityUpdatedEvent::class => ['dbfUpdated'],
            BeforeEntityDeletedEvent::class => ['absenceDeleter'],
        ];
    }

    public function ajoutDeAS(AfterEntityPersistedEvent $event)
    {
        $as = $event->getEntityInstance();
        if (!($as instanceof AutorisationSortie)) {
            return;
        }
        $heurAutoriser = strtotime($as->getHeurAutoriser()->format('H:i:s'));
        dump($heurAutoriser);
   
        /**
         * @var Pointage|null
         */
        $pointage = current(array_filter(array_map(
            fn ($pointage): ?Pointage => ($as->getDateAutorisation() <= $pointage->getDate() && $pointage->getDate() <= $as->getDateAutorisation()) ? $pointage : null,
            $as->getEmployer()->getPointages()->toArray()
        )));
        $retardEnMinute = $pointage->getRetardEnMinute()? strtotime($pointage->getRetardEnMinute()->format('H:i:s')):0;
        $retardMidi =  $pointage->getRetardMidi() ? strtotime($pointage->getRetardMidi()->format('H:i:s')) : 0;
        $departAnticiper =  $pointage->getDepartAnticiper() ? strtotime($pointage->getDepartAnticiper()->format('H:i:s')):0;
       
        for ($i = 0 ; $i<3;$i++) {
            $time = new DateTime();
            if (($retardEnMinute >= $retardMidi) &&($retardEnMinute>=$departAnticiper)) {
                if ($heurAutoriser >=$retardEnMinute) {
                    $time->setTimestamp($heurAutoriser -$retardEnMinute);
                    $heurAutoriser -= $retardEnMinute;
                    $retardEnMinute=0;
                } else {
                    $time->setTimestamp($retardEnMinute -  $heurAutoriser);
                    $retardEnMinute=$retardEnMinute-$heurAutoriser ;
                    $heurAutoriser = 0;
                }
                $pointage->setRetardEnMinute($time);
            } elseif (($retardMidi >=$departAnticiper) && ($retardMidi>=$retardEnMinute)) {
                if ($heurAutoriser >=$retardMidi) {
                    $time->setTimestamp($heurAutoriser -$retardMidi);
                    $heurAutoriser -= $retardMidi;
                    $retardMidi=0;
                } else {
                    $time->setTimestamp($retardMidi -  $heurAutoriser);
                    $retardMidi=$retardMidi-$heurAutoriser ;
                    $heurAutoriser = 0;
                }
                $pointage->setRetardMidi($time);
            } elseif (($departAnticiper >= $retardMidi) && ($departAnticiper >= $retardEnMinute)) {
                if ($heurAutoriser >=$departAnticiper) {
                    $time->setTimestamp($heurAutoriser -$departAnticiper);
                    $heurAutoriser -= $departAnticiper;
                    $departAnticiper=0;
                } else {
                    $time->setTimestamp($departAnticiper -  $heurAutoriser);
                    $departAnticiper=$departAnticiper-$heurAutoriser ;
                    $heurAutoriser = 0;
                }
                $pointage->setDepartAnticiper($time);
            } else {
                dump($retardEnMinute);
                dump($retardMidi);
                dd($departAnticiper);
            }
        }
        $pointage->setTotaleRetard(new DateTime(date('H:i:s', $retardEnMinute+$retardMidi+$departAnticiper)));
        dd($pointage);
        //$pointage->setAbsence(null);
        // $this->congerService->constructFromAbsence($absence);
        $this->manager->flush();
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
