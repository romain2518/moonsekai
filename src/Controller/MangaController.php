<?php

namespace App\Controller;

use App\Entity\Manga;
use App\Entity\Work;
use App\Form\MangaType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/back-office/work/{work_id}/manga')]
#[Entity('work', expr: 'repository.find(work_id)')]
class MangaController extends AbstractController
{
    #[Route('/{limit}/{offset}', name: 'app_manga_index', requirements: ['limit' => '\d+', 'offset' => '\d+'], methods: ['GET'])]
    public function index(Work $work = null, int $limit = 20, int $offset = 0): Response
    {
        if (null === $work) {
            throw $this->createNotFoundException('Work not found.');
        }

        return $this->render('manga/index.html.twig', [
            'work' => $work,
            'mangas' => $work->getMangas()->slice($offset, $limit),
        ]);
    }

    #[Route('/add', name: 'app_manga_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Work $work = null, EntityManagerInterface $entityManager, UserInterface $user): Response
    {
        if (null === $work) {
            throw $this->createNotFoundException('Work not found.');
        }

        $manga = new Manga();
        $form = $this->createForm(MangaType::class, $manga);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manga->setUser($user);
            $manga->setWork($work);

            $entityManager->persist($manga);
            $entityManager->flush();

            return $this->redirectToRoute('app_manga_index', ['work_id' => $work->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('manga/new.html.twig', [
            'work' => $work,
            'manga' => $manga,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_manga_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Work $work = null, Manga $manga = null, EntityManagerInterface $entityManager, UserInterface $user): Response
    {
        if (null === $work) {
            throw $this->createNotFoundException('Work not found.');
        }

        if (null === $manga) {
            throw $this->createNotFoundException('Manga not found.');
        }

        $form = $this->createForm(MangaType::class, $manga);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manga->setUser($user);
            $manga->setWork($work);

            $entityManager->flush();

            return $this->redirectToRoute('app_manga_index', ['work_id' => $work->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('manga/edit.html.twig', [
            'work' => $work,
            'manga' => $manga,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_manga_delete', methods: ['POST'])]
    public function delete(Request $request, Work $work = null, Manga $manga = null, EntityManagerInterface $entityManager): Response
    {
        if (null === $work) {
            throw $this->createNotFoundException('Work not found.');
        }

        if (null === $manga) {
            throw $this->createNotFoundException('Manga not found.');
        }

        if ($this->isCsrfTokenValid('delete'.$manga->getId(), $request->request->get('_token'))) {
            $entityManager->remove($manga);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_manga_index', ['work_id' => $work->getId()], Response::HTTP_SEE_OTHER);
    }
}
