<?php

namespace App\Tests\KernelTest\Service;

use App\Service\TimeService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TimeServiceTest extends KernelTestCase
{
    public function testGenerateTime(): void
    {
        //$kernel = self::bootKernel();
        $container = static::getContainer();
        $timeService = $container->get(TimeService::class);
        $generateTime = $timeService->generateTime("");
        $this->assertEquals("00:00:00", $generateTime->format('H:i:s'));
        for ($i = 0; $i <100; $i++) {
            $randDate = new DateTime();
            $randDate->setTime(mt_rand(0, 23), mt_rand(0, 59));
            //echo  $randDate->format('H:i:s');
            $generateTime = $timeService->generateTime($randDate->format('H:i:s'));
            $this->assertEquals($randDate->format('H:i:s'), $generateTime->format('H:i:s'));
        }
        //$this->assertSame('test', $kernel->getEnvironment());
        //$routerService = static::getContainer()->get('router');
        //$myCustomService = static::getContainer()->get(CustomService::class);
    }
}
