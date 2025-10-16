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

    public function testAddProjectAsLoggedUser(): void
    {
        $client = static::createClient();

        // Récupère un utilisateur depuis la BDD (fixtures nécessaires)
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('splint@test.fr');
        $client->loginUser($testUser);

        // Accède à la page d’ajout de projet
        $crawler = $client->request('GET', '/add');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('h1', 'Créer un projet');
        
        // Soumission du formulaire
        $form = $crawler->selectButton('Créer mon projet')->form();
        $form['project[title]'] = 'Nouveau projet';
        $client->submit($form);
        $this->assertResponseRedirects('/');
        $client->followRedirect();

        // Vérifie que le nouveau projet est affiché
        $this->assertSelectorExists('h1', $testUser->getFirstname());
        $this->assertSelectorTextContains('body', 'Nouveau projet');
    }

    /**
     * @dataProvider provideInvalidProjectTitles
     */
    public function testAddProjectErrors(string $title, string $expectedError): void
    {
        $client = static::createClient();

        // Récupère un utilisateur depuis la BDD (fixtures nécessaires)
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('splint@test.fr');
        $client->loginUser($testUser);

        // Affiche le formulaire
        $crawler = $client->request('GET', '/add');
        $this->assertResponseIsSuccessful();

        // Soumet le formulaire avec un titre invalide
        $form = $crawler->selectButton('Créer mon projet')->form();
        $form['project[title]'] = $title;
        $client->submit($form);

        // Reste sur la même page (pas de redirection si erreur)
        $this->assertResponseStatusCodeSame(422);

        // Vérifie que le message d’erreur est bien affiché
        $this->assertSelectorTextContains('.invalid-feedback', $expectedError);
    }

    public static function provideInvalidProjectTitles(): array
    {
        return [
            'Titre vide' => [
                '',
                'Le titre ne peut pas être vide.'
            ],
            'Titre trop court' => [
                'AB',
                'Le titre doit contenir au moins 3 caractères.'
            ],
            'Titre trop long' => [
                str_repeat('A', 256),
                'Le titre ne peut pas dépasser 255 caractères.'
            ],
        ];
    }

    public function testAddDuplicateProjectTitle(): void
    {
        $client = static::createClient();
        // Récupère un utilisateur depuis la BDD (fixtures nécessaires)
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('splint@test.fr');
        $client->loginUser($testUser);

        // Crée un premier projet valide
        $crawler = $client->request('GET', '/add');
        $form = $crawler->selectButton('Créer mon projet')->form();
        $form['project[title]'] = 'Projet unique';
        $client->submit($form);
        $this->assertResponseRedirects('/');
        $client->followRedirect();

        // Essaie de créer un projet avec le même titre
        $crawler = $client->request('GET', '/add');
        $form = $crawler->selectButton('Créer mon projet')->form();
        $form['project[title]'] = 'Projet unique';
        $client->submit($form);

        // Vérifie la présence du message d’erreur d’unicité
        $this->assertResponseStatusCodeSame(422);
        $this->assertSelectorTextContains('.invalid-feedback', 'Ce titre est déjà utilisé pour un autre projet.');
    }
}
