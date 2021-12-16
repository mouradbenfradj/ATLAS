<?php

namespace App\Tests\WebTest\Service;

use App\Repository\AbsenceRepository;
use App\Repository\UserRepository;
use App\Service\AbsenceService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AbsenceServiceTest extends WebTestCase
{
    public function testConstructEntity(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneBy(['email'=>'mourad.benfradj.atlas@gmail.com']);
        $this->assertEquals(1, $testUser->getId());
        $client->loginUser($testUser);
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
}
