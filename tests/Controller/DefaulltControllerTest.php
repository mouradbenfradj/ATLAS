<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaulltControllerTest extends WebTestCase
{
    public function testPageDefaultIsSuccessful(): void
    {
        $client = static::createClient();
        $response = $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
    }
}