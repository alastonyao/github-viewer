<?php

namespace App\Controller;

use App\Entity\Repositories;
use App\Repository\CommitsRepository;
use App\Repository\RepositoriesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\component\httpfoundation\Response;
use Cz\Git\GitRepository;
use DateTime;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class AddRepositoryController extends AbstractController
{

    
    /**
     * @Route("/repository/add",methods={"POST"} )
     */
    public function addRepo(Request $request, RepositoriesRepository $repository, CommitsRepository $commitManager): Response
    {
        
        if (isset($request->request)) {

            $this->createRepository($request,$commitManager);
            $repos = $repository->findAll();

            foreach($repos as $repo)
            {
                $data[] = array(
                    'name' => $repo->getName(),
                    'id' => $repo->getId()
                );
            }

            return $data = new JsonResponse($data); 
        }
        else{
            return $this->render('home/home.html.twig');
        }        
    }

    /**
     * @param Request $request
     */
    private function createRepository(Request $request, CommitsRepository $commitManager)
    {
        $result = '';
        $url = $request->request->get("url");

        if ($url !== '')
        {        
            $user = $request->request->get("user");
            $password = $request->request->get("password");
            $myDate = new DateTime();
            $folder = "C:\\wamp64\\www\\gitrepoviewer-alas\\temp\\gitClone" .  $myDate->format("u");
    
            $repo = new Repositories();
            $repo->setName(basename($url, ".git"))
                ->setPassword($password)
                ->setPath($folder)
                ->seturl($url)
                ->setUser($user);
    
            $em = $this->getDoctrine()->getManager();
            $em->persist($repo);
            
            $this->updateOldCommitdeleted($repo->getUrl(),$commitManager,$em);
            $em->flush();

            $result = GitRepository::cloneRepository($url, $folder);
        }

        return $result;
    }

    /**
     * Undocumented function
     *
     * @param Repositories $myRepository
     * @return Array
     */
    private static function getAllBrancheFromRepo(Repositories $myRepository, CommitsRepository $commitManager): array
    {
        $gitRepo = new GitRepository($myRepository->getPath());
        $branches = $gitRepo->getBranches();

        return $branches;
    }

    private function updateOldCommitdeleted($urlRepo,CommitsRepository $commitManager,ObjectManager $em)
    {
        $commits = $commitManager->findAllCommitByUrlRepo($urlRepo);
        foreach($commits as $commit)
        {
            $commit->setIsDelete(false);
            $em->persist($commit);
        }

    }
}
