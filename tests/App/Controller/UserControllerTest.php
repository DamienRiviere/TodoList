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

    public function testList(): void
    {
        $this->client->request('GET', '/users');
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());

        $this->loginWithAdmin();

        $crawler = $this->client->request('GET', '/users');
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertContains('Liste des utilisateurs', $crawler->filter('h1')->text());
        self::assertContains('Edit', $crawler->filter('a.btn.btn-success')->text());
    }

    public function testCreate(): void
    {
        $this->client->request('GET', '/users/create');
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());

        $this->loginWithAdmin();

        $crawler = $this->client->request('GET', '/users/create');
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertContains('Créer un utilisateur', $crawler->filter('h1')->text());
        self::assertContains('Ajouter', $crawler->filter('button.btn.btn-success')->text());
        self::assertCount(5, $crawler->filter('input'));

        $buttonCrawlerMode = $crawler->filter('form');
        $form = $buttonCrawlerMode->form([
            'user[username]' => 'admin2',
            'user[password][first]' => 'password',
            'user[password][second]' => 'password',
            'user[email]' => 'admin2@gmail.com',
            'user[roles]' => 'ROLE_ADMIN'
        ]);

        $this->client->submit($form);

        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        self::assertEquals('user_list', $this->client->getRequest()->get('_route'));
        self::assertEquals(
            'L\'utilisateur a bien été ajouté.',
            $crawler->filter('div.alert.alert-success')->text(null, true)
        );

        $user = $this->userRepository->findOneBy(['username' => 'admin2']);
        self::assertInstanceOf(User::class, $user);
        self::assertEquals('admin2', $user->getUsername());
        self::assertEquals('admin2@gmail.com', $user->getEmail());
        self::assertEquals('ROLE_ADMIN', $user->getRoles()[0]);
    }

    public function testEdit(): void
    {
        $this->client->request('GET', '/users/1/edit');
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());

        $this->loginWithAdmin();

        $crawler = $this->client->request('GET', '/users/1/edit');
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertContains('Modifier', $crawler->filter('button.btn.btn-success')->text());
        self::assertCount(5, $crawler->filter('input'));

        $buttonCrawlerMode = $crawler->filter('form');
        $form = $buttonCrawlerMode->form([
            'user[username]' => 'admin3',
            'user[password][first]' => 'password',
            'user[password][second]' => 'password',
            'user[email]' => 'admin3@gmail.com',
            'user[roles]' => 'ROLE_ADMIN'
        ]);

        $this->client->submit($form);
        
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        self::assertEquals('user_list', $this->client->getRequest()->get('_route'));
        self::assertEquals(
            'L\'utilisateur a bien été modifié',
            $crawler->filter('div.alert.alert-success')->text(null, true)
        );

        $user = $this->userRepository->findOneBy(['username' => 'admin3']);
        self::assertInstanceOf(User::class, $user);
        self::assertEquals('admin3', $user->getUsername());
        self::assertEquals('admin3@gmail.com', $user->getEmail());
        self::assertEquals('ROLE_ADMIN', $user->getRoles()[0]);
    }

    public function testAccessList(): void
    {
        $this->loginWithUser();

        $this->client->request('GET', '/users');
        self::assertEquals(403, $this->client->getResponse()->getStatusCode());
    }
}
