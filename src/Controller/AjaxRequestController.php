<?php

namespace App\Controller;

use App\Entity\Commits;
use App\Repository\RepositoriesRepository;
use App\Entity\Repositories;
use App\Repository\CommitsRepository;
use Cz\Git\GitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class AjaxRequestController extends AbstractController
{

    /**
     * 
     * @Route("/branches/list")
     * @param Request $request
     * @param RepositoriesRepository $repositoryManager
     * @return void
     */
    public function fillBranchesList(Request $request, RepositoriesRepository $repositoryManager)
    {
        if (isset($request->request)) {

            $idrepo = $request->request->get('idRepo');
            $repository = $repositoryManager->find($idrepo);
            
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

                $jsonData = new JsonResponse($jsonData); 
                return $jsonData;
            } else {
                return $this->render('home/home.html.twig');
            }
        } else {
            return $this->render('home/home.html.twig');
        }
        
    }

    /**
     * Undocumented function
     * @Route("/Commit/getAll")
     * @param Request $request
     * @param RepositoriesRepository $repositoryManager
     * @return void
     */
    public function getAllCommitForBranch(Request $request,CommitsRepository $commitManager,RepositoriesRepository $repositoryManager) : Response
    {
        if (isset($request->request)) {
            $nameBranch = ($request->request->get("nameBranch"));
            $idRepo = ($request->request->get("idRepo"));

            $myRepository = $repositoryManager->find($idRepo);
            $gitRepo = new GitRepository($myRepository->getPath());

            $gitRepo->checkout($nameBranch);

            $jsonData = array();
            $jsonData = $this->getallCommitSandInsertNewCommitInDb($gitRepo,$nameBranch, $commitManager, $myRepository);

            $jsonData = new JsonResponse($jsonData); 
            return $jsonData;
        } else {
            return $this->render('home/home.html.twig');
        }
    }

    private function getallCommitSandInsertNewCommitInDb($gitRepo,$nameBranch,CommitsRepository $commitManager,$repo)
    {
        $commitIds = $gitRepo->getAllCommitIdsForBranch($nameBranch);
        $allCommits = array();
        $em = $this->getDoctrine()->getManager();

        foreach ($commitIds as $commitId) {
            $commit = $commitManager->findOneBy(['commitId' => $commitId]);
            if ($commit !== null && $commit->getIsDelete() === false)
            {
                $data = array(
                    'commit' => $commitId,
                    'subject' => $commit->getSubject(),
                    'message' => $commit->getMessage(),
                    'author' => $commit->getAuthor(),
                    'date' => $commit->getDate(),
                );

                $allCommits[] = $data;
                
            } else {
                $commit = $gitRepo->getCommitData($commitId);
                $allCommits[] = $commit;
                $newCommit = new Commits();
                $newCommit->setNameBranch($nameBranch)
                ->setSubject($commit['subject'])
                ->setAuthor($commit['author'])
                ->setMessage($commit['message'])
                ->setDate($commit['date'])
                ->setCommitId($commitId)
                ->setUrlRepo($repo->getUrl())
                ->setIsDelete(false);

                $em->persist($newCommit);

                $data = array(
                    'commit' => $commitId,
                    'subject' => $commit['subject'],
                    'message' => $commit['message'],
                    'author' => $commit['author'],
                    'date' => $commit['date'],
                );

                $allCommits[] = $data;
            }
        }

        $em->flush();

        return $allCommits;
    }
}
