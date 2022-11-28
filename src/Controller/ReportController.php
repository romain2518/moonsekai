<?php

namespace App\Controller;

use App\Entity\Report;
use App\Form\ReportType;
use App\Repository\ReportRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class ReportController extends AbstractController
{
    #[Route('/back-office/report/{limit}/{offset}', name: 'app_report_index', requirements: ['limit' => '\d+', 'offset' => '\d+'], methods: ['GET'])]
    public function index(ReportRepository $reportRepository, int $limit = 20, int $offset = 0): Response
    {
        return $this->render('report/index.html.twig', [
            'reports' => $reportRepository->findBy([], ['createdAt' => 'DESC'], $limit, $offset),
        ]);
    }

    #[Route('/report', name: 'app_report_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserInterface $user): Response
    {
        $report = new Report();
        $form = $this->createForm(ReportType::class, $report);
        $form->handleRequest($request);

        if (empty($form->get('url')->getData())) {
            $form->get('url')->setData($request->server->get('HTTP_REFERER'));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $report->setUser($user);

            $entityManager->persist($report);
            $entityManager->flush();

            return $this->redirectToRoute('app_main_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('report/new.html.twig', [
            'report' => $report,
            'form' => $form,
        ]);
    }

    #[Route('/back-office/report/{id}/mark-as-{action}', name: 'app_report_mark-as', 
        requirements: ['action' => '^(processed)|(unprocessed)|(important)|(not-important)$'], 
        methods: ['POST'], defaults: ['_format' => 'json']
    )]
    public function markAs(Request $request, Report $report = null, string $action, EntityManagerInterface $entityManager): JsonResponse
    {
        if ($report === null) {
            return $this->json('Report not found', Response::HTTP_NOT_FOUND);
        }

        //? Checking CSRF Token
        $token = $request->request->get('token');
        $isValidToken = $this->isCsrfTokenValid('mark' . $report->getId(), $token);
        if (!$isValidToken) {
            return $this->json('Invalid token', Response::HTTP_FORBIDDEN);
        }

        //? Edit the report
        switch ($action) {
            case 'processed':
                $report->setIsProcessed(true);
                break;
            case 'unprocessed':
                $report->setIsProcessed(false);
                break;
            case 'important':
                $report->setIsImportant(true);
                break;
            case 'not-important':
                $report->setIsImportant(false);
                break;
        }

        //? Save
        $entityManager->flush();
        
        return $this->json(
            $report, 
            Response::HTTP_PARTIAL_CONTENT,
            [],
            [
                'groups' => [
                    'api_report_show'
                ]
            ]
        );
    }

    #[Route('/back-office/report/{id}/delete', name: 'app_report_delete', methods: ['POST'])]
    public function delete(Request $request, Report $report = null, EntityManagerInterface $entityManager): Response
    {
        if (null === $report) {
            throw $this->createNotFoundException('Report not found.');
        }

        if ($this->isCsrfTokenValid('delete'.$report->getId(), $request->request->get('_token'))) {
            $entityManager->remove($report);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_report_index', [], Response::HTTP_SEE_OTHER);
    }
}
