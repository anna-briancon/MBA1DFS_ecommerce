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
        
        $produit = new Produit();
        $produit->setNom('Test Produit');
        $produit->setPrix(99.99);
        $entityManager->persist($produit);
        $entityManager->flush();
        
        $client->request('GET', '/ajouter-panier/' . $produit->getId());
        
        $this->assertResponseRedirects('/');
        $crawler = $client->followRedirect();
        
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('a[href="/panier"]');
    }

    public function testSuppressionDuPanier(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        
        $produit = new Produit();
        $produit->setNom('Test Produit');
        $produit->setPrix(99.99);
        $entityManager->persist($produit);
        $entityManager->flush();
        
        $client->request('GET', '/ajouter-panier/' . $produit->getId());
        $client->followRedirect();
        
        $client->request('GET', '/supprimer-panier/0');
        
        $this->assertResponseRedirects('/panier');
        $crawler = $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testCalculTotalPanier(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        
        $produit = new Produit();
        $produit->setNom('Test Produit');
        $produit->setPrix(99.99);
        $entityManager->persist($produit);
        $entityManager->flush();
        
        $client->request('GET', '/ajouter-panier/' . $produit->getId());
        $client->followRedirect();
        
        $crawler = $client->request('GET', '/panier');
        
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('p strong'); // Vérifier que le total est affiché

        // Vérifier que le total est un nombre positif
        $total = $crawler->filter('p strong')->text();
        $total = preg_replace('/[^0-9.]/', '', $total);
        $this->assertGreaterThan(0, floatval($total));
    }
}