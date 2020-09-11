<?php

namespace App\Tests\App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;

class TaskControllerTest extends AbstractControllerTest
{

    /** @var TaskRepository */
    protected $taskRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->taskRepository = self::$container->get(TaskRepository::class);
    }

    public function testList(): void
    {
        $this->client->request('GET', '/tasks');
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());

        $this->loginWithAdmin();

        $crawler = $this->client->request('GET', '/tasks');
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertContains('Créer une tâche', $crawler->filter('a.btn.btn-info')->text());
    }

    public function testCreate(): void
    {
        $this->client->request('GET', '/tasks/create');
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());

        $this->loginWithAdmin();

        $crawler = $this->client->request('GET', '/tasks/create');
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertCount(2, $crawler->filter('input'));
        self::assertEquals('Ajouter', $crawler->filter('button.btn.btn-success')->text());

        $buttonCrawlerMode = $crawler->filter('form');
        $form = $buttonCrawlerMode->form([
            'task[title]' => 'Titre de la tâche 2',
            'task[content]' => 'Description de la tâche 2'
        ]);

        $this->client->submit($form);
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        self::assertEquals('task_list', $this->client->getRequest()->get('_route'));
        self::assertEquals(
            'La tâche a été bien été ajoutée.',
            $crawler->filter('div.alert.alert-success')->text(null, true)
        );

        $task = $this->taskRepository->findOneBy(['title' => 'Titre de la tâche 2']);
        self::assertInstanceOf(Task::class, $task);
        self::assertEquals('Titre de la tâche 2', $task->getTitle());
        self::assertEquals('Description de la tâche 2', $task->getContent());
        self::assertEquals('admin', $task->getUser()->getUsername());
        self::assertEquals('admin@gmail.com', $task->getUser()->getEmail());
    }

    public function testEdit(): void
    {
        $this->client->request('GET', '/tasks/1/edit');
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());

        $this->loginWithAdmin();

        $crawler = $this->client->request('GET', '/tasks/1/edit');
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertCount(2, $crawler->filter('input'));
        self::assertEquals('Modifier', $crawler->filter('button.btn.btn-success')->text());

        $buttonCrawlerMode = $crawler->filter('form');
        $form = $buttonCrawlerMode->form([
            'task[title]' => 'Titre de la tâche',
            'task[content]' => 'Description de la tâche'
        ]);

        $this->client->submit($form);
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        self::assertEquals('task_list', $this->client->getRequest()->get('_route'));
        self::assertEquals(
            'La tâche a bien été modifiée.',
            $crawler->filter('div.alert.alert-success')->text(null, true)
        );

        $task = $this->taskRepository->findOneBy(['title' => 'Titre de la tâche']);
        self::assertInstanceOf(Task::class, $task);
        self::assertEquals('Titre de la tâche', $task->getTitle());
        self::assertEquals('Description de la tâche', $task->getContent());
        self::assertEquals('admin', $task->getUser()->getUsername());
        self::assertEquals('admin@gmail.com', $task->getUser()->getEmail());
    }

    public function testToggle(): void
    {
        $this->client->request('GET', '/tasks/1/toggle');
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());

        $this->loginWithAdmin();

        $this->client->request('GET', '/tasks/1/toggle');
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        self::assertEquals('task_list', $this->client->getRequest()->get('_route'));
        self::assertEquals(
            'La tâche Titre de la tâche a bien été marquée comme faite.',
            $crawler->filter('div.alert.alert-success')->text(null, true)
        );

        $task = $this->taskRepository->findOneBy(['title' => 'Titre de la tâche']);
        self::assertInstanceOf(Task::class, $task);
        self::assertEquals('1', $task->getIsDone());
    }

    public function testDelete(): void
    {
        $this->client->request('DELETE', '/tasks/1/delete');
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());

        $this->loginWithAdmin();

        $this->client->request('DELETE', '/tasks/1/delete');
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        self::assertEquals('task_list', $this->client->getRequest()->get('_route'));
        self::assertEquals(
            'La tâche a bien été supprimée.',
            $crawler->filter('div.alert.alert-danger')->text(null, true)
        );

        $task = $this->taskRepository->findOneBy(['title' => 'Titre de la tâche']);
        self::assertEmpty($task);
    }

    public function testAccessDelete(): void
    {
        $this->loginWithUser();

        $this->client->request('DELETE', '/tasks/1/delete');
        self::assertEquals(403, $this->client->getResponse()->getStatusCode());
    }
}
