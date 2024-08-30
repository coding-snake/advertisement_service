<?php

/**
 * Task controller.
 */

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Task;
use App\Entity\User;
use App\Form\Type\TaskType;
use App\Service\TaskServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class TaskController.
 */
#[Route('/task')]
class TaskController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param TaskServiceInterface $taskService task service
     * @param TranslatorInterface  $translator  translator service
     */
    public function __construct(
        private readonly TaskServiceInterface $taskService,
        private readonly TranslatorInterface $translator
    ) {
    }

    /**
     * Index action.
     *
     * @param int $page starting page number
     *
     * @return Response HTTP response
     */
    #[Route('/task', name: 'task_index', methods: 'GET')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(#[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $this->taskService->getPaginatedList($page);

        return $this->render('task/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * IndexAccepted action.
     *
     * @param int $page starting page number
     *
     * @return Response HTTP response
     */
    #[Route('/accepted', name: 'task_index_acc', methods: 'GET')]
    public function indexAccepted(#[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $this->taskService->getPaginatedListAcc($page);

        return $this->render('task/index_admin.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Show action.
     *
     * @param Task $task Task
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}',
        name: 'task_show',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET'
    )]
    public function show(Task $task): Response
    {
        return $this->render('task/show.html.twig', ['task' => $task]);
    }

    /**
     * Show action.
     *
     * @param Category $category Category
     * @param int      $page     starting page number
     *
     * @return Response HTTP response
     */
    #[Route(
        '/category/{id}',
        name: 'category_show',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET'
    )]
    public function showCategory(Category $category, #[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $this->taskService->getPaginatedListPart($page, $category);

        return $this->render('task/show_category.html.twig', ['category' => $category, 'pagination' => $pagination]);
    }

    /**
     * Create action.
     *
     * @param Request                $request       HTTP request
     * @param EntityManagerInterface $entityManager Entity manager
     *
     * @return Response HTTP response
     */
    #[Route(
        '/create',
        name: 'task_create',
        methods: 'GET|POST',
    )]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $task->setAuthor($this->getUser());
                $task->setIsAccepted(true);
            } else {
                $anon = $entityManager->getRepository(User::class)->findOneBy(['email' => 'anon@example.com']);
                if ($anon) {
                    $task->setAuthor($anon);
                }
                $task->setIsAccepted(false);
            }

            $this->taskService->save($task);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('task_index_acc');
        }

        return $this->render(
            'task/create.html.twig',
            ['Form' => $form->createView()]
        );
    }

    /**
     * Edit action.
     *
     * @param Request $request HTTP request
     * @param Task    $task    Task entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/edit', name: 'task_edit', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    #[IsGranted('EDIT', subject: 'task')]
    public function edit(Request $request, Task $task): Response
    {
        $form = $this->createForm(
            TaskType::class,
            $task,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('task_edit', ['id' => $task->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setIsAccepted(false);
            $this->taskService->save($task);
            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('task_index_acc');
        }

        return $this->render(
            'task/edit.html.twig',
            [
                'form' => $form->createView(),
                'task' => $task,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param Task    $task    Task entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/delete', name: 'task_delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    #[IsGranted('DELETE', subject: 'task')]
    public function delete(Request $request, Task $task): Response
    {
        $form = $this->createForm(FormType::class, $task, [
            'method' => 'DELETE',
            'action' => $this->generateUrl('task_delete', ['id' => $task->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->taskService->delete($task);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('task_index');
        }

        return $this->render(
            'task/delete.html.twig',
            [
                'form' => $form->createView(),
                'task' => $task,
            ]
        );
    }

    /**
     * @param Task                   $task
     * @param EntityManagerInterface $entityManager
     *
     * @return RedirectResponse
     */
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}/accept', name: 'task_accept', methods: 'GET')]
    public function toggleAccept(Task $task, EntityManagerInterface $entityManager): RedirectResponse
    {
        $task->setIsAccepted(!$task->getIsAccepted());
        $entityManager->flush();

        return $this->redirectToRoute('task_index');
    }
}
