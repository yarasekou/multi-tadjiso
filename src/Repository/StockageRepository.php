<?php

namespace App\Repository;

use App\Entity\Stockage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Stockage>
 */
class StockageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stockage::class);
    }

    public function getStockageByStationId($stationId)
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.cuves', 'cuves')
            ->leftJoin('cuves.station', 'station')
            ->where('station.id = :stationId')
            ->setParameter('stationId', $stationId)
            ->orderBy('s.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $typeCarburantId
     * @param $date
     * @return float|int|mixed|string
     * @throws NonUniqueResultException
     */
    public function getQteEntreeByTypeCarburant($typeCarburantId, $date): mixed
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.cuves', 'cuves')
            ->leftJoin('cuves.typeCarburant', 'typeCarburant')
            ->where('typeCarburant.id = :typeCarburantId')
            ->andWhere('s.createdAt >= :date')
            ->select('SUM(s.quantite - s.manquant) as SOMME')
            ->setParameter('typeCarburantId', $typeCarburantId)
            ->setParameter('date', $date)
            ->getQuery()
            ->getOneOrNullResult();
    }


//    /**
//     * @return Stockage[] Returns an array of Stockage objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Stockage
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
