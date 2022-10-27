<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main_home')]
    public function home(): Response
    {
        return $this->render('main/home.html.twig');
    }

    #[Route('/about', name: 'app_main_about')]
    public function about(): Response
    {
        return $this->render('main/about.html.twig');
    }

    #[Route('/legal-mentions', name: 'app_main_legal-mentions')]
    public function legalMentions(): Response
    {
        return $this->render('main/legal_mentions.html.twig');
    }

    #[Route('/patch-notes', name: 'app_main_patch-notes')]
    public function patchNotes(): Response
    {
        return $this->render('main/patch_notes.html.twig');
    }

    #[Route('/back-office', name: 'app_main_back-office')]
    public function backOffice(): Response
    {
        return $this->render('main/back_office.html.twig');
    }
}
