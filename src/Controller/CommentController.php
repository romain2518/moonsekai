<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class CommentController extends AbstractController
{
    #[Route('/back-office/{targetType}/{targetId}/comment/{limit}/{offset}', name: 'app_comment_index', 
        requirements: [
            'targetType' => '^(anime)|(light-novel)|(manga)|(movie)|(news)|(user)|(work-news)$', 
            'targetId' => '\d+', 
            'limit' => '\d+', 
            'offset' => '\d+'
        ], methods: ['GET']
    )]
    public function index(string $targetType, int $targetId, EntityManagerInterface $entityManager, CommentRepository $commentRepository, int $limit = 20, int $offset = 0): Response
    {
        //? Checking if target exists
        $target = $entityManager->getRepository(Comment::getTargetTables()[$targetType])->find($targetId);
        if (null === $target) {
            throw $this->createNotFoundException(ucfirst($targetType) . ' not found.');
        }

        switch ($targetType) {
            case 'anime':
            case 'light-novel':
            case 'manga':
            case 'movie':
                $targetName = $target->getName();
                $targetUrl = $this->generateUrl("app_work_show", ['id' => $target->getWork()->getId()]);
                break;
            case 'work-news':
                $targetName = $target->getTitle();
                $targetUrl = $this->generateUrl("app_work_show", ['id' => $target->getWork()->getId()]);
                break;
            case 'news':
                $targetName = $target->getTitle();
                $targetUrl = $this->generateUrl("app_news_show", ['id' => $target->getId()]);
                break;
            case 'user':
                $targetName = $target->getPseudo();
                $targetUrl = $this->generateUrl("app_user_profile", ['id' => $target->getId()]);
                break;
        }

        return $this->render('comment/index.html.twig', [
            'comments' => $commentRepository->findBy(
                ['targetTable' => Comment::getTargetTables()[$targetType], 'targetId' => $targetId, 'parent' => null], 
                ['createdAt' => 'DESC'],
                $limit,
                $offset
            ),
            'targetName' => $targetName,
            'targetUrl' => $targetUrl,
        ]);
    }

    #[Route('/comment/add', name: 'app_comment_new', methods: ['POST'], defaults: ['_format' => 'json'])]
    public function new(Request $request, CommentRepository $commentRepository, EntityManagerInterface $entityManager, UserInterface $user): JsonResponse
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
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

        //? Checking if target exists
        if (!in_array($form->get('targetTable')->getData(), Comment::getTargetTables())) {
            return $this->json('Bad request', Response::HTTP_BAD_REQUEST);
        }
        
        $target = $entityManager->getRepository($form->get('targetTable')->getData())->find(intval($form->get('targetId')->getData()));
        if (null === $target) {
            return $this->json(ucfirst(array_search($form->get('targetTable')->getData(), Comment::getTargetTables())) . ' not found.', Response::HTTP_NOT_FOUND);
        }

        $comment
            ->setTargetTable($form->get('targetTable')->getData())
            ->setTargetId(intval($form->get('targetId')->getData()))
        ;

        //? Checking for parent
        $parentComment = $commentRepository->find(intval($form->get('parentId')->getData()));
        if (
            null !== $parentComment
            && $parentComment->getTargetTable() === $form->get('targetTable')->getData()
            && $parentComment->getTargetId() === intval($form->get('targetId')->getData())
            && null === $parentComment->getParent()
            ) {
            $comment->setParent($parentComment);
        }

        $comment->setUser($user);

        $entityManager->persist($comment);
        $entityManager->flush();

        return $this->json(
            $comment, 
            Response::HTTP_CREATED,
            [],
            [
                'groups' => [
                    'api_comment_show'
                ],
                'enable_max_depth' => true,
                'circular_reference_limit' => 2,
            ]
        );
    }

    #[Route('/comment/{id}/edit', name: 'app_comment_edit', methods: ['POST'], defaults: ['_format' => 'json'])]
    public function edit(Request $request, Comment $comment = null, EntityManagerInterface $entityManager, UserInterface $user): JsonResponse
    {
        if (null === $comment) {
            $this->json('Comment not found.', Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(CommentType::class, $comment);
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

        //? Checking if user is not muted and if logged in user is the author of the comment
        $this->denyAccessUnlessGranted('USER_NOT_MUTED');
        $this->denyAccessUnlessGranted('COMMENT_EDIT', $comment);

        $comment->setUser($user);

        $entityManager->flush();

        return $this->json(
            $comment, 
            Response::HTTP_PARTIAL_CONTENT,
            [],
            [
                'groups' => [
                    'api_comment_show'
                ],
                'enable_max_depth' => true,
                'circular_reference_limit' => 2,
            ]
        );
    }

    #[Route('/comment/{id}/delete', name: 'app_comment_delete', methods: ['POST'], defaults: ['_format' => 'json'])]
    public function delete(Request $request, Comment $comment = null, EntityManagerInterface $entityManager): JsonResponse
    {
        if (null === $comment) {
            return $this->json('Comment not found.', Response::HTTP_NOT_FOUND);
        }

        //? Checking if logged in user is the author of the comment or a moderator
        $this->denyAccessUnlessGranted('COMMENT_DELETE', $comment);

        //? Checking CSRF Token
        $token = $request->request->get('_token');
        $isValidToken = $this->isCsrfTokenValid('delete'.$comment->getId(), $token);
        if (!$isValidToken) {
            return $this->json('Invalid token', Response::HTTP_FORBIDDEN);
        }

        $entityManager->remove($comment);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
