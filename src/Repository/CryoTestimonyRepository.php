<?php

namespace App\Repository;

use App\Entity\CryoTestimony;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CryoTestimony>
 *
 * @method CryoTestimony|null find($id, $lockMode = null, $lockVersion = null)
 * @method CryoTestimony|null findOneBy(array $criteria, array $orderBy = null)
 * @method CryoTestimony[]    findAll()
 * @method CryoTestimony[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CryoTestimonyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CryoTestimony::class);
    }

    public function add(CryoTestimony $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CryoTestimony $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return CryoTestimony[] Returns an array of CryoTestimony objects
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

//    public function findOneBySomeField($value): ?CryoTestimony
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
