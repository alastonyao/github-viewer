<?php

namespace App\Repository;

use App\Entity\Commits;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Commits|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commits|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commits[]    findAll()
 * @method Commits[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommitsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commits::class);
    }

    public function findCommitByIdCommit($idCommit)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.commit_id = :val')
            ->setParameter('val', $idCommit)
            ->getQuery()
            ->getResult();
    }

    public function findAllCommitByUrlRepo($urlRepo)
    {
        return $this->createQueryBuilder('c')
            ->where('c.urlRepo = :val')
            ->setParameter('val', $urlRepo)
            ->getQuery()
            ->getResult();
    }

    public function findAllcommitByUrlRepoNoDeletedBranch($urlRepo,$nameBranch)
    {
        return $this->createQueryBuilder('c')
            ->where('c.urlRepo = :val AND c.nameBranch = :val1 AND c.isDelete = 0')
            ->setParameter('val', $urlRepo)
            ->setParameter('val1',$nameBranch)
            ->getQuery()
            ->getResult();
    }
}
    

    // /**
    //  * @return Commits[] Returns an array of Commits objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Commits
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
