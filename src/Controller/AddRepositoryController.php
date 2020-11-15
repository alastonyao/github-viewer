<?php

namespace App\Controller;

use App\Entity\Repositories;
use App\Repository\RepositoriesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\component\httpfoundation\Response;
use Cz\Git\GitRepository;
use DateTime;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;

class AddRepositoryController extends AbstractController
{

    /**
     * @Route("/repository/add",methods={"POST"} )
     */
    public function addRepo(Request $request, RepositoriesRepository $repository): Response
    {
        if ($request->isMethod('POST')) {
            // dd($request->request);
            $url = $request->request->get("url");
            $user = $request->request->get("user");
            $password = $request->request->get("password");
            $myDate = new DateTime();
            $folder = "C:\\wamp64\\www\\gitrepoviewer-alas\\temp\\gitClone" .  $myDate->format("u");

            $repo = new Repositories();
            $repo->setNom(basename($url, ".git"))
                ->setPassword($password)
                ->setPath($folder)
                ->seturl($url)
                ->setUser($user);

            $em = $this->getDoctrine()->getManager();
            $em->persist($repo);
            $em->flush();

            $result = GitRepository::cloneRepository($url, $folder);
            dd($result);
        }
        $repos = $repository->findAll();

        return $this->render('/home/home.html.twig', ['repositories' => $repos]);
    }
}
