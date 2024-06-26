<?php

/**
 * Category controller.
 */

namespace App\Controller;

use App\Entity\Category;
use App\Service\TaskServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class CategoryController.
 */
#[Route('/category')]
class CategoryController extends AbstractController
{
    /**
     * Constructor.
     */
    public function __construct(private readonly TaskServiceInterface $taskService)
    {
    }

    #[Route(
        '/{id}',
        name: 'category_show',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET',
    )]
    public function index(Category $category, #[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $this->taskService->getPaginatedList($page);

        return $this->render('category/show.html.twig', ['pagination' => $pagination, 'category' => $category]);
    }
}
