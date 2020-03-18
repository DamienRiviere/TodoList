<?php

namespace App\Tests\App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;

class UserControllerTest extends AbstractControllerTest
{

    /** @var UserRepository */
    protected $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = self::$container->get(UserRepository::class);
    }

    public function testList()
    {
        $this->client->request('GET', '/users');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $this->loginWithAdmin();

        $crawler = $this->client->request('GET', '/users');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('Liste des utilisateurs', $crawler->filter('h1')->text());
        $this->assertContains('Edit', $crawler->filter('a.btn.btn-success')->text());
    }

    public function testCreate()
    {
        $this->client->request('GET', '/users/create');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $this->loginWithAdmin();

        $crawler = $this->client->request('GET', '/users/create');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('Créer un utilisateur', $crawler->filter('h1')->text());
        $this->assertContains('Ajouter', $crawler->filter('button.btn.btn-success')->text());
        $this->assertCount(5, $crawler->filter('input'));

        $buttonCrawlerMode = $crawler->filter('form');
        $form = $buttonCrawlerMode->form([
            'user[username]' => 'admin2',
            'user[password][first]' => 'password',
            'user[password][second]' => 'password',
            'user[email]' => 'admin2@gmail.com',
            'user[roles]' => 'ROLE_ADMIN'
        ]);

        $this->client->submit($form);

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        $this->assertEquals('user_list', $this->client->getRequest()->get('_route'));
        $this->assertEquals(
            'L\'utilisateur a bien été ajouté.',
            $crawler->filter('div.alert.alert-success')->text(null, true)
        );

        $user = $this->userRepository->findOneBy(['username' => 'admin2']);
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('admin2', $user->getUsername());
        $this->assertEquals('admin2@gmail.com', $user->getEmail());
        $this->assertEquals('ROLE_ADMIN', $user->getRoles()[0]);
    }

    public function testEdit()
    {
        $this->client->request('GET', '/users/1/edit');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $this->loginWithAdmin();

        $crawler = $this->client->request('GET', '/users/1/edit');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('Modifier', $crawler->filter('button.btn.btn-success')->text());
        $this->assertCount(5, $crawler->filter('input'));

        $buttonCrawlerMode = $crawler->filter('form');
        $form = $buttonCrawlerMode->form([
            'user[username]' => 'admin3',
            'user[password][first]' => 'password',
            'user[password][second]' => 'password',
            'user[email]' => 'admin3@gmail.com',
            'user[roles]' => 'ROLE_ADMIN'
        ]);

        $this->client->submit($form);
        
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        $this->assertEquals('user_list', $this->client->getRequest()->get('_route'));
        $this->assertEquals(
            'L\'utilisateur a bien été modifié',
            $crawler->filter('div.alert.alert-success')->text(null, true)
        );

        $user = $this->userRepository->findOneBy(['username' => 'admin3']);
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('admin3', $user->getUsername());
        $this->assertEquals('admin3@gmail.com', $user->getEmail());
        $this->assertEquals('ROLE_ADMIN', $user->getRoles()[0]);
    }

    public function testAccessList()
    {
        $this->loginWithUser();

        $this->client->request('GET', '/users');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }
}
