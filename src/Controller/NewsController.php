<?php

namespace App\Controller;

use App\Entity\News;
use App\Form\NewsType;
use App\Repository\NewsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class NewsController extends AbstractController
{
    #[Route('/back-office/news/{limit}/{offset}', name: 'app_news_index', requirements: ['limit' => '\d+', 'offset' => '\d+'], methods: ['GET'])]
    public function index(NewsRepository $newsRepository, int $limit = 20, int $offset = 0): Response
    {
        return $this->render('news/index.html.twig', [
            'news' => $newsRepository->findBy([], ['title' => 'ASC'], $limit, $offset),
        ]);
    }

    #[Route('/back-office/news/add', name: 'app_news_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserInterface $user): Response
    {
        $news = new News();
        $form = $this->createForm(NewsType::class, $news);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $news->setUser($user);
            
            $entityManager->persist($news);
            $entityManager->flush();

            return $this->redirectToRoute('app_news_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('news/new.html.twig', [
            'news' => $news,
            'form' => $form,
        ]);
    }

    #[Route('news/{id}', name: 'app_news_show', methods: ['GET'])]
    public function show(News $news = null): Response
    {
        if (null === $news) {
            throw $this->createNotFoundException('News not found.');
        }

        return $this->render('news/show.html.twig', [
            'news' => $news,
        ]);
    }

    #[Route('/back-office/news/{id}/edit', name: 'app_news_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, News $news = null, EntityManagerInterface $entityManager, UserInterface $user): Response
    {
        if (null === $news) {
            throw $this->createNotFoundException('News not found.');
        }

        $form = $this->createForm(NewsType::class, $news);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $news->setUser($user);
            
            $entityManager->flush();

            return $this->redirectToRoute('app_news_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('news/edit.html.twig', [
            'news' => $news,
            'form' => $form,
        ]);
    }

    #[Route('/back-office/news/{id}/delete', name: 'app_news_delete', methods: ['POST'])]
    public function delete(Request $request, News $news = null, EntityManagerInterface $entityManager): Response
    {
        if (null === $news) {
            throw $this->createNotFoundException('News not found.');
        }

        if ($this->isCsrfTokenValid('delete'.$news->getId(), $request->request->get('_token'))) {
            $entityManager->remove($news);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_news_index', [], Response::HTTP_SEE_OTHER);
    }
}
