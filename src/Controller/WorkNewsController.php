<?php

namespace App\Controller;

use App\Entity\CalendarEvent;
use App\Entity\Work;
use App\Entity\WorkNews;
use App\Form\WorkNewsType;
use App\Repository\CalendarEventRepository;
use App\Repository\WorkNewsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/back-office/work/{work_id}/news')]
#[Entity('work', expr: 'repository.find(work_id)')]
class WorkNewsController extends AbstractController
{
    #[Route('/{limit}/{offset}', name: 'app_work-news_index', requirements: ['limit' => '\d+', 'offset' => '\d+'], methods: ['GET'])]
    public function index(Work $work = null, WorkNewsRepository $workNewsRepository, int $limit = 20, int $offset = 0): Response
    {
        if (null === $work) {
            throw $this->createNotFoundException('Work not found.');
        }

        return $this->render('work_news/index.html.twig', [
            'work' => $work,
            'work_news' => $workNewsRepository->findBy(['work' => $work], ['title' => 'ASC'], $limit, $offset),
        ]);
    }

    #[Route('/add', name: 'app_work-news_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Work $work = null, EntityManagerInterface $entityManager, UserInterface $user, ValidatorInterface $validator): Response
    {
        if (null === $work) {
            throw $this->createNotFoundException('Work not found.');
        }

        $workNews = new WorkNews();
        $form = $this->createForm(WorkNewsType::class, $workNews);
        $form->handleRequest($request);

        $eventDateErrors = [];
        if ($form->get('addCalendarEvent')->getData()) {
            $eventDateErrors = $validator->validate($form->get('eventDate')->getData(), [
                new NotBlank(message: 'The event date should not be blank if event checkbox is checked.'),
                new GreaterThan('now', message: 'The event date must be greater than {{ compared_value }}.'),
            ]);
        }

        if ($form->isSubmitted() && $form->isValid() && count($eventDateErrors) === 0) {
            $workNews->setUser($user);
            $workNews->setWork($work);

            $entityManager->persist($workNews);
            $entityManager->flush();

            //? Creating calendar event if needed
            if ($form->get('addCalendarEvent')->getData()) {
                $calendarEvent = new CalendarEvent();

                $calendarEvent
                    ->setTitle($workNews->getTitle())
                    ->setStart($form->get('eventDate')->getData())
                    ->setTargetTable(WorkNews::class)
                    ->setTargetId($workNews->getId())
                    ->setUser($user)
                ;

                $entityManager->persist($calendarEvent);
                $entityManager->flush();
            }

            return $this->redirectToRoute('app_work-news_index', ['work_id' => $work->getId()], Response::HTTP_SEE_OTHER);
        }

        foreach ($eventDateErrors as $error) {
            $this->addFlash('form_errors', $error->getMessage());
        }

        return $this->renderForm('work_news/new.html.twig', [
            'work' => $work,
            'work_news' => $workNews,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_work-news_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, Work $work = null, WorkNews $workNews = null, 
        CalendarEventRepository $calendarEventRepository, EntityManagerInterface $entityManager, 
        UserInterface $user, ValidatorInterface $validator
        ): Response
    {
        if (null === $work) {
            throw $this->createNotFoundException('Work not found.');
        }

        if (null === $workNews) {
            throw $this->createNotFoundException('Work news not found.');
        }

        $form = $this->createForm(WorkNewsType::class, $workNews);
        $form->handleRequest($request);

        $eventDateErrors = [];
        if ($form->get('addCalendarEvent')->getData()) {
            $eventDateErrors = $validator->validate($form->get('eventDate')->getData(), [
                new NotBlank(message: 'The event date should not be blank if event checkbox is checked.'),
                new GreaterThan('now', message: 'The event date must be greater than {{ compared_value }}.'),
            ]);
        }

        if ($form->isSubmitted() && $form->isValid() && count($eventDateErrors) === 0) {
            $workNews->setUser($user);
            $workNews->setWork($work);

            $entityManager->flush();

            //? Creating calendar event if needed
            if ($form->get('addCalendarEvent')->getData()) {
                //? Searching for existing calendar event
                $calendarEvent = $calendarEventRepository->findOneBy(['targetTable' => WorkNews::class, 'targetId' => $workNews->getId()]);
                if (null === $calendarEvent) {
                    $calendarEvent = new CalendarEvent();
                }

                $calendarEvent
                    ->setTitle($workNews->getTitle())
                    ->setStart($form->get('eventDate')->getData())
                    ->setTargetTable(WorkNews::class)
                    ->setTargetId($workNews->getId())
                    ->setUser($user)
                ;

                $entityManager->persist($calendarEvent);
                $entityManager->flush();
            }

            return $this->redirectToRoute('app_work-news_index', ['work_id' => $work->getId()], Response::HTTP_SEE_OTHER);
        }

        foreach ($eventDateErrors as $error) {
            $this->addFlash('form_errors', $error->getMessage());
        }

        return $this->renderForm('work_news/edit.html.twig', [
            'work' => $work,
            'work_news' => $workNews,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_work-news_delete', methods: ['POST'])]
    public function delete(Request $request, Work $work = null, WorkNews $workNews = null, EntityManagerInterface $entityManager): Response
    {
        if (null === $work) {
            throw $this->createNotFoundException('Work not found.');
        }

        if (null === $workNews) {
            throw $this->createNotFoundException('Work news not found.');
        }

        if ($this->isCsrfTokenValid('delete'.$workNews->getId(), $request->request->get('_token'))) {
            $entityManager->remove($workNews);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_work-news_index', ['work_id' => $work->getId()], Response::HTTP_SEE_OTHER);
    }
}
