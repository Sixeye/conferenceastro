<?php

namespace App\Repository;

use App\Entity\InitialisationPassword;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method InitialisationPassword|null find($id, $lockMode = null, $lockVersion = null)
 * @method InitialisationPassword|null findOneBy(array $criteria, array $orderBy = null)
 * @method InitialisationPassword[]    findAll()
 * @method InitialisationPassword[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InitialisationPasswordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InitialisationPassword::class);
    }

    // /**
    //  * @return InitialisationPassword[] Returns an array of InitialisationPassword objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?InitialisationPassword
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}