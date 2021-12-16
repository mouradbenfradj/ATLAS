<?php

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use DateTime;
use PHPUnit\Framework\TestCase;
use stdClass;

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

    private $employer;

    protected function setUp(): void
    {
        $this->employer = new User();
    }
    public function testFailure1(): void
    {
        $this->assertContainsOnlyInstancesOf(
            User::class,
            [new User]
        );
    }
    public function testAttribute(): void
    {
        $this->assertClassHasAttribute('id', User::class);
        $this->assertClassHasAttribute('firstName', User::class);
        $this->assertClassHasAttribute('lastName', User::class);
        $this->assertClassHasAttribute('email', User::class);
        $this->assertClassHasAttribute('password', User::class);
    }


    public function testIsTrue(): void
    {
        $date = new DateTime("2021-11-30");
        $time = new DateTime("23:00:00");
        $sconger = 10.040;
        $this->employer->setEmail(self::EMAIL)
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
        $this->assertSame(self::EMAIL, $this->employer->getEmail());
        $this->assertSame(array('ROLE_USER'), $this->employer->getRoles());
        $this->assertSame("mourad", $this->employer->getPassword());
        $this->assertSame(127, $this->employer->getId());
        $this->assertSame(207, $this->employer->getBadgenumbe());
        $this->assertSame(self::PRENOM, $this->employer->getFirstName());
        $this->assertSame(self::NOM, $this->employer->getLastName());
        $this->assertSame(self::QUALIFICATION, $this->employer->getQualification());
        $this->assertSame(502, $this->employer->getMatricule());
        $this->assertSame($date, $this->employer->getDebutTravaille());
        $this->assertSame($date, $this->employer->getDemission());
        $this->assertSame($time, $this->employer->getSoldAutorisationSortie());
        $this->assertSame($sconger, $this->employer->getSoldConger());
        $this->assertSame(true, $this->employer->isVerified());
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
        $sconger = 10.040;
        $this->employer->setEmail(self::EMAIL)
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
        $this->assertNotSame("false@gmail.com", $this->employer->getEmail());
        $this->assertNotSame(["false"], $this->employer->getRoles());
        $this->assertNotSame("false", $this->employer->getPassword());
        $this->assertNotSame(0, $this->employer->getBadgenumbe());
        $this->assertNotSame("false", $this->employer->getFirstName());
        $this->assertNotSame(0, $this->employer->getId());
        $this->assertNotSame("false", $this->employer->getQualification());
        $this->assertNotSame("false", $this->employer->getLastName());
        $this->assertNotSame(0, $this->employer->getMatricule());
        $this->assertNotSame($date2, $this->employer->getDebutTravaille());
        $this->assertNotSame($date2, $this->employer->getDemission());
        $this->assertNotSame($time2, $this->employer->getSoldAutorisationSortie());
        $this->assertNotSame(1, $this->employer->getSoldConger());
        $this->assertNotSame(false, $this->employer->isVerified());
    }
    /**
     * TestIsEmpty function
     *
     * @return void
     */
    public function testIsEmpty(): void
    {
        $this->assertEmpty($this->employer->getEmail());
        $this->assertEmpty($this->employer->getId());
        $this->assertEmpty($this->employer->getBadgenumbe());
        $this->assertEmpty($this->employer->getFirstName());
        $this->assertEmpty($this->employer->getLastName());
        $this->assertEmpty($this->employer->getQualification());
        $this->assertEmpty($this->employer->getMatricule());
        $this->assertEmpty($this->employer->getDebutTravaille());
        $this->assertEmpty($this->employer->getDemission());
        $this->assertEmpty($this->employer->getSoldAutorisationSortie());
        $this->assertEmpty($this->employer->getSoldConger());
        $this->assertEmpty($this->employer->isVerified());
    }
}
