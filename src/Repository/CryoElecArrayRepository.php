<?php

namespace App\Repository;

use App\Entity\CryoElecArray;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CryoElecArray>
 *
 * @method CryoElecArray|null find($id, $lockMode = null, $lockVersion = null)
 * @method CryoElecArray|null findOneBy(array $criteria, array $orderBy = null)
 * @method CryoElecArray[]    findAll()
 * @method CryoElecArray[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CryoElecArrayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CryoElecArray::class);
    }

    public function add(CryoElecArray $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CryoElecArray $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return CryoElecArray[] Returns an array of CryoElecArray objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CryoElecArray
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
