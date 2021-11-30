<?php

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use DateTime;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testIsTrue(): void
    {
        $date = new DateTime("2021-11-30");
        $time = new DateTime("23:00:00");
        $user = new User();
        $sconger = 10.040;
        $user->setEmail("mourad.ben.fradj@gmail.com")
        ->setRoles(array('ROLE_USER'))
        ->setPassword("mourad")
        ->setUserID(127)
        ->setBadgenumbe(207)
        ->setFirstName("mourad")
        ->setLastName("ben fradj")
        ->setQualification("ingenieur informtique")
        ->setMatricule(502)
        ->setDebutTravaille($date)
        ->setDemission($date)
        ->setSoldAutorisationSortie($time)
        ->setSoldConger($sconger)
        ->setIsVerified(true);
        $this->assertTrue($user->getEmail() === "mourad.ben.fradj@gmail.com");
        $this->assertTrue($user->getRoles() === array('ROLE_USER'));
        $this->assertTrue($user->getPassword() === "mourad");
        $this->assertTrue($user->getUserID() === 127);
        $this->assertTrue($user->getBadgenumbe() === 207);
        $this->assertTrue($user->getFirstName() === "mourad");
        $this->assertTrue($user->getLastName() === "ben fradj");
        $this->assertTrue($user->getQualification() === "ingenieur informtique");
        $this->assertTrue($user->getMatricule() === 502);
        $this->assertTrue($user->getDebutTravaille() ===  $date);
        $this->assertTrue($user->getDemission() ===  $date);
        $this->assertTrue($user->getSoldAutorisationSortie() === $time);
        $this->assertTrue($user->getSoldConger() ===  $sconger);
        $this->assertTrue($user->isVerified() ===true);
    }
    public function testIsFalse(): void
    {
        $date = new DateTime("2021-11-30");
        $time = new DateTime("23:00:00");
        $user = new User();
        $sconger = 10.040;
        $user->setEmail("mourad.ben.fradj@gmail.com")
        ->setRoles(array('ROLE_USER'))
        ->setPassword("mourad")
        ->setUserID(127)
        ->setBadgenumbe(207)
        ->setFirstName("mourad")
        ->setLastName("ben fradj")
        ->setQualification("ingenieur informtique")
        ->setMatricule(502)
        ->setDebutTravaille($date)
        ->setDemission($date)
        ->setSoldAutorisationSortie($time)
        ->setSoldConger($sconger)
        ->setIsVerified(true);
        $this->assertFalse($user->getEmail() === "false@gmail.com");
        $this->assertFalse($user->getRoles() === ["false"]);
        $this->assertFalse($user->getPassword() === "false");
        $this->assertFalse($user->getUserID() === 0);
        $this->assertFalse($user->getBadgenumbe() === 0);
        $this->assertFalse($user->getFirstName() === "false");
        $this->assertFalse($user->getLastName() === "false");
        $this->assertFalse($user->getQualification() === "false");
        $this->assertFalse($user->getMatricule() === 0);
        $this->assertFalse($user->getDebutTravaille() === new DateTime("2020-11-30"));
        $this->assertFalse($user->getDemission() === new DateTime("2020-11-30"));
        $this->assertFalse($user->getSoldAutorisationSortie() === new DateTime("23:23:23"));
        $this->assertFalse($user->getSoldConger() === 1);
        $this->assertFalse($user->isVerified() ===false);
    }
    public function testIsEmpty(): void
    {
        $user = new User();
        $this->assertEmpty($user->getEmail());
        //$this->assertEmpty($user->getRoles());
        //$this->assertEmpty($user->getPassword());
        $this->assertEmpty($user->getUserID());
        $this->assertEmpty($user->getBadgenumbe());
        $this->assertEmpty($user->getFirstName());
        $this->assertEmpty($user->getLastName());
        $this->assertEmpty($user->getQualification());
        $this->assertEmpty($user->getMatricule());
        $this->assertEmpty($user->getDebutTravaille());
        $this->assertEmpty($user->getDemission());
        $this->assertEmpty($user->getSoldAutorisationSortie());
        $this->assertEmpty($user->getSoldConger());
        $this->assertEmpty($user->isVerified());
    }
}
