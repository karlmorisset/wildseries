<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements FixtureGroupInterface
{
    protected $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }


    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail("user.email@monsite.com");
        $user->setRoles(["ROLE_CONTRIBUTOR"]);
        $hashedPassword = $this->passwordHasher->hashPassword($user, "toto");
        $user->setPassword($hashedPassword);
        $manager->persist($user);
        $this->addReference("user_contributor", $user);

        // Création d’un utilisateur de type “administrateur”
        $admin = new User();
        $admin->setEmail('admin@monsite.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $hashedPassword = $this->passwordHasher->hashPassword($admin,'root');
        $admin->setPassword($hashedPassword);
        $manager->persist($admin);
        $this->addReference("user_admin", $admin);

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['user_group'];
    }
}
