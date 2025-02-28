<?php

namespace App\Tests;

use App\Entity\Produit;
use PHPUnit\Framework\TestCase;

class ProduitTest extends TestCase
{
    public function testProduitAttributes()
    {
        // Création d'un produit
        $produit = new Produit();
        $produit->setNom('T-Shirt Dev');
        $produit->setPrix(19.99);

        // Vérifications
        $this->assertEquals('T-Shirt Dev', $produit->getNom());
        $this->assertEquals(19.99, $produit->getPrix());
    }
}
