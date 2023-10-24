<?php

namespace App\Controller;

use App\Repository\PhotoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class PhotoController extends AbstractController
{
    public function __construct(private PhotoRepository $photoRepository, private PaginatorInterface $paginator)
    {
    }

    #[Route('/photos', name: 'photos_index')]
    public function index(Request $request): Response
    {
        $page = $request->query->get("page", 1);
        $limit = 10;
        $photos = $this->paginator->paginate($this->photoRepository->findBy([], []), $page, $limit);

        return $this->render('photo/index.html.twig', [
            'photos' => $photos
        ]);
    }
}
