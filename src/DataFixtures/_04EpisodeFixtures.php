<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Episode;
use App\DataFixtures\SeasonFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class _04EpisodeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 12; $i++) {
            $episode = new Episode();
            $episode->setTitle($faker->sentence);
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
}
