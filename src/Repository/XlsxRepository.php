<?php

namespace App\Repository;

use App\Entity\Xlsx;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Xlsx|null find($id, $lockMode = null, $lockVersion = null)
 * @method Xlsx|null findOneBy(array $criteria, array $orderBy = null)
 * @method Xlsx[]    findAll()
 * @method Xlsx[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class XlsxRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Xlsx::class);
    }

    // /**
    //  * @return Xlsx[] Returns an array of Xlsx objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('x')
            ->andWhere('x.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('x.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Xlsx
    {
        return $this->createQueryBuilder('x')
            ->andWhere('x.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
