<?php

namespace App\Repository;

use App\Entity\AnimalFeeding;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AnimalFeeding>
 *
 * @method AnimalFeeding|null find($id, $lockMode = null, $lockVersion = null)
 * @method AnimalFeeding|null findOneBy(array $criteria, array $orderBy = null)
 * @method AnimalFeeding[]    findAll()
 * @method AnimalFeeding[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnimalFeedingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AnimalFeeding::class);
    }

//    /**
//     * @return AnimalFeeding[] Returns an array of AnimalFeeding objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?AnimalFeeding
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
