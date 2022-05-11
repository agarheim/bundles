<?php

namespace App\Payments\LiqPayRetailCrmBundle\Repository;

use App\Payments\LiqPayRetailCrmBundle\Entity\LiqpayOrders;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LqpayOrders|null find($id, $lockMode = null, $lockVersion = null)
 * @method LqpayOrders|null findOneBy(array $criteria, array $orderBy = null)
 * @method LqpayOrders[]    findAll()
 * @method LqpayOrders[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LiqpayOrdersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LiqpayOrders::class);
    }

    // /**
    //  * @return LqpayOrders[] Returns an array of LqpayOrders objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LqpayOrders
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
