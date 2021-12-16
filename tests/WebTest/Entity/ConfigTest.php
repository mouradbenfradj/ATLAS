<?php

namespace App\Tests\WebTest\Entity;

use App\Entity\Config;
use App\Repository\ConfigRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

//https://github.com/dmaicher/doctrine-test-bundle/blob/master/tests/Functional/FunctionalTestTrait.php
//https://github.com/dmaicher/doctrine-test-bundle/blob/master/tests/Functional/PhpunitTest.php
class ConfigTest extends WebTestCase
{
    private $entityManager;

    
    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testChangeDbState(): void
    {
        $this->assertRowCount(0);
        $this->insertRow();
        $this->assertRowCount(1);
    }
    public function assertRowCount($count): void
    {
        $configs = $this->entityManager
            ->getRepository(Config::class)
            ->findAll()
        ;
        $this->assertEquals($count, count($configs));
    }
    public function insertRow(): void
    {
        $config = new Config();
        $config->setActive(true);
        $config->setDebutSoldAS(new DateTime('00:00:00'));
        $config->setDebutSoldConger(0);
        $config->setIncAutorisationSortie(new DateTime('00:00:00'));
        $config->setIncSoldConger(0);
        $config->setReinitialisationAS(true);
        $config->setReinitialisationC(false);
        $this->entityManager->persist($config);
        $this->entityManager->flush();
    }
    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
