<?php

namespace App\DataFixtures;

use App\Entity\MicroPost;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; $i++){
            $post = new MicroPost();
            $post->setText("Some random content" . rand(0, 100));
            $post->setTime(new \DateTime('2021-09-20'));
            $manager->persist($post);
        }

        $manager->flush();
    }
}
