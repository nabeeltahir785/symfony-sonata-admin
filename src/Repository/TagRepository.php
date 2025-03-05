<?php

namespace App\Repository;

use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    /**
     * Find popular tags
     *
     * @param int $limit
     * @return array
     */
    public function findPopularTags(int $limit = 10)
    {
        return $this->createQueryBuilder('t')
            ->select('t as tag, COUNT(p.id) as productCount')
            ->leftJoin('t.products', 'p')
            ->groupBy('t.id')
            ->orderBy('productCount', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}