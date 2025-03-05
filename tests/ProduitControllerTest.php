<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProduitControllerTest extends WebTestCase
{
    public function testPageAccueilAfficheProduits(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Boutique Symfony');
    }

    public function testPanierPageIsSuccessful(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/panier');

        $this->assertResponseIsSuccessful();

        $this->assertTrue(
            $crawler->filter('ul')->count() > 0 || $crawler->filter('p:contains("Votre panier est vide")')->count() > 0, 'La page panier doit conenir une liste <ul> ou un message "Votre panier est vide"'
        );
    }
}
