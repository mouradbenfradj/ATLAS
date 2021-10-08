<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Service\ConfigService;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * passwordHasher
     *
     * @var UserPasswordHasherInterface
     */
    private $passwordHasher;
    /**
     * configService
     *
     * @var ConfigService
     */
    private $configService;
    /**
     * __construct
     *
     * @param UserPasswordHasherInterface $passwordHasher
     * @param ConfigService $configService
     */
    public function __construct(UserPasswordHasherInterface $passwordHasher, ConfigService $configService)
    {
        $this->passwordHasher = $passwordHasher;
        $this->configService = $configService;
    }
    /**
     * load
     *
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail("mourad.ben.fradj@gmail.com");
        $user->setSoldConger(0);
        $user->setSoldAutorisationSortie(new DateTime("00:00:00"));
        $user->setBadgenumbe(207);
        $user->setFirstName("Mourad");
        $user->setLastName("Ben Fradj");
        $user->setDebutTravaille(new DateTime("2016/04/02"));
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($this->passwordHasher->hashPassword(
            $user,
            'mourad'
        ));

        $user->setSoldConger($this->configService->getConfig()->getDebutSoldConger());
        $user->setSoldAutorisationSortie($this->configService->getConfig()->getDebutSoldAS());

        $manager->persist($user);
        $user = new User();
        $user->setEmail("mourad.benfradj.atlas@gmail.com");
        $user->setBadgenumbe(302);
        $user->setSoldConger(0);
        $user->setSoldAutorisationSortie(new DateTime("00:00:00"));
        $user->setFirstName("mourad");
        $user->setLastName("k");
        $user->setDebutTravaille(new DateTime("2021/01/01"));
        $user->setRoles(['ROLE_EMPLOYER']);
        $user->setPassword($this->passwordHasher->hashPassword(
            $user,
            'cherif'
        ));

        $user->setSoldConger($this->configService->getConfig()->getDebutSoldConger());
        $user->setSoldAutorisationSortie($this->configService->getConfig()->getDebutSoldAS());

        $manager->persist($user);

        $manager->flush();
    }

    /**
     * getDependencies
     *
     * @return void
     */
    public function getDependencies()
    {
        return [ConfigFixtures::class];
    }
}
