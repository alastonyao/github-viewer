<?php

namespace App\Controller;

use App\Entity\Repositories;
use App\Repository\RepositoriesRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\component\httpfoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    private $repository;
    private $em;

    public function __construct(RepositoriesRepository $repository)
    {
        $this->repository = $repository;
    }
    /**
     * @Route("/")
     * @return Response
     */
    public function home(): Response
    {
        $repos = $this->repository->findAll();

        return $this->render('home/home.html.twig', ['repositories' => $repos]);
    }
}
