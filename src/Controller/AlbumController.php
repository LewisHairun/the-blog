<?php

namespace App\Controller;

use App\Repository\AlbumRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

class AlbumController extends AbstractController
{
    public function __construct(private AlbumRepository $albumRepository, private PaginatorInterface $paginator)
    {
    }

    #[Route('/albums', name: 'albums_index')]
    public function index(Request $request): Response
    {
        $page = $request->query->get("page", 1);
        $limit = 10;
        $albums = $this->paginator->paginate($this->albumRepository->findBy([], []), $page, $limit);

        return $this->render('album/index.html.twig', [
            'albums' => $albums
        ]);
    }
}
