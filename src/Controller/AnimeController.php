<?php

namespace App\Controller;

use App\Entity\Anime;
use App\Entity\Work;
use App\Form\AnimeType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/back-office/work/{work_id}/anime')]
#[Entity('work', expr: 'repository.find(work_id)')]
class AnimeController extends AbstractController
{
    #[Route('/{limit}/{offset}', name: 'app_anime_index', requirements: ['limit' => '\d+', 'offset' => '\d+'], methods: ['GET'])]
    public function index(Work $work = null, int $limit = 20, int $offset = 0): Response
    {
        if (null === $work) {
            throw $this->createNotFoundException('Work not found.');
        }

        return $this->render('anime/index.html.twig', [
            'work' => $work,
            'animes' => $work->getAnimes()->slice($offset, $limit),
        ]);
    }

    #[Route('/add', name: 'app_anime_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Work $work = null, EntityManagerInterface $entityManager, UserInterface $user): Response
    {
        if (null === $work) {
            throw $this->createNotFoundException('Work not found.');
        }

        $anime = new Anime();
        $form = $this->createForm(AnimeType::class, $anime);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $anime->setUser($user);
            $anime->setWork($work);
            
            $entityManager->persist($anime);
            $entityManager->flush();

            return $this->redirectToRoute('app_anime_index', ['work_id' => $work->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('anime/new.html.twig', [
            'work' => $work,
            'anime' => $anime,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_anime_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Work $work = null, Anime $anime = null, EntityManagerInterface $entityManager, UserInterface $user): Response
    {
        if (null === $work) {
            throw $this->createNotFoundException('Work not found.');
        }

        if (null === $anime) {
            throw $this->createNotFoundException('Anime not found.');
        }

        $form = $this->createForm(AnimeType::class, $anime);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $anime->setUser($user);
            $anime->setWork($work);

            $entityManager->flush();

            return $this->redirectToRoute('app_anime_index', ['work_id' => $work->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('anime/edit.html.twig', [
            'work' => $work,
            'anime' => $anime,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_anime_delete', methods: ['POST'])]
    public function delete(Request $request, Work $work = null, Anime $anime = null, EntityManagerInterface $entityManager): Response
    {
        if (null === $work) {
            throw $this->createNotFoundException('Work not found.');
        }

        if (null === $anime) {
            throw $this->createNotFoundException('Anime not found.');
        }

        if ($this->isCsrfTokenValid('delete'.$anime->getId(), $request->request->get('_token'))) {
            $entityManager->remove($anime);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_anime_index', ['work_id' => $work->getId()], Response::HTTP_SEE_OTHER);
    }
}
