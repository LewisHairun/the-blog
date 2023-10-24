<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class PostController extends AbstractController
{
    public function __construct(private PostRepository $postRepository, private PaginatorInterface $paginator)
    {
    }

    #[Route('/articles', name: 'posts_index')]
    public function index(Request $request): Response
    {
        $page = $request->query->get("page", 1);
        $limit = 10;
        $posts = $this->paginator->paginate($this->postRepository->findBy([], []), $page, $limit);

        return $this->render('post/index.html.twig', [
            'posts' => $posts
        ]);
    }
}
