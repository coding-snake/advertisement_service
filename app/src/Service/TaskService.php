<?php

/**
 * Task service.
 */

namespace App\Service;

use App\Entity\Category;
use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class TaskService.
 */
class TaskService implements TaskServiceInterface
{
    /**
     * Items per page.
     *
     * Use constants to define configuration options that rarely change instead
     * of specifying them in app/config/config.yml.
     * See https://symfony.com/doc/current/best_practices.html#configuration
     *
     * @constant int
     */
    private const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param TaskRepository     $taskRepository Task repository
     * @param PaginatorInterface $paginator      Paginator
     */
    public function __construct(private readonly TaskRepository $taskRepository, private readonly PaginatorInterface $paginator)
    {
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
            $this->taskRepository->queryAll(),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Get part of paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedListPart(int $page, Category $category): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->taskRepository->queryPart($category),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save entity.
     *
     * @param Task category entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Task $task): void
    {
        $currentTime = new \DateTimeImmutable();
        if (null === $task->getId()) {
            $task->setCreatedAt($currentTime);
        }
        $task->setUpdatedAt($currentTime);
        $this->taskRepository->save($task);
    }
}
