<?php

namespace App\Tests\KernelTest\Service;

use App\Entity\Config;
use App\Service\ConfigService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CongerServiceTest extends KernelTestCase
{
    public function testGetConfig()
    {
        // (1) boot the Symfony kernel
        self::bootKernel();

        // (2) use static::getContainer() to access the service container
        $container = static::getContainer();

        // (3) run some service & test the result
        $configService = $container->get(ConfigService::class);
        $this->assertEquals(null, $configService->getConfig());
    }
    public function testSetConfigConfig()
    {
        // (1) boot the Symfony kernel
        self::bootKernel();

        $config = new Config();
        $config2 = new Config();
        // (2) use static::getContainer() to access the service container
        $container = static::getContainer();

        // (3) run some service & test the result
        $configService = $container->get(ConfigService::class);
        $configService->setConfig($config);
        $this->assertEquals($config2, $configService->getConfig());
    }
    public function testSetConfigWithWrongInputConfig()
    {
        // (1) boot the Symfony kernel
        self::bootKernel();

        $config = new Config();
        $config2 = '';
        // (2) use static::getContainer() to access the service container
        $container = static::getContainer();

        // (3) run some service & test the result
        $configService = $container->get(ConfigService::class);
        $configService->setConfig($config);
        $this->assertNotEquals($config2, $configService->getConfig());
    }
}
