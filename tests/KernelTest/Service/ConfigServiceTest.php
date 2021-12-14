<?php

namespace App\Tests\KernelTest\Service;

use App\Entity\Config;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ConfigServiceTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }
    
    public function notExistEntity(): void
    {
        $config = $this->entityManager
        ->getRepository(Config::class)
        ->findOneBy(['id' => 1])
    ;

        $this->assertSame(null, $config);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
