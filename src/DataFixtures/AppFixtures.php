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

    private const USERS = [
        [
            'username' => 'john_doe',
            'email' => 'john_doe@doe.com',
            'password' => 'password',
            'fullName' => 'John Doe',
        ],
        [
            'username' => 'rob_smith',
            'email' => 'rob_smith@smith.com',
            'password' => 'password',
            'fullName' => 'Rob Smith',
        ],
        [
            'username' => 'marry_gold',
            'email' => 'marry_gold@gold.com',
            'password' => 'password',
            'fullName' => 'Marry Gold',
        ],
    ];

    private const POST_TEXT = [
        'Hello, how are you?',
        'It\'s nice sunny weather today',
        'I need to buy some ice cream!',
        'I wanna buy a new car',
        'There\'s a problem with my phone',
        'I need to go to the doctor',
        'What are you up to today?',
        'Did you watch the game yesterday?',
        'How was your day?'
    ];

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
        $this->loadPosts($manager);
    }

    public function loadPosts(ObjectManager $manager)
    {
        for ($i = 0; $i < 30; $i++){
            $post = new MicroPost();
            $post->setText(self::POST_TEXT[rand(0, count(self::POST_TEXT) -1)]);
            $date = new \DateTime();
            $date->modify('-' . rand(0, 10) . 'day');
            $post->setTime($date);
            $post->setUser($this->getReference(
                self::USERS[rand(0, count(self::USERS) -1)]['username'])
            );
            $manager->persist($post);
        }

        $manager->flush();
    }

    public function loadUsers(ObjectManager $manager)
    {
        foreach (self::USERS as $userData) {
            $user = new User();
            $user->setUsername($userData['username']);
            $user->setEmail($userData['email']);
            $user->setFullName($userData['fullName']);
            $user->setPassword($this->passwordEncoder->encodePassword($user, $userData['password']));

            $this->addReference($userData['username'], $user);

            $manager->persist($user);
        }
        $manager->flush();
    }
}
