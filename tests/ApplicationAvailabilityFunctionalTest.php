<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApplicationAvailabilityFunctionalTest extends WebTestCase
{
   
    public function testNotLogiedIsSuccessful($url)
    {
        $client = self::createClient();
        $client->request('GET', '/login');

        $this->assertResponseIsSuccessful('/login');
    }

    public function urlProviderWithRedirectionToLogin()
    {
        yield ['/'];
        yield ['/admin'];
        yield ['/dbf/file/uploader/1'];
        yield ['/verify/email'];
        // ...
    }


    
    public function urlProvider()
    {
        yield ['/'];
        yield ['/admin'];
        yield ['/dbf/file/uploader/1'];
        yield ['/login'];
        yield ['/register'];
        yield ['/verify/email'];
        // ...
    }
}