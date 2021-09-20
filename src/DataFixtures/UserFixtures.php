<?php

namespace App\DataFixtures;

use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private $passwordHasher;
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail("mourad.ben.fradj@gmail.com");
        $user->setBadgenumbe(207);
        $user->setFirstName("Mourad");
        $user->setLastName("Ben Fradj");
        $user->setDebutTravaille(new DateTime("2016/04/02"));
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($this->passwordHasher->hashPassword(
            $user,
            'mourad'
        ));
        $manager->persist($user);

        $manager->flush();
    }
}
