<?php

namespace App\Controller;

use App\Entity\Repositories;
use App\Repository\CommitsRepository;
use App\Repository\RepositoriesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Cz\Git\GitRepository;
use Doctrine\Persistence\ObjectManager;
use PhpParser\Node\Stmt\TryCatch;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AddRepositoryController extends AbstractController
{

    /**
     * @Route("/repository/add",methods={"POST"} )
     */
    public function addRepo(Request $request, RepositoriesRepository $repository, CommitsRepository $commitManager): Response
    {
        try {
            $bFromAPIRest = $request->request->get("bapirest") ? $request->request->get("bapirest") : false;
            if (isset($request->request)) {
            
                $repo = $this->createRepository($request, $repository, $commitManager);
                if ($repo == false)
                {
                    return Response::HTTP_NOT_ACCEPTABLE;
                }
                if ($bFromAPIRest === false ) {
                    $repos = $repository->findAll();
    
                    foreach ($repos as $repo) {
                        $data[] = array(
                            'name' => $repo->getName(),
                            'id' => $repo->getId()
                        );
                    }
                } else {
                    $data[] = array(
                        'name' => $repo->getName(),
                        'url' => $repo->getUrl(),
                        'description' => $repo->getDescription()
                    );
                }
    
                return $data = new JsonResponse($data);
            } else {
                return Response::HTTP_NOT_ACCEPTABLE;
                
            }
        } catch (\Throwable $th) {
            return Response::HTTP_NOT_ACCEPTABLE;
        }
        
    }

    /**
     * @Route("/repository/list",methods={"GET"} )
     */
    public function listingRepository(RepositoriesRepository $repository) : Response
    {
        try {
            $repos = $repository->findAll();
        $data = [];
        
        foreach ($repos as $repo) {
            $data[] = array(
                'name' => $repo->getName(),
                'url' => $repo->getUrl(),
                'description' => $repo->getDescription()
            );
        }
        return $data = new JsonResponse($data);
        } catch (\Throwable $th) {
            return Response::HTTP_NOT_ACCEPTABLE;
        }
        
    }

    /**
     * @Route("/repository/delete",methods={"POST"} )
     */
    public function deleteRepository(Request $request,RepositoriesRepository $repository) : Response
    {

        try {
            $data = [];

        if (isset($request->request)) {
            $em = $this->getDoctrine()->getManager();
            $url = $request->request->get("url") ? $request->request->get("url") : '';
            if ($url !== '')
            {
                $repo = $repository->findOneBy(['url' => $url]);
                $em->remove($repo);

                $em->flush();
                $data[] = array(
                    'Result' => "Delete Successful");
            }
            else{
                $data[] = array(
                    'Result' => "please give a valid url");
            }
            
        }
        
        return $data = new JsonResponse($data);
        } catch (\Throwable $th) {
            return Response::HTTP_NOT_ACCEPTABLE;
        }
        
    }


    private function createRepository(Request $request, RepositoriesRepository $repository, CommitsRepository $commitManager)
    {

        $url = $request->request->get("url");
        if ($url !== '') {
            $user = $request->request->get("user") ? $request->request->get("user") : "";
            $password = $request->request->get("password") ? $request->request->get("password") : "";
            $pathrepo = $this->getParameter('app.pathrepo');

            $repo = $repository->saveRepo($url, $pathrepo, $password, $user);

            if (!file_exists($repo->getPath())) {
                GitRepository::cloneRepository($url, $repo->getPath());
            }
            return $repo;
        }
        return false;
    }
}
