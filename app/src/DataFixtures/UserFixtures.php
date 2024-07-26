<?php

/**
 * User fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Enum\UserRole;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class UserFixtures.
 */
class UserFixtures extends AbstractBaseFixtures
{
    /**
     * @param UserPasswordHasherInterface $passwordHasher Password hasher
     */
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    /**
     * Load data.
     */
    protected function loadData(): void
    {
        if (!$this->manager instanceof ObjectManager || !$this->faker instanceof Generator) {
            return;
        }

        $this->createMany(10, 'users', function (int $i) {
            $user = new User();
            $user->setEmail(sprintf('user%d@example.com', $i));
            $user->setRoles([UserRole::ROLE_USER->value]);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'user1234'));

            return $user;
        });
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setRoles([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value]);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin1234'));
        $this->manager->persist($admin);

        $example_user = new User();
        $example_user->setEmail('user@example.com');
        $example_user->setRoles([UserRole::ROLE_USER->value]);
        $example_user->setPassword($this->passwordHasher->hashPassword($example_user, 'user1234'));
        $this->manager->persist($example_user);

        $anon = new User();
        $anon->setEmail('anon@example.com');
        $anon->setRoles([UserRole::ROLE_USER->value]);
        $anon->setPassword($this->passwordHasher->hashPassword($anon, 'anon1234'));
        $this->manager->persist($anon);

        $this->manager->flush();
    }
}
