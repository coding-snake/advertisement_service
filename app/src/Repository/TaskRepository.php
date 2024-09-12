<?php

/**
 * Task repository.
 */

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Tag;
use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class TaskRepository.
 *
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @extends ServiceEntityRepository<Task>
 *
 * @psalm-suppress LessSpecificImplementedReturnType
 */
class TaskRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry Manager registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    /**
     * Query all records.
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->select('task', 'c')
            ->join('task.category', 'c')
            ->where('task.isAccepted = :accepted')
            ->setParameter('accepted', false)
            ->orderBy('task.updatedAt', 'DESC');
    }

    /**
     * Query Part records.
     *
     * @param Category $category Category
     *
     * @return QueryBuilder Query builder
     */
    public function queryPart(Category $category): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->select('task', 'category')
            ->join('task.category', 'category')
            ->where('task.category = :category')
            ->setParameter('category', $category)
            ->orderBy('task.updatedAt', 'DESC');
    }

    /**
     * Query Part records.
     *
     * @param Tag $tag Tag
     *
     * @return QueryBuilder Query builder
     */
    public function queryPartTags(Tag $tag): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->select('task', 'tags')
            ->innerJoin('task.tags', 'tags')
            ->where('tags = :tag')
            ->setParameter('tag', $tag)
            ->orderBy('task.updatedAt', 'DESC');
    }

    /**
     * Query Accepted records.
     *
     * @return QueryBuilder Query builder
     */
    public function queryAcc(): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->select('task', 'c')
            ->join('task.category', 'c')
            ->where('task.isAccepted = :accepted')
            ->setParameter('accepted', true)
            ->orderBy('task.updatedAt', 'DESC');
    }

    /**
     * Save entity.
     *
     * @param Task $task task entity
     */
    public function save(Task $task): void
    {
        assert($this->_em instanceof EntityManager);
        $this->_em->persist($task);
        $this->_em->flush();
    }

    /**
     * Delete entity.
     *
     * @param Task $task Task entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Task $task): void
    {
        assert($this->_em instanceof EntityManager);
        $this->_em->remove($task);
        $this->_em->flush();
    }

    /**
     * Count tasks by category.
     *
     * @param Category $category Category
     *
     * @return int Number of tasks in category
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countByCategory(Category $category): int
    {
        $qb = $this->getOrCreateQueryBuilder();

        return $qb->select($qb->expr()->countDistinct('task.id'))
            ->where('task.category = :category')
            ->setParameter(':category', $category)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Count tasks by tag.
     *
     * @param Tag $tag Category
     *
     * @return int Number of tasks in tag
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countByTag(Tag $tag): int
    {
        $qb = $this->getOrCreateQueryBuilder();

        return $qb->select($qb->expr()->countDistinct('task.id'))
            ->where('task.tag = :tag')
            ->setParameter(':category', $tag)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Get or create new query builder.
     *
     * @param QueryBuilder|null $queryBuilder Query builder
     *
     * @return QueryBuilder Query builder
     */
    private function getOrCreateQueryBuilder(?QueryBuilder $queryBuilder = null): QueryBuilder
    {
        return $queryBuilder ?? $this->createQueryBuilder('task');
    }
}
