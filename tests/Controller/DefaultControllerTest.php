<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testFirstConnextionTime(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $crawler = $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('b', 'ATLAS');
        $this->assertSelectorTextContains('p', 'Sign in to start your session');
        $this->assertSelectorTextContains('label', 'Remember Me');
        $this->assertSelectorTextContains('button', 'Sign In');
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
        $this->assertCount(3, $crawler->filter('a'));
    }
    public function testVisitingWhileLoggedIn()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        // retrieve the test user
        $testUser = $userRepository->findOneByEmail('mourad.ben.fradj@gmail.com');

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $client->request('GET', '/');
        $crawler = $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('b', 'ATLAS');
        $this->assertSelectorTextContains('p', 'Sign in to start your session');
        $this->assertSelectorTextContains('label', 'Remember Me');
        $this->assertSelectorTextContains('button', 'Sign In');
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
        $this->assertCount(3, $crawler->filter('a'));
    }
}
