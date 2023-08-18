<?php

namespace App\Repository;

use App\Entity\Turnover;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Turnover>
 *
 * @method Turnover|null find($id, $lockMode = null, $lockVersion = null)
 * @method Turnover|null findOneBy(array $criteria, array $orderBy = null)
 * @method Turnover[]    findAll()
 * @method Turnover[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TurnoverRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Turnover::class);
    }

    public function save(Turnover $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Turnover $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Turnover[] Returns an array of Turnover objects
     */
    public function findByCompany($company): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.company = :val')
            ->setParameter('val', $company)
            ->orderBy('t.year', 'ASC')
//            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

//    public function findOneBySomeField($value): ?Turnover
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
