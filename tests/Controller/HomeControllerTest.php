<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    public function testHomePageAccessDenied(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        // Vérifie que la page répond bien en 302
        $this->assertResponseStatusCodeSame(302);

        // Suivre la redirection
        $client->followRedirect();

        // Vérifie qu’il y a un titre (par ex. H1) attendu
        $this->assertSelectorExists('h1', 'Se connecter');
    }

    public function testHomePageAccessibleForLoggedUser(): void
    {
        $client = static::createClient();

        // Récupère un utilisateur depuis la BDD (fixtures nécessaires)
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('splint@test.fr');

        // Connecte l’utilisateur
        $client->loginUser($testUser);

        // Accède à la page d'accueil
        $crawler = $client->request('GET', '/');

        // Vérifie que l’accès est OK
        $this->assertResponseIsSuccessful();

        // Vérifie que la page contient bien les projets
        $this->assertSelectorExists('h1', $testUser->getFirstname());
    }
}
