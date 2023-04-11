<?php

namespace App\Repository;

use App\Entity\Specification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Specification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Specification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Specification[]    findAll()
 * @method Specification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SpecificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Specification::class);
    }

    // /**
    //  * @return Specification[] Returns an array of Specification objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Specification
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
