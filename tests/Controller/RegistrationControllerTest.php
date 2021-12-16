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
        $form = $this->factory->create(RegistrationFormType::class, $employer);
        $expected = new User();
        $expected->setFirstName('mourad1');
        $expected->setLastName('bf');
        $expected->setId(102);
        $expected->setBadgenumbe(102);
        $expected->setDebutTravaille(new DateTime('2021-01-01'));
        $expected->setEmail('mourad.benfradj.atlas@gmail.com');
        $expected->setPassword('mourad');
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('b', 'ATLAS');

        /* $status_code = $client->getResponse()->getStatus();
        echo $status_code;
        if ($status_code==200) {

        /*  $form = $crawler->selectButton('Log in')->form();
         $form['username'] = 'user123';
         $form['password'] = 'pass123';
         $crawler = $client->submit($form);

         $crawler->filter('a.gmFkV')->each(function ($node, $i) {
             print $node->text();
             echo "<br />";
         });
        } else {
            echo "Error";
        } */

        /*    $buttonCrawlerNode = $crawler->selectButton('Register');

           // retrieve the Form object for the form belonging to this button
           $form = $buttonCrawlerNode->form();
           $form['registration_form[firstName]'] = 'mourad1';

           $crawler = $client->submitForm('Register', [
               'registration_form[firstName]' => 'mourad1',
               'registration_form[lastName]' => 'bf',
               'registration_form[id]' => 102,
               'registration_form[badgenumbe]' => 102,
               'registration_form[debutTravaille][day]' => '01',
               'registration_form[debutTravaille][month]' => '01',
               'registration_form[debutTravaille][year]' => '2020',
               'registration_form[email]' => 'mourad.benfradj.atlas@gmail.com',
               'registration_form[plainPassword]' => 'mourad',
               'registration_form[agreeTerms]' => true,
           ]);

           $crawler = $this->client->followRedirect(); */
    }
}
