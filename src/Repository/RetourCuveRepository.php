<?php

namespace App\Repository;

use App\Entity\RetourCuve;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @extends ServiceEntityRepository<RetourCuve>
 */
class RetourCuveRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RetourCuve::class);
    }

    /**
     * @param int $stationId
     * @return int|mixed|string
     */
    public function getStationRetourEnCuves(int $stationId)
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.typeCarburant', 'typeCarburant')
            ->leftJoin('typeCarburant.station', 'station')
            ->where('station.id = :stationId')
            ->setParameter('stationId', $stationId)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $dateInf
     * @param $dateSup
     * @param $type
     * @return int|mixed|string
     * @throws Exception
     */
    public function getRetourEnCuveByDate($dateInf, $dateSup, $type)
    {
        $dateDeb = new \DateTime($dateInf, new \DateTimeZone('GMT'));
        $dateFin = new \DateTime($dateSup, new \DateTimeZone('GMT'));

        return $this->createQueryBuilder('r')
            ->leftJoin('r.typeCarburant', 't')
            ->andWhere('t.id = :val')
            ->andWhere('r.createdAt BETWEEN :dateDeb and :dateFin')
            ->setParameter('val', $type)
            ->setParameter('dateDeb', $dateDeb)
            ->setParameter('dateFin', $dateFin)
            ->orderBy('r.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return RetourCuve[] Returns an array of RetourCuve objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?RetourCuve
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
