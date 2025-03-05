<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserSeeder extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Create Super Admin user
        $superAdmin = new User();
        $superAdmin->setEmail('admin@example.com');
        $superAdmin->setFirstName('Admin');
        $superAdmin->setLastName('User');
        $superAdmin->setRoles(['ROLE_SUPER_ADMIN']);
        $superAdmin->setPassword(
            $this->passwordHasher->hashPassword($superAdmin, 'admin123')
        );
        $manager->persist($superAdmin);

        // Create a Product Admin user
        $productAdmin = new User();
        $productAdmin->setEmail('product@example.com');
        $productAdmin->setFirstName('Product');
        $productAdmin->setLastName('Manager');
        $productAdmin->setRoles(['ROLE_PRODUCT_ADMIN']);
        $productAdmin->setPassword(
            $this->passwordHasher->hashPassword($productAdmin, 'product123')
        );
        $manager->persist($productAdmin);

        // Create a Content Admin user
        $contentAdmin = new User();
        $contentAdmin->setEmail('content@example.com');
        $contentAdmin->setFirstName('Content');
        $contentAdmin->setLastName('Editor');
        $contentAdmin->setRoles(['ROLE_CONTENT_ADMIN']);
        $contentAdmin->setPassword(
            $this->passwordHasher->hashPassword($contentAdmin, 'content123')
        );
        $manager->persist($contentAdmin);

        // Create a regular user
        $user = new User();
        $user->setEmail('user@example.com');
        $user->setFirstName('Regular');
        $user->setLastName('User');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, 'user123')
        );
        $manager->persist($user);

        $manager->flush();
    }
}