<?php

namespace App\Tests\App\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class SecurityControllerTest extends AbstractControllerTest
{

    public function testLoginWithValidData()
    {
        $crawler = $this->client->request('GET', '/login');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertCount(3, $crawler->filter('input'));
        $this->assertContains('Connexion', $crawler->filter('button.btn.btn-primary')->text());

        $this->loginWithAdmin();

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();

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

    public function testLoginWithInvalidData()
    {
        $crawler = $this->client->request('GET', '/login');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $buttonCrawlerMode = $crawler->filter('form');
        $form = $buttonCrawlerMode->form([
            'login[username]' => 'test',
            'login[password]' => 'test'
        ]);

        $this->client->submit($form);

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        $this->assertEquals(
            'Nom d\'utilisateur ou mot de passe invalide !',
            $crawler->filter('div.alert.alert-danger')->text(null, true)
        );
    }

    public function testLoginWithInvalidPassword()
    {
        $crawler = $this->client->request('GET', '/login');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $buttonCrawlerMode = $crawler->filter('form');
        $form = $buttonCrawlerMode->form([
            'login[username]' => 'admin',
            'login[password]' => 'test'
        ]);

        $this->client->submit($form);

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        $this->assertEquals(
            'Nom d\'utilisateur ou mot de passe invalide !',
            $crawler->filter('div.alert.alert-danger')->text(null, true)
        );
    }
}
