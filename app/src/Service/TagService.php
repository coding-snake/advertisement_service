<?php

/**
 * Tag service.
 */

namespace App\Service;

use App\Entity\Tag;
use App\Repository\TagRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Tag TaskService.
 */
class TagService implements TagServiceInterface
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
     * @param TagRepository      $tagRepository Tag repository
     * @param PaginatorInterface $paginator     Paginator interface
     */
    public function __construct(private readonly TagRepository $tagRepository, private readonly TaskRepository $taskRepository, private readonly PaginatorInterface $paginator)
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
            $this->tagRepository->queryAll(),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save entity.
     *
     * @param Tag $tag tag entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Tag $tag): void
    {
        $this->tagRepository->save($tag);
    }

    /**
     * Delete entity.
     *
     * @param Tag $tag Tag entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Tag $tag): void
    {
        $this->tagRepository->delete($tag);
    }

    /**
     * Can Tag be deleted?
     *
     * @param Tag $tag Tag entity
     *
     * @return bool Result
     */
    public function canBeDeleted(Tag $tag): bool
    {
        try {
            $result = $this->taskRepository->countByTag($tag);

            return !($result > 0);
        } catch (NoResultException | NonUniqueResultException) {
            return false;
        }
    }

    /**
     * Get part of paginated list.
     *
     * @param int $page Page number
     * @param Tag $tag  Tag
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedListPart(int $page, Tag $tag): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->taskRepository->queryPartTags($tag),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }
}
