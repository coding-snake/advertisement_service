<?php

/**
 * Task repository.
 */

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
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
            ->orderBy('task.updatedAt', 'DESC');
    }

    /**
     * Code from the section about partial querying doesn't seem to work for me so I am using
     * magic code from the internet.
     */
    /**
     * Query Part records.
     *
     * @param Category $category Category
     *
     * @return QueryBuilder Query builder
     */
    public function queryPart(Category $category): QueryBuilder
    {
        return $this->createQueryBuilder('t')
            ->where('t.category = :category')
            ->setParameter('category', $category)
            ->orderBy('t.updatedAt', 'DESC');
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
