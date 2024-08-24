<?php

/**
 * Accout Controller.
 */

namespace App\Controller;

use App\Form\Type\UserType;
use App\Service\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/*
 * Account Controller
 */
#[Route('/acc')]
class AccController extends AbstractController
{
    /*
     * Constructor.
     *
     * @param CategoryServiceInterface $categoryService category service
     * @param TranslatorInterface      $translator      translator service
     */
    public function __construct(private readonly UserServiceInterface $userService, private readonly TranslatorInterface $translator)
    {
    }

    /*
     * Change account
     */
    #[Route('/change', name: 'change_account', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function changeAccount(Request $request, TranslatorInterface $translator): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, [
            'current_email' => $user->getEmail(),
        ]);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if ($data['current_email'] !== $user->getEmail()) {
                $this->addFlash(
                    'warning',
                    $translator->trans('message.error_email')
                );

                return $this->redirectToRoute('change_account');
            }
            if ($data['current_password'] !== $user->getPassword()) {
                $this->addFlash(
                    'warning',
                    $translator->trans('message.error_password')
                );

                return $this->redirectToRoute('change_account');
            }

            $this->userService->updateEmailPassword($user, $data['new_email'], $data['new_password']);

            $this->addFlash(
                'succes',
                $translator->trans('message.success_change')
            );
            return $this->redirectToRoute('chnage_account');
        }
        return $this->render('acc/change_account.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
