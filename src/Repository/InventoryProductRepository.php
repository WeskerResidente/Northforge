<?php

namespace App\Repository;

use App\Entity\InventoryProduct;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InventoryProduct>
 */
class InventoryProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InventoryProduct::class);
    }

    /**
     * @return InventoryProduct[]
     */
    public function findForOwner(User $owner): array
    {
        return $this->createQueryBuilder('product')
            ->andWhere('product.owner = :owner')
            ->setParameter('owner', $owner)
            ->orderBy('product.createdAt', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
