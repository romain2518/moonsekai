<?php

namespace App\Controller;

use App\Entity\Work;
use App\Entity\WorkNews;
use App\Form\WorkNewsType;
use App\Repository\WorkNewsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/back-office/work/{work_id}/news')]
#[Entity('work', expr: 'repository.find(work_id)')]
class WorkNewsController extends AbstractController
{
    #[Route('/{limit}/{offset}', name: 'app_work-news_index', requirements: ['limit' => '\d+', 'offset' => '\d+'], methods: ['GET'])]
    public function index(Work $work = null, WorkNewsRepository $workNewsRepository, int $limit = 20, int $offset = 0): Response
    {
        if (null === $work) {
            throw $this->createNotFoundException('Work not found.');
        }

        return $this->render('work_news/index.html.twig', [
            'work' => $work,
            'work_news' => $workNewsRepository->findBy(['work' => $work], ['title' => 'ASC'], $limit, $offset),
        ]);
    }

    #[Route('/add', name: 'app_work-news_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Work $work = null, EntityManagerInterface $entityManager, UserInterface $user): Response
    {
        if (null === $work) {
            throw $this->createNotFoundException('Work not found.');
        }

        $workNews = new WorkNews();
        $form = $this->createForm(WorkNewsType::class, $workNews);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $workNews->setUser($user);
            $workNews->setWork($work);

            $entityManager->persist($workNews);
            $entityManager->flush();

            return $this->redirectToRoute('app_work-news_index', ['work_id' => $work->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('work_news/new.html.twig', [
            'work' => $work,
            'work_news' => $workNews,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_work-news_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Work $work = null, WorkNews $workNews = null, EntityManagerInterface $entityManager, UserInterface $user): Response
    {
        if (null === $work) {
            throw $this->createNotFoundException('Work not found.');
        }

        if (null === $workNews) {
            throw $this->createNotFoundException('Work news not found.');
        }

        $form = $this->createForm(WorkNewsType::class, $workNews);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $workNews->setUser($user);
            $workNews->setWork($work);

            $entityManager->flush();

            return $this->redirectToRoute('app_work-news_index', ['work_id' => $work->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('work_news/edit.html.twig', [
            'work' => $work,
            'work_news' => $workNews,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_work-news_delete', methods: ['POST'])]
    public function delete(Request $request, Work $work = null, WorkNews $workNews = null, EntityManagerInterface $entityManager): Response
    {
        if (null === $work) {
            throw $this->createNotFoundException('Work not found.');
        }

        if (null === $workNews) {
            throw $this->createNotFoundException('Work news not found.');
        }

        if ($this->isCsrfTokenValid('delete'.$workNews->getId(), $request->request->get('_token'))) {
            $entityManager->remove($workNews);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_work-news_index', ['work_id' => $work->getId()], Response::HTTP_SEE_OTHER);
    }
}
