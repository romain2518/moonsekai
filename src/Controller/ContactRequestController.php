<?php

namespace App\Controller;

use App\Entity\ContactRequest;
use App\Form\ContactRequestType;
use App\Repository\ContactRequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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

    #[Route('/back-office/contact-request/{id}/mark-as-{action}', name: 'app_contact-request_mark-as', 
        requirements: ['action' => '^(processed)|(unprocessed)|(important)|(not-important)$'], 
        methods: ['POST'], defaults: ['_format' => 'json']
    )]
    public function markAs(Request $request, ContactRequest $contactRequest = null, string $action, EntityManagerInterface $entityManager): JsonResponse
    {
        if ($contactRequest === null) {
            return $this->json('Contact request not found', Response::HTTP_NOT_FOUND);
        }

        //? Checking CSRF Token
        $token = $request->request->get('token');
        $isValidToken = $this->isCsrfTokenValid('mark' . $contactRequest->getId(), $token);
        if (!$isValidToken) {
            return $this->json('Invalid token', Response::HTTP_FORBIDDEN);
        }

        //? Edit the request
        switch ($action) {
            case 'processed':
                $contactRequest->setIsProcessed(true);
                break;
            case 'unprocessed':
                $contactRequest->setIsProcessed(false);
                break;
            case 'important':
                $contactRequest->setIsImportant(true);
                break;
            case 'not-important':
                $contactRequest->setIsImportant(false);
                break;
        }

        //? Save
        $entityManager->flush();
        
        return $this->json($contactRequest, Response::HTTP_PARTIAL_CONTENT);
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
