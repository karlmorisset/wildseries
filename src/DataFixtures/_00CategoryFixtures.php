<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class _00CategoryFixtures extends Fixture implements FixtureGroupInterface
{
    const CATEGORIES = [
        'Action',
        'Aventure',
        'Animation',
        'Drame',
        'Fantastique',
        'Horreur',
        'Comedie'
    ];

    public function load(ObjectManager $manager)
    {
        foreach (self::CATEGORIES as $key => $c) {
            $category = new Category();
            $category->setName($c);
            $manager->persist($category);

            $this->addReference('category_' . strtolower($c), $category);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['group1', 'group2'];
    }
}