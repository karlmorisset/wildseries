<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class _00CategoryFixtures extends Fixture
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
            $this->addReference('category_' . strtolower($c), $category);

            $manager->persist($category);
        }

        $manager->flush();
    }
}