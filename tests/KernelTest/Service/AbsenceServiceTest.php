<?php

namespace App\Tests\KernelTest\Service;

use App\Repository\AbsenceRepository;
use App\Repository\UserRepository;
use App\Service\AbsenceService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AbsenceServiceTest extends KernelTestCase
{
    public function testVerifyDataStat(): void
    {
        $container = static::getContainer();
        $absenceRepository = $container->get(AbsenceRepository::class);
        $absencesCount = count($absenceRepository->findAll());
        $this->assertEquals(0, $absencesCount);
    }
    public function testPartielConstruct(): void
    {
        //$kernel = self::bootKernel();
        $container = static::getContainer();
        $userRepository = $container->get(UserRepository::class);
        $absenceService = $container->get(AbsenceService::class);
        $absenceService->partielConstruct();

        $this->assertEquals(null, $absenceService->getEmployer());
        //$this->assertEquals("00:00:00", $generateTime->format('H:i:s'));

        //$routerService = static::getContainer()->get('router');
        //$myCustomService = static::getContainer()->get(CustomService::class);
    }
    public function testConstructEntity(): void
    {
        //$kernel = self::bootKernel();
        $container = static::getContainer();
        $userRepository = $container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('mourad.benfradj.atlas@gmail.com');
        $absenceService = $container->get(AbsenceService::class);

        $this->assertEquals(1, $testUser->getId());
        //$this->assertEquals("00:00:00", $generateTime->format('H:i:s'));
        //$routerService = static::getContainer()->get('router');
        //$myCustomService = static::getContainer()->get(CustomService::class);
    }
}
