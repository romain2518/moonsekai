<?php

namespace App\Controller;

use App\Entity\Anime;
use App\Entity\Season;
use App\Entity\Work;
use App\Form\SeasonType;
use App\Repository\SeasonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/back-office/work/{work_id}/anime/{anime_id}/season')]
#[Entity('work', expr: 'repository.find(work_id)')]
#[Entity('anime', expr: 'repository.find(anime_id)')]
class SeasonController extends AbstractController
{
    #[Route('/{limit}/{offset}', name: 'app_season_index', requirements: ['limit' => '\d+', 'offset' => '\d+'], methods: ['GET'])]
    public function index(Work $work = null, Anime $anime = null, SeasonRepository $seasonRepository, int $limit = 20, int $offset = 0): Response
    {
        if (null === $work) {
            throw $this->createNotFoundException('Work not found.');
        }

        if (null === $anime) {
            throw $this->createNotFoundException('Anime not found.');
        }

        return $this->render('season/index.html.twig', [
            'work' => $work,
            'anime' => $anime,
            'seasons' => $seasonRepository->findBy(['anime' => $anime], ['number' => 'ASC'], $limit, $offset),
        ]);
    }

    #[Route('/add', name: 'app_season_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Work $work = null, Anime $anime = null, EntityManagerInterface $entityManager, UserInterface $user): Response
    {
        if (null === $work) {
            throw $this->createNotFoundException('Work not found.');
        }

        if (null === $anime) {
            throw $this->createNotFoundException('Anime not found.');
        }

        $season = new Season();
        $form = $this->createForm(SeasonType::class, $season);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $season->setUser($user);
            $season->setAnime($anime);

            $entityManager->persist($season);
            $entityManager->flush();

            return $this->redirectToRoute('app_season_index', ['work_id' => $work->getId(), 'anime_id' => $anime->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('season/new.html.twig', [
            'work' => $work,
            'anime' => $anime,
            'season' => $season,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_season_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Work $work = null, Anime $anime = null, Season $season = null, EntityManagerInterface $entityManager, UserInterface $user): Response
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

        $form = $this->createForm(SeasonType::class, $season);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $season->setUser($user);
            $season->setAnime($anime);

            $entityManager->flush();

            return $this->redirectToRoute('app_season_index', ['work_id' => $work->getId(), 'anime_id' => $anime->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('season/edit.html.twig', [
            'work' => $work,
            'anime' => $anime,
            'season' => $season,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_season_delete', methods: ['POST'])]
    public function delete(Request $request, Work $work = null, Anime $anime = null, Season $season = null, EntityManagerInterface $entityManager): Response
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

        if ($this->isCsrfTokenValid('delete'.$season->getId(), $request->request->get('_token'))) {
            $entityManager->remove($season);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_season_index', ['work_id' => $work->getId(), 'anime_id' => $anime->getId()], Response::HTTP_SEE_OTHER);
    }
}
