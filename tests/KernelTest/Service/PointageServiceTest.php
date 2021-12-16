<?php

namespace App\Tests\KernelTest\Service;

use App\Entity\User;
use App\Service\PointageService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PointageServiceTest extends KernelTestCase
{
    public function testDateInDB(): void
    {
        $container = static::getContainer();
        $pointageService = $container->get(PointageService::class);
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
        $employer = new User();
        $pointageService->getPointageDateInDB($employer);
        $this->assertEquals(null, $pointageService->getDate());
    }
}
