<?php

namespace App\Tests\KernelTest\Service;

use App\Service\PointageService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PointageServiceTest extends KernelTestCase
{
    public function testInitAttribute(): void
    {
        $container = static::getContainer();
        $pointageService = $container->get(PointageService::class);
        $pointageService->initAttribute();
        $this->assertEquals(null, $pointageService->getDate());
        $this->assertEquals(null, $pointageService->getEntrer());
        $this->assertEquals(null, $pointageService->getSortie());
        $this->assertEquals(null, $pointageService->getNbrHeurTravailler());
        $this->assertEquals(null, $pointageService->getRetardEnMinute());
        $this->assertEquals(null, $pointageService->getDepartAnticiper());
        $this->assertEquals(null, $pointageService->getRetardMidi());
        $this->assertEquals(null, $pointageService->getTotaleRetard());
        $this->assertEquals(null, $pointageService->getHeurNormalementTravailler());
        $this->assertEquals(null, $pointageService->getDiff());
        $this->assertEquals(null, $pointageService->getEmployer());
        $this->assertEquals(null, $pointageService->getHoraire());
        $this->assertEquals(null, $pointageService->getCongerPayer());
        $this->assertEquals(null, $pointageService->getAutorisationSortie());
        $this->assertEquals(null, $pointageService->getWorkTime());
        $this->assertEquals(null, $pointageService->getAbsence());
    }
    public function testConstructFromDbf(): void
    {
        $container = static::getContainer();
        $pointageService = $container->get(PointageService::class);
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
        $pointageService->constructFromDbf();
        $this->assertEquals(null, $pointageService->getDate());
    }
    public function testConstructFromPointage(): void
    {
        $container = static::getContainer();
        $pointageService = $container->get(PointageService::class);
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
        $pointageService->constructFromPointage();
        $this->assertEquals(null, $pointageService->getDate());
    }
    public function testCreateEntity(): void
    {
        $container = static::getContainer();
        $pointageService = $container->get(PointageService::class);
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
        $pointageService->createEntity();
        $this->assertEquals(null, $pointageService->getDate());
    }
    public function testDbfUpdated(): void
    {
        $container = static::getContainer();
        $pointageService = $container->get(PointageService::class);
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
        $pointageService->dbfUpdated($pointage, $dbf);
        $this->assertEquals(null, $pointageService->getDate());
    }
    public function testDateInDB(): void
    {
        $container = static::getContainer();
        $pointageService = $container->get(PointageService::class);
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
        $pointageService->dateInDB($employer);
        $this->assertEquals(null, $pointageService->getDate());
    }
}
