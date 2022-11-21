<?php

namespace App\Controller;

use App\Entity\CalendarEvent;
use App\Entity\Chapter;
use App\Entity\Manga;
use App\Entity\Volume;
use App\Entity\Work;
use App\Form\VolumeType;
use App\Repository\CalendarEventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/back-office/work/{work_id}/manga/{manga_id}/volume')]
#[Entity('work', expr: 'repository.find(work_id)')]
#[Entity('manga', expr: 'repository.find(manga_id)')]
class VolumeController extends AbstractController
{
    #[Route('/{limit}/{offset}', name: 'app_volume_index', requirements: ['limit' => '\d+', 'offset' => '\d+'], methods: ['GET'])]
    public function index(Work $work = null, Manga $manga = null, int $limit = 20, int $offset = 0): Response
    {
        if (null === $work) {
            throw $this->createNotFoundException('Work not found.');
        }

        if (null === $manga) {
            throw $this->createNotFoundException('Manga not found.');
        }

        return $this->render('volume/index.html.twig', [
            'work' => $work,
            'manga' => $manga,
            'volumes' => $manga->getVolumes()->slice($offset, $limit),
        ]);
    }

    #[Route('/add', name: 'app_volume_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Work $work = null, Manga $manga = null, EntityManagerInterface $entityManager, UserInterface $user, ValidatorInterface $validator): Response
    {
        if (null === $work) {
            throw $this->createNotFoundException('Work not found.');
        }

        if (null === $manga) {
            throw $this->createNotFoundException('Manga not found.');
        }

        $volume = new Volume();
        $form = $this->createForm(VolumeType::class, $volume);
        $form->handleRequest($request);

        $eventDateErrors = new ConstraintViolationList();
        foreach ($form->get('chapters')->all() as $chapter) {
            if ($chapter->get('addCalendarEvent')->getData()) {
                $eventDateErrors->addAll(
                    $validator->validate($chapter->get('eventDate')->getData(), [
                        new NotBlank(message: 'The event date should not be blank if event checkbox is checked.'),
                        new GreaterThan('now', message: 'The event date must be greater than {{ compared_value }}.'),
                    ])
                );
            }
        }

        if ($form->isSubmitted() && $form->isValid() && count($eventDateErrors) === 0) {
            $volume->setUser($user);
            $volume->setManga($manga);

            $entityManager->persist($volume);
            $entityManager->flush();

            //? Creating calendar event if needed
            foreach ($form->get('chapters')->all() as $chapter) {
                if ($chapter->get('addCalendarEvent')->getData()) {
                    $calendarEvent = new CalendarEvent();
    
                    $calendarEvent
                        ->setTitle(
                            $chapter->getData()->getNumber() . 
                            empty($chapter->getData()->getName()) 
                            ? '' : ' : ' . $chapter->getData()->getName()
                            )
                        ->setStart($chapter->get('eventDate')->getData())
                        ->setTargetTable(Chapter::class)
                        ->setTargetId($chapter->getData()->getId())
                        ->setUser($user)
                    ;
    
                    $entityManager->persist($calendarEvent);
                }
            }
            
            $entityManager->flush();

            return $this->redirectToRoute('app_volume_index', ['work_id' => $work->getId(), 'manga_id' => $manga->getId()], Response::HTTP_SEE_OTHER);
        }

        foreach ($eventDateErrors as $error) {
            $this->addFlash('form_errors', $error->getMessage());
        }

        return $this->renderForm('volume/new.html.twig', [
            'work' => $work,
            'manga' => $manga,
            'volume' => $volume,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_volume_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, Work $work = null, Manga $manga = null, 
        Volume $volume = null, CalendarEventRepository $calendarEventRepository,
        EntityManagerInterface $entityManager, UserInterface $user, ValidatorInterface $validator
        ): Response
    {
        if (null === $work) {
            throw $this->createNotFoundException('Work not found.');
        }

        if (null === $manga) {
            throw $this->createNotFoundException('Manga not found.');
        }

        if (null === $volume) {
            throw $this->createNotFoundException('Volume not found.');
        }

        $form = $this->createForm(VolumeType::class, $volume);
        $form->handleRequest($request);

        $eventDateErrors = new ConstraintViolationList();
        foreach ($form->get('chapters')->all() as $chapter) {
            if ($chapter->get('addCalendarEvent')->getData()) {
                $eventDateErrors->addAll(
                    $validator->validate($chapter->get('eventDate')->getData(), [
                        new NotBlank(message: 'The event date should not be blank if event checkbox is checked.'),
                        new GreaterThan('now', message: 'The event date must be greater than {{ compared_value }}.'),
                    ])
                );
            }
        }

        if ($form->isSubmitted() && $form->isValid() && count($eventDateErrors) === 0) {
            $volume->setUser($user);
            $volume->setManga($manga);

            $entityManager->flush();

            //? Creating calendar event if needed
            foreach ($form->get('chapters')->all() as $chapter) {
                if ($chapter->get('addCalendarEvent')->getData()) {
                    //? Searching for existing calendar event
                    $calendarEvent = $calendarEventRepository->findOneBy(['targetTable' => Chapter::class, 'targetId' => $chapter->getData()->getId()]);
                    if (null === $calendarEvent) {
                        $calendarEvent = new CalendarEvent();
                    }
    
                    $calendarEvent
                        ->setTitle(
                            $chapter->getData()->getNumber() . 
                            (empty($chapter->getData()->getName()) 
                            ? '' : ' : ' . $chapter->getData()->getName())
                        )
                        ->setStart($chapter->get('eventDate')->getData())
                        ->setTargetTable(Chapter::class)
                        ->setTargetId($chapter->getData()->getId())
                        ->setUser($user)
                    ;
    
                    $entityManager->persist($calendarEvent);
                }
            }
            
            $entityManager->flush();

            return $this->redirectToRoute('app_volume_index', ['work_id' => $work->getId(), 'manga_id' => $manga->getId()], Response::HTTP_SEE_OTHER);
        }

        foreach ($eventDateErrors as $error) {
            $this->addFlash('form_errors', $error->getMessage());
        }

        return $this->renderForm('volume/edit.html.twig', [
            'work' => $work,
            'manga' => $manga,
            'volume' => $volume,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_volume_delete', methods: ['POST'])]
    public function delete(Request $request, Work $work = null, Manga $manga = null, Volume $volume = null, EntityManagerInterface $entityManager): Response
    {
        if (null === $work) {
            throw $this->createNotFoundException('Work not found.');
        }

        if (null === $manga) {
            throw $this->createNotFoundException('Manga not found.');
        }

        if (null === $volume) {
            throw $this->createNotFoundException('Volume not found.');
        }

        if ($this->isCsrfTokenValid('delete'.$volume->getId(), $request->request->get('_token'))) {
            $entityManager->remove($volume);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_volume_index', ['work_id' => $work->getId(), 'manga_id' => $manga->getId()], Response::HTTP_SEE_OTHER);
    }
}
