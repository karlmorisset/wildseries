<?php

namespace App\DataFixtures;

use App\Entity\Actor;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class _01ActorFixtures extends Fixture implements FixtureGroupInterface
{
    public const ACTORS = [
        "Andrew Lincoln",
        "Norman Reedus",
        "Lauren Cohan",
        "Danai Gurira",
        "Jeffrey Dean Morgan",
        "Chandler Riggs"
    ];

    public function load(ObjectManager $manager): void
    {
        foreach(self::ACTORS as $key => $actorName) {
            $actor = new Actor();
            $actor->setName($actorName);
            $this->addReference('actor_' . $key, $actor);

            $manager->persist($actor);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['group1', 'group2'];
    }
}
