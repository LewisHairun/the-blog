<?php

namespace App\Command;

use App\Entity\Photo;
use App\Repository\AlbumRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:import:photos',
    description: 'Importe les données photos',
)]
class ImportPhotosCommand extends Command
{
    public function __construct(private HttpClientInterface $httpClient, 
                                private EntityManagerInterface $entityManager, 
                                private AlbumRepository $albumRepository,
                                private string $uploadPhoto,
                                private string $uploadPhotoThumbnail)
    {
        parent::__construct();
    }

   

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $data = $this->httpClient->request(Request::METHOD_GET, "https://jsonplaceholder.typicode.com/photos")->toArray();
        $bar = new ProgressBar($output);
        $bar->start();   
        $i = 0;
        
        foreach ($data as $item) {
            if ($i >= 20) {
                $io->success('Les données photos sont importées avec succès');

                return Command::SUCCESS;
            }

            $photo = new Photo;
            $album = $this->albumRepository->find($item["albumId"]);
            $title = $item["title"];

            $url = $item["url"];
            $filenameUrl = explode("/", $url);
            $filenameUrl = end($filenameUrl) . ".jpg";

            $urlFile = file_get_contents((string) $url);
            $urlToPublic = $this->uploadPhoto . "/" . $filenameUrl; 

            file_put_contents($urlToPublic, $urlFile);

            $thumbnailUrl = $item["thumbnailUrl"];
            $filenameThumbnailUrl = explode("/", $thumbnailUrl);
            $filenameThumbnailUrl = end($filenameThumbnailUrl) . ".jpg";

            $thumbnailUrlFile = file_get_contents((string) $url);
            $thumbnailUrlToPublic = $this->uploadPhotoThumbnail . "/" . $filenameUrl; 

            file_put_contents($thumbnailUrlToPublic, $thumbnailUrlFile);

            $photo->setTitle($title);
            $photo->setUrl($filenameUrl);
            $photo->setThumbnailUrl($filenameThumbnailUrl);
            $photo->setAlbum($album);
            
            $this->entityManager->persist($photo);

            $bar->advance();

            $i++;
        }

        $this->entityManager->flush();
        
        $bar->finish();

        $io->success('Les données photos sont importées avec succès');

        return Command::SUCCESS;
    }
}
