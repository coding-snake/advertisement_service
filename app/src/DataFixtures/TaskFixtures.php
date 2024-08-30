<?php

/**
 * Task fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Tag;
use App\Entity\Task;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

/**
 * Class TaskFixtures.
 */
class TaskFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    /**
     * Load data.
     *
     * @psalm-suppress PossiblyNullPropertyFetch
     * @psalm-suppress PossiblyNullReference
     * @psalm-suppress UnusedClosureParam
     */
    public function loadData(): void
    {
        if (!$this->manager instanceof ObjectManager || !$this->faker instanceof Generator) {
            return;
        }

        $this->createMany(100, 'tasks', function (int $i) {
            $task = new Task();
            $task->setTitle($this->faker->sentence);
            $content = $this->faker->paragraph;
            $task->setContent(substr($content, 0, 255));
            $task->setCreatedAt(\DateTimeImmutable::createFromMutable(
                $this->faker->dateTimeBetween('-100 days', '-1 days')
            ));
            $task->setUpdatedAt(\DateTimeImmutable::createFromMutable(
                $this->faker->dateTimeBetween('-100 days', '-1 days')
            ));

            /** @var Category $category */
            $category = $this->getRandomReference('categories');
            $task->setCategory($category);

            /** @var array<array-key, Tag> $tags */
            $tags = $this->getRandomReferences('tags', $this->faker->numberBetween(0, 5));
            foreach ($tags as $tag) {
                $task->addTag($tag); // Use addTag method to add tags
            }

            /** @var User $author */
            $author = $this->getRandomReference('users');
            $task->setAuthor($author);

            $isAccepted = $this->faker->boolean();
            if (true === $isAccepted) {
                $task->setIsAccepted(true);
            }

            return $task;
        });

        $this->manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on.
     *
     * @return string[] of dependencies
     *
     * @psalm-return array{0: CategoryFixtures::class, 1: TagFixtures::class, 2: UserFixtures::class}
     */
    public function getDependencies(): array
    {
        return [CategoryFixtures::class, TagFixtures::class, UserFixtures::class];
    }
}
