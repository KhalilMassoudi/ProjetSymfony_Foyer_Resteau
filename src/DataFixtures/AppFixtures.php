<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


use Symfony\Component\PasswordHasher\PasswordHasherInterface;

/*class AppFixtures extends Fixture
{
    private $passwordHasher;
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }


    public function load(ObjectManager $manager): void
    {
        $user = new User ();
        $plainPassword = 'admin111';
        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setEmail = '';
        $user->setUsername('admin');
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_ADMIN']);

        $manager->persist($user);

        $manager->flush();
    }
}*/
class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Créer un nouvel utilisateur
        $user = new User();
        $plainPassword = 'admin111';
        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);

        // Configuration des propriétés de l'utilisateur
        $user->setEmail('admin@example.com'); // Remplacez par un email valide
        $user->setUsername('admin');
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_ADMIN']);

        // Persister et sauvegarder
        $manager->persist($user);
        $manager->flush();
    }
}
