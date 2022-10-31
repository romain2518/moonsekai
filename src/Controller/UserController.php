<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditLoginsType;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Repository\WorkRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }
    
    // #[Route('/', name: 'app_user_index', methods: ['GET'])]
    // public function index(UserRepository $userRepository): Response
    // {
    //     return $this->render('user/index.html.twig', [
    //         'users' => $userRepository->findAll(),
    //     ]);
    // }

    #[Route('/profile/{id}', name: 'app_user_show', requirements: ['id' => '\d+'])]
    public function profile(UserRepository $userRepository, int $id = null): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        
        if ($user === null || // User is not connected
            $user !== null && ($id !== null && $id !== $user->getId())) { // User is connected but not the target
            $user = $userRepository->find($id);
        }
        
        if ($user === null) {
            throw $this->createNotFoundException('User not found');
        }

        return $this->render('user/profile.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/edit-logins', name: 'app_user_edit-logins', methods: ['GET', 'POST'])]
    public function editLogins(
        Request $request, UserInterface $user, 
        EntityManagerInterface $entityManager, 
        UserPasswordHasherInterface $userPasswordHasher,
        TokenStorageInterface $tokenStorage
        ): Response
    {
        /** @var User $user */
        $lastEmail = $user->getEmail();
        
        $form = $this->createForm(EditLoginsType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $supposedPassword = $form->get('password')->getData();
            if ($userPasswordHasher->isPasswordValid($user, $supposedPassword)) {
                //? If password changes, it needs to be hashed
                $newPassword = $form->get('newPassword')->getData();

                if (null !== $newPassword) {
                    $user->setPassword(
                        $userPasswordHasher->hashPassword(
                            $user,
                            $newPassword
                        )
                    );
                }

                //? If email changes, it needs to be confirmed
                if ($user->getEmail() !== $lastEmail) {
                    $user->setIsVerified(false);
                    
                    // Send email confirmation
                    $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                        (new TemplatedEmail())
                            ->from(new Address('no-reply@moonsekai.com', 'Account confirmationMmail Bot'))
                            ->to($user->getEmail())
                            ->subject('Please Confirm your Email')
                            ->htmlTemplate('registration/confirmation_email.html.twig')
                    );                     
                }

                $entityManager->flush();

                if ($user->getEmail() !== $lastEmail) {
                    // Disconnecting user
                    $tokenStorage->setToken();
                    
                    return $this->redirectToRoute('app_verify_resend_email', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
                }

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

    #[Route('/followed-work/{limit}/{offset}', name: 'app_user_show', requirements: ['limit' => '\d+', 'offset' => '\d+'])]
    public function listFollowedWork(WorkRepository $workRepository, UserRepository $ur, UserInterface $user, int $limit = 20, int $offset = 0): Response
    {
        /** @var User $user */

        return $this->render('user/followed_work.html.twig', [
            'works' => $workRepository->findByFollower($user, 20, 0)
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
