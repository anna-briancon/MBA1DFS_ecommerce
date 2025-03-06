<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Produit;

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

    public function testAjoutProduitAuPanier(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        
        // Récupération du produit
        $produit = $entityManager->getRepository(Produit::class)->findOneBy(['nom' => 'Smartphone Samsung']);
        
        // Ajout du produit au panier
        $client->request('GET', '/ajouter-panier/' . $produit->getId());
        
        // Vérification de la redirection
        $this->assertResponseRedirects('/');
        $crawler = $client->followRedirect();
        
        // Vérification de la page d'accueil
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('a[href="/panier"]');

        // Vérification du contenu du panier
        $crawler = $client->request('GET', '/panier');
        $this->assertSelectorTextContains('li', 'Smartphone Samsung');
        $this->assertSelectorTextContains('li', '799.99€');
    }

    public function testSuppressionDuPanier(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        
        // Récupération du produit
        $produit = $entityManager->getRepository(Produit::class)->findOneBy(['nom' => 'Smartphone Samsung']);
        
        // Ajout du produit au panier
        $client->request('GET', '/ajouter-panier/' . $produit->getId());
        $client->followRedirect();
        
        // Vérification que le produit est bien dans le panier
        $crawler = $client->request('GET', '/panier');
        $this->assertSelectorTextContains('li', 'Smartphone Samsung');
        
        // Suppression du produit du panier
        $client->request('GET', '/supprimer-panier/0');
        
        // Vérification de la redirection vers la page du panier
        $this->assertResponseRedirects('/panier');
        $crawler = $client->followRedirect();
        $this->assertResponseIsSuccessful();
        
        // Vérification que le produit n'est plus dans le panier
        $this->assertSelectorNotExists('li:contains("Smartphone Samsung")');
        $this->assertSelectorTextContains('p', 'Votre panier est vide');
    }

    public function testCalculTotalPanier(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        
        // Récupération du produit
        $produit = $entityManager->getRepository(Produit::class)->findOneBy(['nom' => 'Smartphone Samsung']);
        
        // Ajout du produit au panier
        $client->request('GET', '/ajouter-panier/' . $produit->getId());
        $client->followRedirect();
        
        // Vérification du total dans le panier
        $crawler = $client->request('GET', '/panier');
        
        // Vérification de l'affichage de la page
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('p strong'); // Vérification que le total est affiché

        // Vérification du montant exact du total
        $total = $crawler->filter('p strong')->text();
        $total = preg_replace('/[^0-9.]/', '', $total);
        $this->assertEquals(799.99, floatval($total), 'Le total doit être égal au prix du produit');
    }
}