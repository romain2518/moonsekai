<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/message')]
class MessageController extends AbstractController
{
    /**
     * Display list of conversations
     */
    #[Route('', name: 'app_message_index', methods: ['GET'])]
    public function index(UserRepository $userRepository, UserInterface $user): Response
    {
        /** @var User $user */
        return $this->render('message/index.html.twig', [
            'users' => $userRepository->findConversationsUsersByUser($user),
            'form' => $this->createForm(MessageType::class)->createView(),
            // 'form' => $this->createForm(MessageType::class, null, ['userSenderId' => $user->getId()])->createView(),
        ]);
    }
    
    /**
     * Display messages of one conversation
     */
    #[Route('/{user_receiver_id}/{limit}/{offset}', name: 'app_message_show',
        requirements: ['limit' => '\d+', 'offset' => '\d+'], methods: ['GET'], defaults: ['_format' => 'json'])]
    #[Entity('userReceiver', expr: 'repository.find(user_receiver_id)')]
    public function show(MessageRepository $messageRepository, User $userReceiver = null, UserInterface $user, int $limit = 20, int $offset = 0): JsonResponse
    {
        if (null === $userReceiver) {
            throw $this->createNotFoundException('Receiver not found.');
        }

        return $this->json(
            $messageRepository->findByUsers($user, $userReceiver, $limit, $offset),
            Response::HTTP_OK,
            [],
            [
                'groups' => [
                    'api_message_list'
                ]
            ]
        );
    }

    #[Route('/{user_receiver_id}/add', name: 'app_message_new', methods: ['POST'], defaults: ['_format' => 'json'])]
    #[Entity('userReceiver', expr: 'repository.find(user_receiver_id)')]
    public function new(Request $request, EntityManagerInterface $entityManager, User $userReceiver = null, UserInterface $user): JsonResponse
    {
        if (null === $userReceiver) {
            throw $this->createNotFoundException('Receiver not found.');
        }

        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        //? Checking if form is submitted
        if (!$form->isSubmitted()) {
            return $this->json('Bad request', Response::HTTP_BAD_REQUEST);
        }

        //? Checking if form is valid
        if (!$form->isValid()) {
            $errors = [];
            foreach ($form->getErrors(true) as $error) {
                $errors[] = 'ERROR: ' . $error->getMessage();
            }
            return $this->json($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        //? Checking if user is not muted
        $this->denyAccessUnlessGranted('USER_NOT_MUTED');

        //? Setting and saving message
        $message->setUserSender($user);
        $message->setUserReceiver($userReceiver);
        
        $entityManager->persist($message);
        $entityManager->flush();
        
        return $this->json(
            $message,
            Response::HTTP_CREATED,
            [],
            [
                'groups' => [
                    'api_message_show'
                ]
            ]
        );
    }

    #[Route('/{id}/edit', name: 'app_message_edit', methods: ['POST'], defaults: ['_format' => 'json'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, Message $message = null,): JsonResponse
    {
        if (null === $message) {
            throw $this->createNotFoundException('Message not found.');
        }

        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        //? Checking if form is submitted
        if (!$form->isSubmitted()) {
            return $this->json('Bad request', Response::HTTP_BAD_REQUEST);
        }

        //? Checking if form is valid
        if (!$form->isValid()) {
            $errors = [];
            foreach ($form->getErrors(true) as $error) {
                $errors[] = 'ERROR: ' . $error->getMessage();
            }
            return $this->json($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        //? Checking if user is not muted and if user is the message sender
        $this->denyAccessUnlessGranted('USER_NOT_MUTED');
        $this->denyAccessUnlessGranted('MESSAGE_SENDER', $message);

        $entityManager->flush();
        
        return $this->json(
            $message,
            Response::HTTP_PARTIAL_CONTENT,
            [],
            [
                'groups' => [
                    'api_message_show'
                ]
            ]
        );
    }

    #[Route('/{id}/delete', name: 'app_message_delete', methods: ['POST'], defaults: ['_format' => 'json'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, Message $message = null): JsonResponse
    {
        if (null === $message) {
            throw $this->createNotFoundException('Message not found.');
        }

        //? Checking if logged in user is the message sender
        $this->denyAccessUnlessGranted('MESSAGE_SENDER', $message);

        //? Checking CSRF Token
        $token = $request->request->get('_token');
        $isValidToken = $this->isCsrfTokenValid('delete', $token);
        if (!$isValidToken) {
            return $this->json('Invalid token', Response::HTTP_FORBIDDEN);
        }

        $entityManager->remove($message);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/{user_receiver_id}/mark-as-read', name: 'app_message_mark-conversation-as-read', methods: ['POST'], defaults: ['_format' => 'json'])]
    #[Entity('userReceiver', expr: 'repository.find(user_receiver_id)')]
    public function markConversationAsRead(
        Request $request, EntityManagerInterface $entityManager, 
        MessageRepository $messageRepository, User $userReceiver = null, 
        UserInterface $user
        ): JsonResponse
    {
        if (null === $userReceiver) {
            throw $this->createNotFoundException('Receiver not found.');
        }

        //? Checking CSRF Token
        $token = $request->request->get('token');
        $isValidToken = $this->isCsrfTokenValid('mark conversation as read', $token);
        if (!$isValidToken) {
            return $this->json('Invalid token', Response::HTTP_FORBIDDEN);
        }

        //? Getting & editing messages (targeted messages only are the ones sended by the other user, here $userReceiver)
        $messages = $messageRepository->findBy(['userSender' => $userReceiver, 'userReceiver' => $user, 'isRead' => false]);
        foreach ($messages as $message) {
            $message->setIsRead(true);
        }

        //? Save
        $entityManager->flush();
        
        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/{id}/mark-as-unread', name: 'app_message_mark-message-as-unread', methods: ['PÖST'], defaults: ['_format' => 'json'])]
    public function markMessageAsUnread(Request $request, EntityManagerInterface $entityManager, Message $message = null): JsonResponse
    {
        if (null === $message) {
            throw $this->createNotFoundException('Message not found.');
        }

        //? Checking CSRF Token
        $token = $request->request->get('token');
        $isValidToken = $this->isCsrfTokenValid('mark message as unread', $token);
        if (!$isValidToken) {
            return $this->json('Invalid token', Response::HTTP_FORBIDDEN);
        }

        //? Checking if logged in user is the message receiver
        $this->denyAccessUnlessGranted('MESSAGE_RECEIVER', $message);

        //? Editing message
        $message->setIsRead(false);

        //? Save
        $entityManager->flush();
        
        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
