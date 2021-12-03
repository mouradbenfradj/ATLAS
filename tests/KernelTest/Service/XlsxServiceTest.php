<?php

namespace App\Tests\KernelTest\Service;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class XlsxServiceTest extends KernelTestCase
{
    public function testSomething(): void
    {
        $kernel = self::bootKernel();

        $this->assertSame('test', $kernel->getEnvironment());
        //$routerService = static::getContainer()->get('router');
        //$myCustomService = static::getContainer()->get(CustomService::class);
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
}
