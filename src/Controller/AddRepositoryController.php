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
use PhpParser\Node\Expr\Cast\Array_;
use Symfony\Component\Routing\Annotation\Route;

class AddRepositoryController extends AbstractController
{

    /**
     * @Route("/repository/add",methods={"POST"} )
     */
    public function addRepo(Request $request, RepositoriesRepository $repository): Response
    {
        if ($request->isMethod('POST')) {

            $myRepository = $repository->find(2);
            $gitRepo = new GitRepository($myRepository->getPath());
            $branches = $gitRepo->getBranches();
            dd($branches);
        }
        $repos = $repository->findAll();

        return $this->render('/home/home.html.twig', ['repositories' => $repos]);
    }

    /**
     * @param Request $request
     */
    private function createRepository(Request $request)
    {
        $result = '';

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

        return $result;
    }

    /**
     * Undocumented function
     *
     * @param Repositories $myRepository
     * @return Array
     */
    private static function getAllBrancheFromRepo(Repositories $myRepository): array
    {
        $gitRepo = new GitRepository($myRepository->getPath());
        $branches = $gitRepo->getBranches();

        return $branches;
    }
}
