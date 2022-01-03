<?php

namespace App\Tests\User\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
    public function testRegistrationSuccessful(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');
        $buttonCrawlerNode = $crawler->selectButton('Register');
        $tomorrow = time();

        $date = [
            //Converts the previous timestamp to an integer with the value of the
            //year of tomorrow (to this date 2018)
            'year' => (int)date('Y', $tomorrow),
            //Same with the month
            'month' => (int)date('m', $tomorrow),
            //And now with the day
            'day' => (int)date('d', $tomorrow),
        ];
        // retrieve the Form object for the form belonging to this button
        $form = $buttonCrawlerNode->form();
        $form->setValues([
            'registration_form[firstName]' => 'mourad',
            'registration_form[lastName]' => 'bf',
            'registration_form[id]' => 100,
            'registration_form[badgenumbe]' =>102,
            'registration_form[debutTravaille]' => $date,
            'registration_form[email]' => 'mourad.benfradj.atlas@gmail.com',
            'registration_form[plainPassword]' => 'mourad',
            'registration_form[agreeTerms]' => 'agree',
        ]);
        $client->submit($form);
        $this->assertResponseIsSuccessful();
        $response = $client->getResponse();
        //$this->assertResponseRedirects('/');
    }
}