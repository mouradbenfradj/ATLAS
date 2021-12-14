<?php

namespace App\Tests\KernelTest\Service;

use App\Service\PhpSpreadsheetService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PhpSpreadsheetServiceTest extends KernelTestCase
{
    public function testSomething()
    {
        // (1) boot the Symfony kernel
        self::bootKernel();

        // (2) use static::getContainer() to access the service container
        $container = static::getContainer();

        // (3) run some service & test the result
        $phpSpreadsheetService = $container->get(PhpSpreadsheetService::class);
        $this->assertEquals(null, $phpSpreadsheetService->getConfig());
    }
}
