<?php

/**
 * Accout Controller.
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\RegisterType;
use App\Form\Type\UserFormData;
use App\Form\Type\UserType;
use App\Service\UserServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/*
 * Account Controller
 */
#[Route('/acc')]
class AccController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param UserServiceInterface $userService user service
     * @param TranslatorInterface  $translator  translator service
     */
    public function __construct(
        private readonly UserServiceInterface $userService,
        private readonly TranslatorInterface $translator,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
    }

    /*
     * Change account
     */
    #[Route('/change', name: 'change_account', methods: 'GET|POST')]
    #[IsGranted('ROLE_USER')]
    public function changeAccount(Request $request): Response
    {
        $user = $this->getUser();
        $userFormData = new UserFormData();
        $userFormData->current_email = $user->getEmail();
        $form = $this->createForm(UserType::class, $userFormData);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($userFormData->current_email !== $user->getEmail()) {
                $this->addFlash('warning', $this->translator->trans('message.error_email'));

                return $this->redirectToRoute('change_account');
            }

            if (!$this->passwordHasher->isPasswordValid($user, $userFormData->current_password)) {
                $this->addFlash('warning', $this->translator->trans('message.error_password'));

                return $this->redirectToRoute('change_account');
            }

            $this->userService->updateEmailPassword($user, $userFormData->new_email, $userFormData->new_password);

            $this->addFlash('success', $this->translator->trans('message.success_change'));

            return $this->redirectToRoute('change_account');
        }

        return $this->render('acc/change_account.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /*
     * Register account
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
}
