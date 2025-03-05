<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * Find published products
     *
     * @return Product[]
     */
    public function findPublishedProducts()
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.status = :status')
            ->setParameter('status', 1) // Published
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find products by category
     *
     * @param int $categoryId
     * @return Product[]
     */
    public function findByCategory(int $categoryId)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.category = :categoryId')
            ->setParameter('categoryId', $categoryId)
            ->andWhere('p.status = :status')
            ->setParameter('status', 1) // Published only
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find products by price range
     *
     * @param float $min
     * @param float $max
     * @return Product[]
     */
    public function findByPriceRange(float $min, float $max)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.price >= :min')
            ->andWhere('p.price <= :max')
            ->setParameter('min', $min)
            ->setParameter('max', $max)
            ->andWhere('p.status = :status')
            ->setParameter('status', 1) // Published only
            ->orderBy('p.price', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Search products by name or description
     *
     * @param string $query
     * @return Product[]
     */
    public function searchProducts(string $query)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.name LIKE :query OR p.description LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->andWhere('p.status = :status')
            ->setParameter('status', 1) // Published only
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find related products (same category)
     *
     * @param Product $product
     * @param int $limit
     * @return Product[]
     */
    public function findRelatedProducts(Product $product, int $limit = 4)
    {
        if (!$product->getCategory()) {
            return [];
        }

        return $this->createQueryBuilder('p')
            ->andWhere('p.category = :category')
            ->setParameter('category', $product->getCategory())
            ->andWhere('p.id != :productId')
            ->setParameter('productId', $product->getId())
            ->andWhere('p.status = :status')
            ->setParameter('status', 1) // Published only
            ->setMaxResults($limit)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}