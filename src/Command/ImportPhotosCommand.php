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
                                private AlbumRepository $albumRepository)
    {
        parent::__construct();
    }

   

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $data = $this->httpClient->request(Request::METHOD_GET, "https://jsonplaceholder.typicode.com/photos")->toArray();
        $bar = new ProgressBar($output);
        $bar->start();   
        
        foreach ($data as $item) {
            $photo = new Photo;
            $album = $this->albumRepository->find($item["albumId"]);
            $title = $item["title"];
            $url = $item["url"];
            $thumbnailUrl = $item["thumbnailUrl"];

            $photo->setTitle($title);
            $photo->setUrl($url);
            $photo->setThumbnailUrl($thumbnailUrl);
            $photo->setAlbum($album);
            
            $this->entityManager->persist($photo);
            $this->entityManager->flush();

            $bar->advance();
        }
        
        $bar->finish();

        $io->success('Les données photos sont importées avec succès');

        return Command::SUCCESS;
    }
}
