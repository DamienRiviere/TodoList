<?php

namespace App\Tests\App\Controller;

use App\Tests\App\Controller\AbstractControllerTest;

class DefaultControllerTest extends AbstractControllerTest
{

    public function testIndex(): void
    {
        $this->client->request('GET', '/');
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());

        $this->loginWithAdmin();

        $crawler = $this->client->request('GET', '/');

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
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
}
