<?php

namespace App\Repository;

use App\Entity\Conger;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Conger|null find($id, $lockMode = null, $lockVersion = null)
 * @method Conger|null findOneBy(array $criteria, array $orderBy = null)
 * @method Conger[]    findAll()
 * @method Conger[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CongerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conger::class);
    }

    // /**
    //  * @return Conger[] Returns an array of Conger objects
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
    public function findOneBySomeField($value): ?Conger
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
