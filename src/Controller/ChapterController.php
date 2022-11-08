<?php

namespace App\Controller;

use App\Entity\Chapter;
use App\Entity\Manga;
use App\Entity\Volume;
use App\Entity\Work;
use App\Form\ChapterType;
use App\Repository\ChapterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/back-office/work/{work_id}/manga/{manga_id}/volume/{volume_id}/chapter')]
#[Entity('work', expr: 'repository.find(work_id)')]
#[Entity('manga', expr: 'repository.find(manga_id)')]
#[Entity('volume', expr: 'repository.find(volume_id)')]
class ChapterController extends AbstractController
{
    #[Route('/{limit}/{offset}', name: 'app_chapter_index', requirements: ['limit' => '\d+', 'offset' => '\d+'], methods: ['GET'])]
    public function index(Work $work = null, Manga $manga = null, Volume $volume = null, ChapterRepository $chapterRepository, int $limit = 20, int $offset = 0): Response
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

        return $this->render('chapter/index.html.twig', [
            'work' => $work,
            'manga' => $manga,
            'volume' => $volume,
            'chapters' => $chapterRepository->findByVolumeOrderedByCastedName($volume, $limit, $offset),
        ]);
    }

    #[Route('/add', name: 'app_chapter_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Work $work = null, Manga $manga = null, Volume $volume = null, EntityManagerInterface $entityManager, UserInterface $user): Response
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

        $chapter = new Chapter();
        $form = $this->createForm(ChapterType::class, $chapter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $chapter->setUser($user);
            $chapter->setVolume($volume);

            $entityManager->persist($chapter);
            $entityManager->flush();

            return $this->redirectToRoute('app_chapter_index', ['work_id' => $work->getId(), 'manga_id' => $manga->getId(), 'volume_id' => $volume->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('chapter/new.html.twig', [
            'work' => $work,
            'manga' => $manga,
            'volume' => $volume,
            'chapter' => $chapter,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_chapter_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Work $work = null, Manga $manga = null, Volume $volume = null, Chapter $chapter = null, EntityManagerInterface $entityManager, UserInterface $user): Response
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

        if (null === $chapter) {
            throw $this->createNotFoundException('Chapter not found.');
        }
        
        $form = $this->createForm(ChapterType::class, $chapter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $chapter->setUser($user);
            $chapter->setVolume($volume);

            $entityManager->flush();

            return $this->redirectToRoute('app_chapter_index', ['work_id' => $work->getId(), 'manga_id' => $manga->getId(), 'volume_id' => $volume->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('chapter/edit.html.twig', [
            'work' => $work,
            'manga' => $manga,
            'volume' => $volume,
            'chapter' => $chapter,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_chapter_delete', methods: ['POST'])]
    public function delete(Request $request, Work $work = null, Manga $manga = null, Volume $volume = null, Chapter $chapter = null, EntityManagerInterface $entityManager): Response
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

        if (null === $chapter) {
            throw $this->createNotFoundException('Chapter not found.');
        }

        if ($this->isCsrfTokenValid('delete'.$chapter->getId(), $request->request->get('_token'))) {
            $entityManager->remove($chapter);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_chapter_index', ['work_id' => $work->getId(), 'manga_id' => $manga->getId(), 'volume_id' => $volume->getId()], Response::HTTP_SEE_OTHER);
    }
}
