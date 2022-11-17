<?php

namespace App\Controller;

use App\Entity\CalendarEvent;
use App\Entity\Chapter;
use App\Entity\Episode;
use App\Entity\LightNovel;
use App\Entity\Movie;
use App\Entity\News;
use App\Entity\WorkNews;
use App\Form\CalendarEventType;
use App\Repository\CalendarEventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class CalendarEventController extends AbstractController
{
    #[Route('/back-office/calendar/{limit}/{offset}', name: 'app_calendar-event_index', requirements: ['limit' => '\d+', 'offset' => '\d+'], methods: ['GET'])]
    public function index(CalendarEventRepository $calendarEventRepository, int $limit = 20, int $offset = 0): Response
    {
        return $this->render('calendar_event/index.html.twig', [
            'calendar_events' => $calendarEventRepository->findAllWithTarget($limit, $offset),
        ]);
    }

    #[Route('/back-office/calendar/add', name: 'app_calendar-event_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserInterface $user): Response
    {
        $calendarEvent = new CalendarEvent();
        $form = $this->createForm(CalendarEventType::class, $calendarEvent);
        $form->handleRequest($request);

        //? As target id is unmapped it needs to be handled manually
        if ($form->isSubmitted()) {
            $targetId = $request->request->all()['calendar_event']['targetId'] ?? null;
            $targetId = null !== $targetId ? intval($targetId) : null; // we make sure that $targetId is an integer
            
            $form->get('targetId')->submit($targetId);
            $calendarEvent->setTargetId($targetId);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            //? Form validated the target table but we still need to check the target id
            $target = $entityManager->getRepository($calendarEvent->getTargetTable())->find($calendarEvent->getTargetId());
            if (null !== $target) {
                $calendarEvent->setUser($user);
                
                $entityManager->persist($calendarEvent);
                $entityManager->flush();
                
                return $this->redirectToRoute('app_calendar-event_index', [], Response::HTTP_SEE_OTHER);
            }

            $this->addFlash('form_errors', 'The selected target is invalid.');
        }

        return $this->renderForm('calendar_event/new.html.twig', [
            'calendar_event' => $calendarEvent,
            'form' => $form,
        ]);
    }

    #[Route('/back-office/calendar/{id}/edit', name: 'app_calendar-event_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CalendarEvent $calendarEvent = null, EntityManagerInterface $entityManager, UserInterface $user, EventDispatcherInterface $dispatcher): Response
    {
        if (null === $calendarEvent) {
            throw $this->createNotFoundException('Calendar event not found.');
        }

        //? Work & targetId are unmapped field, we need to give default params to the form
        $targetId = $request->request->all()['calendar_event']['targetId'] ?? null;
        $targetId = null !== $targetId ? intval($targetId) : null; // we make sure that $targetId is an integer
        $work = null;

        //* If no target id has been sent or if it is still the same as before, we can use object values
        if (null === $targetId || $targetId === $calendarEvent->getTargetId()) {
            $targetId = $calendarEvent->getTargetId();

            $target = $entityManager->getRepository($calendarEvent->getTargetTable())->find($calendarEvent->getTargetId());
            switch ($target::class) {
                case News::class:
                    $work = null;
                    break;
                case Movie::class:
                case LightNovel::class:
                case WorkNews::class:
                    $work = $target->getWork();
                    break;
                case Chapter::class:
                    $work = $target->getVolume()->getManga()->getWork();
                    break;
                case Episode::class:
                    $work = $target->getSeason()->getAnime()->getWork();
                    break;
            }
        }
        //* Else, a value has been passed, we can just relay it to the form
        
        $form = $this->createForm(CalendarEventType::class, $calendarEvent, [
            'work' => $work,
            'targetId' => $targetId,
        ]);
        $form->handleRequest($request);

        //? As target id is unmapped it needs to be handled manually
        if ($form->isSubmitted()) {
            $form->get('targetId')->submit($targetId);
            $calendarEvent->setTargetId($targetId);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            //? Form validates the target table but we still need to check the target id
            $target = $entityManager->getRepository($calendarEvent->getTargetTable())->find($calendarEvent->getTargetId());
            if (null !== $target) {
                $calendarEvent->setUser($user);

                $entityManager->flush();

                return $this->redirectToRoute('app_calendar-event_index', [], Response::HTTP_SEE_OTHER);
            }

            $this->addFlash('form_errors', 'The selected target is invalid.');
        }

        return $this->renderForm('calendar_event/edit.html.twig', [
            'calendar_event' => $calendarEvent,
            'form' => $form,
        ]);
    }

    #[Route('/back-office/calendar/{id}/delete', name: 'app_calendar-event_delete', methods: ['POST'])]
    public function delete(Request $request, CalendarEvent $calendarEvent = null, EntityManagerInterface $entityManager): Response
    {
        if (null === $calendarEvent) {
            throw $this->createNotFoundException('Calendar event not found.');
        }

        if ($this->isCsrfTokenValid('delete'.$calendarEvent->getId(), $request->request->get('_token'))) {
            $entityManager->remove($calendarEvent);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_calendar-event_index', [], Response::HTTP_SEE_OTHER);
    }
}
