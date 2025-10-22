<?php
// src/Repository/StationRepository.php

namespace App\Repository;

use App\Entity\Station;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Station>
 */
class StationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Station::class);
    }

    /**
     * Trouve toutes les stations d'une structure
     */
    public function findByStructure(int $structureId): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.structure = :structureId')
            ->setParameter('structureId', $structureId)
            ->orderBy('s.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Compte le nombre de stations d'une structure
     */
    public function countByStructure(int $structureId): int
    {
        return $this->createQueryBuilder('s')
            ->select('COUNT(s.id)')
            ->andWhere('s.structure = :structureId')
            ->setParameter('structureId', $structureId)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
