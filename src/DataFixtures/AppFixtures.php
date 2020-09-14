<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class AppFixtures
 * @package App\DataFixtures
 */
class AppFixtures extends Fixture
{

    /** @var UserPasswordEncoderInterface */
    protected $encoder;

    /**
     * AppFixtures constructor.
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
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
