<?php

namespace App\Repository;

use App\Entity\ExchangeObject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ExchangeObject>
 */
class ExchangeObjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExchangeObject::class);
    }

    /**
     * @return ExchangeObject[]
     */
    public function findFeaturedForHome(int $limit = 3): array
    {
        return $this->createQueryBuilder('exchangeObject')
            ->andWhere('exchangeObject.isFeatured = :isFeatured')
            ->setParameter('isFeatured', true)
            ->orderBy('exchangeObject.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
