<?php

namespace App\DataFixtures;

use App\DataFixtures\Provider\MoonsekaiProvider;
use App\Entity\Anime;
use App\Entity\Ban;
use App\Entity\CalendarEvent;
use App\Entity\Chapter;
use App\Entity\Comment;
use App\Entity\ContactRequest;
use App\Entity\Episode;
use App\Entity\LightNovel;
use App\Entity\Manga;
use App\Entity\Message;
use App\Entity\Movie;
use App\Entity\News;
use App\Entity\Notification;
use App\Entity\Plateform;
use App\Entity\Progress;
use App\Entity\Rate;
use App\Entity\Report;
use App\Entity\Season;
use App\Entity\Tag;
use App\Entity\User;
use App\Entity\Volume;
use App\Entity\Work;
use App\Entity\WorkNews;
use DateInterval;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Provider\ar_EG\Person;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $hasher,
        private Connection $connection
        ) {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $moonsekaiProvider = new MoonsekaiProvider();

        //! User
        //* Superadmin
        $user = new User();
        $user
            ->setPseudo('Superadmin')
            ->setEmail($faker->freeEmail())
            ->setRoles(['ROLE_SUPERADMIN'])
            ->setPassword($this->hasher->hashPassword($user, 'Pass_1234'))
            ->setPicturePath('1.jfif')
            ->setBannerPath('1.jpg')
            ->setBiography($faker->realText(1000))
            ->setNotificationSetting(['*' => true])
            ->setIsNotificationRedirectionEnabled(random_int(0, 1))
            ->setIsMuted(random_int(0, 1))
            ->setIsAccountConfirmed(random_int(0, 1))
            ->setIsSubscribedNewsletter(random_int(0, 1))
            ;
        $users[] = $user;
        $manager->persist($user);

        //* Admin
        $user = new User();
        $user
            ->setPseudo('Admin')
            ->setEmail($faker->freeEmail())
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($this->hasher->hashPassword($user, 'Pass_1234'))
            ->setPicturePath('2.jfif')
            ->setBannerPath('2.jpg')
            ->setBiography($faker->realText(1000))
            ->setNotificationSetting(['*' => true])
            ->setIsNotificationRedirectionEnabled(random_int(0, 1))
            ->setIsMuted(random_int(0, 1))
            ->setIsAccountConfirmed(random_int(0, 1))
            ->setIsSubscribedNewsletter(random_int(0, 1))
            ;
        $users[] = $user;
        $manager->persist($user);

        //* Moderator
        $user = new User();
        $user
            ->setPseudo('Moderator')
            ->setEmail($faker->freeEmail())
            ->setRoles(['ROLE_MODERATOR'])
            ->setPassword($this->hasher->hashPassword($user, 'Pass_1234'))
            ->setPicturePath('3.jfif')
            ->setBannerPath('3.jpg')
            ->setBiography($faker->realText(1000))
            ->setNotificationSetting(['*' => true])
            ->setIsNotificationRedirectionEnabled(random_int(0, 1))
            ->setIsMuted(random_int(0, 1))
            ->setIsAccountConfirmed(random_int(0, 1))
            ->setIsSubscribedNewsletter(random_int(0, 1))
            ;
        $users[] = $user;
        $manager->persist($user);

        //* 17 more users
        for ($i=4; $i <= 20; $i++) { 
            $user = new User();
            $user
                ->setPseudo($faker->userName())
                ->setEmail($faker->freeEmail())
                ->setRoles(['ROLE_USER'])
                ->setPassword($this->hasher->hashPassword($user, 'Pass_1234'))
                ->setPicturePath("$i.jfif")
                ->setBannerPath("$i.jpg")
                ->setBiography($faker->realText(1000))
                ->setNotificationSetting(['*' => true])
                ->setIsNotificationRedirectionEnabled(random_int(0, 1))
                ->setIsMuted(random_int(0, 1))
                ->setIsAccountConfirmed(random_int(0, 1))
                ->setIsSubscribedNewsletter(random_int(0, 1))
                ;
            $users[] = $user;
            $manager->persist($user);
        }

        //! Message
        foreach ($users as $user) {
            //? Sent messages
            for ($i=0; $i < random_int(1,3); $i++) { 
                $message = new Message();
                $message
                    ->setUserSender($user)
                    ->setMessage($faker->realTextBetween(5, 255))
                    ->setIsRead(random_int(0, 1))
                    ;
                
                do {
                    $userReceiver = $users[array_rand($users)];
                } while ($user === $userReceiver);
    
                $message->setUserReceiver($userReceiver);
    
                $messages[] = $message;
                $manager->persist($message);
            }

            //? Received messages
            for ($i=0; $i < random_int(1,3); $i++) { 
                $message = new Message();
                $message
                    ->setUserReceiver($user)
                    ->setMessage($faker->realTextBetween(5, 255))
                    ->setIsRead(random_int(0, 1))
                    ;
                
                do {
                    $userSender = $users[array_rand($users)];
                } while ($user === $userSender);
    
                $message->setUserSender($userSender);
    
                $messages[] = $message;
                $manager->persist($message);
            }
        }

        //! Report
        for ($i=0; $i < 20; $i++) { 
            $report = new Report();

            $report
                ->setUser($users[array_rand($users)])
                ->setMessage($faker->realTextBetween(5, 255))
                ->setType($moonsekaiProvider->getRandomReportType())
                ->setUrl($faker->url()) // Randomly set an url or nothing
                ->setIsProcessed(random_int(0, 1))
                ->setIsImportant(random_int(0, 1))
                ;
            $manager->persist($report);
        }

        //! News
        for ($i=0; $i < 20; $i++) { 
            $news = new News();
            $news
                ->setUser($users[random_int(0,2)])
                ->setTitle($faker->realTextBetween(5, 100))
                ->setMessage($faker->realTextBetween(5, 255))
                // ->setPicturePath() Default null
                ;
            $newsList[] = $news;
            $manager->persist($news);
        }
        
        //! Ban
        for ($i=0; $i < 20; $i++) { 
            $ban = new Ban();
            $ban
                ->setUser($users[random_int(0,1)])
                ->setEmail($faker->freeEmail())
                ->setMessage(random_int(0, 1) ? $faker->realText(255) : null) // Randomly set a message or nothing
                ;
            $manager->persist($ban);
        }

        //! Tag
        foreach ($moonsekaiProvider->getAllTags() as $tagName) {
            $tag = new Tag();
            $tag
                ->setUser($users[random_int(0,2)])
                ->setName($tagName)
                ;
            $tags[] = $tag;
            $manager->persist($tag);
        }

        //! Plateform
        for ($i=0; $i < 15; $i++) { 
            $plateform = new Plateform();
            $plateform
                ->setUser($users[random_int(0,2)])
                ->setName($faker->realTextBetween(1, 100))
                ->setUrl($faker->url()) // Randomly set an url or nothing
                // ->setPicturePath() Default null
                ;
            $plateforms[] = $plateform;
            $manager->persist($plateform);
        }

        //! Work
        for ($i=1; $i < 16; $i++) { 
            $work = new Work();
            $work
                ->setUser($users[random_int(0,2)])
                ->setName($faker->realTextBetween(1, 190))
                ->setType($moonsekaiProvider->getRandomWorkType())
                ->setNativeCountry($faker->country())
                ->setOriginalName(random_int(0, 1) ? $faker->realText(190) : null) // Randomly set a message or nothing
                ->setAlternativeName(random_int(0, 1) ? array_map(fn() => $faker->realText(255), range(1, random_int(1, 10))) : null) // Randomly set from 1 to 10 alt names or null
                ->setPicturePath("$i.jpg")
                ;

            for ($j=1; $j < random_int(1, count($tags)); $j++) { 
                $work->addTag($tags[array_rand($tags)]);
            }

            for ($j=1; $j < random_int(1, count($plateforms)); $j++) { 
                $work->addPlateform($plateforms[array_rand($plateforms)]);
            }
            
            for ($j=1; $j < random_int(1, count($users)); $j++) { 
                $work->addFollower($users[array_rand($users)]);
            }

            $works[] = $work;
            $manager->persist($work);
        }

        //! Movie
        $counter = 1;
        foreach ($works as $work) {
            for ($i=0; $i < random_int(1,4); $i++) { 
                $movie = new Movie();
                $movie
                    ->setUser($users[random_int(0,2)])
                    ->setWork($work)
                    ->setName($faker->realTextBetween(1, 100))
                    ->setDescription(random_int(0, 1) ? $faker->realText(1000) : null) // Randomly set a description or nothing
                    ->setDuration(random_int(1, 300))
                    ->setAnimationStudio($faker->company())
                    ->setReleaseYear($faker->dateTimeBetween('1900', '+10 years'))
                    ->setPicturePath("$counter.jpg")
                    ;
                $movies[] = $movie;
                $counter++;
                $manager->persist($movie);
            }
        }

        //! Light novel
        $counter = 1;
        foreach ($works as $work) {
            for ($i=0; $i < random_int(1,4); $i++) { 
                $lightNovel = new LightNovel();
                $lightNovel
                    ->setUser($users[random_int(0,2)])
                    ->setWork($work)
                    ->setName($faker->realTextBetween(1, 100))
                    ->setDescription(random_int(0, 1) ? $faker->realText(1000) : null) // Randomly set a description or nothing
                    ->setAuthor($faker->realTextBetween(1, 100))
                    ->setEditor($faker->realTextBetween(1, 100))
                    ->setReleaseYear($faker->dateTimeBetween('1900', '+10 years'))
                    ->setPicturePath("$counter.jpg")
                    ;
                $lightNovels[] = $lightNovel;
                $counter++;
                $manager->persist($lightNovel);
            }
        }

        //! Work news
        foreach ($works as $work) {
            for ($i=0; $i < random_int(1,4); $i++) { 
                $workNews = new WorkNews();
                $workNews
                    ->setUser($users[random_int(0,2)])
                    ->setWork($work)
                    ->setTitle($faker->realTextBetween(5, 100))
                    ->setMessage($faker->realTextBetween(5, 255))
                    // ->setPicturePath() Default null
                    ;
                $workNewsList[] = $workNews;
                $manager->persist($workNews);
            }
        }

        //! Manga
        $counter = 1;
        foreach ($works as $work) {
            for ($i=0; $i < random_int(1,4); $i++) { 
                $manga = new Manga();
                $manga
                    ->setUser($users[random_int(0,2)])
                    ->setWork($work)
                    ->setName($faker->realTextBetween(1, 100))
                    ->setDescription(random_int(0, 1) ? $faker->realText(1000) : null) // Randomly set a description or nothing
                    ->setState($moonsekaiProvider->getRandomState())
                    ->setReleaseRegularity($moonsekaiProvider->getRandomMangaReleaseRegularity())
                    ->setAuthor($faker->realTextBetween(1, 100))
                    ->setDesigner($faker->realTextBetween(1, 100))
                    ->setEditor($faker->realTextBetween(1, 100))
                    ->setReleaseYear($faker->dateTimeBetween('1900', '+10 years'))
                    ->setPicturePath("$counter.jpg")
                    ;
                $mangas[] = $manga;
                $counter++;
                $manager->persist($manga);
            }
        }

        //! Volume
        foreach ($mangas as $manga) {
            for ($i=0; $i < 3; $i++) { 
                $volume = new Volume();
                $volume
                    ->setUser($users[random_int(0,2)])
                    ->setManga($manga)
                    ->setNumber(random_int(1, 3) < 1 ? "Extra #$i" : $i) // Randomly set Extra or just the number
                    ->setName(random_int(0, 1) ? $faker->realText(50) : null) // Randomly set a description or nothing
                    // ->setPicturePath() Default null
                    ;
                $volumes[] = $volume;
                $manager->persist($volume);
            }
        }

        //! Chapter
        foreach ($volumes as $volume) {
            for ($i=0; $i < 15; $i++) { 
                $chapter = new Chapter();
                $chapter
                    ->setUser($users[random_int(0,2)])
                    ->setVolume($volume)
                    ->setNumber(random_int(1, 3) < 1 ? "Extra #$i" : $i) // Randomly set Extra or just the number
                    ->setName(random_int(0, 1) ? $faker->realText(50) : null) // Randomly set a description or nothing
                    ;
                $chapters[] = $chapter;
                $manager->persist($chapter);
            }
        }

        //! Anime
        $counter = 1;
        foreach ($works as $work) {
            for ($i=0; $i < random_int(1,4); $i++) { 
                $anime = new Anime();
                $anime
                    ->setUser($users[random_int(0,2)])
                    ->setWork($work)
                    ->setName($faker->realTextBetween(1, 100))
                    ->setDescription(random_int(0, 1) ? $faker->realText(1000) : null) // Randomly set a description or nothing
                    ->setState($moonsekaiProvider->getRandomState())
                    ->setAuthor($faker->realTextBetween(1, 100))
                    ->setAnimationStudio($faker->company())
                    ->setReleaseYear($faker->dateTimeBetween('1900', '+10 years'))
                    ->setPicturePath("$counter.jpg")
                    ;
                $animes[] = $anime;
                $counter++;
                $manager->persist($anime);
            }
        }

        //! Season
        foreach ($animes as $anime) {
            for ($i=0; $i < 3; $i++) { 
                $season = new Season();
                $season
                    ->setUser($users[random_int(0,2)])
                    ->setAnime($anime)
                    ->setNumber(random_int(1, 3) < 1 ? "Extra #$i" : $i) // Randomly set Extra or just the number
                    ->setName(random_int(0, 1) ? $faker->realText(50) : null) // Randomly set a description or nothing
                    // ->setPicturePath() Default null
                    ;
                $seasons[] = $season;
                $manager->persist($season);
            }
        }

        //! Episode
        foreach ($seasons as $season) {
            for ($i=0; $i < 15; $i++) { 
                $episode = new Episode();
                $episode
                    ->setUser($users[random_int(0,2)])
                    ->setSeason($season)
                    ->setNumber(random_int(1, 3) < 1 ? "Extra #$i" : $i) // Randomly set Extra or just the number
                    ->setName(random_int(0, 1) ? $faker->realText(50) : null) // Randomly set a description or nothing
                    ;
                $episodes[] = $episode;
                $manager->persist($episode);
            }
        }

        //! Contact request
        for ($i=0; $i < 10; $i++) { 
            $contactRequest = new ContactRequest();
            $contactRequest
                ->setApplicantEmail($faker->freeEmail())
                ->setMessage($faker->realTextBetween(5, 255))
                ->setIsImportant(random_int(0,1))
                ->setIsProcessed(random_int(0,1))
                ;
            $manager->persist($contactRequest);
        }

        //! Intermediate flush to access previous ressources id below
        $manager->flush();
        
        //! Calendar event
        for ($i=0; $i < 30; $i++) { 
            $calendarEvent = new CalendarEvent();

            $start = $faker->dateTimeBetween('now', '+1 year');
            [
                'table' => $targetTable,
                'id' => $targetId
            ] = $moonsekaiProvider->getRandomCalendarTarget($newsList, $workNewsList, $movies, $lightNovels, $episodes, $chapters);

            $calendarEvent
                ->setUser($users[random_int(0,2)])
                ->setTitle($faker->realTextBetween(3, 100))
                ->setStart($start)
                ->setEnd($start->add(new DateInterval('PT1H')))
                ->setTargetTable($targetTable)
                ->setTargetId($targetId)
                ;
            $manager->persist($calendarEvent);
        }

        //! Comment
        foreach ($users as $user) {
            for ($i=0; $i < 3; $i++) { 
                $comment = new Comment();
    
                [
                    'table' => $targetTable,
                    'id' => $targetId
                ] = $moonsekaiProvider->getRandomCommentTarget($newsList, $movies, $lightNovels, $animes, $mangas, $users);
    
                $comment
                    ->setUser($users[random_int(0,2)])
                    // ->setParent() Default null
                    ->setMessage($faker->realTextBetween(5, 255))
                    ->setTargetTable($targetTable)
                    ->setTargetId($targetId)
                    ;
                $comments[] = $comment;
                $manager->persist($comment);
            }
        }

        //! Intermediate flush to access previous ressources id below
        $manager->flush();
        
        //! Notification
        foreach ($users as $user) {
            for ($i=0; $i < 5; $i++) { 
                $notification = new Notification();
    
                [
                    'table' => $targetTable,
                    'id' => $targetId
                ] = $moonsekaiProvider->getRandomNotificationTarget($newsList, $messages, $comments, $workNewsList, $movies, $lightNovels, $episodes, $chapters, $user);
    
                $notification
                    ->setUser($users[random_int(0,2)])
                    ->setTitle($faker->realTextBetween(2, 20))
                    ->setMessage($faker->realTextBetween(5, 255))
                    ->setIsRead(random_int(0,1))
                    ->setTargetTable($targetTable)
                    ->setTargetId($targetId)
                    ;
                $manager->persist($notification);
            }
        }

        //! Rate
        foreach ($users as $user) {
            $targetedObjects = [];
            for ($i=0; $i < 3; $i++) {
                $rate = new Rate();

                do {
                    [
                        'table' => $targetTable,
                        'id' => $targetId
                    ] = $moonsekaiProvider->getRandomRateTarget($movies, $lightNovels, $animes, $mangas);
                } while (in_array("$targetTable#$targetId", $targetedObjects));

                $rate
                    ->setUser($user)
                    ->setTargetTable($targetTable)
                    ->setTargetId($targetId)
                    ->setRate(random_int(0,5))
                    ;
                $targetedObjects[] = "$targetTable#$targetId";
                $manager->persist($rate);
            }
        }

        //! Progress
        foreach ($users as $user) {
            $targetedWorks = [];
            for ($i=0; $i < 3; $i++) {
                $progress = new Progress();

                do {
                    $work = $works[array_rand($works)];
                } while (in_array($work, $targetedWorks));

                $progress
                    ->setUser($user)
                    ->setWork($work)
                    ->setProgress($moonsekaiProvider->getRandomProgress())
                    ;
                $targetedWorks[] = $work;
                $manager->persist($progress);
            }
        }

        $manager->flush();
    }
}
