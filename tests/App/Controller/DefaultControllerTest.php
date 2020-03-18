<?php

namespace App\Tests\Controller;

use App\Tests\App\Controller\AbstractControllerTest;

class DefaultControllerTest extends AbstractControllerTest
{

    public function testIndex()
    {
        $this->client->request('GET', '/');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $this->loginWithAdmin();

        $crawler = $this->client->request('GET', '/');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('Créer une nouvelle tâche', $crawler->filter('a.btn.btn-success.btn-sm.mb-2')->text());
        $this->assertContains(
            'Consulter la liste des tâches à faire',
            $crawler->filter('a.btn.btn-info.btn-sm.mb-2')->text()
        );
        $this->assertContains(
            "Bienvenue sur Todo List, l'application vous permettant de gérer l'ensemble de vos tâches sans effort !",
            $crawler->filter('h1')->text()
        );
    }
}
