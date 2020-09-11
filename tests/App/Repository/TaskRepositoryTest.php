<?php

namespace App\Tests\App\Repository;

use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskRepositoryTest extends KernelTestCase
{

    /** @var EntityManagerInterface */
    private $em;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->em = $kernel->getContainer()
            ->get('doctrine')
            ->getManager()
        ;
    }

    public function testSearchByTitle(): void
    {
        $task = $this->em
            ->getRepository(Task::class)
            ->findOneBy(['title' => 'Titre de la tâche'])
        ;

        self::assertSame('Titre de la tâche', $task->getTitle());
        self::assertSame('Description de la tâche', $task->getContent());
        self::assertSame('admin', $task->getUser()->getUsername());
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $this->em->close();
        $this->em = null;
    }
}
