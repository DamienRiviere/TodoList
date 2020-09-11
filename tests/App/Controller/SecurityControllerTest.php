<?php

namespace App\Tests\App\Controller;

class SecurityControllerTest extends AbstractControllerTest
{

    public function testLoginWithValidData(): void
    {
        $crawler = $this->client->request('GET', '/login');
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertCount(3, $crawler->filter('input'));
        self::assertContains('Connexion', $crawler->filter('button.btn.btn-primary')->text());

        $this->loginWithAdmin();

        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();

        self::assertContains('Créer une nouvelle tâche', $crawler->filter('a.btn.btn-success.btn-sm.mb-2')->text());
        self::assertContains(
            'Consulter la liste des tâches à faire',
            $crawler->filter('a.btn.btn-info.btn-sm.mb-2')->text()
        );
        self::assertContains(
            "Bienvenue sur Todo List, l'application vous permettant de gérer l'ensemble de vos tâches sans effort !",
            $crawler->filter('h1')->text()
        );
    }

    public function testLoginWithInvalidData(): void
    {
        $crawler = $this->client->request('GET', '/login');
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());

        $buttonCrawlerMode = $crawler->filter('form');
        $form = $buttonCrawlerMode->form([
            'login[username]' => 'test',
            'login[password]' => 'test'
        ]);

        $this->client->submit($form);

        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        self::assertEquals(
            'Nom d\'utilisateur ou mot de passe invalide !',
            $crawler->filter('div.alert.alert-danger')->text(null, true)
        );
    }

    public function testLoginWithInvalidPassword(): void
    {
        $crawler = $this->client->request('GET', '/login');
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());

        $buttonCrawlerMode = $crawler->filter('form');
        $form = $buttonCrawlerMode->form([
            'login[username]' => 'admin',
            'login[password]' => 'test'
        ]);

        $this->client->submit($form);

        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        self::assertEquals(
            'Nom d\'utilisateur ou mot de passe invalide !',
            $crawler->filter('div.alert.alert-danger')->text(null, true)
        );
    }
}
