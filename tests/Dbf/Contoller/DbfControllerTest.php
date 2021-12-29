<?php

namespace App\Tests\Dbf\Contoller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DbfControllerTest extends WebTestCase
{
    public function testUrl(): void
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
        $client = static::createClient();
        //$crawler = $client->request('GET', '/');
    
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Hello World');
    }
}
