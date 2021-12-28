<?php

namespace App\Repository;

use App\Entity\Dbf;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Dbf|null find($id, $lockMode = null, $lockVersion = null)
 * @method Dbf|null findOneBy(array $criteria, array $orderBy = null)
 * @method Dbf[]    findAll()
 * @method Dbf[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DbfRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dbf::class);
    }

    // /**
    //  * @return Dbf[] Returns an array of Dbf objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Dbf
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
