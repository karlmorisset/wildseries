<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Episode;
use App\Services\Slugify;
use App\DataFixtures\SeasonFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class EpisodeFixtures extends Fixture implements FixtureGroupInterface
{
    public function __construct(Slugify $slugify)
    {
        $this->slugify = $slugify;
    }


    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 12; $i++) {
            $episode = new Episode();
            $title = $faker->sentence;
            $episode->setTitle($title);
            $episode->setSlug($this->slugify->generate($title));
            $episode->setNumber($i);
            $episode->setSynopsis($faker->realText(200, 2));
            $episode->setSeason($this->getReference("season_1"));

            $manager->persist($episode);
        }

        $manager->flush();
    }


    public function getDependencies()
    {
        return [
          SeasonFixtures::class
        ];
    }

    public static function getGroups(): array
    {
        return ['group2'];
    }
}
