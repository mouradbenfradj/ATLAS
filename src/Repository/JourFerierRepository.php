<?php

namespace App\Repository;

use App\Entity\JourFerier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method JourFerier|null find($id, $lockMode = null, $lockVersion = null)
 * @method JourFerier|null findOneBy(array $criteria, array $orderBy = null)
 * @method JourFerier[]    findAll()
 * @method JourFerier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JourFerierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JourFerier::class);
    }

    // /**
    //  * @return JourFerier[] Returns an array of JourFerier objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('j.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?JourFerier
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
