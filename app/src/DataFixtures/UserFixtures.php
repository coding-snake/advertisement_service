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

        $exampleUser = new User();
        $exampleUser->setEmail('user@example.com');
        $exampleUser->setRoles([UserRole::ROLE_USER->value]);
        $exampleUser->setPassword($this->passwordHasher->hashPassword($exampleUser, 'user1234'));
        $this->manager->persist($exampleUser);

        $anon = new User();
        $anon->setEmail('anon@example.com');
        $anon->setRoles([UserRole::ROLE_USER->value]);
        $anon->setPassword($this->passwordHasher->hashPassword($anon, 'anon1234'));
        $this->manager->persist($anon);

        $this->manager->flush();
    }
}
