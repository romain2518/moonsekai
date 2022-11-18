<?php

namespace App\Controller;

use App\Entity\CalendarEvent;
use App\Entity\News;
use App\Form\NewsType;
use App\Repository\CalendarEventRepository;
use App\Repository\NewsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
    public function new(Request $request, EntityManagerInterface $entityManager, UserInterface $user, ValidatorInterface $validator): Response
    {
        $news = new News();
        $form = $this->createForm(NewsType::class, $news);
        $form->handleRequest($request);

        $eventDateErrors = [];
        if ($form->get('addCalendarEvent')->getData()) {
            $eventDateErrors = $validator->validate($form->get('eventDate')->getData(), [
                new NotBlank(message: 'The event date should not be blank if event checkbox is checked.'),
                new GreaterThan('now', message: 'The event date must be greater than {{ compared_value }}.'),
            ]);
        }

        if ($form->isSubmitted() && $form->isValid() && count($eventDateErrors) === 0) {
            $news->setUser($user);

            $entityManager->persist($news);
            $entityManager->flush();
            
            //? Creating calendar event if needed
            if ($form->get('addCalendarEvent')->getData()) {
                $calendarEvent = new CalendarEvent();

                $calendarEvent
                    ->setTitle($news->getTitle())
                    ->setStart($form->get('eventDate')->getData())
                    ->setTargetTable(News::class)
                    ->setTargetId($news->getId())
                    ->setUser($user)
                ;

                $entityManager->persist($calendarEvent);
                $entityManager->flush();
            }

            return $this->redirectToRoute('app_news_index', [], Response::HTTP_SEE_OTHER);
        }

        foreach ($eventDateErrors as $error) {
            $this->addFlash('form_errors', $error->getMessage());
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
    public function edit(
        Request $request, News $news = null, CalendarEventRepository $calendarEventRepository, 
        EntityManagerInterface $entityManager, UserInterface $user, ValidatorInterface $validator
        ): Response
    {
        if (null === $news) {
            throw $this->createNotFoundException('News not found.');
        }

        $form = $this->createForm(NewsType::class, $news);
        $form->handleRequest($request);

        $eventDateErrors = [];
        if ($form->get('addCalendarEvent')->getData()) {
            $eventDateErrors = $validator->validate($form->get('eventDate')->getData(), [
                new NotBlank(message: 'The event date should not be blank if event checkbox is checked.'),
                new GreaterThan('now', message: 'The event date must be greater than {{ compared_value }}.'),
            ]);
        }

        if ($form->isSubmitted() && $form->isValid() && count($eventDateErrors) === 0) {
            $news->setUser($user);
            
            $entityManager->flush();

            //? Creating calendar event if needed
            if ($form->get('addCalendarEvent')->getData()) {
                //? Searching for existing calendar event
                $calendarEvent = $calendarEventRepository->findOneBy(['targetTable' => News::class, 'targetId' => $news->getId()]);
                if (null === $calendarEvent) {
                    $calendarEvent = new CalendarEvent();
                }

                $calendarEvent
                    ->setTitle($news->getTitle())
                    ->setStart($form->get('eventDate')->getData())
                    ->setTargetTable(News::class)
                    ->setTargetId($news->getId())
                    ->setUser($user)
                ;

                $entityManager->persist($calendarEvent);
                $entityManager->flush();
            }

            return $this->redirectToRoute('app_news_index', [], Response::HTTP_SEE_OTHER);
        }

        foreach ($eventDateErrors as $error) {
            $this->addFlash('form_errors', $error->getMessage());
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
