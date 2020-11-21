<?php

namespace App\Repository;

use App\Entity\Repositories;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;

/**
 * @method Repositories|null find($id, $lockMode = null, $lockVersion = null)
 * @method Repositories|null findOneBy(array $criteria, array $orderBy = null)
 * @method Repositories[]    findAll()
 * @method Repositories[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RepositoriesRepository extends ServiceEntityRepository
{
    private $entityManager;

    public function __construct(ManagerRegistry $registry,EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct($registry, Repositories::class);
    }

    /**
     *
     * @param string $url
     * @param string $password
     * @param string $folder
     * @param string $user
     * @return void
     */
    public function saveRepo($url, $pathrepo, $password= '', $user = '') : Repositories
    {
        
        $myDate = new DateTime();
        $folder = $pathrepo . $myDate->format("u");

        $repo = $this->findOneBy(['url' => $url]);
        if ($repo == null) {
            $repo = new Repositories();
            $repo->setName(basename($url, ".git"))
                ->setPassword($password)
                ->setPath($folder)
                ->seturl($url)
                ->setUser($user);

            $em = $this->entityManager;
            $em->persist($repo);
            $em->flush();
        }

        $this->updateOldCommitdeletedForRepo($repo->getUrl());

        return $repo;
    }

    public function updateOldCommitdeletedForRepo($urlRepo)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        
        $sql = "UPDATE commits SET is_delete = 0 WHERE url_repo = '" .  $urlRepo . "'";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
    }

    // /**
    //  * @return Repositories[] Returns an array of Repositories objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Repositories
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
