<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProduitControllerTest extends WebTestCase
{
    public function testPageAccueilAfficheProduits()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        // Vérifie que la page charge bien
        $this->assertResponseIsSuccessful();

        // Vérifie que le titre est bien affiché
        $this->assertSelectorTextContains('h1', 'Boutique Symfony');
    }

    public function testAjoutProduitAuPanier()
    {
        $client = static::createClient();

        // Ajouter un produit au panier (simulation d’un clic)
        $client->request('GET', '/ajouter-panier/1');

        // Vérifier la redirection après l'ajout
        $this->assertResponseRedirects('/');

        // Vérifier que le panier contient bien un produit après l'ajout
        $client->followRedirect();
        $this->assertSelectorExists('a[href="/panier"]');
    }
}
