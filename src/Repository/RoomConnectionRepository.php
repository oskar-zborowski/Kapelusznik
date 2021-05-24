<?php

namespace App\Repository;

use App\Entity\RoomConnection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RoomConnection|null find($id, $lockMode = null, $lockVersion = null)
 * @method RoomConnection|null findOneBy(array $criteria, array $orderBy = null)
 * @method RoomConnection[]    findAll()
 * @method RoomConnection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoomConnectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RoomConnection::class);
    }

    // /**
    //  * @return RoomConnection[] Returns an array of RoomConnection objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RoomConnection
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
