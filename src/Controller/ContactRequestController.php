<?php

namespace App\Controller;

use App\Entity\ContactRequest;
use App\Form\ContactRequestType;
use App\Repository\ContactRequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactRequestController extends AbstractController
{
    #[Route('/back-office/contact-request/{limit}/{offset}', name: 'app_contact-request_index', requirements: ['limit' => '\d+', 'offset' => '\d+'], methods: ['GET'])]
    public function index(ContactRequestRepository $contactRequestRepository, int $limit = 20, int $offset = 0): Response
    {
        return $this->render('contact_request/index.html.twig', [
            'contact_requests' => $contactRequestRepository->findBy([], ['createdAt' => 'DESC'], $limit, $offset),
        ]);
    }

    #[Route('/contact', name: 'app_contact-request_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $contactRequest = new ContactRequest();
        $form = $this->createForm(ContactRequestType::class, $contactRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($contactRequest);
            $entityManager->flush();

            return $this->redirectToRoute('app_main_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('contact_request/new.html.twig', [
            'contact_request' => $contactRequest,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_contact-request_delete', methods: ['POST'])]
    public function delete(Request $request, ContactRequest $contactRequest = null, EntityManagerInterface $entityManager): Response
    {
        if (null === $contactRequest) {
            throw $this->createNotFoundException('Contact request not found.');
        }

        if ($this->isCsrfTokenValid('delete'.$contactRequest->getId(), $request->request->get('_token'))) {
            $entityManager->remove($contactRequest);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_contact-request_index', [], Response::HTTP_SEE_OTHER);
    }
}
