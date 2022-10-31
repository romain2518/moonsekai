<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditLoginsType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class UserController extends AbstractController
{
    // #[Route('/', name: 'app_user_index', methods: ['GET'])]
    // public function index(UserRepository $userRepository): Response
    // {
    //     return $this->render('user/index.html.twig', [
    //         'users' => $userRepository->findAll(),
    //     ]);
    // }

    // #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    // public function show(User $user): Response
    // {
    //     return $this->render('user/show.html.twig', [
    //         'user' => $user,
    //     ]);
    // }

    #[Route('/edit-logins', name: 'app_user_edit-logins', methods: ['GET', 'POST'])]
    public function editLogins(Request $request, UserInterface $user, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        /** @var User $user */

        $form = $this->createForm(EditLoginsType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currentPassword = $form->get('password')->getData();
            if ($userPasswordHasher->isPasswordValid($user, $currentPassword)) {
                $newPassword = $form->get('newPassword')->getData();
                if (null !== $newPassword) {
                    $user->setPassword(
                        $userPasswordHasher->hashPassword(
                            $user,
                            $newPassword
                        )
                    );
                }

                $entityManager->flush();

                //TODO
                // return $this->redirectToRoute('app_user_profile', [], Response::HTTP_SEE_OTHER);
                return $this->redirectToRoute('app_main_home', [], Response::HTTP_SEE_OTHER);
            }

            $this->addFlash('edit_logins_error', 'Incorrect password');
        }

        return $this->renderForm('user/edit_logins.html.twig', [
            'form' => $form,
        ]);
    }

    // #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    // public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    // {
    //     if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
    //         $entityManager->remove($user);
    //         $entityManager->flush();
    //     }

    //     return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    // }
}
