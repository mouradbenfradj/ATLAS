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

    public function testConstructEntity(): void
    {
        $container = static::getContainer();
        $userRepository = $container->get(UserRepository::class);
        $testUser = $userRepository->findOneBy(['email'=>'mourad.benfradj.atlas@gmail.com']);
        $absenceService = $container->get(AbsenceService::class);

        $this->assertEquals(1, $testUser->getId());
    }
}
