<?php

namespace App\Tests\KernelTest\Service;

use App\Service\HoraireService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class HoraireServiceTest extends KernelTestCase
{
    public function testGetHoraireForDate(): void
    {
        $container = static::getContainer();
        $horaireService = $container->get(HoraireService::class);
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
        $horaireService->getHoraireForDate($dateTime, $employer);
        $this->assertEquals(null, $horaireService->getDate());
    }
    public function testgetHoraireByHoraireName(): void
    {
        $container = static::getContainer();
        $horaireService = $container->get(HoraireService::class);
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
        $horaireService->getHoraireByHoraireName($horaireName);
        $this->assertEquals(null, $horaireService->getDate());
    }
}
