<?php

namespace App\DataFixtures;

use App\Entity\Profile;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProfileFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $profile = new Profile();
        $profile->setRs('LinkedIn');
        $profile->setUrl('https://www.linkedin.com/in/asma-abichou-8995a6257/');

        $profile1 = new Profile();
        $profile1->setRs('twitter');
        $profile1->setUrl('https://twitter.com/asma_ab07');

        $profile2 = new Profile();
        $profile2->setRs('gitHub');
        $profile2->setUrl('https://github.com/asma-abichou');

         $manager->persist($profile);
         $manager->persist($profile1);
         $manager->persist($profile2);

        $manager->flush();
    }
}
