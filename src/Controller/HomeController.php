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

    public function __construct(RepositoriesRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }
    /**
     * @Route("/") 
     */
    public function home(): Response
    {
        // $repositories = new Repositories();
        // $repositories->seturl("https://github.com/wizardjedi/php-git.git")
        //     ->setNom("php-git");

        // $em = $this->getDoctrine()->getManager();
        // $em->persist($repositories);
        // $em->flush();

        $repo = $this->repository->findAll();
        $repo[0]->setDescription("test");

        $this->em->flush();


        return $this->render('home/home.html.twig');
    }
}
