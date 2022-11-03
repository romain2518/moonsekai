<?php

namespace App\Controller;

use App\Entity\Ban;
use App\Form\BanType;
use App\Repository\BanRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/back-office/ban')]
class BanController extends AbstractController
{
    #[Route('/{limit}/{offset}', name: 'app_ban_index', requirements: ['limit' => '\d+', 'offset' => '\d+'], methods: ['GET', 'POST'])]
    public function index(
        Request $request, BanRepository $banRepository, 
        UserRepository $userRepository, EntityManagerInterface $entityManager, 
        UserInterface $user, int $limit = 20, int $offset = 0
        ): Response
    {
        $ban = new Ban();
        $form = $this->createForm(BanType::class, $ban);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //? Checking if a user linked to this email does exist
            $targetedUser = $userRepository->findOneBy(['email' => $form->get('email')->getData()]);
            if (null !== $targetedUser) {
                $entityManager->remove($targetedUser);
            }

            $ban->setUser($user);

            $entityManager->persist($ban);
            $entityManager->flush();

            return $this->redirectToRoute('app_ban_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ban/index.html.twig', [
            'form' => $form->createView(),
            'bans' => $banRepository->findBy([], ['email' => 'ASC'], $limit, $offset),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_ban_delete', methods: ['POST'])]
    public function delete(Request $request, Ban $ban, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ban->getId(), $request->request->get('_token'))) {
            $entityManager->remove($ban);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_ban_index', [], Response::HTTP_SEE_OTHER);
    }
}
