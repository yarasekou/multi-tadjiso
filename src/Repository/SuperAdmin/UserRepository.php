<?php

namespace App\Repository\SuperAdmin;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Trouve tous les super administrateurs
     * (structure = null, role = SUPER_ADMIN, level = 1 ou 2)
     */
    public function findAllSuperAdmins(): array
    {
        $qb = $this->createQueryBuilder('u')
            ->join('u.userRoles', 'r')
            ->where('r.name = :role')
            ->andWhere('u.structure IS NULL')
            ->andWhere('u.level IN (:levels)')
            ->setParameter('levels', [1, 2])
            ->setParameter('role', 'SUPER_ADMIN')
            ->orderBy('u.level', 'ASC');

        return $qb->getQuery()->getResult();
    }
    /**
     * Trouve les utilisateurs administrateurs AVEC structure (level = 3 ET structure != null)
     */
    public function findAdminsWithAdminRoleAndStructure(): array
    {
        return $this->createQueryBuilder('u')
            ->join('u.userRoles', 'r')
            ->andWhere('u.level = :level')
            ->andWhere('r.name = :role')
            ->andWhere('u.structure IS NOT NULL')
            ->setParameter('level', 3)
            ->setParameter('role', 'ADMIN')
            ->orderBy('u.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Vérifie si une structure a déjà un administrateur
     */
    public function structureHasAdmin(?int $structureId): bool
    {
        if (!$structureId) {
            return false;
        }

        return $this->createQueryBuilder('u')
                ->select('COUNT(u.id)')
                ->join('u.userRoles', 'r')
                ->andWhere('u.structure = :structureId')
                ->andWhere('r.name = :role')
                ->andWhere('u.level = :level')
                ->setParameter('structureId', $structureId)
                ->setParameter('role', 'ADMIN')
                ->setParameter('level', 3)
                ->getQuery()
                ->getSingleScalarResult() > 0;
    }

    /**
     * Trouve l'administrateur d'une structure
     */
    public function findAdminByStructure(?int $structureId): ?User
    {
        if (!$structureId) {
            return null;
        }

        return $this->createQueryBuilder('u')
            ->join('u.userRoles', 'r')
            ->andWhere('u.structure = :structureId')
            ->andWhere('r.name = :role')
            ->andWhere('u.level = :level')
            ->setParameter('structureId', $structureId)
            ->setParameter('role', 'ADMIN')
            ->setParameter('level', 3)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Trouve tous les administrateurs du structure
     * (structure not null, role = ADMIN, level = 3 ou 4)
     */
    public function findAllAdminsByStructure(int $structureId): array
    {
        return $this->createQueryBuilder('u')
            ->join('u.userRoles', 'r')
            ->where('r.name = :role')
            ->andWhere('u.structure = :structureId')
            ->andWhere('u.level IN (:levels)') // Levels 3 ET 4
            ->setParameter('structureId', $structureId)
            ->setParameter('role', 'ADMIN')
            ->setParameter('levels', [3, 4]) // Correction : tableau [3, 4]
            ->orderBy('u.level', 'ASC') // Trie par niveau (3 d'abord, puis 4)
            ->addOrderBy('u.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
