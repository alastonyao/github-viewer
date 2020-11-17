<?php

namespace App\Controller;

use App\Repository\RepositoriesRepository;
use App\Entity\Repositories;
use Cz\Git\GitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class AjaxRequestController extends AbstractController
{
    /**
     * 
     * @Route("/branches/comboBox")
     * @param Request $request
     * @param RepositoriesRepository $repositoryManager
     * @return void
     */
    public function fillBranchesdropDown(Request $request, RepositoriesRepository $repositoryManager)
    {
        $repository = $repositoryManager->find(2);
        
        $gitrepo = new GitRepository($repository->getPath());
        $branches = $gitrepo->getBranches();
        
        if ($request->isXmlHttpRequest()) {
            $jsonData = array();
            $idx = 0;
            foreach ($branches as $branch) {
                $temp = array(
                    "name" => $branch
                );
                $jsonData[$idx++] = $temp;
            }
            dump($jsonData);
            return new JsonResponse($jsonData);
        } else {
            return $this->render('home/home.html.twig');
        }
    }
}
