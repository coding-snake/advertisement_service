<?php

/**
 * User service.
 */

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class UserService.
 */
class UserService implements UserServiceInterface
{
    private const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * A construct function
     *
     * @param EntityManagerInterface      $entityManager
     * @param UserPasswordHasherInterface $passwordHasher
     * @param PaginatorInterface          $paginator
     * @param UserRepository              $userRepository
     */
    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly UserPasswordHasherInterface $passwordHasher, private readonly PaginatorInterface $paginator, private readonly UserRepository $userRepository)
    {
    }

    /**
     * Updated email and password.
     *
     * @param User   $user
     * @param string $newEmail
     * @param string $newPassword
     * @param int    $flag
     */
    public function updateEmailPassword(User $user, string $newEmail, string $newPassword, int $flag): void
    {
        if (0 === $flag || 2 === $flag) {
            $user->setEmail($newEmail);
        }
        if (0 === $flag || 1 === $flag) {
            $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
            $user->setPassword($hashedPassword);
        }
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->userRepository->queryAll(),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE,
        );
    }
}
