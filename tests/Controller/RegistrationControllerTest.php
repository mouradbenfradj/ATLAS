<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
    public function testRegister(): void
    {
        $formData = [
            'firstName' => 'mourad1',
            'lastName' => 'bf',
            'id' => 102,
            'badgenumbe' => 102,
            'debutTravaille][day' => '01',
            'debutTravaille][month' => '01',
            'debutTravaille][year' => '2020',
            'email' => 'mourad.benfradj.atlas@gmail.com',
            'plainPassword' => 'mourad',
            'agreeTerms' => true,
        ];
        $employer = new User();
        //$form = $this->factory->create(RegistrationFormType::class, $employer);
        $expected = new User();
        $expected->setFirstName('mourad1');
        $expected->setLastName('bf');
        $expected->setId(102);
        $expected->setBadgenumbe(102);
        $expected->setDebutTravaille(new DateTime('2021-01-01'));
        $expected->setEmail('mourad.benfradj.atlas@gmail.com');
        $expected->setPassword('mourad');
        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($expected, $employer);

        $client = static::createClient();
        $crawler = $client->request('GET', '/register');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('b', 'ATLAS');
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
}
