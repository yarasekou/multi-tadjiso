<?php

namespace App\Repository;

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

//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
