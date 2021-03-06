<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DbfControllerTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('POST', '/upload/100');

        $this->assertResponseIsSuccessful();
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
        $this->assertSelectorTextContains('h1', 'Hello World');
    }
}
