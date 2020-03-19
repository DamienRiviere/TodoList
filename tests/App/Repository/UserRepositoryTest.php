<?php

namespace App\Tests\App\Repository;

use App\DataFixtures\AppFixtures;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
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

    public function testSearchByUsername()
    {
        $user = $this->em
            ->getRepository(User::class)
            ->findOneBy(['username' => 'admin'])
        ;

        $this->assertSame('admin', $user->getUsername());
        $this->assertSame('admin@gmail.com', $user->getEmail());
        $this->assertSame('ROLE_ADMIN', $user->getRoles()[0]);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $this->em->close();
        $this->em = null;
    }
}
