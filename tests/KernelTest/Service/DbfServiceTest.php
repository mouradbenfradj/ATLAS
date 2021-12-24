<?php

namespace App\Tests\KernelTest\Service;

use App\Repository\UserRepository;
use App\Service\DbfService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DbfServiceTest extends KernelTestCase
{
    public function testGetDbfDateInDB(): void
    {
        $container = static::getContainer();
        $userRepository = $container->get(UserRepository::class);
        $testUser = $userRepository->findOneBy(['email'=>'mourad.ben.fradj@gmail.com']);
        /**
         * @var DbfService
         */
        $dbfService = $container->get(DbfService::class);
        $dbfService->setEmployer($testUser);
        $this->assertIsArray($dbfService->getDbfDateInDB());
        $this->assertEmpty($dbfService->getDbfDateInDB());
    }
}
