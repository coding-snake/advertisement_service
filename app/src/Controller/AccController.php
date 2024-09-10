<?php

/**
 * Accout Controller.
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\RegisterType;
use App\Form\Type\UserFormData;
use App\Form\Type\UserType;
use App\Repository\UserRepository;
use App\Service\UserServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Account Controller.
 */
#[Route('/acc')]
class AccController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param UserServiceInterface        $userService    user service
     * @param TranslatorInterface         $translator     translator service
     * @param UserPasswordHasherInterface $passwordHasher hasher service
     */
    public function __construct(private readonly UserServiceInterface $userService, private readonly TranslatorInterface $translator, private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    /**
     * Change account.
     *
     * @param Request        $request        Http request
     * @param UserRepository $userRepository User repository
     *
     * @return Response HTTP Response
     */
    #[Route('/change', name: 'change_account', methods: 'GET|POST')]
    #[IsGranted('ROLE_USER')]
    public function changeAccount(Request $request, UserRepository $userRepository): Response
    {
        $user = $this->getUser();
        if ($this->isGranted('ROLE_ADMIN')) {
            $id = $request->query->get('id');

            if ($id) {
                $user = $userRepository->find($id);
                if (!$user) {
                    $this->addFlash('warning', $this->translator->trans('message.error_user_not_found'));

                    return $this->redirectToRoute('change_account');
                }
            }
        }

        $userFormData = new UserFormData();
        $userFormData->currentEmail = $user->getEmail();

        if ($this->isGranted('ROLE_ADMIN')) {
            $userFormData->currentPassword = 'example';
        }

        $form = $this->createForm(UserType::class, $userFormData);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($userFormData->currentEmail !== $user->getEmail() && !$this->isGranted('ROLE_ADMIN')) {
                $this->addFlash('warning', $this->translator->trans('message.error_email'));

                return $this->redirectToRoute('change_account');
            }

            if (empty($userFormData->currentEmail) || empty($userFormData->newEmail)) {
                $this->addFlash('warning', $this->translator->trans('message.error_empty_email'));

                return $this->redirectToRoute('change_account');
            }

            if (empty($userFormData->currentPassword) || empty($userFormData->newPassword)) {
                $this->addFlash('warning', $this->translator->trans('message.error_empty_password'));

                return $this->redirectToRoute('change_account');
            }

            if ($this->isGranted('ROLE_ADMIN')) {
                $this->userService->updateEmailPassword($user, $userFormData->newEmail, $userFormData->newPassword);
            } else {
                if (!$this->passwordHasher->isPasswordValid($user, $userFormData->currentPassword)) {
                    $this->addFlash('warning', $this->translator->trans('message.error_password'));

                    return $this->redirectToRoute('change_account');
                }

                $this->userService->updateEmailPassword($user, $userFormData->newEmail, $userFormData->newPassword);

                $this->addFlash('success', $this->translator->trans('message.success_change'));
            }

            return $this->redirectToRoute('change_account');
        }

        return $this->render('acc/change_account.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Register account.
     *
     * @param Request                $request       HTTP Request
     * @param EntityManagerInterface $entityManager Entity manager
     *
     * @return Response HTTP response
     */
    #[Route('/register', name: 'register_acc', methods: 'GET|POST')]
    public function registerAccount(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();

        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($password);

            $user->setEmail($form->get('email')->getData());

            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', $this->translator->trans('message.success_register'));

            return $this->redirectToRoute('app_login');
        }

        return $this->render('acc/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * List of accounts.
     *
     * @param MapQueryParameter $page number of pages
     *
     * @return Response respomse
     */
    #[Route('/list', name: 'acc_list', methods: 'GET|POST')]
    #[IsGranted('ROLE_ADMIN')]
    public function listAccounts(#[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $this->userService->getPaginatedList($page);

        return $this->render('acc/index_acc.html.twig', ['pagination' => $pagination]);
    }
}
