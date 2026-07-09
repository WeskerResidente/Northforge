<?php

namespace App\Repository;

use App\Entity\InventoryMovement;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InventoryMovement>
 */
class InventoryMovementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InventoryMovement::class);
    }

    /**
     * @return InventoryMovement[]
     */
    public function findCurrentForOwner(User $owner): array
    {
        return $this->createQueryBuilder('movement')
            ->leftJoin('movement.items', 'item')
            ->addSelect('item')
            ->andWhere('movement.owner = :owner')
            ->setParameter('owner', $owner)
            ->orderBy('movement.movementDate', 'DESC')
            ->addOrderBy('movement.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
