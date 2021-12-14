<?php

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use DateTime;
use PHPUnit\Framework\TestCase;

/**
 * UserTest class
 *
 * @category User
 * @package  Test
 * @author   Mourad <mourad.ben.fradj@gmail.com>
 * @license  MIT http://
 * @link     http://url.com
 */
class UserTest extends TestCase
{
    const EMAIL = 'mourad.benfradj.atlas@gmail.com';
    const NOM = 'Ben Fradj';
    const PRENOM = 'Mourad';
    const QUALIFICATION = 'Ingenieur Informatique';

    /**
     * TestIsTrue function
     *
     * @return void
     */
    public function testIsTrue(): void
    {
        $date = new DateTime("2021-11-30");
        $time = new DateTime("23:00:00");
        $user = new User();
        $sconger = 10.040;
        $user->setEmail(self::EMAIL)
            ->setRoles(array('ROLE_USER'))
            ->setPassword("mourad")
            ->setId(127)
            ->setBadgenumbe(207)
            ->setFirstName(self::PRENOM)
            ->setLastName(self::NOM)
            ->setQualification(self::QUALIFICATION)
            ->setMatricule(502)
            ->setDebutTravaille($date)
            ->setDemission($date)
            ->setSoldAutorisationSortie($time)
            ->setSoldConger($sconger)
            ->setIsVerified(true);
        $this->assertSame(self::EMAIL, $user->getEmail());
        $this->assertSame(array('ROLE_USER'), $user->getRoles());
        $this->assertSame("mourad", $user->getPassword());
        $this->assertSame(127, $user->getId());
        $this->assertSame(207, $user->getBadgenumbe());
        $this->assertSame(self::PRENOM, $user->getFirstName());
        $this->assertSame(self::NOM, $user->getLastName());
        $this->assertSame(self::QUALIFICATION, $user->getQualification());
        $this->assertSame(502, $user->getMatricule());
        $this->assertSame($date, $user->getDebutTravaille());
        $this->assertSame($date, $user->getDemission());
        $this->assertSame($time, $user->getSoldAutorisationSortie());
        $this->assertSame($sconger, $user->getSoldConger());
        $this->assertSame(true, $user->isVerified());
    }
    /**
     * TestIsFalse function
     *
     * @return void
     */
    public function testIsFalse(): void
    {
        $date = new DateTime("2021-11-30");
        $date2 = new DateTime("2020-11-30");
        $time = new DateTime("23:00:00");
        $time2 = new DateTime("23:23:23");
        $user = new User();
        $sconger = 10.040;
        $user->setEmail(self::EMAIL)
            ->setRoles(array('ROLE_USER'))
            ->setPassword("mourad")
            ->setId(127)
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
        $this->assertNotSame("false@gmail.com", $user->getEmail());
        $this->assertNotSame(["false"], $user->getRoles());
        $this->assertNotSame("false", $user->getPassword());
        $this->assertNotSame(0, $user->getBadgenumbe());
        $this->assertNotSame("false", $user->getFirstName());
        $this->assertNotSame(0, $user->getId());
        $this->assertNotSame("false", $user->getQualification());
        $this->assertNotSame("false", $user->getLastName());
        $this->assertNotSame(0, $user->getMatricule());
        $this->assertNotSame($date2, $user->getDebutTravaille());
        $this->assertNotSame($date2, $user->getDemission());
        $this->assertNotSame($time2, $user->getSoldAutorisationSortie());
        $this->assertNotSame(1, $user->getSoldConger());
        $this->assertNotSame(false, $user->isVerified());
    }
    /**
     * TestIsEmpty function
     *
     * @return void
     */
    public function testIsEmpty(): void
    {
        $user = new User();
        $this->assertEmpty($user->getEmail());
        $this->assertEmpty($user->getId());
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
