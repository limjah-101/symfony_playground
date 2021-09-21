<?php

namespace App\DataFixtures;

use App\Entity\MicroPost;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->loadPosts($manager);
        $this->loadUsers($manager);
    }

    public function loadPosts(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; $i++){
            $post = new MicroPost();
            $post->setText("Some random content " . rand(0, 100));
            $post->setTime(new \DateTime('2021-09-20'));
            $manager->persist($post);
        }

        $manager->flush();
    }

    public function loadUsers(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername("admin");
        $user->setEmail("admin@gmail.com");
        $user->setFullName("Bob");
        $user->setPassword($this->passwordEncoder->encodePassword($user, "password"));

        $manager->persist($user);
        $manager->flush();
    }
}
