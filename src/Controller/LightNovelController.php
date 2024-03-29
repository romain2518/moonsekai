<?php

namespace App\Controller;

use App\Entity\CalendarEvent;
use App\Entity\LightNovel;
use App\Entity\Work;
use App\Form\LightNovelType;
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
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/back-office/work/{work_id}/light-novel')]
#[Entity('work', expr: 'repository.find(work_id)')]
class LightNovelController extends AbstractController
{
    #[Route('/{limit}/{offset}', name: 'app_light-novel_index', requirements: ['limit' => '\d+', 'offset' => '\d+'], methods: ['GET'])]
    public function index(Work $work = null, int $limit = 20, int $offset = 0): Response
    {
        if (null === $work) {
            throw $this->createNotFoundException('Work not found.');
        }

        return $this->render('light_novel/index.html.twig', [
            'work' => $work,
            'light_novels' => $work->getLightNovels()->slice($offset, $limit),
        ]);
    }

    #[Route('/add', name: 'app_light-novel_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Work $work = null, EntityManagerInterface $entityManager, UserInterface $user, ValidatorInterface $validator): Response
    {
        if (null === $work) {
            throw $this->createNotFoundException('Work not found.');
        }

        $lightNovel = new LightNovel();
        $form = $this->createForm(LightNovelType::class, $lightNovel);
        $form->handleRequest($request);

        $eventDateErrors = [];
        if ($form->get('addCalendarEvent')->getData()) {
            $eventDateErrors = $validator->validate($form->get('eventDate')->getData(), [
                new NotBlank(message: 'The event date should not be blank if event checkbox is checked.'),
                new GreaterThan('now', message: 'The event date must be greater than {{ compared_value }}.'),
            ]);
        }

        if ($form->isSubmitted() && $form->isValid() && count($eventDateErrors) === 0) {
            $lightNovel->setUser($user);
            $lightNovel->setWork($work);
            
            $entityManager->persist($lightNovel);
            $entityManager->flush();

            //? Creating calendar event if needed
            if ($form->get('addCalendarEvent')->getData()) {
                $calendarEvent = new CalendarEvent();

                $calendarEvent
                    ->setTitle($lightNovel->getName())
                    ->setStart($form->get('eventDate')->getData())
                    ->setTargetTable(LightNovel::class)
                    ->setTargetId($lightNovel->getId())
                    ->setUser($user)
                ;

                $entityManager->persist($calendarEvent);
                $entityManager->flush();
            }

            return $this->redirectToRoute('app_light-novel_index', ['work_id' => $work->getId()], Response::HTTP_SEE_OTHER);
        }

        foreach ($eventDateErrors as $error) {
            $this->addFlash('form_errors', $error->getMessage());
        }

        return $this->renderForm('light_novel/new.html.twig', [
            'work' => $work,
            'light_novel' => $lightNovel,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_light-novel_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, Work $work = null, LightNovel $lightNovel = null, 
        CalendarEventRepository $calendarEventRepository, EntityManagerInterface $entityManager, 
        UserInterface $user, ValidatorInterface $validator
        ): Response
    {
        if (null === $work) {
            throw $this->createNotFoundException('Work not found.');
        }

        if (null === $lightNovel) {
            throw $this->createNotFoundException('Light novel not found.');
        }

        $form = $this->createForm(LightNovelType::class, $lightNovel);
        $form->handleRequest($request);

        $eventDateErrors = [];
        if ($form->get('addCalendarEvent')->getData()) {
            $eventDateErrors = $validator->validate($form->get('eventDate')->getData(), [
                new NotBlank(message: 'The event date should not be blank if event checkbox is checked.'),
                new GreaterThan('now', message: 'The event date must be greater than {{ compared_value }}.'),
            ]);
        }

        if ($form->isSubmitted() && $form->isValid() && count($eventDateErrors) === 0) {
            $lightNovel->setUser($user);
            $lightNovel->setWork($work);
            
            $entityManager->flush();

            //? Creating calendar event if needed
            if ($form->get('addCalendarEvent')->getData()) {
                //? Searching for existing calendar event
                $calendarEvent = $calendarEventRepository->findOneBy(['targetTable' => LightNovel::class, 'targetId' => $lightNovel->getId()]);
                if (null === $calendarEvent) {
                    $calendarEvent = new CalendarEvent();
                }

                $calendarEvent
                    ->setTitle($lightNovel->getName())
                    ->setStart($form->get('eventDate')->getData())
                    ->setTargetTable(LightNovel::class)
                    ->setTargetId($lightNovel->getId())
                    ->setUser($user)
                ;

                $entityManager->persist($calendarEvent);
                $entityManager->flush();
            }

            return $this->redirectToRoute('app_light-novel_index', ['work_id' => $work->getId()], Response::HTTP_SEE_OTHER);
        }

        foreach ($eventDateErrors as $error) {
            $this->addFlash('form_errors', $error->getMessage());
        }

        return $this->renderForm('light_novel/edit.html.twig', [
            'work' => $work,
            'light_novel' => $lightNovel,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_light-novel_delete', methods: ['POST'])]
    public function delete(Request $request, Work $work = null, LightNovel $lightNovel = null, EntityManagerInterface $entityManager): Response
    {
        if (null === $work) {
            throw $this->createNotFoundException('Work not found.');
        }

        if (null === $lightNovel) {
            throw $this->createNotFoundException('Light novel not found.');
        }

        if ($this->isCsrfTokenValid('delete'.$lightNovel->getId(), $request->request->get('_token'))) {
            $entityManager->remove($lightNovel);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_light-novel_index', ['work_id' => $work->getId()], Response::HTTP_SEE_OTHER);
    }
}
