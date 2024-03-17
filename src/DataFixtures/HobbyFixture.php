<?php

namespace App\DataFixtures;

use App\Entity\Hobby;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class HobbyFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $data = [
            "Reading",
            "Writing",
            "Drawing",
            "Painting",
            "Playing a musical instrument",
            "Meditation",
            "Playing board games",
            "Playing video games",
           " Photography",
            "DIY projects",
            "Investing",
            "Learning a new language",
            "Traveling",
            "Volunteering",
            "Bird watching",
            "Stamp collecting",
            "Playing sports such as soccer, golf, or tennis",
            "Solving puzzles",
            "Geocaching",
            "Playing chess or other strategy games.",
        ];

        for($i=0; $i < count($data); $i++)
        {
            $hobby = new Hobby();
            $hobby->setDesignation($data[$i]);
            $manager->persist($hobby);
        }
        $manager->flush();
    }
}
