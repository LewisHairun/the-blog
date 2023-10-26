<?php

namespace App\Command;

use App\Entity\Photo;
use App\Repository\AlbumRepository;
use App\Service\UploadAwsService;
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
                                private string $uploadTmp,
                                private UploadAwsService $uploadAwsService)
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

        if (!file_exists($this->uploadTmp)) {
            mkdir($this->uploadTmp, 0755);
        }

        foreach ($data as $item) {
            // supprimer le compteur si vous voulez enregitrer tous les photos
            if ($i >= 1)  break;

            $photo = new Photo;
            $album = $this->albumRepository->find($item["albumId"]);
            $title = $item["title"];
            $filenameUrl = $this->getFileName($item["url"], $this->uploadTmp);
            $filenameThumbnailUrl = $this->getFileName($item["thumbnailUrl"], $this->uploadTmp);

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

    private function getFileName(string $url, string $pathUpload): string
    {

        $filename = explode("/", $url);
        $fileSize = $filename[3];
        $filename = end($filename) . "-" . $fileSize. ".jpg";

        $urlFile = file_get_contents((string) $url);
        $pathUrl = $pathUpload . "/" . $filename; 

        file_put_contents($pathUrl, $urlFile);

        $this->uploadAwsService->upload($pathUrl, $filename);

        return $filename;
    } 
}
