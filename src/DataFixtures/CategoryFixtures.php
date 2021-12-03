<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    const CATEGORIES = [
        'Action',
        'Aventure',
        'Animation',
        'Fantastique',
        'Horreur',
        'Comédie'
    ];

    public function load(ObjectManager $manager)
    {
        foreach (self::CATEGORIES as $c) {
            $category = new Category();
            $category->setName($c);
            $manager->persist($category);
        }

        $manager->flush();
    }
}