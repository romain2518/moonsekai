<?php

namespace App\Controller;

use App\Entity\Ban;
use App\Form\BanType;
use App\Repository\BanRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/back-office/ban')]
class BanController extends AbstractController
{
    #[Route('/{limit}/{offset}', name: 'app_ban_list', requirements: ['limit' => '\d+', 'offset' => '\d+'], methods: ['GET'])]
    public function index(BanRepository $banRepository, int $limit = 20, int $offset = 0): Response
    {
        return $this->render('ban/index.html.twig', [
            'bans' => $banRepository->findBy([], ['email' => 'ASC'], $limit, $offset),
        ]);
    }

    // #[Route('/new', name: 'app_ban_new', methods: ['GET', 'POST'])]
    // public function new(Request $request, EntityManagerInterface $entityManager): Response
    // {
    //     $ban = new Ban();
    //     $form = $this->createForm(BanType::class, $ban);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $entityManager->persist($ban);
    //         $entityManager->flush();

    //         return $this->redirectToRoute('app_ban_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->renderForm('ban/new.html.twig', [
    //         'ban' => $ban,
    //         'form' => $form,
    //     ]);
    // }

    // #[Route('/{id}', name: 'app_ban_delete', methods: ['POST'])]
    // public function delete(Request $request, Ban $ban, EntityManagerInterface $entityManager): Response
    // {
    //     if ($this->isCsrfTokenValid('delete'.$ban->getId(), $request->request->get('_token'))) {
    //         $entityManager->remove($ban);
    //         $entityManager->flush();
    //     }

    //     return $this->redirectToRoute('app_ban_index', [], Response::HTTP_SEE_OTHER);
    // }
}
