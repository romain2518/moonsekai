<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:download-fake-pictures',
    description: 'Download pictures from copyright free websites.',
)]
class DownloadFakePicturesCommand extends Command
{
    public function __construct(
        private HttpClientInterface $client
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        //! Clearing each folder
        $finder = new Finder();
        $fileSystem = new Filesystem();

        $folders = [
            'userPictures',
            'userBanners',
            'workPictures',
            'moviePictures',
            'lightNovelPictures',
            'mangaPictures',
            'animePictures',
        ];

        foreach ($folders as $folder) {
            $files = $finder->files()->in("public/images/$folder");
            $fileSystem->remove($files);
        }

        //! User pictures
        //? https://i.pravatar.cc/200?img={number} will return an image

        $io->section('Downloading user pictures');

        $ppProgressBar = new ProgressBar($output, 20);

        for ($i=1; $i < 21; $i++) { 
            $content = file_get_contents("https://i.pravatar.cc/200?img=" . $i);
    
            //Store in the filesystem.
            $fp = fopen("public/images/userPictures/" . $i . ".jfif", "w");
            fwrite($fp, $content);
            fclose($fp);

            $ppProgressBar->advance();
        }

        $io->info('User pictures => Ok!');

        //! User banners
        //? https://picsum.photos/500/350 will return an image

        $io->section('Downloading user banners');

        $experienceProgressBar = new ProgressBar($output, 20);

        //* Adding context to file_get_contents to prevent error HTTP request failed
        $opts = array('http'=>array('header' => "User-Agent:MyAgent/1.0\r\n")); 
        $context = stream_context_create($opts);

        for ($i=1; $i < 21; $i++) { 
            $content = file_get_contents("https://picsum.photos/500/350", false, $context);
    
            //Store in the filesystem.
            $fp = fopen("public/images/userBanners/" . $i . ".jpg", "w");
            fwrite($fp, $content);
            fclose($fp);

            $experienceProgressBar->advance();
        }

        $io->info('User banners => Ok!');

        //! Work pictures
        //? https://hmtai.herokuapp.com/sfw/wallpaper will return a json response of the following format :
        /** {
         *      url: 'image url'
         *  }
         */

        $io->section('Downloading work pictures');

        $experienceProgressBar = new ProgressBar($output, 15);

        for ($i=1; $i < 16; $i++) { 
            $response = $this->client->request(
                'GET',
                'https://hmtai.herokuapp.com/sfw/wallpaper'
            );

            $responseContent = $response->toArray();
            $imageUrl = $responseContent['url'];

            $content = file_get_contents($imageUrl);

            //Store in the filesystem.
            $fp = fopen("public/images/workPictures/" . $i . '.jpg', "w");
            fwrite($fp, $content);
            fclose($fp);

            $experienceProgressBar->advance();
        }

        $io->info('Work pictures => Ok!');

        //! Movie pictures
        //? https://hmtai.herokuapp.com/sfw/mobileWallpaper will return a json response of the following format :
        /** {
         *      url: 'image url'
         *  }
         */

        $io->section('Downloading movie pictures');

        $experienceProgressBar = new ProgressBar($output, 60);

        for ($i=1; $i < 61; $i++) { 
            $response = $this->client->request(
                'GET',
                'https://hmtai.herokuapp.com/sfw/wallpaper'
            );

            $responseContent = $response->toArray();
            $imageUrl = $responseContent['url'];

            $content = file_get_contents($imageUrl);

            //Store in the filesystem.
            $fp = fopen("public/images/moviePictures/" . $i . '.jpg', "w");
            fwrite($fp, $content);
            fclose($fp);

            $experienceProgressBar->advance();
        }

        $io->info('Movie pictures => Ok!');

        //! Light novel pictures
        //? https://hmtai.herokuapp.com/sfw/mobileWallpaper will return a json response of the following format :
        /** {
         *      url: 'image url'
         *  }
         */

        $io->section('Downloading light novel pictures');

        $experienceProgressBar = new ProgressBar($output, 60);

        for ($i=1; $i < 61; $i++) { 
            $response = $this->client->request(
                'GET',
                'https://hmtai.herokuapp.com/sfw/wallpaper'
            );

            $responseContent = $response->toArray();
            $imageUrl = $responseContent['url'];

            $content = file_get_contents($imageUrl);

            //Store in the filesystem.
            $fp = fopen("public/images/lightNovelPictures/" . $i . '.jpg', "w");
            fwrite($fp, $content);
            fclose($fp);

            $experienceProgressBar->advance();
        }

        $io->info('Light novel pictures => Ok!');

        //! Manga pictures
        //? https://hmtai.herokuapp.com/sfw/mobileWallpaper will return a json response of the following format :
        /** {
         *      url: 'image url'
         *  }
         */

        $io->section('Downloading manga pictures');

        $experienceProgressBar = new ProgressBar($output, 60);

        for ($i=1; $i < 61; $i++) { 
            $response = $this->client->request(
                'GET',
                'https://hmtai.herokuapp.com/sfw/wallpaper'
            );

            $responseContent = $response->toArray();
            $imageUrl = $responseContent['url'];

            $content = file_get_contents($imageUrl);

            //Store in the filesystem.
            $fp = fopen("public/images/mangaPictures/" . $i . '.jpg', "w");
            fwrite($fp, $content);
            fclose($fp);

            $experienceProgressBar->advance();
        }

        $io->info('Manga pictures => Ok!');

        //! Anime pictures
        //? https://hmtai.herokuapp.com/sfw/mobileWallpaper will return a json response of the following format :
        /** {
         *      url: 'image url'
         *  }
         */

        $io->section('Downloading anime pictures');

        $experienceProgressBar = new ProgressBar($output, 60);

        for ($i=1; $i < 61; $i++) { 
            $response = $this->client->request(
                'GET',
                'https://hmtai.herokuapp.com/sfw/wallpaper'
            );

            $responseContent = $response->toArray();
            $imageUrl = $responseContent['url'];

            $content = file_get_contents($imageUrl);

            //Store in the filesystem.
            $fp = fopen("public/images/animePictures/" . $i . '.jpg', "w");
            fwrite($fp, $content);
            fclose($fp);

            $experienceProgressBar->advance();
        }

        $io->info('Anime pictures => Ok!');

        $io->success([
            'Success, the following pictures were downloaded :',
            '20 user pictures,',
            '20 user banners,',
            '15 work pictures,',
            '60 movie pictures,',
            '60 light novel pictures,',
            '60 manga pictures,',
            '60 anime pictures',
        ]);
        return Command::SUCCESS;
    }
}
