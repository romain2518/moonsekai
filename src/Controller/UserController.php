<?php

namespace App\Controller;

use App\Entity\Ban;
use App\Entity\Progress;
use App\Entity\User;
use App\Entity\Work;
use App\Form\DeleteAccountFormType;
use App\Form\EditLoginsType;
use App\Form\EditProfileFormType;
use App\Repository\ProgressRepository;
use App\Repository\UserRepository;
use App\Repository\WorkRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }

    #[Route('/profile/{id}', name: 'app_user_profile', requirements: ['id' => '\d+'])]
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

    #[Route('/profile/edit', name: 'app_user_edit-profile')]
    public function editProfile(Request $request, UserInterface $user, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */

        $form = $this->createForm(EditProfileFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            // Setting file properties to null as user object is serialized and saved in the session (a File is not serializable)
            $user->setPictureFile(null);
            $user->setBannerFile(null);

            return $this->redirectToRoute('app_user_profile', [], Response::HTTP_SEE_OTHER);
        }

        // Setting file properties to null as user object is serialized and saved in the session (a File is not serializable)
        $user->setPictureFile(null);
        $user->setBannerFile(null);


        return $this->renderForm('user/edit_profile.html.twig', [
            'form' => $form,
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

                return $this->redirectToRoute('app_user_profile', [], Response::HTTP_SEE_OTHER);
            }

            $this->addFlash('edit_logins_error', 'Incorrect password');
        }

        return $this->renderForm('user/edit_logins.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/followed-work/{limit}/{offset}', name: 'app_user_followed-work', requirements: ['limit' => '\d+', 'offset' => '\d+'])]
    public function listFollowedWork(WorkRepository $workRepository, UserInterface $user, int $limit = 20, int $offset = 0): Response
    {
        /** @var User $user */

        return $this->render('user/followed_work.html.twig', [
            'works' => $workRepository->findByFollower($user, $limit, $offset)
        ]);
    }

    #[Route('/follow/{id}', name: 'app_user_follow', requirements: ['id' => '\d+'], methods: ['POST'], defaults: ['_format' => 'json'])]
    public function follow(Request $request, Work $work = null, UserInterface $user, EntityManagerInterface $entityManager): JsonResponse
    {
        /** @var User $user */

        if ($work === null) {
            return $this->json('Work not found', Response::HTTP_NOT_FOUND);
        }

        //? Checking CSRF Token
        $token = $request->request->get('token');
        $isValidToken = $this->isCsrfTokenValid('follow'.$work->getId(), $token);
        if (!$isValidToken) {
            return $this->json('Invalid token', Response::HTTP_FORBIDDEN);
        }

        $user->addFollowedWork($work);

        $entityManager->flush();

        return $this->json(
            $user,
            Response::HTTP_PARTIAL_CONTENT,
            [],
            [
                'groups' => [
                    'api_user_show'
                ]
            ],
        );
    }

    #[Route('/unfollow/{id}', name: 'app_user_unfollow', requirements: ['id' => '\d+'], methods: ['POST'], defaults: ['_format' => 'json'])]
    public function unfollow(Request $request, Work $work = null, UserInterface $user, EntityManagerInterface $entityManager): JsonResponse
    {
        /** @var User $user */

        if ($work === null) {
            return $this->json('Work not found', Response::HTTP_NOT_FOUND);
        }

        //? Checking CSRF Token
        $token = $request->request->get('token');
        $isValidToken = $this->isCsrfTokenValid('unfollow'.$work->getId(), $token);
        if (!$isValidToken) {
            return $this->json('Invalid token', Response::HTTP_FORBIDDEN);
        }

        $user->removeFollowedWork($work);

        $entityManager->flush();

        return $this->json(
            $user,
            Response::HTTP_PARTIAL_CONTENT,
            [],
            [
                'groups' => [
                    'api_user_show'
                ]
            ],
        );
    }

    #[Route('/work/{id}/mark-progress', name: 'app_user_mark-progress', requirements: ['id' => '\d+'], methods: ['POST'], defaults: ['_format' => 'json'])]
    public function markProgress(
        Request $request, Work $work = null, UserInterface $user, 
        ProgressRepository $progressRepository, EntityManagerInterface $entityManager,
        ValidatorInterface $validator
        ): JsonResponse
    {
        /** @var User $user */

        if ($work === null) {
            return $this->json('Work not found', Response::HTTP_NOT_FOUND);
        }

        //? Checking CSRF Token
        $token = $request->request->get('token');
        $isValidToken = $this->isCsrfTokenValid('mark-progress'.$work->getId(), $token);
        if (!$isValidToken) {
            return $this->json('Invalid token', Response::HTTP_FORBIDDEN);
        }

        //? Checking if user already mark progress on this work
        $progress = $progressRepository->findOneBy(['user' => $user, 'work' => $work]);
        $responseCode = Response::HTTP_PARTIAL_CONTENT;
        if (null === $progress) {
            $progress = new Progress();
            $responseCode = Response::HTTP_CREATED;
        }
        
        $progress
            ->setUser($user)
            ->setWork($work)
            ->setProgress($request->request->get('progress'))
            ;

        //? Validating data
        $errors = $validator->validate($progress);
        if (count($errors) > 0) { 
            return $this->json('Unprocessable content.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entityManager->persist($progress);
        $entityManager->flush();

        return $this->json(
            $progress,
            $responseCode,
            [],
            [
                'groups' => [
                    'api_progress_show'
                ]
            ],
        );
    }

    #[Route('/subscribe-newsletter', name: 'app_user_subscribe-newsletter', methods: ['POST'], defaults: ['_format' => 'json'])]
    public function subscribeNewsletter(Request $request, UserInterface $user, EntityManagerInterface $entityManager): JsonResponse
    {
        /** @var User $user */

        //? Checking CSRF Token
        $token = $request->request->get('token');
        $isValidToken = $this->isCsrfTokenValid('subscribe-newsletter'.$user->getId(), $token);
        if (!$isValidToken) {
            return $this->json('Invalid token', Response::HTTP_FORBIDDEN);
        }

        $user->setIsSubscribedNewsletter(true);

        $entityManager->flush();

        return $this->json(
            $user,
            Response::HTTP_PARTIAL_CONTENT,
            [],
            [
                'groups' => [
                    'api_user_show'
                ]
            ],
        );
    }

    #[Route('/unsubscribe-newsletter', name: 'app_user_unsubscribe-newsletter', methods: ['POST'], defaults: ['_format' => 'json'])]
    public function unsubscribeNewsletter(Request $request, UserInterface $user, EntityManagerInterface $entityManager): JsonResponse
    {
        /** @var User $user */

        //? Checking CSRF Token
        $token = $request->request->get('token');
        $isValidToken = $this->isCsrfTokenValid('unsubscribe-newsletter'.$user->getId(), $token);
        if (!$isValidToken) {
            return $this->json('Invalid token', Response::HTTP_FORBIDDEN);
        }

        $user->setIsSubscribedNewsletter(false);

        $entityManager->flush();

        return $this->json(
            $user,
            Response::HTTP_PARTIAL_CONTENT,
            [],
            [
                'groups' => [
                    'api_user_show'
                ]
            ],
        );
    }

    #[Route('/profile/delete', name: 'app_user_delete', methods: ['GET', 'POST'])]
    public function delete(
        Request $request, UserInterface $user, 
        EntityManagerInterface $entityManager, 
        UserPasswordHasherInterface $userPasswordHasher, 
        TokenStorageInterface $tokenStorage
        ): Response
    {
        /** @var User $user */

        $form = $this->createForm(DeleteAccountFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $supposedPassword = $form->get('password')->getData();
            if ($userPasswordHasher->isPasswordValid($user, $supposedPassword)) {
                $entityManager->remove($user);
                $entityManager->flush();
                // Disconnecting user
                $tokenStorage->setToken();

                return $this->redirectToRoute('app_main_home', [], Response::HTTP_SEE_OTHER);
            }

            $this->addFlash('delete_account_error', 'Incorrect password');
        }

        return $this->renderForm('user/delete.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/back-office/user/{limit}/{offset}', name: 'app_user_admin-list', requirements: ['limit' => '\d+', 'offset' => '\d+'])]
    public function adminList(UserRepository $userRepository, int $limit = 10, int $offset = 0): Response
    {
        return $this->render('user/admin_list.html.twig', [
            'users' => $userRepository->findBy([], null, $limit, $offset),
        ]);
    }

    #[Route('/back-office/user/{id}/{action}', name: 'app_user_manage', 
        requirements: ['id' => '\d+', 'action' => '^(reset-picture)|(reset-banner)|(reset-pseudo)|(reset-biography)|(mute)|(unmute)|(edit-rank)|(ban)$'],
        methods: ['POST'], defaults: ['_format' => 'json'])]
    public function manage(Request $request, User $user = null, UserInterface $loggedUser, string $action, EntityManagerInterface $entityManager): JsonResponse
    {
        /** @var User $loggedUser */

        if ($user === null) {
            return $this->json('User not found', Response::HTTP_NOT_FOUND);
        }

        //? Checking CSRF Token
        $token = $request->request->get('token');
        $isValidToken = $this->isCsrfTokenValid('manage'.$user->getId(), $token);
        if (!$isValidToken) {
            return $this->json('Invalid token', Response::HTTP_FORBIDDEN);
        }
        
        //? Checking if the user has a lower role than the logged in user
        $this->denyAccessUnlessGranted('USER_MANAGE', $user);
        
        //? Checking role if action is edit-rank
        if ($action === 'edit-rank') {
            $role = $request->request->get('role');
            $availableRoles = ['ROLE_ADMIN', 'ROLE_MODERATOR', 'ROLE_USER'];

            //? Checking if the role exists
            if (!in_array($role, $availableRoles)) {
                return $this->json('This role does not exist.', Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            //? Checking if the role is lower than the logged in user user has
            $this->denyAccessUnlessGranted('USER_EDIT_RANK', $role);
        }

        //? Edit the user
        switch ($action) {
            case 'reset-picture':
                $user->setPicturePath('0.png');
                break;
            case 'reset-banner':
                $user->setBannerPath('0.png');
                break;
            case 'reset-pseudo':
                $user->setPseudo('Membre #' . $user->getId());
                break;
            case 'reset-biography':
                $user->setBiography(null);
                break;
            case 'mute':
                $user->setIsMuted(true);
                break;
            case 'unmute':
                $user->setIsMuted(false);
                break;
            case 'edit-rank':
                $user->setRoles([$role]);
                break;
            case 'ban':
                $ban = new Ban();
                $ban
                    ->setEmail($user->getEmail())
                    ->setMessage('User (' . $user->getPseudo() . ') banned by ' . $loggedUser->getPseudo() . '.')
                    ->setUser($loggedUser)
                    ;

                $entityManager->persist($ban);
                $entityManager->remove($user);
                break;
        }

        //? Save
        $entityManager->flush();

        if ($action === 'ban') {
            return $this->json(null, Response::HTTP_NO_CONTENT);
        }
        
        return $this->json(
            $user,
            Response::HTTP_PARTIAL_CONTENT,
            [],
            [
                'groups' => [
                    'api_user_show'
                ]
            ],
        );
    }
}
