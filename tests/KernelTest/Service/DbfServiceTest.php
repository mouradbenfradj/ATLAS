<?php

namespace App\Tests\KernelTest\Service;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DbfServiceTest extends KernelTestCase
{
    public function testSomething(): void
    {
        $kernel = self::bootKernel();

        $this->assertSame('test', $kernel->getEnvironment());
        
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
}
