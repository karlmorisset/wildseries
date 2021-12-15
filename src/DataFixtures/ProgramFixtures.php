<?php

namespace App\DataFixtures;

use App\Entity\Program;
use App\Services\Slugify;
use App\DataFixtures\ActorFixtures;
use App\DataFixtures\CategoryFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ProgramFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    protected $slugify;

    const PROGRAMS = [
        [
            "title" => "The Big Bang Theory",
            "synopsis" => "Leonard Hofstadter et Sheldon Cooper vivent en colocation à Pasadena, ville de l'agglomération de Los Angeles. Ce sont tous deux des physiciens surdoués, « geeks » de surcroît. C'est d'ailleurs autour de cela qu'est axée la majeure partie comique de la série. Ils partagent quasiment tout leur temps libre avec leurs deux amis Howard Wolowitz et Rajesh Koothrappali pour jouer à des jeux vidéo comme Halo, organiser un marathon de la saga Star Wars, jouer à des jeux de société comme le Boggle klingon ou de rôles tel que Donjons et Dragons, voire discuter de théories scientifiques très complexes.Leur univers routinier est perturbé lorsqu'une jeune femme, Penny, s'installe dans l'appartement d'en face. Leonard a immédiatement des vues sur elle et va tout faire pour la séduire ainsi que l'intégrer au groupe et à son univers, auquel elle ne connaît rien.",
            "poster" => "https://upload.wikimedia.org/wikipedia/fr/6/69/BigBangTheory_Logo.png",
            'category' => "category_comedie"
        ],
        [
            "title" => "Breaking Bad",
            "synopsis" => "Un bel exemple de reconversion professionnelle",
            "poster" => "https://fr.web.img5.acsta.net/pictures/19/06/18/12/11/3956503.jpg",
            'category' => "category_drame"
        ],
        [
            "title" => "West Side Story",
            "synopsis" => "La série issue du film légendaire",
            "poster" => "https://encrypted-tbn3.gstatic.com/images?q=tbn:ANd9GcRclvfN2QyG-zFYqitbpXZoVDqxK-tsNuiUYSMcXvfiP_HJWAXp",
            'category' => "category_drame"

        ],
        [
            "title" => "Clan",
            "synopsis" => "5 sœurs et un sale type",
            "poster" => "https://upload.wikimedia.org/wikipedia/fr/6/69/BigBangTheory_Logo.png",
            'category' => "category_comedie"
        ],
        [
            "title" => "How I met your mother",
            "synopsis" => "Un type raconte à ses enfants comment il a rencontré leur mère",
            "poster" => "https://fr.web.img5.acsta.net/medias/nmedia/18/74/38/63/20215058.jpg",
            'category' => "category_comedie"
        ],
        [
            "title" => "Walking Dead",
            "synopsis" => "Le policier Rick Grimes se réveille après un long coma. Il découvre avec effarement que le monde, ravagé par une épidémie, est envahi par les morts-vivants.",
            "poster" => "https://m.media-amazon.com/images/M/MV5BZmFlMTA0MmUtNWVmOC00ZmE1LWFmMDYtZTJhYjJhNGVjYTU5XkEyXkFqcGdeQXVyMTAzMDM4MjM0._V1_.jpg",
            'category' => "category_horreur"
            ]
        ];
        
    public const USERS = ["user_contributor", "user_admin"];

    public function __construct(Slugify $slugify)
    {
        $this->slugify = $slugify;
    }

    public function load(ObjectManager $manager): void
    {
        foreach(self::PROGRAMS as $key => $p){
            $program = new Program();
            $program->setTitle($p['title']);
            $program->setSynopsis($p['synopsis']);
            $program->setPoster($p['poster']);
            $program->setCategory($this->getReference($p['category']));
            $program->setSlug($this->slugify->generate($p['title']));
            $program->setOwner($this->getReference(self::USERS[rand(0,1)]));

            for ($i=0; $i < 3; $i++) {
                $program->addActor($this->getReference('actor_' . $i));
            }

            $this->addReference('program_' . $key, $program);

            $manager->persist($program);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        // Tu retournes ici toutes les classes de fixtures dont ProgramFixtures dépend
        return [
            UserFixtures::class,
            ActorFixtures::class,
            CategoryFixtures::class
        ];
    }

    public static function getGroups(): array
    {
        return ['group1', 'group2'];
    }
}
