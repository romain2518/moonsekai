<?php

namespace App\Controller;

use App\Entity\CalendarEvent;
use App\Entity\Movie;
use App\Entity\Work;
use App\Form\MovieType;
use App\Repository\CalendarEventRepository;
use App\Repository\MovieRepository;
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

#[Route('/back-office/work/{work_id}/movie')]
#[Entity('work', expr: 'repository.find(work_id)')]
class MovieController extends AbstractController
{
    #[Route('/{limit}/{offset}', name: 'app_movie_index', requirements: ['limit' => '\d+', 'offset' => '\d+'], methods: ['GET'])]
    public function index(Work $work = null, MovieRepository $movieRepository, int $limit = 20, int $offset = 0): Response
    {
        if (null === $work) {
            throw $this->createNotFoundException('Work not found.');
        }

        return $this->render('movie/index.html.twig', [
            'work' => $work,
            'movies' => $movieRepository->findBy(['work' => $work], ['name' => 'ASC'], $limit, $offset),
        ]);
    }

    #[Route('/add', name: 'app_movie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Work $work = null, EntityManagerInterface $entityManager, UserInterface $user, ValidatorInterface $validator): Response
    {
        if (null === $work) {
            throw $this->createNotFoundException('Work not found.');
        }

        $movie = new Movie();
        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        $eventDateErrors = [];
        if ($form->get('addCalendarEvent')->getData()) {
            $eventDateErrors = $validator->validate($form->get('eventDate')->getData(), [
                new NotBlank(message: 'The event date should not be blank if event checkbox is checked.'),
                new GreaterThan('now', message: 'The event date must be greater than {{ compared_value }}.'),
            ]);
        }

        if ($form->isSubmitted() && $form->isValid() && count($eventDateErrors) === 0) {
            $movie->setUser($user);
            $movie->setWork($work);

            $entityManager->persist($movie);
            $entityManager->flush();

            //? Creating calendar event if needed
            if ($form->get('addCalendarEvent')->getData()) {
                $calendarEvent = new CalendarEvent();

                $calendarEvent
                    ->setTitle($movie->getName())
                    ->setStart($form->get('eventDate')->getData())
                    ->setTargetTable(Movie::class)
                    ->setTargetId($movie->getId())
                    ->setUser($user)
                ;

                $entityManager->persist($calendarEvent);
                $entityManager->flush();
            }

            return $this->redirectToRoute('app_movie_index', ['work_id' => $work->getId()], Response::HTTP_SEE_OTHER);
        }

        foreach ($eventDateErrors as $error) {
            $this->addFlash('form_errors', $error->getMessage());
        }

        return $this->renderForm('movie/new.html.twig', [
            'work' => $work,
            'movie' => $movie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_movie_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, Work $work = null, Movie $movie = null, 
        CalendarEventRepository $calendarEventRepository, EntityManagerInterface $entityManager, 
        UserInterface $user, ValidatorInterface $validator
        ): Response
    {
        if (null === $work) {
            throw $this->createNotFoundException('Work not found.');
        }

        if (null === $movie) {
            throw $this->createNotFoundException('Movie not found.');
        }

        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        $eventDateErrors = [];
        if ($form->get('addCalendarEvent')->getData()) {
            $eventDateErrors = $validator->validate($form->get('eventDate')->getData(), [
                new NotBlank(message: 'The event date should not be blank if event checkbox is checked.'),
                new GreaterThan('now', message: 'The event date must be greater than {{ compared_value }}.'),
            ]);
        }

        if ($form->isSubmitted() && $form->isValid() && count($eventDateErrors) === 0) {
            $movie->setUser($user);
            $movie->setWork($work);

            $entityManager->flush();

            //? Creating calendar event if needed
            if ($form->get('addCalendarEvent')->getData()) {
                //? Searching for existing calendar event
                $calendarEvent = $calendarEventRepository->findOneBy(['targetTable' => Movie::class, 'targetId' => $movie->getId()]);
                if (null === $calendarEvent) {
                    $calendarEvent = new CalendarEvent();
                }

                $calendarEvent
                    ->setTitle($movie->getName())
                    ->setStart($form->get('eventDate')->getData())
                    ->setTargetTable(Movie::class)
                    ->setTargetId($movie->getId())
                    ->setUser($user)
                ;

                $entityManager->persist($calendarEvent);
                $entityManager->flush();
            }

            return $this->redirectToRoute('app_movie_index', ['work_id' => $work->getId()], Response::HTTP_SEE_OTHER);
        }

        foreach ($eventDateErrors as $error) {
            $this->addFlash('form_errors', $error->getMessage());
        }

        return $this->renderForm('movie/edit.html.twig', [
            'work' => $work,
            'movie' => $movie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_movie_delete', methods: ['POST'])]
    public function delete(Request $request, Work $work = null, Movie $movie = null, EntityManagerInterface $entityManager): Response
    {
        if (null === $work) {
            throw $this->createNotFoundException('Work not found.');
        }

        if (null === $movie) {
            throw $this->createNotFoundException('Movie not found.');
        }

        if ($this->isCsrfTokenValid('delete'.$movie->getId(), $request->request->get('_token'))) {
            $entityManager->remove($movie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_movie_index', ['work_id' => $work->getId()], Response::HTTP_SEE_OTHER);
    }
}
