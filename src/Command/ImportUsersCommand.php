<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\AddressRepository;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:import:users',
    description: 'Importe les données utilisateurs',
)]
class ImportUsersCommand extends Command
{
    public function __construct(private HttpClientInterface $httpClient, 
                                private EntityManagerInterface $entityManager,
                                private UserPasswordHasherInterface $passwordHasher,
                                private AddressRepository $addressRepository,
                                private CompanyRepository $companyRepository)
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
            $user = new User;
            $password = $this->passwordHasher->hashPassword($user, "password");
            $address = $this->addressRepository->findOneBy(["suite" => $item["address"]["suite"], "city" => $item["address"]["city"]]);
            $company = $this->companyRepository->findOneBy(["name" => $item["name"]]);

            $user->setName($item["name"]);
            $user->setUsername($item["username"]);
            $user->setPassword($password);
            $user->setEmail($item["email"]);
            $user->setLatitude($item["address"]["geo"]["lat"]);
            $user->setLongitude($item["address"]["geo"]["lng"]);
            $user->setPhone($item["phone"]);
            $user->setWebsite($item["website"]);
            $user->setAddress($address);
            $user->setCompany($company);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $bar->advance();
        }
        
        $bar->finish();

        $io->success("Les données utilisateurs sont importées avec succès");

        return Command::SUCCESS;
    }
}
