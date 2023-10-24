<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
{
    public function __construct(private UserRepository $userRepository, private PaginatorInterface $paginator)
    {
    }

    #[Route('/', name: 'users_index')]
    public function index(Request $request): Response
    {
        $page = $request->query->get("page", 1);
        $limit = 10;
        $users = $this->paginator->paginate($this->userRepository->findBy([], ["name" => "DESC"]), $page, $limit);

        return $this->render('user/index.html.twig', [
            'users' => $users
        ]);
    }
}
