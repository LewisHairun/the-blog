<?php

namespace App\Command;

use App\Entity\Album;
use App\Repository\UserRepository;
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
    name: 'app:import:albums',
    description: 'Importe les données albums',
)]
class ImportAlbumsCommand extends Command
{
    public function __construct(private HttpClientInterface $httpClient, 
                                private EntityManagerInterface $entityManager, 
                                private UserRepository $userRepository)
    {
        parent::__construct();
    }

   

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $data = $this->httpClient->request(Request::METHOD_GET, "https://jsonplaceholder.typicode.com/albums")->toArray();
        $bar = new ProgressBar($output);
        $bar->start();   
        
        foreach ($data as $item) {
            $album = new Album;
            $user = $this->userRepository->find($item["userId"]);
            $title = $item["title"];

            $album->setTitle($title);
            $album->setUser($user);
            
            $this->entityManager->persist($album);
            $this->entityManager->flush();

            $bar->advance();
        }
        
        $bar->finish();

        $io->success('Les données albums sont importées avec succès');

        return Command::SUCCESS;
    }
}
