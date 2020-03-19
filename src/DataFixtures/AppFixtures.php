<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    /** @var UserPasswordEncoderInterface */
    protected $encoder;

    /** @var EntityManagerInterface */
    protected $em;

    public function __construct(UserPasswordEncoderInterface $encoder, EntityManagerInterface $em)
    {
        $this->encoder = $encoder;
        $this->em = $em;
    }

    /**
     * @param ObjectManager $manager
     * @throws ConnectionException
     * @throws DBALException
     */
    public function load(ObjectManager $manager)
    {
        $connection = $this->em->getConnection();
        $plaform = $connection->getDatabasePlatform();
        $connection->beginTransaction();

        $connection->query('SET FOREIGN_KEY_CHECKS=0');
        $connection->executeUpdate($plaform->getTruncateTableSQL('user', true));
        $connection->executeUpdate($plaform->getTruncateTableSQL('task', true));
        $connection->query('SET FOREIGN_KEY_CHECKS=1');
        $connection->commit();

        $admin = new User();
        $admin
            ->setUsername("admin")
            ->setPassword($this->encoder->encodePassword($admin, 'password'))
            ->setEmail('admin@gmail.com')
            ->setRoles(['ROLE_ADMIN'])
        ;

        $user = new User();
        $user
            ->setUsername('user')
            ->setPassword($this->encoder->encodePassword($user, 'password'))
            ->setEmail('user@gmail.com')
            ->setRoles(['ROLE_USER'])
        ;

        $task = new Task();
        $task
            ->setTitle("Titre de la tâche")
            ->setContent("Description de la tâche")
            ->setUser($admin)
        ;

        $manager->persist($admin);
        $manager->persist($user);
        $manager->persist($task);
        $manager->flush();
    }
}
