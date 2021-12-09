<?php

namespace App\DataFixtures;

use App\Entity\Season;
use App\Entity\Program;
use App\DataFixtures\ActorFixtures;
use App\DataFixtures\ProgramFixtures;
use App\DataFixtures\CategoryFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class _03SeasonFixtures extends Fixture implements FixtureGroupInterface
{
    const SEASONS = [
        [
            "number" => "1",
            "year" => "2012",
            "description" => "Season 1",
            "program" => "program_0"
        ],
        [
            "number" => "2",
            "year" => "2014",
            "description" => "Season 2",
            "program" => "program_0"
        ],
        [
            "number" => "3",
            "year" => "2015",
            "description" => "Season 3",
            "program" => "program_1"
        ],
        [
            "number" => "4",
            "year" => "2017",
            "description" => "Season 4",
            "program" => "program_2"
        ],
        [
            "number" => "5",
            "year" => "2018",
            "description" => "Season 5",
            "program" => "program_3"
        ]
    ];

    public function load(ObjectManager $manager): void
    {
        foreach(self::SEASONS as $key => $s){
            $season = new Season();
            $season->setNumber($s['number']);
            $season->setYear($s['year']);
            $season->setDescription($s['description']);
            $season->setProgram($this->getReference($s['program']));

            $this->addReference('season_' . $key, $season);

            $manager->persist($season);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ProgramFixtures::class
        ];
    }

    public static function getGroups(): array
    {
        return ['group2'];
    }
}
