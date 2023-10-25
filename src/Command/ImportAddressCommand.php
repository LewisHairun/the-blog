<?php

namespace App\Command;

use App\Entity\Address;
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
    name: 'app:import:address',
    description: 'Importe les données addresses',
)]
class ImportAddressCommand extends Command
{
    public function __construct(private HttpClientInterface $httpClient, 
                                private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $data = $this->httpClient->request(Request::METHOD_GET, "https://jsonplaceholder.typicode.com/users")->toArray();
        $bar = new ProgressBar($output);
        $bar->start();

        foreach ($data as $item) {
            $address = new Address;
            $address->setStreet($item["address"]["street"]);
            $address->setSuite($item["address"]["suite"]);
            $address->setCity($item["address"]["city"]);
            $address->setZipcode($item["address"]["zipcode"]);

            $this->entityManager->persist($address);

            $bar->advance();
        }

        $this->entityManager->flush();
        
        $bar->finish();

        $io->success("Les données addresses sont importées avec succès");

        return Command::SUCCESS;
    }
}
