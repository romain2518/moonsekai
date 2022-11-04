<?php

namespace App\Controller;

use App\Entity\Progress;
use App\Entity\Work;
use App\Form\WorkType;
use App\Repository\WorkRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class WorkController extends AbstractController
{
    #[Route('/back-office/work/{limit}/{offset}', name: 'app_work_index', requirements: ['limit' => '\d+', 'offset' => '\d+'], methods: ['GET'])]
    public function index(WorkRepository $workRepository, int $limit = 20, int $offset = 0): Response
    {
        return $this->render('work/index.html.twig', [
            'works' => $workRepository->findBy([], ['name' => 'ASC'], $limit, $offset),
        ]);
    }

    #[Route('/back-office/work/add', name: 'app_work_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserInterface $user): Response
    {
        $work = new Work();
        $form = $this->createForm(WorkType::class, $work);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $work->setUser($user);

            $entityManager->persist($work);
            $entityManager->flush();

            return $this->redirectToRoute('app_work_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('work/new.html.twig', [
            'work' => $work,
            'form' => $form,
        ]);
    }

    #[Route('/work/{id}', name: 'app_work_show', methods: ['GET'])]
    public function show(Work $work = null): Response
    {
        if (null === $work) {
            throw $this->createNotFoundException('Work not found.');
        }

        return $this->render('work/show.html.twig', [
            'work' => $work,
            'progressList' => Progress::getProgressList(),
        ]);
    }

    #[Route('/back-office/work/{id}/edit', name: 'app_work_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Work $work = null, EntityManagerInterface $entityManager, UserInterface $user): Response
    {
        if (null === $work) {
            throw $this->createNotFoundException('Work not found.');
        }

        $form = $this->createForm(WorkType::class, $work);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $work->setUser($user);
            
            $entityManager->flush();

            return $this->redirectToRoute('app_work_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('work/edit.html.twig', [
            'work' => $work,
            'form' => $form,
        ]);
    }

    #[Route('/back-office/work/{id}/delete', name: 'app_work_delete', methods: ['POST'])]
    public function delete(Request $request, Work $work = null, EntityManagerInterface $entityManager): Response
    {
        if (null === $work) {
            throw $this->createNotFoundException('Work not found.');
        }

        if ($this->isCsrfTokenValid('delete'.$work->getId(), $request->request->get('_token'))) {
            $entityManager->remove($work);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_work_index', [], Response::HTTP_SEE_OTHER);
    }
}
