<?php

namespace App\Controller;

use App\Form\SearchFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\UnicodeString;

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

    #[Route('/search/{limit}/{offset}', name: 'app_main_search', requirements: ['limit' => '\d+', 'offset' => '\d+'], methods: ['GET', 'POST'])]
    public function search(Request $request, EntityManagerInterface $entityManager, int $limit = 20, int $offset = 0): Response
    {
        $form = $this->createForm(SearchFormType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $subject = $form->get('subject')->getData();
            switch ($subject) {
                case 'News':
                case 'WorkNews':
                    $field = 'title';
                    break;

                case 'User':
                    $field = 'pseudo';
                    break;
                    
                default:
                    $field = 'name';
                    break;
            }

            //? Basic search
            $results = $entityManager
                ->getRepository('App\\Entity\\' . $subject)
                ->createQueryBuilder('r')
                ->andWhere("r.$field LIKE :query")
                ->setParameter('query', '%' . $form->get('query')->getData() . '%')
                ->orderBy("r.$field", 'ASC')
                ->setFirstResult($offset)
                ->setMaxResults($limit)
                ->getQuery()
                ->getResult()
            ;

            //? Advanced search
            if (empty($results) && in_array($subject, ['Anime', 'LightNovel', 'Manga', 'Movie', 'Work'])) {
                $advancedSearchProperties = [
                    'Anime'      => ['name', 'state', 'author', 'animationStudio', 'releaseYear'],
                    'LightNovel' => ['name', 'author', 'editor', 'releaseYear'],
                    'Manga'      => ['name', 'state', 'releaseRegularity', 'author', 'designer', 'editor', 'releaseYear'],
                    'Movie'      => ['name', 'duration', 'animationStudio', 'releaseYear'],
                    'Work'       => ['name', 'type', 'nativeCountry', 'originalName'],
                ];

                $whereStatement = '';
                foreach ($advancedSearchProperties[$subject] as $key => $property) {
                    $whereStatement .= "r.$property LIKE :query";
                    $whereStatement .= $key !== count($advancedSearchProperties[$subject]) -1 ? ' OR ' : '';
                }
                
                $results = $entityManager
                    ->getRepository('App\\Entity\\' . $subject)
                    ->createQueryBuilder('r')
                    ->andWhere($whereStatement)
                    ->setParameter('query', '%' . $form->get('query')->getData() . '%')
                    ->orderBy("r.$field", 'ASC')
                    ->setFirstResult($offset)
                    ->setMaxResults($limit)
                    ->getQuery()
                    ->getResult()
                ;
            }
        }

        return $this->render('main/search.html.twig', [
            'form' => $form->createView(),
            'results' => $results ?? [],
            'subject' => (new UnicodeString($form->get('subject')->getData() ?? ''))->snake(), // Used to include the result card template, (must be snake case string)
        ]);
    }

    public static function getSearchSubjects(): array
    {
        return [
            'Anime' => 'Anime',
            'Light novel' => 'LightNovel',
            'Manga' => 'Manga',
            'Movie' => 'Movie',
            'News' => 'News',
            'Platform' => 'Platform',
            'User' => 'User',
            'Work' => 'Work',
            'Work news' => 'WorkNews',
        ];
    }
}
