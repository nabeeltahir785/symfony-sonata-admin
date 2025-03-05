<?php

namespace App\Repository;


use App\Entity\ProductVariant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProductVariantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductVariant::class);
    }

    /**
     * Find variants with low stock
     *
     * @param int $threshold
     * @return ProductVariant[]
     */
    public function findLowStockVariants(int $threshold = 5)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.stock <= :threshold')
            ->andWhere('v.stock > 0')
            ->setParameter('threshold', $threshold)
            ->orderBy('v.stock', 'ASC')
            ->getQuery()
            ->getResult();
    }
}