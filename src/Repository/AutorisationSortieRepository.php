<?php

namespace App\Repository;

use DateTime;
use App\Entity\User;
use App\Entity\AutorisationSortie;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method AutorisationSortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method AutorisationSortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method AutorisationSortie[]    findAll()
 * @method AutorisationSortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AutorisationSortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AutorisationSortie::class);
    }
    public function findOneByEmployerAndDate(string $date, User $employer): ?AutorisationSortie
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.employer = :employer')
            ->andWhere('a.debut <= :date')
            ->andWhere('a.fin >= :date')
            ->setParameter('employer', $employer)
            ->setParameter('date', new DateTime($date))
            ->getQuery()
            ->getOneOrNullResult();;
    }

    // /**
    //  * @return AutorisationSortie[] Returns an array of AutorisationSortie objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AutorisationSortie
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
