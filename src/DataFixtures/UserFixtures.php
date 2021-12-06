<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Service\ConfigService;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    /**
     * passwordHasher
     *
     * @var UserPasswordHasherInterface
     */
    private $passwordHasher;

    /**
     * __construct
     *
     * @param UserPasswordHasherInterface $passwordHasher
     */
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
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
        $user->setId(100);
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
        $user->setIsVerified(true);
        $user->setSoldConger(0);
        $user->setSoldAutorisationSortie(new DateTime('23:00:00'));
        $manager->persist($user);
        $manager->flush();
    }
}
