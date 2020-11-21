<?php

namespace App\Tests\Util;

use App\Entity\Repositories;
use App\Repository\CommitsRepository;
use App\Repository\RepositoriesRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Email;

class GithubREpoTest extends WebTestCase
{
    public function testlistRepository()
    {
        $client = static::createClient();
        $client->request('GET','/repository/list');
        
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

    }

    public function testAddRepository()
    {
      $client = static::createClient();
      $client->request('POST','/repository/add',['bapirest'=>true,'url'=>'https://github.com/github/training-kit.git']);
      
      $this->assertResponseStatusCodeSame(Response::HTTP_OK);

    }

   public function testDeleteCommit()
   {
      $client = static::createClient();
      $commitRepository = static::$container->get(CommitsRepository::class);
      $testCommit = $commitRepository->findOneBy(['isDelete' => false]);

      $commitRepository->deleteCommit($testCommit->getId());
      $testResult = $commitRepository->getOneCommit($testCommit->getId());
      $this->assertEquals(1,$testResult[0]['is_delete']);

      $RepoRepository = static::$container->get(RepositoriesRepository::class);
      $RepoRepository->updateOldCommitdeletedForRepo($testCommit->getUrlRepo());

      $testResult = $commitRepository->getOneCommit($testCommit->getId());
      $this->assertEquals(0,$testResult[0]['is_delete']);

   }


   public function testListCommits()
   {
      $client = static::createClient();
      $RepoRepository = static::$container->get(RepositoriesRepository::class);
      $testRepos = $RepoRepository->findAll();
      $testRepo = $testRepos[0];


      $commitRepository = static::$container->get(CommitsRepository::class);
      $testCommit = $commitRepository->findOneBy(['urlRepo' => $testRepo->getUrl()]);

      
      $client->request('POST', '/commit/getAll', ['nameBranch'=>$testCommit->getNameBranch(),'idRepo'=>$testRepo->getId()]);

      $this->assertResponseStatusCodeSame(Response::HTTP_OK);
   }
}