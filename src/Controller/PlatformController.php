<?php

namespace App\Controller;

use App\Entity\Platform;
use App\Form\PlatformType;
use App\Repository\PlatformRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class PlatformController extends AbstractController
{
    #[Route('/back-office/platform/{limit}/{offset}', name: 'app_platform_index', requirements: ['limit' => '\d+', 'offset' => '\d+'], methods: ['GET'])]
    public function index(PlatformRepository $platformRepository, int $limit = 20, int $offset = 0): Response
    {
        return $this->render('platform/index.html.twig', [
            'platforms' => $platformRepository->findBy([], ['name' => 'ASC'], $limit, $offset),
        ]);
    }

    #[Route('/back-office/platform/add', name: 'app_platform_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserInterface $user): Response
    {
        $platform = new Platform();
        $form = $this->createForm(PlatformType::class, $platform);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $platform->setUser($user);
            
            $entityManager->persist($platform);
            $entityManager->flush();

            return $this->redirectToRoute('app_platform_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('platform/new.html.twig', [
            'platform' => $platform,
            'form' => $form,
        ]);
    }

    #[Route('/platform/{id}', name: 'app_platform_show', methods: ['GET'])]
    public function show(Platform $platform = null): Response
    {
        if (null === $platform) {
            throw $this->createNotFoundException('Platform not found.');
        }

        return $this->render('platform/show.html.twig', [
            'platform' => $platform,
        ]);
    }

    #[Route('/back-office/platform/{id}/edit', name: 'app_platform_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Platform $platform = null, EntityManagerInterface $entityManager, UserInterface $user): Response
    {
        if (null === $platform) {
            throw $this->createNotFoundException('Platform not found.');
        }

        $form = $this->createForm(PlatformType::class, $platform);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $platform->setUser($user);

            $entityManager->flush();

            return $this->redirectToRoute('app_platform_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('platform/edit.html.twig', [
            'platform' => $platform,
            'form' => $form,
        ]);
    }

    #[Route('/back-office/platform/{id}/delete', name: 'app_platform_delete', methods: ['POST'])]
    public function delete(Request $request, Platform $platform = null, EntityManagerInterface $entityManager): Response
    {
        if (null === $platform) {
            throw $this->createNotFoundException('Platform not found.');
        }
        
        if ($this->isCsrfTokenValid('delete'.$platform->getId(), $request->request->get('_token'))) {
            $entityManager->remove($platform);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_platform_index', [], Response::HTTP_SEE_OTHER);
    }
}
