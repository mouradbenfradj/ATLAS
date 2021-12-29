<?php

namespace App\Tests\User\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
    public function testPageIsSuccessful(): void
    {
        $client = static::createClient();
        $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();
    }
}
