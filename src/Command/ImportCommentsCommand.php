<?php

namespace App\Command;

use App\Entity\Comment;
use App\Repository\PostRepository;
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
    name: 'app:import:comments',
    description: 'Importe les données commentaires',
)]
class ImportCommentsCommand extends Command
{
    public function __construct(private HttpClientInterface $httpClient, 
                                private EntityManagerInterface $entityManager, 
                                private PostRepository $postRepository)
    {
        parent::__construct();
    }

   

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $data = $this->httpClient->request(Request::METHOD_GET, "https://jsonplaceholder.typicode.com/comments")->toArray();
        $bar = new ProgressBar($output);
        $bar->start();   
        
        foreach ($data as $item) {
            $comment = new Comment;
            $post = $this->postRepository->find($item["postId"]);
            $body = $item["body"];

            $comment->setPost($post);
            $comment->setBody($body);

            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            $bar->advance();
        }
        
        $bar->finish();

        $io->success('Les données commentaires sont importées avec succès');

        return Command::SUCCESS;
    }
}
