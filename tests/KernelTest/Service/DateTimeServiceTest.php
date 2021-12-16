<?php

namespace App\Tests\KernelTest\Service;

use App\Service\DateTimeService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DateTimeServiceTest extends KernelTestCase
{
    const FORMATTIMEHI = 'H:i';
    const FORMATTIMEHIS = self::FORMATTIMEHI . ':s';
    public function testGenerateTime(): void
    {
        $container = static::getContainer();
        $dateTimeService = $container->get(DateTimeService::class);
        $generateTime = $dateTimeService->generateTime("");
        $this->assertEquals("00:00:00", $generateTime->format(self::FORMATTIMEHIS));
        for ($i = 0; $i < 100; $i++) {
            $randDate = new DateTime();
            $randDate->setTime(mt_rand(0, 23), mt_rand(0, 59));
            $generateTime = $dateTimeService->generateTime($randDate->format(self::FORMATTIMEHIS));
            $this->assertEquals($randDate->format(self::FORMATTIMEHIS), $generateTime->format(self::FORMATTIMEHIS));
        }
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
}
