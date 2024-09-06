<?php

/**
 * User service interface.
 */

namespace App\Service;

use App\Entity\User;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Interface UserServiceInterface.
 */
interface UserServiceInterface
{
    /**
     * Updates the user's email and password.
     *
     * @param UserInterface $user        User
     * @param string        $newEmail    New Email
     * @param string        $newPassword New Password
     */
    public function updateEmailPassword(User $user, string $newEmail, string $newPassword, int $flag): void;

    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface;
}
