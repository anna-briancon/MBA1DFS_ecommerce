<?php

namespace App\Tests\Functional;

use Symfony\Component\Panther\PantherTestCase;

class LoginTest extends PantherTestCase
{
    public function testSuccessfulLogin()
    {
        $client = static::createPantherClient();

        $client->getCookieJar()->clear();
        
        // Accéder à la page de connexion
        $client->request('GET', '/login');
        
        $client->waitFor('#inputEmail');

        $this->assertSelectorTextContains('h1', 'Please sign in');

        $form = $client->getCrawler()->filter('form')->form([
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]);

        $client->submit($form);
        
        $client->waitFor('.card');
        $this->assertSelectorTextContains('h1', 'Boutique Symfony');
    }
} 