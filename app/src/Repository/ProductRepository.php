<?php

namespace App\Repository;

use App\Entity\Product;
use App\Data\SearchData;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    private $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Product::class);

        $this->paginator = $paginator;
    }

    /**
     * Recupère les produits en lien avec une recherche
     *
     * @return PaginationInterface
     */
    public function findSearch(SearchData $search): PaginationInterface
    {
        $query = $this
        ->createQueryBuilder('p')
        ->select('c', 'p')
        ->join('p.category', 'c')
        ;

        if (!empty($search->q)) {
            $query = $query
            ->andWhere('p.title LIKE :q')
            ->setParameter('q', "%{$search->q}%")
            ;
        }

        if (!empty($search->min)) {
            $query = $query
            ->andWhere('p.price >= :min')
            ->setParameter('min', $search->min)
            ;
        }

        if (!empty($search->max)) {
            $query = $query
            ->andWhere('p.price <= :max')
            ->setParameter('max', $search->max)
            ;
        }

        if (!empty($search->categories)) {
            $query = $query
            ->andWhere('c.id IN (:categories)')
            ->setParameter('categories', $search->categories)
            ;
        }

        if (!empty($search->promo)) {
            $query = $query
            ->andWhere('p.promo = 1')
           
            ;
        }

         $query = $query->getQuery();

         return $this->paginator->paginate(
             $query, 
             $search->page,
             12
         );

    }

   

    
}
