<?php

namespace App\Controller;

use App\Entity\Anime;
use App\Entity\CalendarEvent;
use App\Entity\Episode;
use App\Entity\Season;
use App\Entity\Work;
use App\Form\EpisodeType;
use App\Repository\CalendarEventRepository;
use App\Repository\EpisodeRepository;
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

#[Route('/back-office/work/{work_id}/anime/{anime_id}/season/{season_id}/episode')]
#[Entity('work', expr: 'repository.find(work_id)')]
#[Entity('anime', expr: 'repository.find(anime_id)')]
#[Entity('season', expr: 'repository.find(season_id)')]
class EpisodeController extends AbstractController
{
    #[Route('/{limit}/{offset}', name: 'app_episode_index', requirements: ['limit' => '\d+', 'offset' => '\d+'], methods: ['GET'])]
    public function index(Work $work = null, Anime $anime = null, Season $season = null, EpisodeRepository $episodeRepository, int $limit = 20, int $offset = 0): Response
    {
        if (null === $work) {
            throw $this->createNotFoundException('Work not found.');
        }

        if (null === $anime) {
            throw $this->createNotFoundException('Anime not found.');
        }

        if (null === $season) {
            throw $this->createNotFoundException('Season not found.');
        }
        
        return $this->render('episode/index.html.twig', [
            'work' => $work,
            'anime' => $anime,
            'season' => $season,
            'episodes' => $episodeRepository->findBySeasonOrderedByCastedName($season, $limit, $offset),
        ]);
    }

    #[Route('/add', name: 'app_episode_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request, Work $work = null, Anime $anime = null, 
        Season $season = null, EntityManagerInterface $entityManager, 
        UserInterface $user, ValidatorInterface $validator
        ): Response
    {
        if (null === $work) {
            throw $this->createNotFoundException('Work not found.');
        }

        if (null === $anime) {
            throw $this->createNotFoundException('Anime not found.');
        }

        if (null === $season) {
            throw $this->createNotFoundException('Season not found.');
        }
        
        $episode = new Episode();
        $form = $this->createForm(EpisodeType::class, $episode);
        $form->handleRequest($request);

        $eventDateErrors = [];
        if ($form->get('addCalendarEvent')->getData()) {
            $eventDateErrors = $validator->validate($form->get('eventDate')->getData(), [
                new NotBlank(message: 'The event date should not be blank if event checkbox is checked.'),
                new GreaterThan('now', message: 'The event date must be greater than {{ compared_value }}.'),
            ]);
        }

        if ($form->isSubmitted() && $form->isValid() && count($eventDateErrors) === 0) {
            $episode->setUser($user);
            $episode->setSeason($season);

            $entityManager->persist($episode);
            $entityManager->flush();

            //? Creating calendar event if needed
            if ($form->get('addCalendarEvent')->getData()) {
                $calendarEvent = new CalendarEvent();

                $calendarEvent
                    ->setTitle($episode->getNumber() . (empty($episode->getName()) ? '' : ' : '.$episode->getName()))
                    ->setStart($form->get('eventDate')->getData())
                    ->setTargetTable(Episode::class)
                    ->setTargetId($episode->getId())
                    ->setUser($user)
                ;

                $entityManager->persist($calendarEvent);
                $entityManager->flush();
            }

            return $this->redirectToRoute('app_episode_index', ['work_id' => $work->getId(), 'anime_id' => $anime->getId(), 'season_id' => $season->getId()], Response::HTTP_SEE_OTHER);
        }

        foreach ($eventDateErrors as $error) {
            $this->addFlash('form_errors', $error->getMessage());
        }

        return $this->renderForm('episode/new.html.twig', [
            'work' => $work,
            'anime' => $anime,
            'season' => $season,
            'episode' => $episode,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_episode_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, Work $work = null, Anime $anime = null, Season $season = null, 
        Episode $episode = null, CalendarEventRepository $calendarEventRepository,
        EntityManagerInterface $entityManager, UserInterface $user, 
        ValidatorInterface $validator
        ): Response
    {
        if (null === $work) {
            throw $this->createNotFoundException('Work not found.');
        }

        if (null === $anime) {
            throw $this->createNotFoundException('Anime not found.');
        }

        if (null === $season) {
            throw $this->createNotFoundException('Season not found.');
        }
        
        if (null === $episode) {
            throw $this->createNotFoundException('Episode not found.');
        }

        $form = $this->createForm(EpisodeType::class, $episode);
        $form->handleRequest($request);

        $eventDateErrors = [];
        if ($form->get('addCalendarEvent')->getData()) {
            $eventDateErrors = $validator->validate($form->get('eventDate')->getData(), [
                new NotBlank(message: 'The event date should not be blank if event checkbox is checked.'),
                new GreaterThan('now', message: 'The event date must be greater than {{ compared_value }}.'),
            ]);
        }

        if ($form->isSubmitted() && $form->isValid() && count($eventDateErrors) === 0) {
            $episode->setUser($user);
            $episode->setSeason($season);
            
            $entityManager->flush();

            //? Creating calendar event if needed
            if ($form->get('addCalendarEvent')->getData()) {
                //? Searching for existing calendar event
                $calendarEvent = $calendarEventRepository->findOneBy(['targetTable' => Episode::class, 'targetId' => $episode->getId()]);
                if (null === $calendarEvent) {
                    $calendarEvent = new CalendarEvent();
                }

                $calendarEvent
                    ->setTitle($episode->getNumber() . (empty($episode->getName()) ? '' : ' : '.$episode->getName()))
                    ->setStart($form->get('eventDate')->getData())
                    ->setTargetTable(Episode::class)
                    ->setTargetId($episode->getId())
                    ->setUser($user)
                ;

                $entityManager->persist($calendarEvent);
                $entityManager->flush();
            }

            return $this->redirectToRoute('app_episode_index', ['work_id' => $work->getId(), 'anime_id' => $anime->getId(), 'season_id' => $season->getId()], Response::HTTP_SEE_OTHER);
        }

        foreach ($eventDateErrors as $error) {
            $this->addFlash('form_errors', $error->getMessage());
        }

        return $this->renderForm('episode/edit.html.twig', [
            'work' => $work,
            'anime' => $anime,
            'season' => $season,
            'episode' => $episode,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_episode_delete', methods: ['POST'])]
    public function delete(Request $request, Work $work = null, Anime $anime = null, Season $season = null, Episode $episode = null, EntityManagerInterface $entityManager): Response
    {
        if (null === $work) {
            throw $this->createNotFoundException('Work not found.');
        }

        if (null === $anime) {
            throw $this->createNotFoundException('Anime not found.');
        }

        if (null === $season) {
            throw $this->createNotFoundException('Season not found.');
        }
        
        if (null === $episode) {
            throw $this->createNotFoundException('Episode not found.');
        }

        if ($this->isCsrfTokenValid('delete'.$episode->getId(), $request->request->get('_token'))) {
            $entityManager->remove($episode);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_episode_index', ['work_id' => $work->getId(), 'anime_id' => $anime->getId(), 'season_id' => $season->getId()], Response::HTTP_SEE_OTHER);
    }
}
