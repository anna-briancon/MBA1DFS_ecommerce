<?php

namespace App\Tests;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testGetUsers(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/users');
        
        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testGetUsersReturnsArray(): void
    {
        $client = static::createClient();
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        
        // Créer un utilisateur de test
        $testUser = new User();
        $testUser->setEmail('admin@example.com');
        $testUser->setPassword('password123');
        $testUser->setRoles(['ROLE_ADMIN']);
        
        $entityManager->persist($testUser);
        $entityManager->flush();
        
        // Faire la requête
        $client->request('GET', '/api/users');
        
        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());
        
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($responseData); // Vérifie que la réponse est un tableau
        $this->assertCount(1, $responseData);

        // Supprimer l'utilisateur de test
        $entityManager->remove($testUser);
        $entityManager->flush();
    }

    public function testGetUsersReturnsEmptyArrayWhenNoUsers(): void
    {
        $client = static::createClient();
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        
        // Supprimer tous les utilisateurs existants
        $users = $entityManager->getRepository(User::class)->findAll();
        foreach ($users as $user) {
            $entityManager->remove($user);
        }
        $entityManager->flush();
        
        // Faire la requête
        $client->request('GET', '/api/users');
        
        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());
        
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($responseData); // Vérifie que la réponse est un tableau
        $this->assertEmpty($responseData);
    }
}
