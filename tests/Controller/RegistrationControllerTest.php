<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
    public function testRegisterPageLoads(): void
    {
        $client = static::createClient();
        $client->request('GET', '/register');

        // Vérifie que la page se charge correctement
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('h1', 'Inscription');
    }

    public function testSuccessfulRegistration(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        // Remplir et soumettre le formulaire
        $form = $crawler->selectButton('s\'inscrire')->form([
            'registration_form[lastname]' => 'Doe',
            'registration_form[firstname]' => 'John',
            'registration_form[email]' => 'test@example.com',
            'registration_form[plainPassword][first]' => 'password123',
            'registration_form[plainPassword][second]' => 'password123',
        ]);
        $client->submit($form);

        // Vérifie que la redirection a eu lieu (vers home par ex.)
        $this->assertResponseRedirects('/');

        // Vérifie que l'utilisateur est bien en base
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'test@example.com']);
        $this->assertNotNull($user);
    }

    public function testRegistrationWithMismatchedPasswords(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        // Remplir et soumettre le formulaire avec des mots de passe différents
        $form = $crawler->selectButton('s\'inscrire')->form([
            'registration_form[lastname]' => 'Doe',
            'registration_form[firstname]' => 'John',
            'registration_form[email]' => 'wrong@example.com',
            'registration_form[plainPassword][first]' => 'password123',
            'registration_form[plainPassword][second]' => 'differentPassword',
        ]);

        $client->submit($form);

        // Pas de redirection, erreur affichée
        $this->assertResponseStatusCodeSame(422);
        $this->assertSelectorTextContains('.invalid-feedback', 'identiques');
    }
}
