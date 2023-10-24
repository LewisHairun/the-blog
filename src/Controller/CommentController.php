<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class CommentController extends AbstractController
{
    public function __construct(private CommentRepository $commentRepository, private PaginatorInterface $paginator)
    {
    }

    #[Route('/commentaires', name: 'comments_index')]
    public function index(Request $request): Response
    {
        $page = $request->query->get("page", 1);
        $limit = 10;
        $comments = $this->paginator->paginate($this->commentRepository->findBy([], []), $page, $limit);

        return $this->render('comment/index.html.twig', [
            'comments' => $comments
        ]);
    }
}
