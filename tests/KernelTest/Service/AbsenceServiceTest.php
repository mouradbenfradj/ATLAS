<?php

namespace App\Tests\KernelTest\Service;

use App\Service\CongerService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AbsenceServiceTest extends KernelTestCase
{
    public function testConstructEntity(): void
    {
        //$kernel = self::bootKernel();
        $container = static::getContainer();
        $timeService = $container->get(CongerService::class);
        $generateTime = $timeService->generateTime("");
        //$this->assertEquals("00:00:00", $generateTime->format('H:i:s'));
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
        //$routerService = static::getContainer()->get('router');
        //$myCustomService = static::getContainer()->get(CustomService::class);
    }
}
