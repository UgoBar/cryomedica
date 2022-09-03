<?php

namespace App\Repository;

use App\Entity\CryoBio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CryoBio>
 *
 * @method CryoBio|null find($id, $lockMode = null, $lockVersion = null)
 * @method CryoBio|null findOneBy(array $criteria, array $orderBy = null)
 * @method CryoBio[]    findAll()
 * @method CryoBio[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CryoBioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CryoBio::class);
    }

    public function add(CryoBio $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CryoBio $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


//    /**
//     * @return array
//     * @throws \Doctrine\ORM\NoResultException
//     * @throws \Doctrine\ORM\NonUniqueResultException
//     */
//    public function findLastRecord(): array
//    {
//        return $this->createQueryBuilder('bio')
//            ->select('bio')
//            ->from('CryoBio')
////            ->leftJoin('bio.Cryomedia', 'media')
//            ->orderBy('bio.id', 'ASC')
//            ->setMaxResults(1)
//            ->getQuery()
//            ->getScalarResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CryoBio
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
