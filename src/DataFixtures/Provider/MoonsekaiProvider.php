<?php

namespace App\DataFixtures\Provider;

use App\Entity\User;

class MoonsekaiProvider
{
    private $reportTypes = [
        'user behaviour',
        'minor error',
        'bug',
    ];

    private $tags = [
        'Action',
        'Amour',
        'Combat',
        'Comédie',
        'Danse',
        'Fiction',
        'Guerre',
        'Horreur',
        'Autre',
    ];

    private $workTypes = [
        'shōnen',
        'seinen',
        'shōjo',
        'josei',
    ];

    private $states = [
        'ongoing',
        'finished',
        'paused',
    ];

    private $mangaReleaseRegularities = [
        'daily',
        'weekly',
        'bi-weekly',
        'monthly',
    ];

    private $progress = [
        'in progress',
        'to see',
        'finished',
        'paused',
        'abandoned',
    ];
    
    public function getRandomReportType()
    {
        return $this->reportTypes[array_rand($this->reportTypes)];
    }

    public function getAllTags()
    {
        return $this->tags;
    }

    public function getRandomWorkType()
    {
        return $this->workTypes[array_rand($this->workTypes)];
    }

    public function getRandomState()
    {
        return $this->states[array_rand($this->states)];
    }

    public function getRandomMangaReleaseRegularity()
    {
        return $this->mangaReleaseRegularities[array_rand($this->mangaReleaseRegularities)];
    }

    public function getRandomCalendarTarget(
        array $newsList, array $workNewsList, 
        array $movies, array $lightNovels, 
        array $episodes, array $chapters
        )
    {
        $allObjects = array_merge($newsList, $workNewsList, $movies, $lightNovels, $episodes, $chapters);
        $randomTarget = $allObjects[array_rand($allObjects)];
        
        return [
            'table' => $randomTarget::class,
            'id' => $randomTarget->getId(),
        ];
    }

    public function getRandomCommentTarget(
        array $newsList, array $movies,
        array $lightNovels, array $animes,
        array $mangas, array $users
        )
    {
        $allObjects = array_merge($newsList, $movies, $lightNovels, $animes, $mangas, $users);
        $randomTarget = $allObjects[array_rand($allObjects)];

        return [
            'table' => $randomTarget::class,
            'id' => $randomTarget->getId(),
        ];
    }

    public function getRandomNotificationTarget(
        array $newsList, array $messages, 
        array $comments, array $workNewsList,
        array $movies, array $lightNovels, 
        array $episodes, array $chapters,
        User $user
        )
    {
        $allObjects = array_merge($newsList, $messages, $comments, $workNewsList, $movies, $lightNovels, $episodes, $chapters);
        $randomTarget = $allObjects[array_rand($allObjects)];

        if ($randomTarget::class === 'Comment' && $randomTarget->getUserReceiver() !== $user) {
            $randomTarget = $user->getReceivedMessages()[0];
        }
        
        return [
            'table' => $randomTarget::class,
            'id' => $randomTarget->getId(),
        ];
    }

    public function getRandomRateTarget(
        array $movies, array $lightNovels, 
        array $animes, array $mangas
        )
    {
        $allObjects = array_merge($movies, $lightNovels, $animes, $mangas);
        $randomTarget = $allObjects[array_rand($allObjects)];

        return [
            'table' => $randomTarget::class,
            'id' => $randomTarget->getId(),
        ];
    }

    public function getRandomProgress()
    {
        return $this->progress[array_rand($this->progress)];
    }
}
