<?php

namespace App\DataFixtures;

use App\Entity\Project;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        // 1. Create Admin User
        $admin = new User();
        $admin->setUsername('admin');
        $admin->setEmail('admin@patchstash.com');
        $admin->setRole('ROLE_ADMIN');
        $admin->setIsVerified(true);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'password'));
        $manager->persist($admin);

        // 2. Create Regular User
        $user = new User();
        $user->setUsername('user');
        $user->setEmail('user@patchstash.com');
        $user->setRole('ROLE_USER');
        $user->setIsVerified(true);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
        $manager->persist($user);

        // 3. Create Sample Project
        $project = new Project();
        $project->setName('Alpha Centauri System');
        $project->setDescription('Critical infrastructure control system for the alpha sector.');
        $project->setStatus('active');
        $project->setUser($user);
        $manager->persist($project);

        $manager->flush();
    }
}
