<?php

namespace App\Controller;

use App\Entity\Manga;
use App\Entity\Volume;
use App\Entity\Work;
use App\Form\VolumeType;
use App\Repository\VolumeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/back-office/work/{work_id}/manga/{manga_id}/volume')]
#[Entity('work', expr: 'repository.find(work_id)')]
#[Entity('manga', expr: 'repository.find(manga_id)')]
class VolumeController extends AbstractController
{
    #[Route('/{limit}/{offset}', name: 'app_volume_index', requirements: ['limit' => '\d+', 'offset' => '\d+'], methods: ['GET'])]
    public function index(Work $work = null, Manga $manga = null, VolumeRepository $volumeRepository, int $limit = 20, int $offset = 0): Response
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
            'volumes' => $volumeRepository->findBy(['manga' => $manga], ['number' => 'ASC'], $limit, $offset),
        ]);
    }

    #[Route('/add', name: 'app_volume_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Work $work = null, Manga $manga = null, EntityManagerInterface $entityManager, UserInterface $user): Response
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

        if ($form->isSubmitted() && $form->isValid()) {
            $volume->setUser($user);
            $volume->setManga($manga);

            $entityManager->persist($volume);
            $entityManager->flush();

            return $this->redirectToRoute('app_volume_index', ['work_id' => $work->getId(), 'manga_id' => $manga->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('volume/new.html.twig', [
            'work' => $work,
            'manga' => $manga,
            'volume' => $volume,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_volume_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Work $work = null, Manga $manga = null, Volume $volume = null, EntityManagerInterface $entityManager, UserInterface $user): Response
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

        if ($form->isSubmitted() && $form->isValid()) {
            $volume->setUser($user);
            $volume->setManga($manga);

            $entityManager->flush();

            return $this->redirectToRoute('app_volume_index', ['work_id' => $work->getId(), 'manga_id' => $manga->getId()], Response::HTTP_SEE_OTHER);
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
