<?php

namespace App\Repository;

use App\Entity\Poll;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Poll>
 */
class PollRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Poll::class);
    }

    /**
     * @return Poll[]
     */
    public function findLatest(int $int): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.startAt', 'DESC')
            ->setMaxResults($int)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneActive(): ?Poll
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.startAt <= :now')
            ->andWhere('p.endAt >= :now')
            ->andWhere('p.isDraft = false')
            ->setParameter('now', new \DateTimeImmutable('now', new \DateTimeZone('UTC')))
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    //    /**
    //     * @return Poll[] Returns an array of Poll objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Poll
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
