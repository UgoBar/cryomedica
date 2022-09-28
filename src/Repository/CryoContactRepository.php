<?php

namespace App\Repository;

use App\Entity\CryoCenters;
use App\Entity\CryoContact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CryoContact>
 *
 * @method CryoContact|null find($id, $lockMode = null, $lockVersion = null)
 * @method CryoContact|null findOneBy(array $criteria, array $orderBy = null)
 * @method CryoContact[]    findAll()
 * @method CryoContact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CryoContactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CryoContact::class);
    }

    public function add(CryoContact $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CryoContact $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getContactAsArray(): array
    {
        return $this->createQueryBuilder('c')
            ->select([
                'c.firstname', 'c.lastname', 'c.email', 'c.phone',
                'center.name', 'center.city', 'center.is_open'])
            ->leftJoin(CryoCenters::class, 'center', 'WITH', 'c.center = center.id')
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getArrayResult();
    }

//    /**
//     * @return CryoContact[] Returns an array of CryoContact objects
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

//    public function findOneBySomeField($value): ?CryoContact
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
