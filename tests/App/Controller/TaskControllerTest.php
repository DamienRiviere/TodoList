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

    public function testList()
    {
        $this->client->request('GET', '/tasks');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $this->loginWithAdmin();

        $crawler = $this->client->request('GET', '/tasks');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('Créer une tâche', $crawler->filter('a.btn.btn-info')->text());
    }

    public function testCreate()
    {
        $this->client->request('GET', '/tasks/create');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $this->loginWithAdmin();

        $crawler = $this->client->request('GET', '/tasks/create');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertCount(2, $crawler->filter('input'));
        $this->assertEquals('Ajouter', $crawler->filter('button.btn.btn-success')->text());

        $buttonCrawlerMode = $crawler->filter('form');
        $form = $buttonCrawlerMode->form([
            'task[title]' => 'Titre de la tâche 2',
            'task[content]' => 'Description de la tâche 2'
        ]);

        $this->client->submit($form);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        $this->assertEquals('task_list', $this->client->getRequest()->get('_route'));
        $this->assertEquals(
            'La tâche a été bien été ajoutée.',
            $crawler->filter('div.alert.alert-success')->text(null, true)
        );

        $task = $this->taskRepository->findOneBy(['title' => 'Titre de la tâche 2']);
        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals('Titre de la tâche 2', $task->getTitle());
        $this->assertEquals('Description de la tâche 2', $task->getContent());
        $this->assertEquals('admin', $task->getUser()->getUsername());
        $this->assertEquals('admin@gmail.com', $task->getUser()->getEmail());
    }

    public function testEdit()
    {
        $this->client->request('GET', '/tasks/1/edit');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $this->loginWithAdmin();

        $crawler = $this->client->request('GET', '/tasks/1/edit');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertCount(2, $crawler->filter('input'));
        $this->assertEquals('Modifier', $crawler->filter('button.btn.btn-success')->text());

        $buttonCrawlerMode = $crawler->filter('form');
        $form = $buttonCrawlerMode->form([
            'task[title]' => 'Titre de la tâche',
            'task[content]' => 'Description de la tâche'
        ]);

        $this->client->submit($form);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        $this->assertEquals('task_list', $this->client->getRequest()->get('_route'));
        $this->assertEquals(
            'La tâche a bien été modifiée.',
            $crawler->filter('div.alert.alert-success')->text(null, true)
        );

        $task = $this->taskRepository->findOneBy(['title' => 'Titre de la tâche']);
        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals('Titre de la tâche', $task->getTitle());
        $this->assertEquals('Description de la tâche', $task->getContent());
        $this->assertEquals('admin', $task->getUser()->getUsername());
        $this->assertEquals('admin@gmail.com', $task->getUser()->getEmail());
    }

    public function testToggleTask()
    {
        $this->client->request('GET', '/tasks/1/toggle');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $this->loginWithAdmin();

        $this->client->request('GET', '/tasks/1/toggle');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        $this->assertEquals('task_list', $this->client->getRequest()->get('_route'));
        $this->assertEquals(
            'La tâche Titre de la tâche a bien été marquée comme faite.',
            $crawler->filter('div.alert.alert-success')->text(null, true)
        );

        $task = $this->taskRepository->findOneBy(['title' => 'Titre de la tâche']);
        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals('1', $task->getIsDone());
    }

    public function testDeleteTask()
    {
        $this->client->request('DELETE', '/tasks/1/delete');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $this->loginWithAdmin();

        $this->client->request('DELETE', '/tasks/1/delete');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        $this->assertEquals('task_list', $this->client->getRequest()->get('_route'));
        $this->assertEquals(
            'La tâche a bien été supprimée.',
            $crawler->filter('div.alert.alert-danger')->text(null, true)
        );

        $task = $this->taskRepository->findOneBy(['title' => 'Titre de la tâche']);
        $this->assertEmpty($task);
    }

    public function testAccessDeleteTask()
    {
        $this->loginWithUser();

        $this->client->request('DELETE', '/tasks/1/delete');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }
}
