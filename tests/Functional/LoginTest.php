<?php

namespace App\Tests\Functional;

use Symfony\Component\Panther\PantherTestCase;

class LoginTest extends PantherTestCase {
    public function testLogin() {
        $client = static::createPantherClient();

        // Aller sur la page de connexion
        $crawler = $client->request('GET', '/login');

        // Remplir le formulaire
        $form = $crawler->selectButton('Se connecter')->form([
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);
        $client->submit($form);

        // Vérifier la redirection après connexion
        $this->assertPageTitleContains('Tableau de bord');
    }
}