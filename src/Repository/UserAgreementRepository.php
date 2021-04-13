<?php

namespace App\Repository;

use App\Entity\UserAgreement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserAgreement|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserAgreement|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserAgreement[]    findAll()
 * @method UserAgreement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserAgreementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserAgreement::class);
    }

    // /**
    //  * @return UserAgreement[] Returns an array of UserAgreement objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserAgreement
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
